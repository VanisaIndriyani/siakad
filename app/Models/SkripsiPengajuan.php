<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SkripsiPengajuan extends Model
{
    use HasFactory;

    protected $table = 'skripsi_pengajuans';

    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'deskripsi',
        'status',
        'catatan_admin',
        'dosen_pembimbing_id',
        'nomor_sk',
        'tanggal_sk',
        'approved_at',
        'approved_by_user_id',
        'assigned_at',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'approved_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SkripsiBimbinganMessage::class, 'skripsi_pengajuan_id');
    }
}

