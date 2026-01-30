<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * TestUsersSeeder - Create test users untuk development dan testing
 * 
 * User yang di-create:
 * 1. admin@test.com - Admin (full access)
 * 2. verifikator@test.com - Verifikator (verifikasi pengajuan & aktivasi user)
 * 3. eksekutor@test.com - Eksekutor (eksekusi pengajuan)
 * 4. user@test.com - Pengguna biasa
 * 5. user.inactive@test.com - Pengguna yang belum aktif (untuk test aktivasi)
 * 
 * Password untuk semua: password
 */
class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating test users...');

        // Get roles - cari berdasarkan nama peran
        $roles = [
            'Admin' => Peran::where('nm_peran', 'LIKE', '%Admin%')->first(),
            'Verifikator' => Peran::where('nm_peran', 'Verifikator')->first(),
            'Eksekutor' => Peran::where('nm_peran', 'Eksekutor')->first(),
            'Pengguna' => Peran::where('nm_peran', 'Pengguna')->first(),
        ];

        // Validate roles exist
        $missingRoles = [];
        foreach ($roles as $roleName => $role) {
            if (!$role) {
                $missingRoles[] = $roleName;
            }
        }

        if (!empty($missingRoles)) {
            $this->command->warn("Some roles not found: " . implode(', ', $missingRoles));
            $this->command->warn("Attempting to use 'Administrator' role as fallback...");
            
            // Fallback: Try to find Administrator role (dari DatabaseSeeder)
            $adminRole = Peran::where('nm_peran', 'Administrator')->first();
            if ($adminRole && !$roles['Admin']) {
                $roles['Admin'] = $adminRole;
            }
            
            // Check again if we have at least Pengguna role
            if (!$roles['Pengguna']) {
                $this->command->error("Role 'Pengguna' not found! Cannot create test users.");
                $this->command->info("Please ensure database is seeded with roles first.");
                return;
            }
        }

        // Define test users
        $testUsers = [
            [
                'nm' => 'Admin Test',
                'usn' => 'admin.test',
                'email' => 'admin@test.com',
                'peran_uuid' => $roles['Admin'] ? $roles['Admin']->UUID : $roles['Pengguna']->UUID,
                'a_aktif' => true,
                'sso_id' => null, // Akun lokal
            ],
            [
                'nm' => 'Verifikator Test',
                'usn' => 'verifikator',
                'email' => 'verifikator@test.com',
                'peran_uuid' => $roles['Verifikator'] ? $roles['Verifikator']->UUID : $roles['Pengguna']->UUID,
                'a_aktif' => true,
                'sso_id' => null,
            ],
            [
                'nm' => 'Eksekutor Test',
                'usn' => 'eksekutor',
                'email' => 'eksekutor@test.com',
                'peran_uuid' => $roles['Eksekutor'] ? $roles['Eksekutor']->UUID : $roles['Pengguna']->UUID,
                'a_aktif' => true,
                'sso_id' => null,
            ],
            [
                'nm' => 'User Active',
                'usn' => 'useractive',
                'email' => 'user@test.com',
                'peran_uuid' => $roles['Pengguna']->UUID,
                'a_aktif' => true,
                'sso_id' => null,
            ],
            [
                'nm' => 'User Inactive',
                'usn' => 'userinactive',
                'email' => 'user.inactive@test.com',
                'peran_uuid' => $roles['Pengguna']->UUID,
                'a_aktif' => false, // Belum aktif - untuk test aktivasi
                'sso_id' => null,
            ],
            // SSO Test Users
            [
                'nm' => 'Mahasiswa SSO Test',
                'usn' => 'mahasiswa.sso',
                'email' => 'mahasiswa@student.unila.ac.id',
                'peran_uuid' => $roles['Pengguna']->UUID,
                'a_aktif' => false, // Belum aktif
                'sso_id' => 'sso_mahasiswa_' . uniqid(),
                'id_pd' => 'PD_' . Str::random(8), // Mahasiswa identifier
                'id_sdm' => null,
            ],
            [
                'nm' => 'Dosen SSO Test',
                'usn' => 'dosen.sso',
                'email' => 'dosen@unila.ac.id',
                'peran_uuid' => $roles['Pengguna']->UUID,
                'a_aktif' => false, // Belum aktif
                'sso_id' => 'sso_dosen_' . uniqid(),
                'id_pd' => null,
                'id_sdm' => 'SDM_' . Str::random(8), // Dosen/Tendik identifier
            ],
            [
                'nm' => 'Tendik SSO Test',
                'usn' => 'tendik.sso',
                'email' => 'tendik@unila.ac.id',
                'peran_uuid' => $roles['Pengguna']->UUID,
                'a_aktif' => false, // Belum aktif
                'sso_id' => 'sso_tendik_' . uniqid(),
                'id_pd' => null,
                'id_sdm' => 'SDM_' . Str::random(8), // Dosen/Tendik identifier
            ],
        ];

        foreach ($testUsers as $userData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $userData['email'])
                         ->orWhere('usn', $userData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $userData['nm'],
                    'usn' => $userData['usn'],
                    'email' => $userData['email'],
                    'kata_sandi' => Hash::make('password'), // Default password
                    'peran_uuid' => $userData['peran_uuid'],
                    'a_aktif' => $userData['a_aktif'],
                    'sso_id' => $userData['sso_id'],
                    'id_pd' => $userData['id_pd'] ?? null,
                    'id_sdm' => $userData['id_sdm'] ?? null,
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $status = $userData['a_aktif'] ? '✓ Active' : '⊘ Inactive';
                $type = $userData['sso_id'] ? '[SSO]' : '[Local]';
                $this->command->info("{$status} {$type} {$userData['email']} ({$userData['nm']})");
            } else {
                $this->command->warn("⊘ User {$userData['email']} already exists, skipped");
            }
        }

        $this->command->info('');
        $this->command->info('Test users created successfully!');
        $this->command->info('Default password for all users: password');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->table(
            ['Role', 'Email', 'Username', 'Password', 'Status'],
            [
                ['Admin', 'admin@test.com', 'admin', 'password', 'Active'],
                ['Verifikator', 'verifikator@test.com', 'verifikator', 'password', 'Active'],
                ['Eksekutor', 'eksekutor@test.com', 'eksekutor', 'password', 'Active'],
                ['Pengguna', 'user@test.com', 'useractive', 'password', 'Active'],
                ['Pengguna', 'user.inactive@test.com', 'userinactive', 'password', 'Inactive'],
                ['Pengguna (SSO)', 'mahasiswa@student.unila.ac.id', 'mahasiswa.sso', 'password', 'Inactive'],
                ['Pengguna (SSO)', 'dosen@unila.ac.id', 'dosen.sso', 'password', 'Inactive'],
                ['Pengguna (SSO)', 'tendik@unila.ac.id', 'tendik.sso', 'password', 'Inactive'],
            ]
        );
    }
}
