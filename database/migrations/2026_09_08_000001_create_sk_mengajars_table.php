<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sk_mengajars', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->unsignedTinyInteger('semester')->default(1);
            $table->string('tahun_ajaran', 20)->nullable();
            $table->string('nomor_sk', 120)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->decimal('beban_sks', 4, 1)->default(0);
            $table->string('kelas', 20)->nullable();
            $table->string('program_studi', 120)->nullable();
            $table->string('jabatan_dosen', 120)->nullable();
            $table->text('tugas_tambahan')->nullable();
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['mata_kuliah_id', 'semester']);
            $table->index(['dosen_id', 'semester']);
            $table->unique(['mata_kuliah_id', 'dosen_id', 'semester', 'tahun_ajaran'], 'sk_mengajars_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sk_mengajars');
    }
};
