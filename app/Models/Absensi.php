<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'jurusan',
        'semester',
        'mata_kuliah_id',
        'pertemuan',
        'tanggal',
        'materi',
        'created_by_user_id',
    ];

    protected $casts = [
        'semester' => 'integer',
        'pertemuan' => 'integer',
        'tanggal' => 'date',
    ];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AbsensiItem::class, 'absensi_id');
    }
}

