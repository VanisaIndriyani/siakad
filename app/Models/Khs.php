<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Khs extends Model
{
    use HasFactory;

    protected $table = 'khs';

    protected $fillable = [
        'mahasiswa_id',
        'semester',
        'tahun_ajaran',
        'ips',
        'ipk',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(KhsItem::class);
    }
}
