<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SSOController extends Controller
{
    /**
     * SSO Base URL
     */
    protected string $ssoBaseUrl = 'https://akses.unila.ac.id/api/live/v1/auth';

    /**
     * Redirect user to SSO login page
     */
    public function redirectToSSO(Request $request)
    {
        $appKey = config('services.sso.app_key');
        
        Log::info('SSO Redirect Initiated', [
            'app_key' => $appKey ? 'SET ('. strlen($appKey) .' chars)' : 'NOT SET',
        ]);
        
        if (!$appKey) {
            return redirect()->route('login')
                ->with('error', 'Konfigurasi SSO belum diatur. Hubungi administrator.');
        }

        // Build callback URL
        $callbackUrl = route('sso.callback');
        
        // SSO login URL with app_key and callback
        $params = [
            'app_key' => $appKey,
            'redirect_uri' => $callbackUrl,
        ];
        
        // Jika ada parameter ?force=1, tambahkan ke SSO URL untuk force re-login
        if ($request->query('force')) {
            $params['prompt'] = 'login'; // atau 'force' tergantung SSO server
        }
        
        $ssoUrl = "{$this->ssoBaseUrl}/login/sso?" . http_build_query($params);

        Log::info('SSO Redirecting to', [
            'callback_url' => $callbackUrl,
            'sso_url' => $ssoUrl,
        ]);

        return redirect()->away($ssoUrl);
    }

    /**
     * Handle SSO callback
     */
    public function handleCallback(Request $request)
    {
        Log::info('SSO Callback Received', [
            'all_params' => $request->all(),
            'query' => $request->query(),
            'ip' => $request->ip(),
        ]);

        $token = $request->query('token');

        if (!$token) {
            Log::warning('SSO Callback: No token provided', [
                'ip' => $request->ip(),
                'query' => $request->query(),
            ]);
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

            // Login user
            Auth::login($user, true); // Remember me = true

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
     * Logout user dari aplikasi
     * 
     * Strategy:
     * 1. Logout dari Laravel Auth
     * 2. Destroy semua session data
     * 3. Hapus semua cookies (termasuk laravel_session, XSRF-TOKEN, dll)
     * 4. Set flag di session untuk force re-login di SSO saat login berikutnya
     * 
     * Note: SSO Unila tidak support redirect_uri di logout endpoint,
     * jadi kita hanya logout dari aplikasi lokal dan force re-login saat masuk lagi
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User Logout', [
                'user_uuid' => $user->UUID,
                'username' => $user->usn,
                'ip' => $request->ip(),
            ]);
        }

        // Logout dari Laravel Auth
        Auth::logout();
        
        // Hapus semua data session
        session()->flush();
        
        // Invalidate session ID  
        $request->session()->invalidate();

        // Buat response dengan redirect
        $response = redirect()->route('home')
            ->with('success', 'Anda telah keluar dari aplikasi.');
        
        // Hapus cookies yang relevan
        $cookiesToForget = [
            'laravel_session',
            'XSRF-TOKEN', 
            'remember_web_' . sha1(get_class(Auth::guard()) . Auth::getRecallerName()),
        ];
        
        foreach ($cookiesToForget as $cookieName) {
            $response->withCookie(cookie()->forget($cookieName));
        }
        
        // Juga coba hapus semua cookies dari request
        foreach ($request->cookies->keys() as $cookieName) {
            $response->withCookie(cookie()->forget($cookieName));
        }

        Log::info('User Logout Complete - Session & Cookies Destroyed');

        return $response;
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
