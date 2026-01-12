<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PONDASI UTAMA
        DB::statement('CREATE SCHEMA IF NOT EXISTS referensi');
        DB::statement('CREATE SCHEMA IF NOT EXISTS transaksi');
        DB::statement('CREATE SCHEMA IF NOT EXISTS master');
    }

    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS master CASCADE');
    }
};