<?php

namespace App\Services;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Exception;

class SsoSyncService
{
    public function getRedirectUrl(): string
    {
        $baseUrl = config('services.sso.base_url', env('SSO_BASE_URL'));
        $callback = route('auth.sso.callback');
        return $baseUrl . "/auth/login?service=" . urlencode($callback);
    }

    public function handleCallback(string $ticket): User
    {
        // 1. Validasi Tiket
        $baseUrl = config('services.sso.base_url', env('SSO_BASE_URL'));
        
        $response = Http::get($baseUrl . '/api/validate-ticket', [
            'ticket' => $ticket
        ]);

        if ($response->failed()) {
            throw new Exception("Gagal koneksi ke SSO.");
        }

        $ssoData = $response->json();

        if (!isset($ssoData['sub'])) {
            throw new Exception("User tidak valid.");
        }

        // 2. Sync Data (Termasuk Password)
        return DB::transaction(function () use ($ssoData) {
            
            // Mapping Role
            $roleName = $ssoData['role'] ?? 'Guest';
            $peran = Peran::where('nama_peran', 'ILIKE', $roleName)->first();
            $peranUuid = $peran ? $peran->UUID : null;

            // Mapping Data Lokal
            $localData = [
                'nm'          => $ssoData['name'],
                'email'       => $ssoData['email'],
                'tgl_lahir'   => $ssoData['birthdate'] ?? null,
                'peran_uuid'  => $peranUuid,
                'a_aktif'     => true,
                'last_sync'   => now(),
                
                // [KUNCI SINKRONISASI PASSWORD]
                // Kita ambil hash langsung dari SSO.
                // Karena sesama Laravel (Bcrypt), hash ini valid di sini juga.
                'kata_sandi'  => $ssoData['password_hash'], 
            ];

            // Update jika ada, Buat baru jika tidak ada
            $user = User::updateOrCreate(
                ['usn' => $ssoData['sub']], // Kunci Sync: NIP/NPM
                $localData
            );

            return $user;
        });
    }
}