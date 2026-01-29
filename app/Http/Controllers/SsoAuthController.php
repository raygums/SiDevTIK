<?php

namespace App\Http\Controllers;

use App\Services\SsoSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoAuthController extends Controller
{
    protected $ssoService;

    public function __construct(SsoSyncService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    // 1. Redirect (Bisa dipanggil dari tombol Login maupun Register)
    public function redirect()
    {
        return redirect($this->ssoService->getRedirectUrl());
    }

    // 2. Callback Handler
    public function callback(Request $request)
    {
        $ticket = $request->query('ticket');

        if (!$ticket) {
            return redirect()->route('login')->withErrors(['sso' => 'Tiket otentikasi hilang.']);
        }

        try {
            // Proses Sync (Register User Baru / Update User Lama)
            $user = $this->ssoService->handleCallback($ticket);

            // [LOGIC VALIDASI ADMIN]
            // Jika akun belum diaktifkan oleh Admin -> Tampilkan Halaman Pending
            if (!$user->a_aktif) {
                // Kita simpan nama di session flash untuk sapaan, tapi jangan login-kan user
                return redirect()->route('auth.pending')->with('user_name', $user->nm);
            }

            // Jika Aktif -> Login Masuk Dashboard
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['sso' => 'Gagal Login: ' . $e->getMessage()]);
        }
    }
}