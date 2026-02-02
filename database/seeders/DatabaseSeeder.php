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
        // ==========================================================
        // 1. STATUS PENGAJUAN (Flow: Draft → Diajukan → Verifikasi → Eksekusi → Selesai)
        // ==========================================================
        $statuses = [
            'Draft',                    // Belum disubmit
            'Diajukan',                 // Sudah submit, menunggu verifikasi
            'Menunggu Verifikasi',      // Antrian verifikator
            'Disetujui Verifikator',    // Lolos verifikasi, masuk ke eksekutor
            'Ditolak Verifikator',      // Ditolak oleh verifikator
            'Menunggu Eksekusi',        // Antrian eksekutor
            'Sedang Dikerjakan',        // Eksekutor sedang mengerjakan
            'Ditolak Eksekutor',        // Eksekutor menolak (ada kendala)
            'Selesai',                  // Pengajuan berhasil diselesaikan
            'Dibatalkan',               // User membatalkan
        ];
        
        foreach ($statuses as $status) {
            DB::table('referensi.status_pengajuan')->insert([
                'UUID' => Str::uuid(),
                'nm_status' => $status
            ]);
        }

        // ==========================================================
        // 2. KATEGORI UNIT
        // ==========================================================
        $catFakultasId = Str::uuid();
        $catUptId = Str::uuid();
        $catLembagaId = Str::uuid();
        
        DB::table('referensi.kategori_unit')->insert([
            ['UUID' => $catFakultasId, 'nm_kategori' => 'Fakultas'],
            ['UUID' => $catUptId, 'nm_kategori' => 'UPT'],
            ['UUID' => $catLembagaId, 'nm_kategori' => 'Lembaga'],
        ]);

        // ==========================================================
        // 3. UNIT KERJA
        // ==========================================================
        DB::table('referensi.unit_kerja')->insert([
            [
                'UUID' => Str::uuid(),
                'nm_lmbg' => 'Fakultas Teknik',
                'kode_unit' => 'FT',
                'kategori_uuid' => $catFakultasId,
                'a_aktif' => true
            ],
            [
                'UUID' => Str::uuid(),
                'nm_lmbg' => 'Fakultas MIPA',
                'kode_unit' => 'FMIPA',
                'kategori_uuid' => $catFakultasId,
                'a_aktif' => true
            ],
            [
                'UUID' => Str::uuid(),
                'nm_lmbg' => 'UPT TIK',
                'kode_unit' => 'TIK',
                'kategori_uuid' => $catUptId,
                'a_aktif' => true
            ],
        ]);

        // ==========================================================
        // 4. JENIS LAYANAN
        // ==========================================================
        $layanan = [
            ['nm' => 'domain', 'desc' => 'Sub Domain *.unila.ac.id'],
            ['nm' => 'hosting', 'desc' => 'Web Hosting'],
            ['nm' => 'vps', 'desc' => 'Virtual Private Server'],
        ];
        foreach ($layanan as $l) {
            DB::table('referensi.jenis_layanan')->insert([
                'UUID' => Str::uuid(),
                'nm_layanan' => $l['nm'],
                'deskripsi' => $l['desc'],
                'a_aktif' => true
            ]);
        }

        // ==========================================================
        // 5. PERAN (ROLES) - 4 Role Utama
        // ==========================================================
        $roleAdminId = Str::uuid();
        $roleVerifikatorId = Str::uuid();
        $roleEksekutorId = Str::uuid();
        $rolePenggunaId = Str::uuid();

        DB::table('akun.peran')->insert([
            [
                'UUID' => $roleAdminId,
                'nm_peran' => 'Administrator',
                'a_aktif' => true
            ],
            [
                'UUID' => $roleVerifikatorId,
                'nm_peran' => 'Verifikator',
                'a_aktif' => true
            ],
            [
                'UUID' => $roleEksekutorId,
                'nm_peran' => 'Eksekutor',
                'a_aktif' => true
            ],
            [
                'UUID' => $rolePenggunaId,
                'nm_peran' => 'Pengguna',
                'a_aktif' => true
            ],
        ]);

        // ==========================================================
        // 6. PEMETAAN SSO → ROLE
        // ==========================================================
        DB::table('akun.pemetaan_peran_sso')->insert([
            [
                'UUID' => Str::uuid(),
                'atribut_sso' => 'mahasiswa',
                'peran_uuid' => $rolePenggunaId,
            ],
            [
                'UUID' => Str::uuid(),
                'atribut_sso' => 'dosen',
                'peran_uuid' => $rolePenggunaId,
            ],
            [
                'UUID' => Str::uuid(),
                'atribut_sso' => 'tendik',
                'peran_uuid' => $rolePenggunaId,
            ],
        ]);

        // ==========================================================
        // 7. PENGGUNA - Seeded melalui seeder terpisah
        // ==========================================================
        $this->call([
            AdminSeeder::class,        // 5 Admin users
            VerifikatorSeeder::class,  // 5 Verifikator users
            EksekutorSeeder::class,    // 5 Eksekutor users
            PimpinanSeeder::class,     // 5 Pimpinan users
            MahasiswaSeeder::class,    // 50 Mahasiswa users (tanpa SSO)
        ]);
    }
}