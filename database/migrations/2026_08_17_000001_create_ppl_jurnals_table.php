<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppl_jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppl_pengajuan_id')->constrained('ppl_pengajuans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('kegiatan', 255);
            $table->text('deskripsi')->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->string('pihak_terkait', 255)->nullable();
            $table->text('catatan_pembimbing')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            $table->index(['ppl_pengajuan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppl_jurnals');
    }
};
