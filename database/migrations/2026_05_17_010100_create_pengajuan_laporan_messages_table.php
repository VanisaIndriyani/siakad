<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_laporan_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_laporan_id')->constrained('pengajuan_laporans')->onDelete('cascade');
            $table->foreignId('sender_user_id')->constrained('users')->onDelete('cascade');
            $table->text('pesan');
            $table->timestamps();

            $table->index(['pengajuan_laporan_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_laporan_messages');
    }
};

