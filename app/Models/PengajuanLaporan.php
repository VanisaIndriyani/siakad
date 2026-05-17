<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PengajuanLaporan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_laporans';

    protected $fillable = [
        'mahasiswa_id',
        'jenis',
        'pengajuan_id',
        'judul',
        'status',
        'last_message_at',
        'mahasiswa_last_read_at',
        'staff_last_read_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'mahasiswa_last_read_at' => 'datetime',
        'staff_last_read_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PengajuanLaporanMessage::class, 'pengajuan_laporan_id');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(PengajuanLaporanMessage::class, 'pengajuan_laporan_id')->latestOfMany();
    }
}

