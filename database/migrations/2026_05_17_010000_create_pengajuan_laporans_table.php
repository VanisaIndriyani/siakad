<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');

            $table->string('jenis');
            $table->unsignedBigInteger('pengajuan_id');

            $table->string('judul');
            $table->string('status')->default('open');

            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('mahasiswa_last_read_at')->nullable();
            $table->timestamp('staff_last_read_at')->nullable();

            $table->timestamps();

            $table->index(['mahasiswa_id', 'status']);
            $table->index(['jenis', 'pengajuan_id']);
            $table->index(['status', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_laporans');
    }
};

