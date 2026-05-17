<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanLaporanMessage extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_laporan_messages';

    protected $fillable = [
        'pengajuan_laporan_id',
        'sender_user_id',
        'pesan',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(PengajuanLaporan::class, 'pengajuan_laporan_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}

