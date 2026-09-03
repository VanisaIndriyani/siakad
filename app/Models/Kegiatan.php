<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $fillable = [
        'judul',
        'jenis_kegiatan',
        'deskripsi',
        'lokasi',
        'tanggal_kegiatan',
        'tanggal_selesai',
        'waktu_mulai',
        'waktu_selesai',
        'penyelenggara',
        'narasumber',
        'ketua_panitia_nama',
        'ketua_panitia_nip',
        'narasumber_nip',
        'rektor_nama',
        'rektor_nip',
        'gambar_path',
        'is_published',
        'tampilkan_ke_dosen',
        'sertifikat_aktif',
        'nomor_sertifikat_prefix',
        'template_sertifikat',
        'sertifikat_upload_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kegiatan' => 'date',
            'tanggal_selesai' => 'date',
            'is_published' => 'boolean',
            'tampilkan_ke_dosen' => 'boolean',
            'sertifikat_aktif' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function peserta(): HasMany
    {
        return $this->hasMany(PesertaKegiatan::class, 'kegiatan_id');
    }

    public function pesertaHadir(): HasMany
    {
        return $this->hasMany(PesertaKegiatan::class, 'kegiatan_id')->where('status_hadir', true);
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (empty($this->gambar_path)) {
            return null;
        }

        $path = (string) $this->gambar_path;

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            $cleanPath = ltrim($path, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, strlen('storage/'));
            }
            return asset('storage/' . ltrim($cleanPath, '/'));
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public function getSertifikatUploadUrlAttribute(): ?string
    {
        if (empty($this->sertifikat_upload_path)) {
            return null;
        }
        $path = (string) $this->sertifikat_upload_path;
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (str_starts_with($path, 'storage/') || str_starts_with($path, '/storage/')) {
            $cleanPath = ltrim($path, '/');
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, strlen('storage/'));
            }
            return asset('storage/' . ltrim($cleanPath, '/'));
        }
        return asset('storage/' . ltrim($path, '/'));
    }

    public function getTanggalWaktuAttribute(): string
    {
        $result = $this->getTanggalRangeAttribute();

        if ($this->waktu_mulai) {
            $result .= ', ' . substr($this->waktu_mulai, 0, 5);
            if ($this->waktu_selesai) {
                $result .= ' - ' . substr($this->waktu_selesai, 0, 5) . ' WIB';
            }
        }

        return $result;
    }

    public function getTanggalRangeAttribute(): string
    {
        $start = $this->tanggal_kegiatan;
        if (empty($start)) return '-';

        $end = $this->tanggal_selesai;

        $startDt = is_string($start) ? \Illuminate\Support\Carbon::parse($start) : $start;
        if (empty($end)) {
            return $startDt->format('d M Y');
        }

        $endDt = is_string($end) ? \Illuminate\Support\Carbon::parse($end) : $end;

        if ($startDt->isSameDay($endDt)) {
            return $startDt->format('d M Y');
        }

        if ($startDt->format('m Y') === $endDt->format('m Y')) {
            return $startDt->format('d') . ' - ' . $endDt->format('d M Y');
        }

        return $startDt->format('d M Y') . ' - ' . $endDt->format('d M Y');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderByDesc('tanggal_kegiatan');
    }

    public function generateNomorSertifikat(PesertaKegiatan $peserta): string
    {
        $prefix = $this->nomor_sertifikat_prefix ?: 'SERT';
        $bulan = $this->tanggal_kegiatan?->format('m') ?? date('m');
        $tahun = $this->tanggal_kegiatan?->format('Y') ?? date('Y');
        $nomorUrut = str_pad((string) $peserta->id, 4, '0', STR_PAD_LEFT);

        return "{$prefix}/{$nomorUrut}/{$bulan}/{$tahun}";
    }
}
