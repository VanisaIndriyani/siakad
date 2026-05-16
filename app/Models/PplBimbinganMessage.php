<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PplBimbinganMessage extends Model
{
    use HasFactory;

    protected $table = 'ppl_bimbingan_messages';

    protected $fillable = [
        'ppl_pengajuan_id',
        'sender_user_id',
        'pesan',
    ];

    public function ppl(): BelongsTo
    {
        return $this->belongsTo(PplPengajuan::class, 'ppl_pengajuan_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}

