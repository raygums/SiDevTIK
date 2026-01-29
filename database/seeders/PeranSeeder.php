<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'Verifikator', 'Eksekutor', 'Pemohon'];
        
        foreach ($roles as $role) {
            // Cek existensi agar tidak duplikat
            if (!DB::table('akun.peran')->where('nama_peran', $role)->exists()) {
                DB::table('akun.peran')->insert([
                    'UUID' => Str::uuid(),
                    'nama_peran' => $role,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}