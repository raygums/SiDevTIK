<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi.rincian_pengajuan', function (Blueprint $table) {
            // VPS Specifications
            $table->string('vps_os', 50)->nullable()->after('lokasi_server')->comment('Operating System (e.g., Ubuntu 22.04, CentOS 7)');
            $table->integer('vps_cpu')->nullable()->after('vps_os')->comment('CPU cores');
            $table->integer('vps_ram')->nullable()->after('vps_cpu')->comment('RAM in GB');
            $table->integer('vps_storage')->nullable()->after('vps_ram')->comment('Storage in GB');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi.rincian_pengajuan', function (Blueprint $table) {
            $table->dropColumn(['vps_os', 'vps_cpu', 'vps_ram', 'vps_storage']);
        });
    }
};
