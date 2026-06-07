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
        Schema::table('publikasi_kks', function (Blueprint $table) {
            $table->string('kategori')->change();
            $table->string('reputasi')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publikasi_kks', function (Blueprint $table) {
            $table->enum('kategori', ['Penelitian', 'PKM', 'HAKI', 'Buku'])->change();
            $table->enum('reputasi', ['Internasional', 'Nasional', 'tidakbersinta'])->change();
        });
    }
};
