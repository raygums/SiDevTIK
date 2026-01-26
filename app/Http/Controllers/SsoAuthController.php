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

    // 1. Redirect User ke SSO Clone
    public function redirect()
    {
        return redirect($this->ssoService->getRedirectUrl());
    }

    // 2. Terima Tiket & Login User
    public function callback(Request $request)
    {
        $ticket = $request->query('ticket');

        if (!$ticket) {
            return redirect()->route('login')->withErrors(['sso' => 'Tiket otentikasi tidak ditemukan.']);
        }

        try {
            // Panggil Service untuk sync data & dapatkan user lokal
            $user = $this->ssoService->handleCallback($ticket);

            // Login user ke sesi Laravel
            Auth::login($user);

            // Regenerasi sesi untuk keamanan (Fixation Attack Protection)
            $request->session()->regenerate();

            return redirect()->intended('dashboard'); // Redirect ke Dashboard

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['sso' => 'Login Gagal: ' . $e->getMessage()]);
        }
    }
}