<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * VerifikatorSeeder - Create 5 verifikator accounts
 * Password untuk semua: password
 */
class VerifikatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating verifikator users...');

        // Get Verifikator role
        $verifikatorRole = Peran::where('nm_peran', 'Verifikator')->first();
        
        if (!$verifikatorRole) {
            $this->command->error("Role 'Verifikator' not found! Cannot create verifikator users.");
            return;
        }

        // Define verifikator users
        $verifikators = [
            [
                'nm' => 'Siti Nurhaliza',
                'usn' => '198702152011012002',
                'email' => 'siti.nurhaliza@unila.ac.id',
                'ktp' => '1871000000000021',
                'tgl_lahir' => '1987-02-15',
            ],
            [
                'nm' => 'Budi Santoso',
                'usn' => '198905102012011001',
                'email' => 'budi.santoso@unila.ac.id',
                'ktp' => '1871000000000022',
                'tgl_lahir' => '1989-05-10',
            ],
            [
                'nm' => 'Dewi Lestari',
                'usn' => '199112202014012003',
                'email' => 'dewi.lestari@unila.ac.id',
                'ktp' => '1871000000000023',
                'tgl_lahir' => '1991-12-20',
            ],
            [
                'nm' => 'Ahmad Fauzi',
                'usn' => '198808152013011002',
                'email' => 'ahmad.fauzi@unila.ac.id',
                'ktp' => '1871000000000024',
                'tgl_lahir' => '1988-08-15',
            ],
            [
                'nm' => 'Ratna Sari',
                'usn' => '199203102015012001',
                'email' => 'ratna.sari@unila.ac.id',
                'ktp' => '1871000000000025',
                'tgl_lahir' => '1992-03-10',
            ],
        ];

        foreach ($verifikators as $verifikatorData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $verifikatorData['email'])
                         ->orWhere('usn', $verifikatorData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $verifikatorData['nm'],
                    'usn' => $verifikatorData['usn'],
                    'email' => $verifikatorData['email'],
                    'ktp' => $verifikatorData['ktp'],
                    'tgl_lahir' => $verifikatorData['tgl_lahir'],
                    'kata_sandi' => Hash::make('password'),
                    'peran_uuid' => $verifikatorRole->UUID,
                    'a_aktif' => true,
                    'sso_id' => null,
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $this->command->info("✓ Created: {$verifikatorData['email']} ({$verifikatorData['nm']})");
            } else {
                $this->command->warn("⊘ Verifikator {$verifikatorData['email']} already exists, skipped");
            }
        }

        $this->command->info('');
        $this->command->info('Verifikator users created successfully!');
        $this->command->info('Default password for all verifikators: password');
    }
}
