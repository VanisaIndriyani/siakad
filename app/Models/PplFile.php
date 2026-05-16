<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PplFile extends Model
{
    use HasFactory;

    protected $table = 'ppl_files';

    protected $fillable = [
        'ppl_pengajuan_id',
        'created_by_user_id',
        'file_path',
        'file_name',
        'keterangan',
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

