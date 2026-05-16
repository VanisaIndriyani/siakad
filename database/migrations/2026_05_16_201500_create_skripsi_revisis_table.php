<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skripsi_revisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skripsi_pengajuan_id')->constrained('skripsi_pengajuans')->onDelete('cascade');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->text('revisi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skripsi_revisis');
    }
};

