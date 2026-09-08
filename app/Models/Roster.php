<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Roster extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_kuliah_id', 'dosen_id', 'semester', 'tahun_ajaran', 'pertemuan_ke',
        'hari', 'jam_mulai', 'jam_selesai', 'ruang', 'tanggal', 'materi',
        'keterangan', 'metode_pembelajaran', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'jam_mulai' => 'datetime:H:i',
            'jam_selesai' => 'datetime:H:i',
            'tanggal' => 'date',
        ];
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
