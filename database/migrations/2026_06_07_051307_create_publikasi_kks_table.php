<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('publikasi_kks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('penulis');
            $table->string('judul');
            $table->string('penerbit');
            $table->enum('kategori', ['Penelitian', 'PKM', 'HAKI', 'Buku']);
            $table->year('tahun_terbit');
            $table->enum('reputasi', ['Internasional', 'Nasional', 'tidakbersinta']);
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publikasi_kks');
    }
};
