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

        User::factory()->create([
            'name' => 'Staf Keuangan',
            'email' => 'keuangan@iaiddisidrap.ac.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_KEUANGAN,
        ]);

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
                'nama_lengkap' => 'Mahasiswa 20260005',
                'npm' => '20260005',
                'angkatan' => 2026,
                'program_studi' => 'Perbankan Syariah',
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
                    'tempat_lahir' => 'Kota',
                    'tanggal_lahir' => '2004-01-01',
                    'nik' => null,
                    'npm' => $row['npm'],
                    'alamat' => 'Jalan Kampus No. 1',
                    'nomor_telp' => '081234567001',
                    'angkatan' => $row['angkatan'],
                    'program_studi' => $row['program_studi'],
                    'asal_sekolah' => 'SMA Negeri 1',
                    'status_mahasiswa' => 'Aktif',
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

        foreach ($mahasiswaList as $mhs) {
            $tahunAjaran = '2026/2027';

            $mkSem3 = MataKuliah::query()
                ->where('jurusan', $mhs->program_studi)
                ->where('semester', 3)
                ->orderBy('kode')
                ->limit(3)
                ->get();

            $krsApproved = Krs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'semester' => 3,
                ],
                [
                    'tahun_ajaran' => $tahunAjaran,
                    'status_approval' => 'approved',
                ]
            );

            foreach ($mkSem3 as $mk) {
                KrsItem::query()->firstOrCreate([
                    'krs_id' => $krsApproved->id,
                    'mata_kuliah_id' => $mk->id,
                ]);
            }

            $khsSem3 = Khs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'semester' => 3,
                ],
                [
                    'tahun_ajaran' => $tahunAjaran,
                ]
            );

            foreach ($mkSem3 as $mk) {
                KhsItem::query()->firstOrCreate(
                    [
                        'khs_id' => $khsSem3->id,
                        'mata_kuliah_id' => $mk->id,
                    ],
                    [
                        'nilai_angka' => null,
                        'nilai_huruf' => null,
                    ]
                );
            }

            $mkSem4 = MataKuliah::query()
                ->where('jurusan', $mhs->program_studi)
                ->where('semester', 4)
                ->orderBy('kode')
                ->limit(3)
                ->get();

            $krsPending = Krs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $mhs->id,
                    'semester' => 4,
                ],
                [
                    'tahun_ajaran' => $tahunAjaran,
                    'status_approval' => 'pending',
                ]
            );

            foreach ($mkSem4 as $mk) {
                KrsItem::query()->firstOrCreate([
                    'krs_id' => $krsPending->id,
                    'mata_kuliah_id' => $mk->id,
                ]);
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
    }
}
