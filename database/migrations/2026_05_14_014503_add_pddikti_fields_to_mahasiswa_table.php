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
            $table->string('jenis_kelamin')->nullable()->after('nama_lengkap');
            $table->string('nama_ibu')->nullable()->after('jenis_kelamin');
            $table->string('agama')->nullable()->after('tanggal_lahir');
            $table->string('kewarganegaraan')->nullable()->after('agama');
            $table->string('nisn')->nullable()->after('nik');
            $table->string('npwp')->nullable()->after('nisn');
            
            // Alamat detail
            $table->string('jalan')->nullable()->after('alamat');
            $table->string('dusun')->nullable()->after('jalan');
            $table->string('rt')->nullable()->after('dusun');
            $table->string('rw')->nullable()->after('rt');
            $table->string('kelurahan')->nullable()->after('rw');
            $table->string('kode_pos')->nullable()->after('kelurahan');
            $table->string('kecamatan')->nullable()->after('kode_pos');
            $table->string('jenis_tinggal')->nullable()->after('kecamatan');
            $table->string('alat_transportasi')->nullable()->after('jenis_tinggal');
            
            // KPS
            $table->string('penerima_kps')->default('Tidak')->after('alat_transportasi');
            $table->string('no_kps')->nullable()->after('penerima_kps');
            
            // Ayah
            $table->string('ayah_nik')->nullable()->after('no_kps');
            $table->string('ayah_nama')->nullable()->after('ayah_nik');
            $table->date('ayah_tanggal_lahir')->nullable()->after('ayah_nama');
            $table->string('ayah_pendidikan')->nullable()->after('ayah_tanggal_lahir');
            $table->string('ayah_pekerjaan')->nullable()->after('ayah_pendidikan');
            $table->string('ayah_penghasilan')->nullable()->after('ayah_pekerjaan');
            
            // Ibu
            $table->string('ibu_nik')->nullable()->after('ayah_penghasilan');
            $table->string('ibu_nama')->nullable()->after('ibu_nik');
            $table->date('ibu_tanggal_lahir')->nullable()->after('ibu_nama');
            $table->string('ibu_pendidikan')->nullable()->after('ibu_tanggal_lahir');
            $table->string('ibu_pekerjaan')->nullable()->after('ibu_pendidikan');
            $table->string('ibu_penghasilan')->nullable()->after('ibu_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_kelamin', 'nama_ibu', 'agama', 'kewarganegaraan', 'nisn', 'npwp',
                'jalan', 'dusun', 'rt', 'rw', 'kelurahan', 'kode_pos', 'kecamatan',
                'jenis_tinggal', 'alat_transportasi', 'penerima_kps', 'no_kps',
                'ayah_nik', 'ayah_nama', 'ayah_tanggal_lahir', 'ayah_pendidikan', 'ayah_pekerjaan', 'ayah_penghasilan',
                'ibu_nik', 'ibu_nama', 'ibu_tanggal_lahir', 'ibu_pendidikan', 'ibu_pekerjaan', 'ibu_penghasilan'
            ]);
        });
    }
};
