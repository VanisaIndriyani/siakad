<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('kegiatan', 'sertifikat_upload_path')) {
                $table->string('sertifikat_upload_path', 500)->nullable()
                    ->after('template_sertifikat')
                    ->comment('Path PDF sertifikat master upload manual admin');
            }
            if (Schema::hasColumn('kegiatan', 'nomor_sertifikat_prefix')) {
                $table->string('nomor_sertifikat_prefix', 100)->nullable()->change();
            }
        });

        Schema::table('peserta_kegiatan', function (Blueprint $table) {
            if (!Schema::hasColumn('peserta_kegiatan', 'jenis_peserta')) {
                $table->enum('jenis_peserta', ['mahasiswa', 'dosen'])->default('mahasiswa')
                    ->after('kegiatan_id')
                    ->comment('Jenis peserta: mahasiswa atau dosen');
            }
            if (!Schema::hasColumn('peserta_kegiatan', 'dosen_id')) {
                $table->unsignedBigInteger('dosen_id')->nullable()
                    ->after('mahasiswa_id')
                    ->comment('FK ke tabel dosen jika jenis_peserta = dosen');
            }
            if (!Schema::hasColumn('peserta_kegiatan', 'nidn')) {
                $table->string('nidn', 50)->nullable()->after('npm');
            }
            if (!Schema::hasColumn('peserta_kegiatan', 'sertifikat_peserta_path')) {
                $table->string('sertifikat_peserta_path', 500)->nullable()
                    ->after('nomor_sertifikat')
                    ->comment('Path file sertifikat per peserta (jika beda per orang)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peserta_kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('peserta_kegiatan', 'sertifikat_peserta_path')) {
                $table->dropColumn('sertifikat_peserta_path');
            }
            if (Schema::hasColumn('peserta_kegiatan', 'nidn')) {
                $table->dropColumn('nidn');
            }
            if (Schema::hasColumn('peserta_kegiatan', 'dosen_id')) {
                $table->dropColumn('dosen_id');
            }
            if (Schema::hasColumn('peserta_kegiatan', 'jenis_peserta')) {
                $table->dropColumn('jenis_peserta');
            }
        });

        Schema::table('kegiatan', function (Blueprint $table) {
            if (Schema::hasColumn('kegiatan', 'sertifikat_upload_path')) {
                $table->dropColumn('sertifikat_upload_path');
            }
        });
    }
};
