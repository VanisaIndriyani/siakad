<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Krs extends Model
{
    use HasFactory;

    protected $table = 'krs';

    protected $fillable = [
        'mahasiswa_id',
        'semester',
        'tahun_ajaran',
        'status_approval',
        'approved_by_dosen_id',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function approvedByDosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'approved_by_dosen_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(KrsItem::class);
    }
}
