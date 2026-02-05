<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * PimpinanSeeder - Create 5 pimpinan accounts
 * Password untuk semua: password
 */
class PimpinanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating pimpinan users...');

        // Get Pimpinan role (super admin)
        $pimpinanRole = Peran::where('nm_peran', 'Pimpinan')->first();
        
        if (!$pimpinanRole) {
            $this->command->error("Role 'Pimpinan' not found! Cannot create pimpinan users.");
            return;
        }

        // Define pimpinan users
        $pimpinans = [
            [
                'nm' => 'pimpinan',
                'usn' => 'pimpinan',
                'email' => 'pimpinan@unila.ac.id',
                'ktp' => '1871000000000041',
                'tgl_lahir' => '1965-05-10',
            ],
            [
                'nm' => 'Prof. Dr. Ir. Murhadi, M.T.',
                'usn' => '196708201992031002',
                'email' => 'murhadi@unila.ac.id',
                'ktp' => '1871000000000042',
                'tgl_lahir' => '1967-08-20',
            ],
            [
                'nm' => 'Dr. Eng. Ardian Ulvan, S.T., M.T.',
                'usn' => '197512152000031001',
                'email' => 'ardian.ulvan@unila.ac.id',
                'ktp' => '1871000000000043',
                'tgl_lahir' => '1975-12-15',
            ],
            [
                'nm' => 'Dr. Helmy Fitriawan, S.Kom., M.Kom.',
                'usn' => '198003102005011002',
                'email' => 'helmy.fitriawan@unila.ac.id',
                'ktp' => '1871000000000044',
                'tgl_lahir' => '1980-03-10',
            ],
            [
                'nm' => 'Dr. Rina Fitriana, S.Si., M.Si.',
                'usn' => '197809252003012001',
                'email' => 'rina.fitriana@unila.ac.id',
                'ktp' => '1871000000000045',
                'tgl_lahir' => '1978-09-25',
            ],
        ];

        foreach ($pimpinans as $pimpinanData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $pimpinanData['email'])
                         ->orWhere('usn', $pimpinanData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $pimpinanData['nm'],
                    'usn' => $pimpinanData['usn'],
                    'email' => $pimpinanData['email'],
                    'ktp' => $pimpinanData['ktp'],
                    'tgl_lahir' => $pimpinanData['tgl_lahir'],
                    'kata_sandi' => Hash::make('password'),
                    'peran_uuid' => $pimpinanRole->UUID,
                    'a_aktif' => true,
                    'sso_id' => null,
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $this->command->info("✓ Created: {$pimpinanData['email']} ({$pimpinanData['nm']})");
            } else {
                $this->command->warn("⊘ Pimpinan {$pimpinanData['email']} already exists, skipped");
            }
        }

        $this->command->info('');
        $this->command->info('Pimpinan users created successfully!');
        $this->command->info('Default password for all pimpinans: password');
    }
}
