<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KhsItem extends Model
{
    use HasFactory;

    protected $table = 'khs_items';

    protected $fillable = [
        'khs_id',
        'mata_kuliah_id',
        'nilai_tm',
        'nilai_quis',
        'nilai_mid',
        'nilai_final',
        'nilai_angka',
        'nilai_huruf',
    ];

    public function khs(): BelongsTo
    {
        return $this->belongsTo(Khs::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
