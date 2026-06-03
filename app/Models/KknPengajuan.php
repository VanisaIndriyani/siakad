<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknPengajuan extends Model
{
    use HasFactory;

    protected $table = 'kkn_pengajuans';

    protected $fillable = [
        'mahasiswa_id',
        'kkn_posko_id',
        'status',
        'catatan_admin',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function posko(): BelongsTo
    {
        return $this->belongsTo(KknPosko::class, 'kkn_posko_id');
    }
}
