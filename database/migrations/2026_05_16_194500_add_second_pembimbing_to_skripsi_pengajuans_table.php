<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->foreignId('dosen_pembimbing_id_2')->nullable()->after('dosen_pembimbing_id')->constrained('dosen')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('skripsi_pengajuans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dosen_pembimbing_id_2');
        });
    }
};

