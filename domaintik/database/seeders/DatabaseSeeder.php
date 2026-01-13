<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'Pemohon', 'code' => 'user'],
            ['id' => 2, 'name' => 'Verifikator', 'code' => 'verifikator'],
            ['id' => 3, 'name' => 'Eksekutor', 'code' => 'eksekutor'],
            ['id' => 4, 'name' => 'Super Admin', 'code' => 'admin'],
        ];
        
        DB::table('referensi.roles')->upsert($roles, ['id'], ['name', 'code']);

        DB::table('referensi.sso_role_mappings')->insert([
            ['sso_group_name' => 'mahasiswa', 'target_role_id' => 1], 
            ['sso_group_name' => 'dosen', 'target_role_id' => 1],     
            ['sso_group_name' => 'tendik', 'target_role_id' => 1],    
      
        ]);

        DB::table('users')->insertOrIgnore([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi.mhs@unila.ac.id',
            'nomor_identitas' => '2215061001',
            'role_id' => 1,
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        // Siti (Verifikator) -> Role 2
        DB::table('users')->insertOrIgnore([
            'name' => 'Siti Verifikator',
            'email' => 'siti.verif@tik.unila.ac.id',
            'nomor_identitas' => '198702152011012002',
            'role_id' => 2,
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        // Andi (Eksekutor) -> Role 3
        DB::table('users')->insertOrIgnore([
            'name' => 'Andi Eksekutor',
            'email' => 'andi.eksekutor@tik.unila.ac.id',
            'nomor_identitas' => '199003202015011003',
            'role_id' => 3, 
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        // Admin TIK -> Role 4
        DB::table('users')->insertOrIgnore([
            'name' => 'Super Admin TIK',
            'email' => 'admin@tik.unila.ac.id',
            'nomor_identitas' => '198501012010011001',
            'role_id' => 4, 
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);
        

        $catFakultas = DB::table('referensi.unit_categories')->insertGetId(['name' => 'Fakultas']);
        $catLembaga  = DB::table('referensi.unit_categories')->insertGetId(['name' => 'Lembaga/UPT']);
        $catUKM      = DB::table('referensi.unit_categories')->insertGetId(['name' => 'UKM/Organisasi']);

        $units = [
            // Fakultas
            ['category_id' => $catFakultas, 'name' => 'Fakultas Teknik', 'code' => 'FT'],
            ['category_id' => $catFakultas, 'name' => 'Fakultas Ekonomi dan Bisnis', 'code' => 'FEB'],
            ['category_id' => $catFakultas, 'name' => 'Fakultas Kedokteran', 'code' => 'FK'],
            ['category_id' => $catFakultas, 'name' => 'Fakultas Hukum', 'code' => 'FH'],
            
            // Lembaga
            ['category_id' => $catLembaga, 'name' => 'UPA TIK', 'code' => 'TIK'],
            ['category_id' => $catLembaga, 'name' => 'LP3M', 'code' => 'LP3M'],
            
            // UKM
            ['category_id' => $catUKM, 'name' => 'BEM Universitas', 'code' => 'BEM-U'],
            ['category_id' => $catUKM, 'name' => 'Hima Komputasi', 'code' => 'HIMAKOM'],
        ];

        DB::table('referensi.units')->insert($units);

        DB::table('referensi.service_types')->insert([
            ['name' => 'Pembuatan Domain Baru (.unila.ac.id)'],
            ['name' => 'Pembuatan Hosting Baru'],
            ['name' => 'Perpanjangan Layanan'],
            ['name' => 'Permintaan VPS (Virtual Private Server)'],
        ]);
        
        $this->command->info('Data Master Berhasil Ditanam! 🌱');
    }
}