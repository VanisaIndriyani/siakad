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
        'status_approval',
        'catatan_approval',
        'approved_at',
        'approved_by_user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'approved_at' => 'datetime',
    ];

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class);
    }
}
