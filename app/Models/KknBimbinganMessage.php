<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KknBimbinganMessage extends Model
{
    use HasFactory;

    protected $table = 'kkn_bimbingan_messages';

    protected $fillable = [
        'kkn_posko_id',
        'sender_user_id',
        'pesan',
    ];

    public function posko(): BelongsTo
    {
        return $this->belongsTo(KknPosko::class, 'kkn_posko_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
