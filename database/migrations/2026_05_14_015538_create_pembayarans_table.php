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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->integer('semester');
            $table->string('tahun_ajaran');
            $table->decimal('total_biaya', 15, 2);
            $table->decimal('total_dibayar', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('Belum Lunas'); // Lunas, Belum Lunas, Cicil
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
