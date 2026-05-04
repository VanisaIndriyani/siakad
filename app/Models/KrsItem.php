<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KrsItem extends Model
{
    use HasFactory;

    protected $table = 'krs_items';

    protected $fillable = [
        'krs_id',
        'mata_kuliah_id',
    ];

    public function krs(): BelongsTo
    {
        return $this->belongsTo(Krs::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }
}
