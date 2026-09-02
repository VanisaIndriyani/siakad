<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            if (!Schema::hasColumn('kegiatan', 'tampilkan_ke_dosen')) {
                Schema::table('kegiatan', function (Blueprint $table) {
                    $table->boolean('tampilkan_ke_dosen')->default(true)->after('is_published');
                });
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        try {
            if (Schema::hasColumn('kegiatan', 'tampilkan_ke_dosen')) {
                Schema::table('kegiatan', function (Blueprint $table) {
                    $table->dropColumn('tampilkan_ke_dosen');
                });
            }
        } catch (\Throwable $e) {}
    }
};
