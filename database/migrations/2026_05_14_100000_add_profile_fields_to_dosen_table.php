<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->string('email')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('nip')->nullable();
            $table->string('jabatan_fungsional')->nullable();
            $table->string('kepangkatan')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('rumpun_ilmu')->nullable();
            $table->string('status_serdos')->nullable();
            $table->string('status_pegawai')->nullable();
            $table->string('ikatan_kerja')->nullable();
            $table->date('tanggal_pengangkatan')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('dosen', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'tempat_lahir',
                'tanggal_lahir',
                'nip',
                'jabatan_fungsional',
                'kepangkatan',
                'pendidikan_terakhir',
                'rumpun_ilmu',
                'status_serdos',
                'status_pegawai',
                'ikatan_kerja',
                'tanggal_pengangkatan',
            ]);
        });
    }
};
