<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Add file_ktp_ktm_path column untuk registrasi mandiri
 * 
 * Purpose:
 * - Menyimpan path file KTP/KTM yang diupload saat registrasi
 * - Mendukung proses verifikasi user oleh admin
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akun.pengguna', function (Blueprint $table) {
            $table->string('file_ktp_ktm_path', 255)
                  ->nullable()
                  ->after('ktp')
                  ->comment('Path file KTP/KTM untuk verifikasi registrasi mandiri');
        });
    }

    public function down(): void
    {
        Schema::table('akun.pengguna', function (Blueprint $table) {
            $table->dropColumn('file_ktp_ktm_path');
        });
    }
};
