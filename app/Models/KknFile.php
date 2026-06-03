<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknFile extends Model
{
    use HasFactory;

    protected $table = 'kkn_files';

    protected $fillable = [
        'kkn_posko_id',
        'user_id',
        'file_path',
        'file_name',
        'keterangan',
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
