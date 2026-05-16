<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkripsiBimbinganMessage extends Model
{
    use HasFactory;

    protected $table = 'skripsi_bimbingan_messages';

    protected $fillable = [
        'skripsi_pengajuan_id',
        'sender_user_id',
        'pesan',
    ];

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(SkripsiPengajuan::class, 'skripsi_pengajuan_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}

