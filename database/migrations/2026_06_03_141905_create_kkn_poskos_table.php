<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkn_poskos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_posko');
            $table->string('lokasi')->nullable();
            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->string('nomor_sk')->nullable();
            $table->string('sk_pembimbing_path')->nullable();
            $table->string('sk_pembimbing_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkn_poskos');
    }
};
