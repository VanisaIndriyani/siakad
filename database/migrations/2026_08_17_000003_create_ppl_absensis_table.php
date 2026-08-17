<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppl_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppl_pengajuan_id')->constrained('ppl_pengajuans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->enum('status_kehadiran', ['hadir', 'izin', 'sakit', 'alpha'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->text('catatan_pembimbing')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->unique(['ppl_pengajuan_id', 'tanggal'], 'ppl_absensi_unique_tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppl_absensis');
    }
};
