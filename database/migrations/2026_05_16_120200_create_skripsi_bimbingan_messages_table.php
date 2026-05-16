<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi_bimbingan_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_pengajuan_id')->constrained('skripsi_pengajuans')->onDelete('cascade');
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi_bimbingan_messages');
    }
};

