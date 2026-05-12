<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    protected $fillable = [
        'user_id',
        'nama',
        'nik',
        'nidn',
        'nuptk',
        'nomor_sk',
        'alamat',
        'nomor_hp',
        'mata_kuliah',
        'foto_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mataKuliah(): HasMany
    {
        return $this->hasMany(MataKuliah::class);
    }
}
