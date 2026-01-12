<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User biasa (Mahasiswa)
        DB::table('users')->insert([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi.mahasiswa@students.unila.ac.id',
            'nomor_identitas' => '2215061001',
            'role' => 'user',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Admin TIK (Verifikator + Admin)
        DB::table('users')->insert([
            'name' => 'Admin TIK',
            'email' => 'helpdesk@tik.unila.ac.id',
            'nomor_identitas' => '198501012010011001',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verifikator
        DB::table('users')->insert([
            'name' => 'Siti Verifikator',
            'email' => 'siti.verifikator@unila.ac.id',
            'nomor_identitas' => '198702152011012002',
            'role' => 'verifikator',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Eksekutor (Teknisi)
        DB::table('users')->insert([
            'name' => 'Andi Eksekutor',
            'email' => 'andi.teknisi@tik.unila.ac.id',
            'nomor_identitas' => '199003202015011003',
            'role' => 'eksekutor',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
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