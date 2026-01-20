<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // SKEMA: TRANSAKSI
        // ==========================================

        // 1. Pengajuan (Header)
        Schema::create('transaksi.pengajuan', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('no_tiket', 50)->unique();
            
            // Relasi ke Skema Lain
            $table->foreignUuid('pengguna_uuid')->constrained('akun.pengguna', 'UUID');
            $table->foreignUuid('unit_kerja_uuid')->constrained('referensi.unit_kerja', 'UUID');
            $table->foreignUuid('jenis_layanan_uuid')->constrained('referensi.jenis_layanan', 'UUID');
            $table->foreignUuid('status_uuid')->constrained('referensi.status_pengajuan', 'UUID');
            
            $table->date('tgl_pengajuan')->useCurrent();

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            
            // Creator/Updater FK
            $table->foreignUuid('id_creator')->nullable()->constrained('akun.pengguna', 'UUID');
            $table->foreignUuid('id_updater')->nullable()->constrained('akun.pengguna', 'UUID');
        });

        // 2. Rincian Pengajuan (Detail)
        Schema::create('transaksi.rincian_pengajuan', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            
            $table->foreignUuid('pengajuan_uuid')
                  ->unique()
                  ->constrained('transaksi.pengajuan', 'UUID')
                  ->cascadeOnDelete();
            
            $table->string('nm_domain', 150)->nullable();
            $table->string('alamat_ip', 45)->nullable();
            $table->string('kapasitas_penyimpanan', 50)->nullable();
            $table->string('lokasi_server', 100)->nullable();
            $table->text('keterangan_keperluan')->nullable();
            $table->string('file_lampiran', 255)->nullable();

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->foreignUuid('id_creator')->nullable()->constrained('akun.pengguna', 'UUID');
            $table->foreignUuid('id_updater')->nullable()->constrained('akun.pengguna', 'UUID');
        });

        // ==========================================
        // SKEMA: AUDIT
        // ==========================================

        // 3. Riwayat Pengajuan (Log)
        Schema::create('audit.riwayat_pengajuan', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            
            $table->foreignUuid('pengajuan_uuid')
                  ->constrained('transaksi.pengajuan', 'UUID')
                  ->cascadeOnDelete();
            
            $table->foreignUuid('status_lama_uuid')->nullable()->constrained('referensi.status_pengajuan', 'UUID');
            $table->foreignUuid('status_baru_uuid')->nullable()->constrained('referensi.status_pengajuan', 'UUID');
            
            $table->text('catatan_log')->nullable();

            // Optimized Audit (Log tidak butuh last_update/delete_at)
            $table->timestamp('create_at')->useCurrent();
            $table->foreignUuid('id_creator')->nullable()->constrained('akun.pengguna', 'UUID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit.riwayat_pengajuan');
        Schema::dropIfExists('transaksi.rincian_pengajuan');
        Schema::dropIfExists('transaksi.pengajuan');
    }
};