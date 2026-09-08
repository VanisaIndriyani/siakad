<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->unsignedTinyInteger('semester')->default(1);
            $table->string('tahun_ajaran', 20)->nullable();
            $table->unsignedTinyInteger('pertemuan_ke')->default(1);
            $table->string('hari', 20)->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('ruang', 60)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('materi', 200)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('metode_pembelajaran', 60)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['mata_kuliah_id', 'semester', 'pertemuan_ke']);
            $table->index(['dosen_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};
