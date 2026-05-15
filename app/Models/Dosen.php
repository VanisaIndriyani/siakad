<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'nidn',
        'nuptk',
        'nomor_sk',
        'alamat',
        'nomor_hp',
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
        'mata_kuliah',
        'program_studi',
        'status_akademik',
        'foto_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }
}
