<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_archives', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code')->index();
            $table->unsignedInteger('semester')->index();
            $table->string('tahun_ajaran')->nullable();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('mata_kuliah_id')->constrained('mata_kuliah')->cascadeOnDelete();
            $table->foreignId('khs_id')->nullable()->constrained('khs')->nullOnDelete();
            $table->foreignId('khs_item_id')->nullable()->constrained('khs_items')->nullOnDelete();
            $table->decimal('nilai_tm', 5, 2)->nullable();
            $table->decimal('nilai_quis', 5, 2)->nullable();
            $table->decimal('nilai_mid', 5, 2)->nullable();
            $table->decimal('nilai_final', 5, 2)->nullable();
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->string('nilai_huruf')->nullable();
            $table->decimal('ips_saat_reset', 3, 2)->nullable();
            $table->decimal('ipk_saat_reset', 3, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('reset_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reset_at')->useCurrent();
            $table->timestamps();

            $table->index(['semester', 'batch_code']);
            $table->index(['mahasiswa_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_archives');
    }
};
