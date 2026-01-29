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
     * Find existing user or create new one from SSO payload
     */
    protected function findOrCreateUser(object $payload): ?User
    {
        // Try to find user by SSO ID first
        $user = User::where('sso_id', $payload->id_pengguna)->first();

        if ($user) {
            // Update user data from SSO (mapping ke kolom database yang benar)
            $user->update([
                'nm' => $payload->nm_pengguna,
                'email' => $payload->email,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            return $user;
        }

        // Try to find by username (usn)
        $user = User::where('usn', $payload->username)->first();

        if ($user) {
            // Link existing user to SSO
            $user->update([
                'sso_id' => $payload->id_pengguna,
                'nm' => $payload->nm_pengguna,
                'email' => $payload->email,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            return $user;
        }

        // Try to find by email
        $user = User::where('email', $payload->email)->first();

        if ($user) {
            // Link existing user to SSO
            $user->update([
                'sso_id' => $payload->id_pengguna,
                'nm' => $payload->nm_pengguna,
                'id_sdm' => $payload->id_sdm_pengguna,
                'id_pd' => $payload->id_pd_pengguna,
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
            return $user;
        }

        // Create new user
        // Determine role based on SSO peran_pengguna
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
            'a_aktif' => true,
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
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
     * Note: SSO akses.unila.ac.id tidak menyediakan endpoint logout public,
     * jadi user hanya logout dari aplikasi ini. Session SSO di browser
     * akan tetap aktif sampai expired atau user logout manual dari akses.unila.ac.id
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

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah keluar dari aplikasi.');
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
