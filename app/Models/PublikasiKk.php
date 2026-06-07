<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublikasiKk extends Model
{
    protected $table = 'publikasi_kks';

    protected $fillable = [
        'user_id',
        'penulis',
        'judul',
        'penerbit',
        'kategori',
        'tahun_terbit',
        'reputasi',
        'url_link',
        'file_path',
        'file_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
