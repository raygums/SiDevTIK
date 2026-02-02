<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * AdminSeeder - Create 5 admin accounts
 * Password untuk semua: password
 */
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating admin users...');

        // Get Admin role
        $adminRole = Peran::where('nm_peran', 'LIKE', '%Admin%')->first();
        
        if (!$adminRole) {
            $this->command->error("Role 'Administrator' not found! Cannot create admin users.");
            return;
        }

        // Define admin users
        $admins = [
            [
                'nm' => 'Super Admin',
                'usn' => 'admin',
                'email' => 'admin@unila.ac.id',
                'ktp' => '1871000000000001',
                'tgl_lahir' => '1990-01-01',
            ],
            [
                'nm' => 'Admin TIK',
                'usn' => 'admin.tik',
                'email' => 'admin.tik@unila.ac.id',
                'ktp' => '1871000000000011',
                'tgl_lahir' => '1988-05-15',
            ],
            [
                'nm' => 'Admin Sistem',
                'usn' => 'admin.sistem',
                'email' => 'admin.sistem@unila.ac.id',
                'ktp' => '1871000000000012',
                'tgl_lahir' => '1992-08-20',
            ],
            [
                'nm' => 'Admin Infrastruktur',
                'usn' => 'admin.infra',
                'email' => 'admin.infra@unila.ac.id',
                'ktp' => '1871000000000013',
                'tgl_lahir' => '1989-11-10',
            ],
            [
                'nm' => 'Admin Layanan',
                'usn' => 'admin.layanan',
                'email' => 'admin.layanan@unila.ac.id',
                'ktp' => '1871000000000014',
                'tgl_lahir' => '1991-03-25',
            ],
        ];

        foreach ($admins as $adminData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $adminData['email'])
                         ->orWhere('usn', $adminData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $adminData['nm'],
                    'usn' => $adminData['usn'],
                    'email' => $adminData['email'],
                    'ktp' => $adminData['ktp'],
                    'tgl_lahir' => $adminData['tgl_lahir'],
                    'kata_sandi' => Hash::make('password'),
                    'peran_uuid' => $adminRole->UUID,
                    'a_aktif' => true,
                    'sso_id' => null,
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $this->command->info("✓ Created: {$adminData['email']} ({$adminData['nm']})");
            } else {
                $this->command->warn("⊘ Admin {$adminData['email']} already exists, skipped");
            }
        }

        $this->command->info('');
        $this->command->info('Admin users created successfully!');
        $this->command->info('Default password for all admins: password');
    }
}
