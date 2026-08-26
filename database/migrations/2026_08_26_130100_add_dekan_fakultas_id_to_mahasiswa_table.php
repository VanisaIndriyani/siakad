<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('dekan_fakultas_id')->nullable()->constrained('dosen')->nullOnDelete()->after('dosen_penasehat_id');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['dekan_fakultas_id']);
            $table->dropColumn('dekan_fakultas_id');
        });
    }
};
