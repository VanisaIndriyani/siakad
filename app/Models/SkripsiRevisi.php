<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkripsiRevisi extends Model
{
    use HasFactory;

    protected $table = 'skripsi_revisis';

    protected $fillable = [
        'skripsi_pengajuan_id',
        'created_by_user_id',
        'revisi',
    ];

    public function skripsi(): BelongsTo
    {
        return $this->belongsTo(SkripsiPengajuan::class, 'skripsi_pengajuan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

