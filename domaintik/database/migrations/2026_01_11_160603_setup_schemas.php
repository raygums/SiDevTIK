<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS master CASCADE');

        DB::statement('CREATE SCHEMA referensi');
        DB::statement('CREATE SCHEMA transaksi');
        DB::statement('CREATE SCHEMA master');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP SCHEMA IF EXISTS referensi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS transaksi CASCADE');
        DB::statement('DROP SCHEMA IF EXISTS master CASCADE');
    }
};