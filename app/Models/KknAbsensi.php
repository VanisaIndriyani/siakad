<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknAbsensi extends Model
{
    use HasFactory;

    protected $table = 'kkn_absensis';

    protected $fillable = [
        'kkn_pengajuan_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'status_kehadiran',
        'keterangan',
        'catatan_pembimbing',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kknPengajuan(): BelongsTo
    {
        return $this->belongsTo(KknPengajuan::class, 'kkn_pengajuan_id');
    }
}
