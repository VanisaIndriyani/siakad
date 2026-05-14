<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranDetail extends Model
{
    protected $fillable = [
        'pembayaran_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'bukti_pembayaran',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
