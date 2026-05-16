<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_pengajuan_id')->constrained('skripsi_pengajuans')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi_files');
    }
};

