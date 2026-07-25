<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Informasi extends Model
{
    use HasFactory;

    protected $table = 'informasi';

    protected $fillable = [
        'judul',
        'deskripsi',
        'gambar_path',
        'is_aktif',
        'tanggal_aktif',
        'tanggal_kadaluarsa',
        'created_by',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'tanggal_aktif' => 'datetime',
        'tanggal_kadaluarsa' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function getShareUrlAttribute(): string
    {
        return route('informasi.publik', $this);
    }

    public function scopeAktif($query)
    {
        $now = now();

        return $query
            ->where('is_aktif', true)
            ->where(function ($sub) use ($now) {
                $sub->whereNull('tanggal_aktif')
                    ->orWhere('tanggal_aktif', '<=', $now);
            })
            ->where(function ($sub) use ($now) {
                $sub->whereNull('tanggal_kadaluarsa')
                    ->orWhere('tanggal_kadaluarsa', '>=', $now);
            })
            ->orderByDesc('tanggal_aktif')
            ->latest('id');
    }
}
