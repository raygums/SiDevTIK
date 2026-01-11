<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Kategori Unit
        Schema::create('referensi.unit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->timestamps();
        });

        // 2. Daftar Unit (Fakultas/Lembaga)
        Schema::create('referensi.units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('referensi.unit_categories');
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
        });
        
        // 3. Jenis Layanan
        Schema::create('referensi.service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referensi.units');
        Schema::dropIfExists('referensi.unit_categories');
        Schema::dropIfExists('referensi.service_types');
    }
};