<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RosterUpload extends Model
{
    use HasFactory;

    protected $table = 'roster_uploads';

    protected $fillable = [
        'mata_kuliah_id',
        'dosen_id',
        'semester',
        'tahun_ajaran',
        'kelas',
        'keterangan',
        'file_pdf',
        'created_by',
        'program_studi',
    ];

    protected $casts = [
        'semester' => 'integer',
    ];

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
