<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PengajuanLaporan;
use App\Models\PplPengajuan;
use App\Models\SkripsiPengajuan;
use App\Models\CutiPengajuan;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MAHASISWA = 'mahasiswa';
    public const ROLE_DOSEN = 'dosen';
    public const ROLE_KEUANGAN = 'keuangan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'password_plain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function dosen(): HasOne
    {
        return $this->hasOne(Dosen::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_MAHASISWA;
    }

    public function isDosen(): bool
    {
        return $this->role === self::ROLE_DOSEN;
    }

    public function isKeuangan(): bool
    {
        return $this->role === self::ROLE_KEUANGAN;
    }

    /**
     * Count unread notifications for Laporan
     */
    public function unreadLaporanCount(): int
    {
        if ($this->isMahasiswa()) {
            $mahasiswa = $this->mahasiswa;
            if (!$mahasiswa) return 0;

            return PengajuanLaporan::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where(function ($q) {
                    $q->whereNull('mahasiswa_last_read_at')
                      ->orWhereColumn('last_message_at', '>', 'mahasiswa_last_read_at');
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id);
                })
                ->count();
        }

        if ($this->isAdmin()) {
            return PengajuanLaporan::query()
                ->where(function ($q) {
                    $q->whereNull('staff_last_read_at')
                      ->orWhereColumn('last_message_at', '>', 'staff_last_read_at');
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id);
                })
                ->count();
        }

        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen || !$dosen->program_studi) return 0;

            return PengajuanLaporan::query()
                ->whereHas('mahasiswa', function ($q) use ($dosen) {
                    $q->where('program_studi', $dosen->program_studi);
                })
                ->where(function ($q) {
                    $q->whereNull('staff_last_read_at')
                      ->orWhereColumn('last_message_at', '>', 'staff_last_read_at');
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id);
                })
                ->count();
        }

        return 0;
    }

    /**
     * Count unread notifications for PPL
     */
    public function unreadPplCount(): int
    {
        if ($this->isMahasiswa()) {
            $mahasiswa = $this->mahasiswa;
            if (!$mahasiswa) return 0;

            return PplPengajuan::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('ppl_pengajuans.mahasiswa_last_read_at')
                              ->orWhereColumn('ppl_bimbingan_messages.created_at', '>', 'ppl_pengajuans.mahasiswa_last_read_at');
                      });
                })
                ->count();
        }

        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen) return 0;

            return PplPengajuan::query()
                ->where(function ($q) use ($dosen) {
                    $q->where('dosen_pembimbing_id', $dosen->id)
                      ->orWhere('dosen_pembimbing_id_2', $dosen->id);
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('ppl_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('ppl_bimbingan_messages.created_at', '>', 'ppl_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }

        if ($this->isAdmin()) {
            return PplPengajuan::query()
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('ppl_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('ppl_bimbingan_messages.created_at', '>', 'ppl_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }

        return 0;
    }

    /**
     * Count unread notifications for Skripsi
     */
    public function unreadSkripsiCount(): int
    {
        if ($this->isMahasiswa()) {
            $mahasiswa = $this->mahasiswa;
            if (!$mahasiswa) return 0;

            return SkripsiPengajuan::query()
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('skripsi_pengajuans.mahasiswa_last_read_at')
                              ->orWhereColumn('skripsi_bimbingan_messages.created_at', '>', 'skripsi_pengajuans.mahasiswa_last_read_at');
                      });
                })
                ->count();
        }

        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen) return 0;

            return SkripsiPengajuan::query()
                ->where(function ($q) use ($dosen) {
                    $q->where('dosen_pembimbing_id', $dosen->id)
                      ->orWhere('dosen_pembimbing_id_2', $dosen->id);
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('skripsi_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('skripsi_bimbingan_messages.created_at', '>', 'skripsi_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }

        if ($this->isAdmin()) {
            return SkripsiPengajuan::query()
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('skripsi_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('skripsi_bimbingan_messages.created_at', '>', 'skripsi_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }

        return 0;
    }

    /**
     * Count unread notifications for Skripsi (Prodi-wide)
     */
    public function unreadSkripsiProdiCount(): int
    {
        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen || !$dosen->program_studi) return 0;

            return SkripsiPengajuan::query()
                ->whereHas('mahasiswa', function ($q) use ($dosen) {
                    $q->where('program_studi', $dosen->program_studi);
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('skripsi_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('skripsi_bimbingan_messages.created_at', '>', 'skripsi_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }
        return 0;
    }

    /**
     * Count unread notifications for Laporan (Prodi-wide)
     */
    public function unreadLaporanProdiCount(): int
    {
        return $this->unreadLaporanCount();
    }

    /**
     * Count unread notifications for PPL (Prodi-wide)
     */
    public function unreadPplProdiCount(): int
    {
        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen || !$dosen->program_studi) return 0;

            return PplPengajuan::query()
                ->whereHas('mahasiswa', function ($q) use ($dosen) {
                    $q->where('program_studi', $dosen->program_studi);
                })
                ->whereHas('latestMessage', function ($q) {
                    $q->where('sender_user_id', '!=', $this->id)
                      ->where(function ($sub) {
                          $sub->whereNull('ppl_pengajuans.dosen_last_read_at')
                              ->orWhereColumn('ppl_bimbingan_messages.created_at', '>', 'ppl_pengajuans.dosen_last_read_at');
                      });
                })
                ->count();
        }
        return 0;
    }

    /**
     * Count pending leave applications
     */
    public function pendingCutiCount(): int
    {
        if ($this->isAdmin()) {
            return CutiPengajuan::query()->where('status', 'pending')->count();
        }

        if ($this->isDosen()) {
            $dosen = $this->dosen;
            if (!$dosen || !$dosen->program_studi) return 0;

            return CutiPengajuan::query()
                ->where('status', 'pending')
                ->whereHas('mahasiswa', function ($q) use ($dosen) {
                    $q->where('program_studi', $dosen->program_studi);
                })
                ->count();
        }

        return 0;
    }
}
