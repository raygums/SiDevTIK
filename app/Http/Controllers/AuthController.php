<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthService;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Menampilkan halaman login.
     * 
     * Halaman ini menyediakan dua opsi autentikasi:
     * 1. Login lokal dengan username dan password
     * 2. Login melalui SSO Unila untuk dosen/tendik
     * 
     * @return View
     */
    public function index(): View
    {
        return view('auth.login'); 
    }

    /**
     * Memproses login menggunakan kredensial lokal.
     * 
     * Alur proses:
     * 1. Validasi input username dan password
     * 2. Cari user berdasarkan username atau email
     * 3. Verifikasi password menggunakan Hash::check
     * 4. Cek status aktif user
     * 5. Login dan redirect ke dashboard
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Cari user berdasarkan username atau email
        $user = User::where('usn', $credentials['username'])
            ->orWhere('email', $credentials['username'])
            ->first();

        // Validasi user exists
        if (!$user) {
            // Record failed login: User tidak ditemukan
            $this->auditLogService->recordLoginLog(
                userUuid: null,
                status: 'GAGAL_NOT_FOUND',
                request: $request,
                keterangan: "Login attempt dengan username '{$credentials['username']}' - User tidak terdaftar"
            );

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => 'Username atau password yang Anda masukkan salah.',
                ]);
        }

        // Validasi password benar
        if (!Hash::check($credentials['password'], $user->kata_sandi)) {
            // Record failed login: Password salah
            $this->auditLogService->recordLoginLog(
                userUuid: $user->UUID,
                status: 'GAGAL_PASSWORD',
                request: $request,
                keterangan: "Password salah untuk user '{$user->usn}'"
            );

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => 'Username atau password yang Anda masukkan salah.',
                ]);
        }

        // Cek apakah user aktif
        if (!$user->a_aktif) {
            // Record failed login: Akun suspended
            $this->auditLogService->recordLoginLog(
                userUuid: $user->UUID,
                status: 'GAGAL_SUSPEND',
                request: $request,
                keterangan: "Akun suspended - User '{$user->usn}' tidak aktif"
            );

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
                ]);
        }

        // Update last login information
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Login user dengan opsi remember me
        Auth::login($user, $request->filled('remember'));
        
        // Regenerate session untuk keamanan
        $request->session()->regenerate();

        // Record successful login
        $this->auditLogService->recordLoginLog(
            userUuid: $user->UUID,
            status: 'BERHASIL',
            request: $request,
            keterangan: 'Login berhasil via local authentication'
        );

        // Log successful login
        Log::info('Local Login Success', [
            'user_uuid' => $user->UUID,
            'username' => $user->usn,
            'ip' => $request->ip(),
        ]);

        // Redirect ke intended page atau dashboard
        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang, {$user->nm}!");
    }

    /**
     * Logout user dan invalidate session.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (method_exists($this->authService, 'logout')) {
            $this->authService->logout();
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/')->with('success', 'Anda berhasil logout.');
    }
}