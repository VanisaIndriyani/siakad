<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->string('rps_admin_path')->nullable()->after('dosen_id_2');
            $table->string('rps_admin_name')->nullable()->after('rps_admin_path');
            $table->string('rps_dosen_path')->nullable()->after('rps_admin_name');
            $table->string('rps_dosen_name')->nullable()->after('rps_dosen_path');
        });
    }

    public function down(): void
    {
        Schema::table('mata_kuliah', function (Blueprint $table) {
            $table->dropColumn(['rps_admin_path', 'rps_admin_name', 'rps_dosen_path', 'rps_dosen_name']);
        });
    }
};

