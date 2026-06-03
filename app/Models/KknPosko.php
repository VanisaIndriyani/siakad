<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KknPosko extends Model
{
    use HasFactory;

    protected $table = 'kkn_poskos';

    protected $fillable = [
        'nama_posko',
        'lokasi',
        'dosen_pembimbing_id',
        'nomor_sk',
        'sk_pembimbing_path',
        'sk_pembimbing_name',
    ];

    public function dosenPembimbing(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_pembimbing_id');
    }

    public function pembimbingS(): BelongsToMany
    {
        return $this->belongsToMany(Dosen::class, 'kkn_posko_dosen', 'kkn_posko_id', 'dosen_id')->withTimestamps();
    }

    public function pengajuans(): HasMany
    {
        return $this->hasMany(KknPengajuan::class, 'kkn_posko_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(KknBimbinganMessage::class, 'kkn_posko_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(KknFile::class, 'kkn_posko_id');
    }
}
