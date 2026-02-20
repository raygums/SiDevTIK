<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peran;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SSOController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * SSO API Base URL (for login/token endpoints).
     */
    protected string $ssoBaseUrl = 'https://akses.unila.ac.id/api/live/v1/auth';

    /**
     * SSO Web Logout URL (for destroying the IdP session cookie).
     *
     * WHY a separate URL: The SSO API base lives at /api/live/v1/auth
     * which does NOT have a /logout endpoint (returns 404). The IdP's
     * web-facing logout lives at /auth/logout. These are different routes
     * on the SSO server.
     */
    protected string $ssoLogoutUrl = 'https://akses.unila.ac.id/auth/logout';

    /**
     * Redirect user to SSO login page.
     *
     * The IdP session is destroyed during logout (redirect to /auth/logout),
     * so the user will always see a fresh credential form. The `prompt=login`
     * parameter is still sent as a secondary safeguard in case the IdP session
     * was not properly destroyed (e.g., user navigated away mid-logout).
     */
    public function redirectToSSO(Request $request)
    {
        // Pre-flight: destroy any residual LOCAL auth state.
        // WHY: If a stale session or remember cookie lingers, the auth guard
        // can silently re-authenticate the old user on the next request.
        if (Auth::check()) {
            $user = Auth::user();
            if ($user) {
                $user->forceFill(['remember_token' => null])->saveQuietly();
            }
            Auth::logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $appKey = config('services.sso.app_key');

        if (!$appKey) {
            Log::warning('SSO Redirect aborted: APP_KEY not configured');
            return redirect()->route('login')
                ->with('error', 'Konfigurasi SSO belum diatur. Hubungi administrator.');
        }

        $callbackUrl = route('sso.callback');

        $params = [
            'app_key'      => $appKey,
            'redirect_uri' => $callbackUrl,
            'prompt'       => 'login',
        ];

        $ssoUrl = "{$this->ssoBaseUrl}/login/sso?" . http_build_query($params);

        Log::info('SSO Redirect', [
            'callback_url' => $callbackUrl,
            'sso_url'      => $ssoUrl,
        ]);

        return redirect()->away($ssoUrl);
    }

    /**
     * Handle SSO callback.
     *
     * SECURITY: Before authenticating the new user, any residual auth
     * state is explicitly cleared. This is defense-in-depth against the
     * scenario where a remember_me cookie from User A survives and the
     * callback would otherwise merge User B's SSO identity into User A's
     * authenticated session.
     */
    public function handleCallback(Request $request)
    {
        Log::info('SSO Callback Received', [
            'ip' => $request->ip(),
        ]);

        $token = $request->query('token');

        if (!$token) {
            Log::warning('SSO Callback: No token provided', [
                'ip' => $request->ip(),
                'query' => $request->query(),
            ]);

            // Record failed SSO login: No token
            $this->auditLogService->recordLoginLog(
                userUuid: null,
                status: 'GAGAL_SSO',
                request: $request,
                keterangan: 'SSO callback: Token tidak ditemukan'
            );

            return redirect()->route('login')
                ->with('error', 'Token SSO tidak ditemukan. Silakan coba lagi.');
        }

        Log::info('SSO Token received', [
            'token_length' => strlen($token),
            'token_preview' => substr($token, 0, 50) . '...',
        ]);

        // Validate token using TokenSSO helper
        $payload = TokenSSO($token);

        if (!$payload) {
            Log::warning('SSO Callback: Invalid token', [
                'ip' => $request->ip(),
                'token_preview' => substr($token, 0, 50) . '...',
            ]);

            // Record failed SSO login: Invalid token
            $this->auditLogService->recordLoginLog(
                userUuid: null,
                status: 'GAGAL_SSO',
                request: $request,
                keterangan: 'SSO authentication failed: Token tidak valid atau kadaluarsa'
            );

            return redirect()->route('login')
                ->with('error', 'Token SSO tidak valid atau sudah kadaluarsa.');
        }

        try {
            DB::beginTransaction();

            // Find or create user based on SSO data
            $user = $this->findOrCreateUser($payload);

            if (!$user) {
                throw new \Exception('Gagal membuat atau menemukan data user.');
            }

            // SECURITY: Clear any residual auth state BEFORE authenticating.
            // This prevents the edge case where a stale remember_me cookie
            // causes the new login to inherit the previous user's identity.
            if (Auth::check()) {
                Auth::logout();
            }
            $request->session()->regenerate();

            // Login user WITHOUT remember_me cookie (second argument = false).
            //
            // WHY: For SSO-authenticated users, the IdP is the sole source
            // of truth. A remember_me cookie creates a SECOND authentication
            // vector that survives session()->invalidate() during logout.
            // This is the PRIMARY cause of session bleeding:
            //   1. User A logs in -> remember_me cookie set (90 days default)
            //   2. User A logs out -> session destroyed, but cookie persists
            //   3. User B visits /login/sso -> SessionGuard auto-authenticates
            //      via the remember cookie BEFORE the redirect to the IdP
            //   4. User B is now silently logged in as User A
            //
            // CONSEQUENCE: Without remember_me, users must re-authenticate
            // via SSO after session expiry. This is the correct behavior
            // for federated identity — the IdP owns session lifetime.
            Auth::login($user);

            // Record successful SSO login
            $this->auditLogService->recordLoginLog(
                userUuid: $user->UUID,
                status: 'BERHASIL',
                request: $request,
                keterangan: "Login berhasil via SSO Unila - SSO ID: {$payload->id_pengguna}"
            );

            // Log successful login
            Log::info('SSO Login Success', [
                'user_uuid' => $user->UUID,
                'username' => $user->usn,
                'email' => $user->email,
                'ip' => $request->ip(),
                'sso_id' => $payload->id_pengguna,
            ]);

            DB::commit();

            // Redirect to dashboard
            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang, {$user->nm}!");

        } catch (\Exception $e) {
            DB::rollBack();

            // Record failed SSO login: Exception
            $this->auditLogService->recordLoginLog(
                userUuid: null,
                status: 'GAGAL_SSO',
                request: $request,
                keterangan: "SSO authentication failed: {$e->getMessage()}"
            );

            Log::error('SSO Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $payload,
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Terjadi kesalahan saat proses login: ' . $e->getMessage());
        }
    }

    /**
     * Find existing user or create new one from SSO payload.
     * 
     * SSO-Gate Implementation:
     * - User baru: a_aktif = FALSE (default inactive, butuh approval Verifikator)
     * - User existing: UPDATE data dari SSO, KEEP status a_aktif yang sudah ada
     * 
     * Strategi Pencarian (Cascade Lookup):
     * 1. By sso_id (primary identifier)
     * 2. By username (usn) - untuk link existing account
     * 3. By email - fallback identifier
     * 
     * Semua operasi database menggunakan transaction untuk integritas data.
     * Transaction di-handle oleh caller (handleCallback method).
     */
    protected function findOrCreateUser(object $payload): ?User
    {
        // Try to find user by SSO ID first (paling reliable)
        $user = User::where('sso_id', $payload->id_pengguna)->first();

        if ($user) {
            // User existing: update data dari SSO, PRESERVE a_aktif status
            $user->update([
                'nm' => $payload->nm_pengguna,
                'email' => $payload->email,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_update' => now(),
            ]);
            
            Log::info('SSO User Updated', [
                'user_uuid' => $user->UUID,
                'sso_id' => $user->sso_id,
                'a_aktif' => $user->a_aktif,
            ]);
            
            return $user;
        }

        // Try to find by username (usn) - untuk linking existing account
        $user = User::where('usn', $payload->username)->first();

        if ($user) {
            // Link existing local account ke SSO, PRESERVE a_aktif
            $user->update([
                'sso_id' => $payload->id_pengguna,
                'nm' => $payload->nm_pengguna,
                'email' => $payload->email,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_update' => now(),
            ]);
            
            Log::info('SSO Linked to Existing User (by username)', [
                'user_uuid' => $user->UUID,
                'sso_id' => $user->sso_id,
                'a_aktif' => $user->a_aktif,
            ]);
            
            return $user;
        }

        // Try to find by email - fallback identifier
        $user = User::where('email', $payload->email)->first();

        if ($user) {
            // Link existing account by email, PRESERVE a_aktif
            $user->update([
                'sso_id' => $payload->id_pengguna,
                'nm' => $payload->nm_pengguna,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_update' => now(),
            ]);
            
            Log::info('SSO Linked to Existing User (by email)', [
                'user_uuid' => $user->UUID,
                'sso_id' => $user->sso_id,
                'a_aktif' => $user->a_aktif,
            ]);
            
            return $user;
        }

        // Create new user - CRITICAL: a_aktif = FALSE (SSO-Gate)
        // User baru harus di-approve oleh Verifikator sebelum bisa akses fitur
        $peranUuid = $this->determineRole($payload->peran_pengguna);

        $user = User::create([
            'sso_id' => $payload->id_pengguna,
            'usn' => $payload->username,
            'nm' => $payload->nm_pengguna,
            'email' => $payload->email,
            'kata_sandi' => bcrypt(str()->random(32)),   // Random password (tidak digunakan, login via SSO)
            'id_sdm' => $payload->id_sdm_pengguna,
            'id_pd' => $payload->id_pd_pengguna,
            'peran_uuid' => $peranUuid,
            'a_aktif' => false,                          // DEFAULT: INACTIVE untuk user baru (SSO-Gate)
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'create_at' => now(),
            'id_creator' => null,                        // Self-registered via SSO
        ]);

        Log::info('New SSO User Created (INACTIVE)', [
            'user_uuid' => $user->UUID,
            'sso_id' => $user->sso_id,
            'usn' => $user->usn,
            'email' => $user->email,
            'a_aktif' => $user->a_aktif,
            'note' => 'User requires Verifikator approval',
        ]);

        return $user;
    }

    /**
     * Determine user role based on SSO role
     */
    protected function determineRole($ssoPeran): ?string
    {
        // Default role is 'pengguna'
        $defaultRole = Peran::where('nm_peran', 'Pengguna')->first();

        if (!$ssoPeran) {
            return $defaultRole?->UUID;
        }

        // Map SSO roles to local roles if needed
        // For now, all SSO users get 'Pengguna' role by default
        // Admin/Verifikator/Eksekutor roles should be assigned manually
        
        return $defaultRole?->UUID;
    }

    /**
     * Logout user dari aplikasi DAN dari SSO IdP.
     *
     * Flow:
     * 1. Wipe remember_token on DB row (invalidates any surviving remember cookie)
     * 2. Auth::logout()          -> detach user from guard
     * 3. session()->invalidate() -> destroy session data
     * 4. regenerateToken()       -> issue a fresh CSRF token
     * 5. Return relay view       -> the browser loads IdP logout in a hidden
     *                              iframe (destroys the IdP session cookie),
     *                              then JS auto-redirects to our login page.
     *
     * WHY an intermediate view instead of redirect()->away():
     * redirect()->away(idpLogoutUrl) takes the user to the IdP's domain.
     * The IdP destroys its session then redirects to ITS OWN login page
     * (akses.unila.ac.id/auth/login). The user is now stranded on the IdP
     * site with no way back to our application.
     *
     * The relay view keeps the user on OUR domain and triggers the IdP
     * logout in the background via a hidden iframe. The user sees a clean
     * "Berhasil keluar" message and is automatically redirected to our
     * login page after a short delay.
     *
     * Note: The iframe approach is best-effort. If the IdP sets
     * X-Frame-Options: DENY, the iframe request is blocked by the browser
     * but fails silently — the user still lands on our login page, and
     * prompt=login in redirectToSSO() acts as the secondary safeguard.
     */
    public function logout(Request $request)
    {
        $userInfo = Auth::check()
            ? ['user_uuid' => Auth::user()->UUID, 'username' => Auth::user()->usn, 'ip' => $request->ip()]
            : ['ip' => $request->ip()];

        try {
            // Step 1: Wipe the remember_token on the database row.
            // Ensures any surviving browser cookie is cryptographically invalid.
            $user = Auth::user();
            if ($user) {
                $user->forceFill(['remember_token' => null])->saveQuietly();
            }

            // Step 2: Detach user from the authentication guard.
            Auth::logout();

            // Step 3: Destroy session data + regenerate session ID.
            $request->session()->invalidate();

            // Step 4: Regenerate CSRF token on the new session.
            $request->session()->regenerateToken();

            Log::info('Logout completed', $userInfo);
        } catch (\Exception $e) {
            // Non-critical: local session is destroyed even if this fails.
            Log::warning('Logout session teardown failed', [
                'error'     => $e->getMessage(),
                'user_info' => $userInfo,
            ]);
        }

        // Step 5: Return relay view.
        // The view loads the IdP logout URL in a hidden iframe to destroy
        // the SSO session cookie, then redirects the user to our login page.
        return view('auth.sso-logout', [
            'ssoLogoutUrl' => config('services.sso.logout_url', $this->ssoLogoutUrl),
            'loginUrl'     => route('login'),
        ]);
    }

    /**
     * Get current user info (API endpoint)
     */
    public function me(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $user->UUID,
                'username' => $user->usn,
                'nama' => $user->nm,
                'email' => $user->email,
                'role' => $user->peran?->nm_peran,
            ],
        ]);
    }
}
