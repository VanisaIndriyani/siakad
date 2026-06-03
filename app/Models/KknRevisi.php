<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknRevisi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kkn_posko_id',
        'user_id',
        'tanggal',
        'uraian_revisi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function posko(): BelongsTo
    {
        return $this->belongsTo(KknPosko::class, 'kkn_posko_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
