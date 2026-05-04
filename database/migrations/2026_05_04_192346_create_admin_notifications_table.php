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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->comment('Tipe notifikasi: user_registered, user_activated, submission_created, dll');
            $table->string('title');
            $table->text('message');
            $table->foreignUuid('related_user_uuid')->nullable()->constrained('akun.pengguna', 'UUID')->comment('User yang terkait dengan notifikasi');
            $table->foreignUuid('related_submission_uuid')->nullable()->constrained('transaksi.pengajuan', 'UUID')->comment('Pengajuan yang terkait dengan notifikasi');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
