<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * EksekutorSeeder - Create 5 eksekutor accounts
 * Password untuk semua: password
 */
class EksekutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating eksekutor users...');

        // Get Eksekutor role
        $eksekutorRole = Peran::where('nm_peran', 'Eksekutor')->first();
        
        if (!$eksekutorRole) {
            $this->command->error("Role 'Eksekutor' not found! Cannot create eksekutor users.");
            return;
        }

        // Define eksekutor users
        $eksekutors = [
            [
                'nm' => 'Andi Prasetyo',
                'usn' => '199003202015011003',
                'email' => 'andi.prasetyo@unila.ac.id',
                'ktp' => '1871000000000031',
                'tgl_lahir' => '1990-03-20',
            ],
            [
                'nm' => 'Rudi Hartono',
                'usn' => '198906152013011001',
                'email' => 'rudi.hartono@unila.ac.id',
                'ktp' => '1871000000000032',
                'tgl_lahir' => '1989-06-15',
            ],
            [
                'nm' => 'Yoga Pratama',
                'usn' => '199110252016011002',
                'email' => 'yoga.pratama@unila.ac.id',
                'ktp' => '1871000000000033',
                'tgl_lahir' => '1991-10-25',
            ],
            [
                'nm' => 'Hendra Wijaya',
                'usn' => '198807302014011003',
                'email' => 'hendra.wijaya@unila.ac.id',
                'ktp' => '1871000000000034',
                'tgl_lahir' => '1988-07-30',
            ],
            [
                'nm' => 'Dimas Kusuma',
                'usn' => '199204182017011001',
                'email' => 'dimas.kusuma@unila.ac.id',
                'ktp' => '1871000000000035',
                'tgl_lahir' => '1992-04-18',
            ],
        ];

        foreach ($eksekutors as $eksekutorData) {
            // Check if user already exists by email OR username
            $exists = User::where('email', $eksekutorData['email'])
                         ->orWhere('usn', $eksekutorData['usn'])
                         ->exists();

            if (!$exists) {
                User::create([
                    'UUID' => Str::uuid(),
                    'nm' => $eksekutorData['nm'],
                    'usn' => $eksekutorData['usn'],
                    'email' => $eksekutorData['email'],
                    'ktp' => $eksekutorData['ktp'],
                    'tgl_lahir' => $eksekutorData['tgl_lahir'],
                    'kata_sandi' => Hash::make('password'),
                    'peran_uuid' => $eksekutorRole->UUID,
                    'a_aktif' => true,
                    'sso_id' => null,
                    'create_at' => now(),
                    'last_login_at' => null,
                ]);

                $this->command->info("✓ Created: {$eksekutorData['email']} ({$eksekutorData['nm']})");
            } else {
                $this->command->warn("⊘ Eksekutor {$eksekutorData['email']} already exists, skipped");
            }
        }

        $this->command->info('');
        $this->command->info('Eksekutor users created successfully!');
        $this->command->info('Default password for all eksekutors: password');
    }
}
