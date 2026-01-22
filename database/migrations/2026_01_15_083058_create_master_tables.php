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
        // SKEMA: REFERENSI
        // ==========================================

        // 1. Kategori Unit
        Schema::create('referensi.kategori_unit', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm_kategori', 100)->unique();

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });

        // 2. Unit Kerja (nm_lmbg)
        Schema::create('referensi.unit_kerja', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm_lmbg', 125); // Sesuai Notulensi
            $table->string('kode_unit', 50)->nullable();
            
            $table->foreignUuid('kategori_uuid')
                  ->constrained('referensi.kategori_unit', 'UUID')
                  ->cascadeOnDelete();
                  
            $table->boolean('a_aktif')->default(true);

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });

        // 3. Jenis Layanan
        Schema::create('referensi.jenis_layanan', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm_layanan', 100);
            $table->text('deskripsi')->nullable();
            $table->boolean('a_aktif')->default(true);

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });

        // 4. Status Pengajuan (Static - No Full Audit)
        Schema::create('referensi.status_pengajuan', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm_status', 50); 
            // Minimal audit jika perlu, atau kosongkan
            $table->timestamp('create_at')->useCurrent();
        });

        // ==========================================
        // SKEMA: AKUN
        // ==========================================

        // 5. Peran
        Schema::create('akun.peran', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm_peran', 50)->unique();
            $table->boolean('a_aktif')->default(true);

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });

        // 6. Pengguna
        Schema::create('akun.pengguna', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('nm', 125);
            $table->string('usn', 100)->unique();
            $table->string('email', 125)->unique();
            $table->string('ktp', 20)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kata_sandi');
            
            $table->foreignUuid('peran_uuid')
                  ->nullable()
                  ->constrained('akun.peran', 'UUID')
                  ->nullOnDelete();
            
            $table->boolean('a_aktif')->default(true);

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            
            // Self-referencing FK defined later to avoid error or nullable
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });

        // Add Self-Reference FK for Pengguna
        Schema::table('akun.pengguna', function (Blueprint $table) {
            $table->foreign('id_creator')->references('UUID')->on('akun.pengguna');
            $table->foreign('id_updater')->references('UUID')->on('akun.pengguna');
        });

        // 7. Pemetaan SSO
        Schema::create('akun.pemetaan_peran_sso', function (Blueprint $table) {
            $table->uuid('UUID')->primary()->default(DB::raw('gen_random_uuid()'));
            $table->string('atribut_sso', 100);
            
            $table->foreignUuid('peran_uuid')
                  ->constrained('akun.peran', 'UUID')
                  ->cascadeOnDelete();

            // Full Audit Columns
            $table->timestamp('create_at')->useCurrent();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('last_sync')->nullable();
            $table->timestamp('delete_at')->nullable();
            $table->timestamp('expired_date')->nullable();
            $table->uuid('id_creator')->nullable();
            $table->uuid('id_updater')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('akun.pemetaan_peran_sso');
        Schema::dropIfExists('akun.pengguna'); // Drop constraints handled auto by Postgres
        Schema::dropIfExists('akun.peran');
        Schema::dropIfExists('referensi.status_pengajuan');
        Schema::dropIfExists('referensi.jenis_layanan');
        Schema::dropIfExists('transaksi.rincian_pengajuan');
        Schema::dropIfExists('transaksi.pengajuan');
        Schema::dropIfExists('referensi.unit_kerja');
        Schema::dropIfExists('referensi.kategori_unit');
    }
};