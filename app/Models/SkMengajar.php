<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkMengajar extends Model
{
    use HasFactory;

    protected $fillable = [
        'mata_kuliah_id', 'dosen_id', 'semester', 'tahun_ajaran', 'nomor_sk',
        'tanggal_sk', 'tanggal_mulai', 'tanggal_selesai', 'beban_sks', 'kelas',
        'program_studi', 'jabatan_dosen', 'tugas_tambahan', 'catatan', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_sk' => 'date',
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'beban_sks' => 'decimal:1',
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
