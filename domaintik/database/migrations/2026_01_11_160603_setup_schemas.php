<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extension UUID
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        // 2. Buat 5 Skema sesuai request
        // Public sudah ada default postgres, tapi kita pastikan urutannya
        DB::statement('CREATE SCHEMA IF NOT EXISTS akun');
        DB::statement('CREATE SCHEMA IF NOT EXISTS referensi');
        DB::statement('CREATE SCHEMA IF NOT EXISTS transaksi');
        DB::statement('CREATE SCHEMA IF NOT EXISTS audit');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS audit CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS akun CASCADE');
    }
};