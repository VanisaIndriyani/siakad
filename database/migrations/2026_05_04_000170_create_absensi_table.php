<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->string('jurusan');
            $table->unsignedTinyInteger('semester');
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->unsignedTinyInteger('pertemuan');
            $table->date('tanggal')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['jurusan', 'semester', 'mata_kuliah_id', 'pertemuan'], 'absensi_unique_session');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

