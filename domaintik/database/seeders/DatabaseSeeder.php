<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi.mahasiswa@students.unila.ac.id',
            'password' => Hash::make('password'),
            'created_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin TIK',
            'email' => 'helpdesk@tik.unila.ac.id',
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