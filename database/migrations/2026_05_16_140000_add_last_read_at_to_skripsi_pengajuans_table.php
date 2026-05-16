<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->timestamp('mahasiswa_last_read_at')->nullable()->after('assigned_at');
            $table->timestamp('dosen_last_read_at')->nullable()->after('mahasiswa_last_read_at');
        });
    }

    public function down(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->dropColumn(['mahasiswa_last_read_at', 'dosen_last_read_at']);
        });
    }
};

