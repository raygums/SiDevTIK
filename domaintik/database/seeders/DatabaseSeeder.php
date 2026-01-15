<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Status Pengajuan (Referensi)
        $statuses = ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Revisi'];
        foreach ($statuses as $status) {
            DB::table('referensi.status_pengajuan')->insert([
                'UUID' => Str::uuid(),
                'nm_status' => $status
            ]);
        }

        // 2. Seed Kategori Unit (Referensi)
        $catFakultasId = Str::uuid();
        DB::table('referensi.kategori_unit')->insert([
            'UUID' => $catFakultasId,
            'nm_kategori' => 'Fakultas'
        ]);

        // 3. Seed Unit Kerja (Referensi)
        DB::table('referensi.unit_kerja')->insert([
            'UUID' => Str::uuid(),
            'nm_unit' => 'Fakultas Teknik',
            'kode_unit' => 'FT',
            'kategori_uuid' => $catFakultasId,
            'a_aktif' => true
        ]);

        // 4. Seed Jenis Layanan (Referensi)
        $layanan = ['Domain Baru', 'Hosting', 'VPS'];
        foreach ($layanan as $l) {
            DB::table('referensi.jenis_layanan')->insert([
                'UUID' => Str::uuid(),
                'nm_layanan' => $l,
                'a_aktif' => true
            ]);
        }

        // 5. Seed Peran (Akun)
        $roleAdminId = Str::uuid();
        DB::table('akun.peran')->insert([
            'UUID' => $roleAdminId,
            'nm_peran' => 'Administrator',
            'a_aktif' => true
        ]);

        DB::table('akun.peran')->insert([
            'UUID' => Str::uuid(),
            'nm_peran' => 'Pengguna',
            'a_aktif' => true
        ]);

        // 6. Seed Pengguna Admin (Akun)
        DB::table('akun.pengguna')->insert([
            'UUID' => Str::uuid(),
            'nm' => 'Super Admin',
            'usn' => 'admin',
            'email' => 'admin@unila.ac.id',
            'ktp' => '1871000000000001',
            'tgl_lahir' => '1990-01-01',
            'kata_sandi' => Hash::make('password'),
            'peran_uuid' => $roleAdminId,
            'a_aktif' => true,
            'wkt_dibuat' => now()
        ]);
        
        // 7. Seed Mapping SSO (Opsional)
        DB::table('akun.pemetaan_peran_sso')->insert([
            'UUID' => Str::uuid(),
            'atribut_sso' => 'admin-group',
            'peran_uuid' => $roleAdminId
        ]);
    }
}