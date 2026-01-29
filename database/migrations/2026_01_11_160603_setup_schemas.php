<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extension UUID (Wajib untuk gen_random_uuid)
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto";'); // Optional tapi recommended

        // 2. Drop existing schemas first (untuk migrate:fresh)
        DB::statement('DROP SCHEMA IF EXISTS audit CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS akun CASCADE');

        // 3. Buat Skema baru
        DB::statement('CREATE SCHEMA akun');
        DB::statement('CREATE SCHEMA referensi');
        DB::statement('CREATE SCHEMA transaksi');
        DB::statement('CREATE SCHEMA audit');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS audit CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS akun CASCADE');
    }
};