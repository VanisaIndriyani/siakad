<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PesertaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'peserta_kegiatan';

    protected $fillable = [
        'kegiatan_id',
        'jenis_peserta',
        'mahasiswa_id',
        'dosen_id',
        'nama_lengkap',
        'npm',
        'nidn',
        'program_studi',
        'fakultas',
        'nomor_telp',
        'email',
        'status_hadir',
        'waktu_hadir',
        'nomor_sertifikat',
        'sertifikat_peserta_path',
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

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function getStatusHadirLabelAttribute(): string
    {
        return $this->status_hadir ? 'Hadir' : 'Belum Hadir';
    }

    public function getWaktuHadirFormatAttribute(): ?string
    {
        return $this->waktu_hadir?->format('d M Y H:i');
    }

    public function getJenisPesertaLabelAttribute(): string
    {
        return $this->jenis_peserta === 'dosen' ? 'Dosen' : 'Mahasiswa';
    }

    public function getNomorIdentitasAttribute(): string
    {
        return $this->jenis_peserta === 'dosen' ? (string) ($this->nidn ?? '-') : (string) ($this->npm ?? '-');
    }

    public function getSertifikatPesertaUrlAttribute(): ?string
    {
        $path = (string) ($this->sertifikat_peserta_path ?? '');
        if ($path === '') {
            $masterPath = (string) ($this->kegiatan?->sertifikat_upload_path ?? '');
            if ($masterPath === '' || !$this->status_hadir) {
                return null;
            }
            return $this->buildAssetUrl($masterPath);
        }
        return $this->buildAssetUrl($path);
    }

    public function hasSertifikat(): bool
    {
        if ($this->sertifikat_peserta_path !== null && trim((string) $this->sertifikat_peserta_path) !== '') {
            return true;
        }
        $master = (string) ($this->kegiatan?->sertifikat_upload_path ?? '');
        return $master !== '' && $this->status_hadir;
    }

    protected function buildAssetUrl(string $path): ?string
    {
        $p = trim($path);
        if ($p === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $p)) {
            return $p;
        }
        if (str_starts_with($p, 'storage/') || str_starts_with($p, '/storage/')) {
            $clean = ltrim($p, '/');
            if (str_starts_with($clean, 'storage/')) {
                $clean = substr($clean, strlen('storage/'));
            }
            return asset('storage/' . ltrim($clean, '/'));
        }
        return asset('storage/' . ltrim($p, '/'));
    }
}

