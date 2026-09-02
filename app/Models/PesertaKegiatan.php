<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'peserta_kegiatan';

    protected $fillable = [
        'kegiatan_id',
        'mahasiswa_id',
        'nama_lengkap',
        'npm',
        'program_studi',
        'fakultas',
        'nomor_telp',
        'email',
        'status_hadir',
        'waktu_hadir',
        'nomor_sertifikat',
        'sertifikat_diunduh_at',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'status_hadir' => 'boolean',
            'waktu_hadir' => 'datetime',
            'sertifikat_diunduh_at' => 'datetime',
        ];
    }

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function getStatusHadirLabelAttribute(): string
    {
        return $this->status_hadir ? 'Hadir' : 'Belum Hadir';
    }

    public function getWaktuHadirFormatAttribute(): ?string
    {
        return $this->waktu_hadir?->format('d M Y H:i');
    }
}
