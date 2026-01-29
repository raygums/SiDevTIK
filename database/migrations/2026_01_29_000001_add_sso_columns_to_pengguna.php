<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom-kolom untuk integrasi SSO
     */
    public function up(): void
    {
        Schema::table('akun.pengguna', function (Blueprint $table) {
            // SSO ID dari akses.unila.ac.id
            $table->string('sso_id', 100)->nullable()->unique()->after('UUID');
            
            // ID SDM dan PD dari SSO
            $table->string('id_sdm', 100)->nullable()->after('peran_uuid');
            $table->string('id_pd', 100)->nullable()->after('id_sdm');
            
            // Last login tracking
            $table->timestamp('last_login_at')->nullable()->after('a_aktif');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            
            // Remember token for Laravel Auth
            $table->rememberToken()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akun.pengguna', function (Blueprint $table) {
            $table->dropColumn([
                'sso_id',
                'id_sdm',
                'id_pd',
                'last_login_at',
                'last_login_ip',
                'remember_token',
            ]);
        });
    }
};
