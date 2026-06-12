<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionnaireResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'mahasiswa_id',
        'khs_id',
        'mata_kuliah_id',
        'semester',
        'tahun_ajaran',
        'komentar',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function khs(): BelongsTo
    {
        return $this->belongsTo(Khs::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }
}
