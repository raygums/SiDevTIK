<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * PeranSeeder - Seed roles untuk sistem
 * 
 * Roles yang di-seed:
 * 1. Admin - Full access ke semua fitur
 * 2. Verifikator - Verifikasi pengajuan dan aktivasi user
 * 3. Eksekutor - Eksekusi pengajuan yang sudah disetujui
 * 4. Pengguna - User biasa yang mengajukan layanan
 */
class PeranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles dengan struktur yang jelas
        $roles = [
            [
                'nm_peran' => 'Admin',
                'a_aktif' => true,
            ],
            [
                'nm_peran' => 'Verifikator',
                'a_aktif' => true,
            ],
            [
                'nm_peran' => 'Eksekutor',
                'a_aktif' => true,
            ],
            [
                'nm_peran' => 'Pengguna',
                'a_aktif' => true,
            ],
        ];

        foreach ($roles as $role) {
            // Check if role already exists
            $exists = DB::table('akun.peran')
                ->where('nm_peran', $role['nm_peran'])
                ->exists();

            if (!$exists) {
                DB::table('akun.peran')->insert([
                    'UUID' => Str::uuid(),
                    'nm_peran' => $role['nm_peran'],
                    'a_aktif' => $role['a_aktif'],
                    'create_at' => now(),
                ]);

                $this->command->info("✓ Role '{$role['nm_peran']}' created");
            } else {
                $this->command->warn("⊘ Role '{$role['nm_peran']}' already exists, skipped");
            }
        }
    }
}
