<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kode',
        'nama',
        'jurusan',
        'sks',
        'semester',
        'dosen_id',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class);
    }

    public function krsItems(): HasMany
    {
        return $this->hasMany(KrsItem::class);
    }

    public function khsItems(): HasMany
    {
        return $this->hasMany(KhsItem::class);
    }
}
