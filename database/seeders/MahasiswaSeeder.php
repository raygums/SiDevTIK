<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * MahasiswaSeeder - Create 50 mahasiswa accounts (tanpa SSO)
 * Password untuk semua: password
 */
class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating mahasiswa users...');

        // Get Pengguna role
        $penggunaRole = Peran::where('nm_peran', 'Pengguna')->first();
        
        if (!$penggunaRole) {
            $this->command->error("Role 'Pengguna' not found! Cannot create mahasiswa users.");
            return;
        }

        // Daftar nama mahasiswa
        $namaDepan = ['Ahmad', 'Budi', 'Citra', 'Dedi', 'Eka', 'Fitri', 'Gita', 'Hadi', 'Intan', 'Joko',
                      'Kartika', 'Lina', 'Maya', 'Nanda', 'Oki', 'Putri', 'Qori', 'Rani', 'Sari', 'Toni',
                      'Umar', 'Vina', 'Wati', 'Yudi', 'Zahra'];
        $namaBelakang = ['Pratama', 'Santoso', 'Wijaya', 'Kusuma', 'Lestari', 'Permata', 'Saputra', 'Andini',
                         'Nugroho', 'Rahayu', 'Setiawan', 'Wulandari', 'Hakim', 'Safitri', 'Ramadhan',
                         'Maharani', 'Putra', 'Dewi', 'Firmansyah', 'Anggraini', 'Kurniawan', 'Salsabila',
                         'Hidayat', 'Azzahra', 'Hermawan'];

        $fakultas = ['FT', 'FMIPA', 'FEB', 'FISIP', 'FH', 'FP', 'FKIP', 'FK'];
        $tahunMasuk = [2020, 2021, 2022, 2023, 2024];

        $mahasiswas = [];
        
        for ($i = 1; $i <= 50; $i++) {
            $namaIndex1 = ($i - 1) % count($namaDepan);
            $namaIndex2 = floor(($i - 1) / count($namaDepan)) % count($namaBelakang);
            
            $nama = $namaDepan[$namaIndex1] . ' ' . $namaBelakang[$namaIndex2];
            $tahun = $tahunMasuk[($i - 1) % count($tahunMasuk)];
            $fakultasCode = $fakultas[($i - 1) % count($fakultas)];
            
            // Format NIM: TTTTFXXXXX (TTTT=Tahun, F=Fakultas index, XXXXX=Nomor urut)
            $nim = $tahun . str_pad($i, 5, '0', STR_PAD_LEFT);
            
            $mahasiswas[] = [
                'nm' => $nama,
                'usn' => $nim,
                'email' => strtolower(str_replace(' ', '.', $nama)) . '@student.unila.ac.id',
                'ktp' => '1871' . str_pad(1000 + $i, 12, '0', STR_PAD_LEFT),
                'tgl_lahir' => (2000 + ($i % 5)) . '-' . str_pad(($i % 12) + 1, 2, '0', STR_PAD_LEFT) . '-' . str_pad(($i % 28) + 1, 2, '0', STR_PAD_LEFT),
            ];
        }

        $created = 0;
        $skipped = 0;

        foreach ($mahasiswas as $mahasiswaData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $mahasiswaData['email'])
                         ->orWhere('usn', $mahasiswaData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $mahasiswaData['nm'],
                    'usn' => $mahasiswaData['usn'],
                    'email' => $mahasiswaData['email'],
                    'ktp' => $mahasiswaData['ktp'],
                    'tgl_lahir' => $mahasiswaData['tgl_lahir'],
                    'kata_sandi' => Hash::make('password'),
                    'peran_uuid' => $penggunaRole->UUID,
                    'a_aktif' => false, // Belum aktif, perlu aktivasi admin
                    'sso_id' => null, // Tanpa SSO
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $created++;
                
                // Show progress every 10 users
                if ($created % 10 == 0) {
                    $this->command->info("✓ Created {$created} mahasiswa users...");
                }
            } else {
                $skipped++;
            }
        }

        $this->command->info('');
        $this->command->info("Mahasiswa users created successfully!");
        $this->command->info("Total created: {$created} mahasiswa");
        if ($skipped > 0) {
            $this->command->warn("Total skipped (already exists): {$skipped} mahasiswa");
        }
        $this->command->info('Default password for all mahasiswa: password');
        $this->command->info('Status: Belum aktif (perlu aktivasi oleh Admin/Verifikator)');
    }
}
