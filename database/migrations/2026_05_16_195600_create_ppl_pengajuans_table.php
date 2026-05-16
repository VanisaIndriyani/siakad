<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppl_pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');

            $table->string('instansi_nama');
            $table->text('instansi_alamat')->nullable();
            $table->text('keterangan')->nullable();

            $table->string('status')->default('pending');
            $table->text('catatan_admin')->nullable();

            $table->foreignId('dosen_pembimbing_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->foreignId('dosen_pembimbing_id_2')->nullable()->constrained('dosen')->nullOnDelete();

            $table->string('nomor_sk')->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->string('sk_pembimbing_path')->nullable();
            $table->string('sk_pembimbing_name')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();

            $table->timestamp('mahasiswa_last_read_at')->nullable();
            $table->timestamp('dosen_last_read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppl_pengajuans');
    }
};

