<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('jenis_kegiatan', 100)->default('Seminar');
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->date('tanggal_kegiatan');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('penyelenggara', 255)->nullable();
            $table->string('narasumber', 255)->nullable();
            $table->string('gambar_path', 255)->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('sertifikat_aktif')->default(true);
            $table->string('nomor_sertifikat_prefix', 50)->nullable();
            $table->text('template_sertifikat')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
