<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KknPengajuan extends Model
{
    use HasFactory;

    protected $table = 'kkn_pengajuans';

    protected $fillable = [
        'mahasiswa_id',
        'kkn_posko_id',
        'status',
        'catatan_admin',
        'dosen_pembimbing_id',
        'dosen_pembimbing_id_2',
        'nomor_sk',
        'tanggal_sk',
        'sk_pembimbing_path',
        'sk_pembimbing_name',
        'assigned_at',
        'mahasiswa_last_read_at',
        'dosen_last_read_at',
    ];

    protected $casts = [
        'tanggal_sk' => 'date',
        'assigned_at' => 'datetime',
        'mahasiswa_last_read_at' => 'datetime',
        'dosen_last_read_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function posko(): BelongsTo
    {
        return $this->belongsTo(KknPosko::class, 'kkn_posko_id');
    }

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function dosenPembimbing2(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id_2');
    }

    public function jurnals(): HasMany
    {
        return $this->hasMany(KknJurnal::class, 'kkn_pengajuan_id')->orderBy('tanggal', 'asc');
    }

    public function absensis(): HasMany
    {
        return $this->hasMany(KknAbsensi::class, 'kkn_pengajuan_id')->orderBy('tanggal', 'asc');
    }
}
