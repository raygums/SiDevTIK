<?php

namespace App\Services;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Str;
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
        // 1. Validasi Tiket ke Server SSO
        $baseUrl = config('services.sso.base_url', env('SSO_BASE_URL'));
        $response = Http::get($baseUrl . '/api/validate-ticket', ['ticket' => $ticket]);

        if ($response->failed() || !isset($response->json()['sub'])) {
            throw new Exception("Gagal memvalidasi tiket SSO.");
        }

        $ssoData = $response->json();

        // 2. Transaksi Database (Sync Data)
        return DB::transaction(function () use ($ssoData) {
            
            // Cek apakah user sudah ada di database kita?
            $user = User::where('usn', $ssoData['sub'])->first();

            // === SKENARIO A: USER BARU (REGISTRASI) ===
            if (!$user) {
                // Ambil Role Default: "Pemohon" (Pastikan di database tabel peran ada 'Pemohon')
                $defaultRole = Peran::where('nama_peran', 'Pemohon')->first();
                
                $user = User::create([
                    'usn'           => $ssoData['sub'], // Identifier (NIP/NPM)
                    'nm'            => $ssoData['name'],
                    'email'         => $ssoData['email'],
                    'tgl_lahir'     => $ssoData['birthdate'] ?? null,
                    'kata_sandi'    => $ssoData['password_hash'], // Sync Password
                    
                    // [PENTING] Set Status NON-AKTIF untuk pendaftar baru
                    'a_aktif'       => false, 
                    'peran_uuid'    => $defaultRole ? $defaultRole->UUID : null,
                    
                    'last_sync'     => now(),
                ]);
            } 
            // === SKENARIO B: USER LAMA (LOGIN RUTIN) ===
            else {
                // Update data profil, tapi JANGAN ubah status aktif/role manual admin
                $user->update([
                    'nm'            => $ssoData['name'],
                    'email'         => $ssoData['email'],
                    'tgl_lahir'     => $ssoData['birthdate'] ?? null,
                    'kata_sandi'    => $ssoData['password_hash'], 
                    'last_sync'     => now(),
                ]);
            }

            return $user;
        });
    }
}