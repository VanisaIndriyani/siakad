<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknJurnal extends Model
{
    use HasFactory;

    protected $table = 'kkn_jurnals';

    protected $fillable = [
        'kkn_pengajuan_id',
        'tanggal',
        'kegiatan',
        'deskripsi',
        'lokasi',
        'pihak_terkait',
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
