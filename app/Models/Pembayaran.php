<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembayaran extends Model
{
    protected $fillable = [
        'mahasiswa_id',
        'semester',
        'tahun_ajaran',
        'jenis_tagihan',
        'total_biaya',
        'total_dibayar',
        'status_pembayaran',
        'catatan',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PembayaranDetail::class);
    }

    public function updateStatus(): void
    {
        $totalDibayar = $this->details()->where('status_approval', 'approved')->sum('jumlah_bayar');
        $this->total_dibayar = $totalDibayar;

        if ($totalDibayar >= $this->total_biaya) {
            $this->status_pembayaran = 'Lunas';
        } elseif ($totalDibayar > 0) {
            $this->status_pembayaran = 'Cicil';
        } else {
            $this->status_pembayaran = 'Belum Lunas';
        }

        $this->save();
    }
}
