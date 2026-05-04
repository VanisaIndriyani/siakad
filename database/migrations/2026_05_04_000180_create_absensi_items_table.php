<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absensi_id')->constrained('absensi')->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpha'])->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['absensi_id', 'mahasiswa_id'], 'absensi_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_items');
    }
};

