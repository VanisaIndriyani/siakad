<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nik')->nullable();
            $table->string('npm')->unique();
            $table->text('alamat')->nullable();
            $table->string('nomor_telp')->nullable();
            $table->unsignedSmallInteger('angkatan')->nullable();
            $table->string('program_studi')->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('status_mahasiswa')->default('Aktif');
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
