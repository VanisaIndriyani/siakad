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
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->foreignId('dosen_penasehat_id')->nullable()->constrained('dosen')->nullOnDelete();
            $table->string('nomor_sk_penasehat')->nullable();
            $table->date('tanggal_sk_penasehat')->nullable();
            $table->string('sk_penasehat_path')->nullable();
            $table->string('sk_penasehat_name')->nullable();
            $table->datetime('mahasiswa_last_read_at')->nullable();
            $table->datetime('dosen_last_read_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['dosen_penasehat_id']);
            $table->dropColumn([
                'dosen_penasehat_id',
                'nomor_sk_penasehat',
                'tanggal_sk_penasehat',
                'sk_penasehat_path',
                'sk_penasehat_name',
                'mahasiswa_last_read_at',
                'dosen_last_read_at'
            ]);
        });
    }
};
