<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migration: Tabel Riwayat Login (Login History/Audit Trail)
 * 
 * Purpose:
 * - Mencatat setiap percobaan login (berhasil/gagal)
 * - Audit trail untuk keamanan sistem
 * - Monitoring aktivitas autentikasi oleh Admin/Pimpinan
 * - Deteksi percobaan brute force dan akses mencurigakan
 * 
 * Schema: audit
 * Table: riwayat_login
 * 
 * Performance Considerations:
 * - Index pada pengguna_uuid untuk query per-user
 * - Index pada create_at untuk filtering berdasarkan tanggal
 * - Composite index untuk query kombinasi user + date
 * - Tipe data dioptimalkan untuk jutaan record
 * 
 * Security:
 * - Foreign key dengan ON DELETE SET NULL (preserve audit trail)
 * - IP address dan user agent akan disanitasi di application layer
 * - Status akses terkontrol dengan nilai enum
 * 
 * @author Domain TIK Development Team
 * @version 1.0.0
 * @created 2026-02-03
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates audit.riwayat_login table with optimized structure
     * for high-volume login tracking and audit reporting.
     */
    public function up(): void
    {
        Schema::create('audit.riwayat_login', function (Blueprint $table) {
            // ==========================================
            // PRIMARY KEY
            // ==========================================
            $table->uuid('UUID')
                ->primary()
                ->default(DB::raw('gen_random_uuid()'))
                ->comment('Primary Key - Auto-generated UUID');

            // ==========================================
            // FOREIGN KEY - User Reference
            // ==========================================
            // Note: ON DELETE SET NULL untuk preserve audit trail
            // Jika user dihapus, log tetap ada tapi pengguna_uuid jadi NULL
            $table->foreignUuid('pengguna_uuid')
                ->nullable()
                ->constrained('akun.pengguna', 'UUID')
                ->nullOnDelete() // Preserve log even if user deleted
                ->comment('FK ke akun.pengguna - User yang melakukan login attempt');

            // ==========================================
            // LOGIN ATTEMPT DETAILS
            // ==========================================
            
            /**
             * Alamat IP - Support IPv4 dan IPv6
             * - IPv4: max 15 chars (xxx.xxx.xxx.xxx)
             * - IPv6: max 45 chars (xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx:xxxx)
             * - Nullable untuk kasus edge (proxy, testing, dll)
             */
            $table->string('alamat_ip', 45)
                ->nullable()
                ->comment('IP Address user (IPv4/IPv6) - max 45 chars');

            /**
             * Perangkat - User Agent String
             * - Menyimpan informasi browser, OS, device
             * - Text type untuk menampung user agent yang panjang
             * - Nullable jika request tidak memiliki user agent
             */
            $table->text('perangkat')
                ->nullable()
                ->comment('User Agent string - Browser, OS, Device info');

            /**
             * Status Akses - Enum untuk status login
             * Values:
             * - BERHASIL: Login sukses
             * - GAGAL_PASSWORD: Password salah
             * - GAGAL_SUSPEND: Akun suspended/tidak aktif
             * - GAGAL_NOT_FOUND: User tidak ditemukan
             * - GAGAL_SSO: SSO authentication failed
             */
            $table->string('status_akses', 30)
                ->comment('Status login attempt: BERHASIL, GAGAL_PASSWORD, GAGAL_SUSPEND, dll');

            /**
             * Keterangan - Detail tambahan
             * - Error message untuk login gagal
             * - IP asal request jika proxy
             * - Informasi tambahan untuk debugging
             * - Nullable karena tidak selalu diperlukan
             */
            $table->text('keterangan')
                ->nullable()
                ->comment('Detail tambahan - Error message, notes, debugging info');

            // ==========================================
            // AUDIT TIMESTAMP
            // ==========================================
            
            /**
             * create_at - Waktu kejadian
             * - Menggunakan naming sesuai standar project (bukan created_at)
             * - Non-nullable, default CURRENT_TIMESTAMP
             * - Indexed untuk query berdasarkan tanggal
             */
            $table->timestamp('create_at')
                ->useCurrent()
                ->comment('Timestamp login attempt - indexed untuk query by date');

            // ==========================================
            // INDEXES - Performance Optimization
            // ==========================================
            
            /**
             * Index 1: pengguna_uuid
             * - Query: "Tampilkan semua login history user X"
             * - Use case: Admin view user activity timeline
             */
            $table->index('pengguna_uuid', 'idx_riwayat_login_pengguna');

            /**
             * Index 2: create_at (DESC)
             * - Query: "Tampilkan login terbaru"
             * - Use case: Recent activity monitoring, dashboard
             */
            $table->index('create_at', 'idx_riwayat_login_waktu');

            /**
             * Index 3: Composite - pengguna_uuid + create_at
             * - Query: "Tampilkan login history user X dalam rentang waktu Y"
             * - Use case: Filtered user activity report
             * - Order: pengguna_uuid ASC, create_at DESC
             */
            $table->index(['pengguna_uuid', 'create_at'], 'idx_riwayat_login_user_waktu');

            /**
             * Index 4: status_akses
             * - Query: "Tampilkan semua failed login attempts"
             * - Use case: Security monitoring, brute force detection
             */
            $table->index('status_akses', 'idx_riwayat_login_status');

            /**
             * Index 5: alamat_ip
             * - Query: "Tampilkan semua login dari IP X"
             * - Use case: Suspicious IP detection, geo-location analysis
             */
            $table->index('alamat_ip', 'idx_riwayat_login_ip');
        });

        // ==========================================
        // POST-CREATION OPTIMIZATION
        // ==========================================
        
        // Add table comment for documentation
        DB::statement("COMMENT ON TABLE audit.riwayat_login IS 'Audit Trail - Login History (Successful & Failed Attempts)'");
    }

    /**
     * Reverse the migrations.
     * 
     * Drops the audit.riwayat_login table and all associated indexes.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit.riwayat_login');
    }
};
