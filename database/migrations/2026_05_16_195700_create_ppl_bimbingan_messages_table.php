<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppl_bimbingan_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppl_pengajuan_id')->constrained('ppl_pengajuans')->onDelete('cascade');
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppl_bimbingan_messages');
    }
};

