<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PplRevisi extends Model
{
    use HasFactory;

    protected $table = 'ppl_revisis';

    protected $fillable = [
        'ppl_pengajuan_id',
        'created_by_user_id',
        'tanggal',
        'revisi',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function ppl(): BelongsTo
    {
        return $this->belongsTo(PplPengajuan::class, 'ppl_pengajuan_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
