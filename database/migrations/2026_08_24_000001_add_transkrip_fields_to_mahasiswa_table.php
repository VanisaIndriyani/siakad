<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->string('nomor_transkrip', 100)->nullable()->after('status_mahasiswa');
            $table->date('tanggal_lulus')->nullable()->after('nomor_transkrip');
            $table->string('nomor_sk_banpt', 100)->nullable()->after('tanggal_lulus');
            $table->text('ujian_kompre')->nullable()->after('nomor_sk_banpt');
            $table->text('judul_skripsi')->nullable()->after('ujian_kompre');
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_transkrip',
                'tanggal_lulus',
                'nomor_sk_banpt',
                'ujian_kompre',
                'judul_skripsi',
            ]);
        });
    }
};
