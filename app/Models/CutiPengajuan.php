<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CutiPengajuan extends Model
{
    use HasFactory;

    protected $table = 'cuti_pengajuan';

    protected $fillable = [
        'mahasiswa_id',
        'tahun_ajaran',
        'semester',
        'alasan',
        'status',
        'catatan_prodi',
        'catatan_admin',
        'approved_by_admin_id',
        'approved_by_prodi_id',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function approvedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_admin_id');
    }

    public function approvedByProdi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_prodi_id');
    }
}
