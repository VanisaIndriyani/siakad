<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PplJurnal extends Model
{
    use HasFactory;

    protected $table = 'ppl_jurnals';

    protected $fillable = [
        'ppl_pengajuan_id',
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

    public function pplPengajuan(): BelongsTo
    {
        return $this->belongsTo(PplPengajuan::class, 'ppl_pengajuan_id');
    }
}
