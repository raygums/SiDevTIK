<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Header Permohonan
        Schema::create('transaksi.submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique();
            
            // Relasi ke User (SSO)
            $table->foreignId('applicant_id')->constrained('public.users'); 
            
            // Relasi ke Referensi
            $table->foreignId('unit_id')->constrained('referensi.units');
            
            // Data Atasan (Manual Input sesuai PDF)
            $table->string('admin_responsible_name');
            $table->string('admin_responsible_nip')->nullable();
            $table->string('admin_responsible_position');
            $table->string('admin_responsible_phone');
            
            // Data Aplikasi
            $table->string('application_name');
            $table->text('description');
            
            // Status & Flow
            $table->enum('status', ['draft', 'submitted', 'in_review', 'approved_admin', 'processing', 'completed', 'rejected'])->default('draft');
            $table->foreignId('assigned_verifier_id')->nullable()->constrained('public.users');
            $table->foreignId('assigned_executor_id')->nullable()->constrained('public.users');
            
            // Dokumen
            $table->string('generated_form_path')->nullable();
            $table->string('signed_form_path')->nullable();
            $table->string('attachment_identity_path')->nullable();
            $table->timestamps();
        });

        // 2. Detail Teknis
        Schema::create('transaksi.submission_details', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('submission_id')->constrained('transaksi.submissions')->onDelete('cascade');
            
            $table->enum('request_type', ['domain', 'hosting', 'vps']);
            $table->string('requested_domain')->nullable();
            $table->integer('requested_quota_gb')->nullable();
            $table->string('initial_password_hint')->nullable();
            
            $table->timestamps();
        });

        // 3. Log Aktivitas
        Schema::create('transaksi.submission_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('submission_id')->constrained('transaksi.submissions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('public.users');
            
            $table->string('action');
            $table->string('note')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi.submission_logs');
        Schema::dropIfExists('transaksi.submission_details');
        Schema::dropIfExists('transaksi.submissions');
    }
};