<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PplAbsensi extends Model
{
    use HasFactory;

    protected $table = 'ppl_absensis';

    protected $fillable = [
        'ppl_pengajuan_id',
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

    public function pplPengajuan(): BelongsTo
    {
        return $this->belongsTo(PplPengajuan::class, 'ppl_pengajuan_id');
    }
}
