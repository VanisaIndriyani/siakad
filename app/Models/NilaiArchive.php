<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiArchive extends Model
{
    use HasFactory;

    protected $table = 'nilai_archives';

    protected $fillable = [
        'batch_code',
        'semester',
        'tahun_ajaran',
        'mahasiswa_id',
        'mata_kuliah_id',
        'khs_id',
        'khs_item_id',
        'nilai_tm',
        'nilai_quis',
        'nilai_mid',
        'nilai_final',
        'nilai_angka',
        'nilai_huruf',
        'ips_saat_reset',
        'ipk_saat_reset',
        'catatan',
        'reset_by',
        'reset_at',
    ];

    protected $casts = [
        'reset_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function khs(): BelongsTo
    {
        return $this->belongsTo(Khs::class);
    }

    public function khsItem(): BelongsTo
    {
        return $this->belongsTo(KhsItem::class);
    }

    public function resetBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reset_by');
    }
}
