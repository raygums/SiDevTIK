<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referensi.roles', function (Blueprint $table) {
            $table->id(); 
            $table->string('name'); 
            $table->string('code')->unique(); 
            $table->timestamps();
        });

        Schema::create('referensi.sso_role_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('sso_group_name');
            $table->foreignId('target_role_id')->constrained('referensi.roles');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
            $table->foreignId('role_id')->nullable()->constrained('referensi.roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_id');
            $table->enum('role', ['user', 'verifikator', 'eksekutor', 'admin'])->default('user');
        });
        Schema::dropIfExists('referensi.sso_role_mappings');
        Schema::dropIfExists('referensi.roles');
    }
};