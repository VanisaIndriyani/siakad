<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sk_mengajars', function (Blueprint $table) {
            $table->string('file_pdf', 255)->nullable()->after('catatan');
        });

        Schema::create('roster_uploads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_kuliah_id');
            $table->unsignedBigInteger('dosen_id')->nullable();
            $table->unsignedTinyInteger('semester')->default(1);
            $table->string('tahun_ajaran', 20)->nullable();
            $table->string('kelas', 20)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->string('file_pdf', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['mata_kuliah_id', 'semester']);
            $table->index(['dosen_id', 'semester']);
            $table->unique(['mata_kuliah_id', 'semester', 'tahun_ajaran'], 'roster_uploads_unique_idx');
        });
    }

    public function down(): void
    {
        Schema::table('sk_mengajars', function (Blueprint $table) {
            $table->dropColumn('file_pdf');
        });
        Schema::dropIfExists('roster_uploads');
    }
};
