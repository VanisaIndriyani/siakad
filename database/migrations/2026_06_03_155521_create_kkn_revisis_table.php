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
        Schema::create('kkn_revisis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kkn_posko_id')->constrained('kkn_poskos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Dosen yang memberikan revisi
            $table->date('tanggal');
            $table->text('uraian_revisi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kkn_revisis');
    }
};
