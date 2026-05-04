<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khs_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('khs_id')->constrained('khs')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->string('nilai_huruf')->nullable();
            $table->timestamps();

            $table->unique(['khs_id', 'mata_kuliah_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khs_items');
    }
};
