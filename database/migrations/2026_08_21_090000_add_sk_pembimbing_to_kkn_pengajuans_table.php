<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kkn_pengajuans', function (Blueprint $table) {
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('dosen')->nullOnDelete()->after('catatan_admin');
            $table->foreignId('dosen_pembimbing_id_2')->nullable()->constrained('dosen')->nullOnDelete()->after('dosen_pembimbing_id');
            $table->string('nomor_sk')->nullable()->after('dosen_pembimbing_id_2');
            $table->date('tanggal_sk')->nullable()->after('nomor_sk');
            $table->string('sk_pembimbing_path')->nullable()->after('tanggal_sk');
            $table->string('sk_pembimbing_name')->nullable()->after('sk_pembimbing_path');
            $table->timestamp('assigned_at')->nullable()->after('sk_pembimbing_name');
            $table->timestamp('mahasiswa_last_read_at')->nullable()->after('assigned_at');
            $table->timestamp('dosen_last_read_at')->nullable()->after('mahasiswa_last_read_at');
        });
    }

    public function down(): void
    {
        Schema::table('kkn_pengajuans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dosen_pembimbing_id');
            $table->dropConstrainedForeignId('dosen_pembimbing_id_2');
            $table->dropColumn([
                'nomor_sk',
                'tanggal_sk',
                'sk_pembimbing_path',
                'sk_pembimbing_name',
                'assigned_at',
                'mahasiswa_last_read_at',
                'dosen_last_read_at',
            ]);
        });
    }
};
