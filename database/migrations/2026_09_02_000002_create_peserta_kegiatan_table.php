<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peserta_kegiatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_id');
            $table->unsignedBigInteger('mahasiswa_id')->nullable();
            $table->string('nama_lengkap', 255);
            $table->string('npm', 50)->nullable();
            $table->string('program_studi', 100)->nullable();
            $table->string('fakultas', 100)->nullable();
            $table->string('nomor_telp', 50)->nullable();
            $table->string('email', 255)->nullable();
            $table->boolean('status_hadir')->default(false);
            $table->timestamp('waktu_hadir')->nullable();
            $table->string('nomor_sertifikat', 100)->nullable();
            $table->timestamp('sertifikat_diunduh_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('kegiatan_id')->references('id')->on('kegiatan')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('set null');

            $table->unique(['kegiatan_id', 'mahasiswa_id']);
            $table->unique(['kegiatan_id', 'nomor_sertifikat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_kegiatan');
    }
};
