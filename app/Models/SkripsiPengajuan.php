<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'dosen_pembimbing_id_2',
        'nomor_sk',
        'tanggal_sk',
        'sk_pembimbing_path',
        'sk_pembimbing_name',
        'approved_at',
        'approved_by_user_id',
        'assigned_at',
        'mahasiswa_last_read_at',
        'dosen_last_read_at',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'approved_at' => 'datetime',
        'assigned_at' => 'datetime',
        'mahasiswa_last_read_at' => 'datetime',
        'dosen_last_read_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function dosenPembimbing2(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id_2');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SkripsiBimbinganMessage::class, 'skripsi_pengajuan_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(SkripsiBimbinganMessage::class, 'skripsi_pengajuan_id')->latestOfMany();
    }

    public function revisis(): HasMany
    {
        return $this->hasMany(SkripsiRevisi::class, 'skripsi_pengajuan_id');
    }

    public function latestRevisi(): HasOne
    {
        return $this->hasOne(SkripsiRevisi::class, 'skripsi_pengajuan_id')->latestOfMany();
    }

    public function files(): HasMany
    {
        return $this->hasMany(SkripsiFile::class, 'skripsi_pengajuan_id');
    }
}
