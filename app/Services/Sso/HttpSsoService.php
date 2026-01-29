<?php

namespace App\Services\Sso;

use App\Contracts\SsoProviderInterface;
use App\DTOs\SsoUserDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class HttpSsoService implements SsoProviderInterface
{
    public function getLoginUrl(): string
    {
        // URL Callback: Tempat user kembali setelah login di SSO
        $callbackUrl = route('auth.sso.callback');

        // URL Server SSO: Kita arahkan user ke port 9000
        $ssoBaseUrl = config('services.sso.url', env('SSO_BASE_URL'));
        
        return $ssoBaseUrl . "/auth/login?service=" . urlencode($callbackUrl);
    }

    public function handleCallback(Request $request): SsoUserDto
    {
        // 1. Tangkap Tiket dari URL
        $ticket = $request->input('ticket');

        if (!$ticket) {
            throw ValidationException::withMessages(['sso' => 'Gagal login: Tiket otentikasi tidak ditemukan.']);
        }

        // 2. BACK-CHANNEL REQUEST (Server ke Server)
        // Aplikasi Utama (Port 8000) menelpon SSO Clone (Port 9000)
        // "Halo SSO, ini ada tiket ST-XXX, siapa pemiliknya?"
        $ssoBaseUrl = config('services.sso.url', env('SSO_BASE_URL'));
        
        try {
            $response = Http::timeout(5)->get($ssoBaseUrl . '/api/validate-ticket', [
                'ticket' => $ticket
            ]);
        } catch (\Exception $e) {
             throw ValidationException::withMessages(['sso' => 'Gagal menghubungi server SSO. Pastikan server SSO berjalan di port 9000.']);
        }

        if ($response->failed()) {
            throw ValidationException::withMessages(['sso' => 'Validasi tiket SSO gagal atau tiket sudah kadaluarsa.']);
        }

        $userData = $response->json();

        // 3. Mapping Data dari JSON SSO ke DTO Aplikasi Kita
        // Perhatikan key arraynya ('sub', 'name', dll) harus sesuai output JSON SSO Clone
        return new SsoUserDto(
            identifier: $userData['sub'],        // NIP/NPM
            name:       $userData['name'],
            email:      $userData['email'],
            rawRole:    $userData['role'],       // 'mhs', 'dosen', 'staff'
            unit:       $userData['unit'] ?? '-',
            birthdate:  $userData['birthdate'] ?? null
        );
    }
}