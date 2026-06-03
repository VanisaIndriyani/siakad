<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'jenis_kelamin',
        'nama_ibu',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kewarganegaraan',
        'nik',
        'nisn',
        'npwp',
        'npm',
        'alamat',
        'jalan',
        'dusun',
        'rt',
        'rw',
        'kelurahan',
        'kode_pos',
        'kecamatan',
        'jenis_tinggal',
        'alat_transportasi',
        'nomor_telp',
        'angkatan',
        'program_studi',
        'fakultas',
        'asal_sekolah',
        'status_mahasiswa',
        'foto_path',
        'kartu_mahasiswa_path',
        'penerima_kps',
        'no_kps',
        'ayah_nik',
        'ayah_nama',
        'ayah_tanggal_lahir',
        'ayah_pendidikan',
        'ayah_pekerjaan',
        'ayah_penghasilan',
        'ibu_nik',
        'ibu_nama',
        'ibu_tanggal_lahir',
        'ibu_pendidikan',
        'ibu_pekerjaan',
        'ibu_penghasilan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'ayah_tanggal_lahir' => 'date',
            'ibu_tanggal_lahir' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function krs(): HasMany
    {
        return $this->hasMany(Krs::class);
    }

    public function khs(): HasMany
    {
        return $this->hasMany(Khs::class);
    }
}
