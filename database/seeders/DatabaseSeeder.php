<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use App\Models\KrsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $jurusanList = [
            'Pendidikan Agama Islam',
            'Pendidikan Islam Anak Usia Dini',
            'Hukum Keluarga Islam',
            'Hukum Tata Negara',
            'Perbankan Syariah',
            'Ekonomi Syariah',
        ];

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'role' => User::ROLE_ADMIN,
                'password' => Hash::make('password'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'akademik@iaiddisidrap.ac.id'],
            [
                'name' => 'Staf Akademik',
                'role' => User::ROLE_AKADEMIK,
                'password' => Hash::make('password'),
            ]
        );

        User::query()->firstOrCreate(
            ['email' => 'keuangan@iaiddisidrap.ac.id'],
            [
                'name' => 'Staf Keuangan',
                'role' => User::ROLE_KEUANGAN,
                'password' => Hash::make('password'),
            ]
        );

        $dosenRows = [
            ['email' => 'dosen@gmail.com', 'nama' => 'Dosen Demo', 'nidn' => '1234567890', 'mata_kuliah' => 'Pemrograman Web'],
            ['email' => 'andi@iaiddisidrap.ac.id', 'nama' => 'Dr. Andi', 'nidn' => '1987000001', 'mata_kuliah' => 'Manajemen Pendidikan'],
            ['email' => 'siti@iaiddisidrap.ac.id', 'nama' => 'Siti, M.Pd', 'nidn' => '1987000002', 'mata_kuliah' => 'Metodologi Penelitian'],
            ['email' => 'budi@iaiddisidrap.ac.id', 'nama' => 'Budi, M.H', 'nidn' => '1987000003', 'mata_kuliah' => 'Hukum Islam'],
            ['email' => 'rina@iaiddisidrap.ac.id', 'nama' => 'Rina, M.E', 'nidn' => '1987000004', 'mata_kuliah' => 'Ekonomi Syariah'],
        ];

        $dosens = collect($dosenRows)->map(function (array $row) {
            $user = User::query()->firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama'],
                    'role' => User::ROLE_DOSEN,
                    'password' => Hash::make('password'),
                ]
            );

            return Dosen::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $row['nama'],
                    'nik' => '1234567890'.rand(100, 999),
                    'nidn' => $row['nidn'],
                    'nomor_sk' => 'SK/'.rand(100, 999).'/2026',
                    'alamat' => 'Kampus Hijau, Kota',
                    'nomor_hp' => '081234567800',
                    'mata_kuliah' => $row['mata_kuliah'],
                ]
            );
        })->values();

        $mahasiswaRows = [
            [
                'email' => 'mahasiswa@gmail.com',
                'nama_lengkap' => 'Mahasiswa Demo',
                'npm' => '20260001',
                'angkatan' => 2026,
                'program_studi' => 'Pendidikan Agama Islam',
            ],
            [
                'email' => 'mhs20260002@iaiddisidrap.ac.id',
                'nama_lengkap' => 'Mahasiswa 20260002',
                'npm' => '20260002',
                'angkatan' => 2026,
                'program_studi' => 'Pendidikan Islam Anak Usia Dini',
            ],
            [
                'email' => 'mhs20260003@iaiddisidrap.ac.id',
                'nama_lengkap' => 'Mahasiswa 20260003',
                'npm' => '20260003',
                'angkatan' => 2026,
                'program_studi' => 'Hukum Keluarga Islam',
            ],
            [
                'email' => 'mhs20260004@iaiddisidrap.ac.id',
                'nama_lengkap' => 'Mahasiswa 20260004',
                'npm' => '20260004',
                'angkatan' => 2026,
                'program_studi' => 'Hukum Tata Negara',
            ],
            [
                'email' => 'mhs20260005@iaiddisidrap.ac.id',
                'nama_lengkap' => 'Nur Afiah',
                'npm' => '20260005',
                'angkatan' => 2026,
                'program_studi' => 'Perbankan Syariah',
                'tempat_lahir' => 'Pangkajene',
                'tanggal_lahir' => '2002-05-15',
                'foto_path' => 'foto_mahasiswa/20260005.jpg',
                'tanggal_lulus' => '2026-08-25',
                'judul_skripsi' => 'PRODUKTIVITAS EKONOMI MASYARAKAT PASCA BANJIR DI DESA LEPPANGENG KABUPATEN WAJO (TINJAUAN EKONOMI ISLAM)',
            ],
        ];

        $mahasiswaList = collect($mahasiswaRows)->map(function (array $row) {
            $user = User::query()->firstOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['nama_lengkap'],
                    'role' => User::ROLE_MAHASISWA,
                    'password' => Hash::make('password'),
                ]
            );

            return Mahasiswa::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $row['nama_lengkap'],
                    'tempat_lahir' => $row['tempat_lahir'] ?? 'Kota',
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? '2004-01-01',
                    'nik' => '73' . str_pad((string) (($user->id ?? 1) * 137), 14, '0', STR_PAD_LEFT),
                    'npm' => $row['npm'],
                    'alamat' => 'Jalan Kampus No. 1',
                    'nomor_telp' => '081234567001',
                    'angkatan' => $row['angkatan'],
                    'program_studi' => $row['program_studi'],
                    'fakultas' => 'Fakultas Ekonomi dan Bisnis Islam',
                    'asal_sekolah' => 'SMA Negeri 1 Pangkajene',
                    'status_mahasiswa' => 'Lulus',
                    'foto_path' => $row['foto_path'] ?? null,
                    'tanggal_lulus' => $row['tanggal_lulus'] ?? null,
                    'judul_skripsi' => $row['judul_skripsi'] ?? null,
                    'nomor_transkrip' => isset($row['npm']) ? ('TR' . $row['npm'] . '202608') : null,
                ]
            );
        })->values();

        $abbr = [
            'Pendidikan Agama Islam' => 'PAI',
            'Pendidikan Islam Anak Usia Dini' => 'PIAUD',
            'Hukum Keluarga Islam' => 'HKI',
            'Hukum Tata Negara' => 'HTN',
            'Perbankan Syariah' => 'PBS',
            'Ekonomi Syariah' => 'EKS',
        ];

        foreach ($jurusanList as $jIndex => $jurusan) {
            $prefix = $abbr[$jurusan] ?? 'MK';
            foreach (range(1, 8) as $semester) {
                foreach (range(1, 3) as $i) {
                    $kode = $prefix.(($semester * 100) + $i);
                    $dosenId = $dosens->get(($jIndex + $semester + $i) % $dosens->count())?->id;

                    MataKuliah::query()->firstOrCreate(
                        ['kode' => $kode],
                        [
                            'nama' => "{$jurusan} - Semester {$semester} (MK {$i})",
                            'jurusan' => $jurusan,
                            'sks' => $i === 3 ? 2 : 3,
                            'semester' => $semester,
                            'dosen_id' => $dosenId,
                        ]
                    );
                }
            }
        }

        $bobotHuruf = [
            ['angka' => 92, 'huruf' => 'A'],
            ['angka' => 86, 'huruf' => 'A-'],
            ['angka' => 81, 'huruf' => 'B+'],
            ['angka' => 76, 'huruf' => 'B'],
            ['angka' => 71, 'huruf' => 'B-'],
            ['angka' => 66, 'huruf' => 'C+'],
            ['angka' => 60, 'huruf' => 'C'],
            ['angka' => 50, 'huruf' => 'D'],
        ];
        $bobot = [
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
        ];

        $totalSksKumulatif = 0;
        $totalMutuKumulatif = 0;

        foreach ($mahasiswaList as $mhs) {
            $tahunAjaran = '2026/2027';
            $mhsTotalSks = 0;
            $mhsTotalMutu = 0;

            foreach (range(1, 8) as $semester) {
                $jumlahMkPerSmt = ($semester <= 2 || $semester >= 7) ? 4 : 5;
                if ($semester === 6) $jumlahMkPerSmt = 6;

                $mkSemesterIni = MataKuliah::query()
                    ->where('jurusan', $mhs->program_studi)
                    ->where('semester', $semester)
                    ->orderBy('kode')
                    ->limit($jumlahMkPerSmt)
                    ->get();

                if ($mkSemesterIni->count() < $jumlahMkPerSmt) {
                    $mkLainnya = MataKuliah::query()
                        ->where('jurusan', '!=', $mhs->program_studi)
                        ->where('semester', $semester)
                        ->orderBy('kode')
                        ->limit($jumlahMkPerSmt - $mkSemesterIni->count())
                        ->get();
                    $mkSemesterIni = $mkSemesterIni->merge($mkLainnya);
                }

                $krs = Krs::query()->firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                        'semester' => $semester,
                    ],
                    [
                        'tahun_ajaran' => $tahunAjaran,
                        'status_approval' => $semester <= 6 ? 'approved' : 'pending',
                    ]
                );

                foreach ($mkSemesterIni as $mk) {
                    KrsItem::query()->firstOrCreate([
                        'krs_id' => $krs->id,
                        'mata_kuliah_id' => $mk->id,
                    ]);
                }

                $khs = Khs::query()->firstOrCreate(
                    [
                        'mahasiswa_id' => $mhs->id,
                        'semester' => $semester,
                    ],
                    [
                        'tahun_ajaran' => $tahunAjaran,
                    ]
                );

                $totalSkorSmt = 0;
                $totalSksSmt = 0;

                foreach ($mkSemesterIni as $idx => $mk) {
                    $bobotIdx = (($semester - 1) * 5 + $idx) % count($bobotHuruf);
                    if ($semester >= 5 && $idx < 2) $bobotIdx = 0;
                    if ($semester >= 5 && $idx === 4) $bobotIdx = 1;

                    $pilihan = $bobotHuruf[$bobotIdx];
                    $nilaiAngka = $pilihan['angka'] + rand(0, 6);
                    $nilaiHuruf = $pilihan['huruf'];
                    if ($nilaiAngka > 100) $nilaiAngka = 100;

                    KhsItem::query()->updateOrCreate(
                        [
                            'khs_id' => $khs->id,
                            'mata_kuliah_id' => $mk->id,
                        ],
                        [
                            'nilai_angka' => $nilaiAngka,
                            'nilai_huruf' => $nilaiHuruf,
                        ]
                    );

                    $sks = (int) ($mk->sks ?? 0);
                    $bobotH = $bobot[$nilaiHuruf] ?? 0;
                    $totalSkorSmt += $sks * $bobotH;
                    $totalSksSmt += $sks;
                    $mhsTotalSks += $sks;
                    $mhsTotalMutu += $sks * $bobotH;
                }

                $ips = $totalSksSmt > 0 ? round($totalSkorSmt / $totalSksSmt, 2) : 0;
                $ipkSmt = $mhsTotalSks > 0 ? round($mhsTotalMutu / $mhsTotalSks, 2) : $ips;

                $khs->ips = $ips;
                $khs->ipk = $ipkSmt;
                $khs->save();
            }
        }

        Mahasiswa::query()->select('id')->orderBy('id')->each(function (Mahasiswa $mhs) {
            foreach (range(1, 8) as $semester) {
                Khs::query()->firstOrCreate([
                    'mahasiswa_id' => $mhs->id,
                    'semester' => $semester,
                ]);
            }
        });

        Mahasiswa::query()
            ->where('npm', '20260005')
            ->update([
                'nama_lengkap' => 'Nur Afiah',
                'tempat_lahir' => 'Pangkajene',
                'tanggal_lahir' => '2002-05-15',
                'fakultas' => 'Fakultas Ekonomi dan Bisnis Islam',
                'asal_sekolah' => 'SMA Negeri 1 Pangkajene',
                'status_mahasiswa' => 'Lulus',
                'foto_path' => 'foto_mahasiswa/20260005.jpg',
                'tanggal_lulus' => '2026-08-25',
                'judul_skripsi' => 'PRODUKTIVITAS EKONOMI MASYARAKAT PASCA BANJIR DI DESA LEPPANGENG KABUPATEN WAJO (TINJAUAN EKONOMI ISLAM)',
                'nomor_transkrip' => 'TR20260005202608',
            ]);
    }
}
