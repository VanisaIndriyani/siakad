<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use App\Models\KrsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Database\Seeder;

class Transkrip60MkSeeder extends Seeder
{
    public function run(): void
    {
        $npmTarget = env('SEEDER_TRANSKRIP_NPM', '20260005');
        $mhs = Mahasiswa::query()->where('npm', $npmTarget)->first();
        if (!$mhs) {
            $mhs = Mahasiswa::query()->orderBy('id')->first();
            if (!$mhs) {
                $this->command->error('Mahasiswa tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu, atau set env SEEDER_TRANSKRIP_NPM.');
                return;
            }
        }
        $this->command->info("Mahasiswa target: {$mhs->nama_lengkap} (NPM {$mhs->npm})");

        $dosen = Dosen::query()->first();
        $dosenId = $dosen?->id;

        $jurusan = (string) ($mhs->program_studi ?: 'Pendidikan Islam Anak Usia Dini');
        $semesterMk = [
            1 => ['nama' => 'Semester 1 (2022/2023 Ganjil)', 'mk' => [
                ['kode' => 'TRK101', 'nama' => 'Pendidikan Agama Islam', 'sks' => 2],
                ['kode' => 'TRK102', 'nama' => 'Pendidikan Pancasila', 'sks' => 2],
                ['kode' => 'TRK103', 'nama' => 'Bahasa Indonesia', 'sks' => 2],
                ['kode' => 'TRK104', 'nama' => 'Bahasa Inggris I', 'sks' => 2],
                ['kode' => 'TRK105', 'nama' => 'Ilmu Jiwa Perkembangan AUD', 'sks' => 3],
                ['kode' => 'TRK106', 'nama' => 'Bimbingan Baca Tulis Al-Quran II (Imla\' Khat)', 'sks' => 2],
                ['kode' => 'TRK107', 'nama' => 'Pengantar Ilmu Pendidikan', 'sks' => 3],
                ['kode' => 'TRK108', 'nama' => 'Struktur dan Perkembangan Bahasa Indonesia Anak Usia Dini', 'sks' => 2],
                ['kode' => 'TRK109', 'nama' => 'Tafsir Maudu\'i Pendidikan Anak', 'sks' => 2],
                ['kode' => 'TRK110', 'nama' => 'Aqidah Akhlak Islam', 'sks' => 2],
                ['kode' => 'TRK111', 'nama' => 'Statistik Dasar Pendidikan', 'sks' => 2],
            ]],
            2 => ['nama' => 'Semester 2 (2022/2023 Genap)', 'mk' => [
                ['kode' => 'TRK201', 'nama' => 'Pendidikan Kewarganegaraan', 'sks' => 2],
                ['kode' => 'TRK202', 'nama' => 'Bahasa Inggris II', 'sks' => 2],
                ['kode' => 'TRK203', 'nama' => 'Perencanaan Pembelajaran AUD', 'sks' => 3],
                ['kode' => 'TRK204', 'nama' => 'Strategi Pembelajaran AUD', 'sks' => 3],
                ['kode' => 'TRK205', 'nama' => 'Pendidikan Seni Musik AUD', 'sks' => 2],
                ['kode' => 'TRK206', 'nama' => 'Psikologi Perkembangan AUD I', 'sks' => 3],
                ['kode' => 'TRK207', 'nama' => 'Ilmu Ahlak', 'sks' => 2],
                ['kode' => 'TRK208', 'nama' => 'Perkembangan Motorik AUD', 'sks' => 2],
                ['kode' => 'TRK209', 'nama' => 'Fiqih Ibadah Harian Anak', 'sks' => 2],
                ['kode' => 'TRK210', 'nama' => 'Pendidikan Jasmani Dasar', 'sks' => 2],
                ['kode' => 'TRK211', 'nama' => 'Kuliah Kerja Lapangan (KKL) Dasar', 'sks' => 2],
            ]],
            3 => ['nama' => 'Semester 3 (2023/2024 Ganjil)', 'mk' => [
                ['kode' => 'TRK301', 'nama' => 'Kurikulum AUD I', 'sks' => 3],
                ['kode' => 'TRK302', 'nama' => 'Met Khusus Pembelajaran AUD', 'sks' => 2],
                ['kode' => 'TRK303', 'nama' => 'Met Pembelajaran Bahasa Inggris AUD', 'sks' => 2],
                ['kode' => 'TRK304', 'nama' => 'Metode Penelitian Karya Ilmiah', 'sks' => 3],
                ['kode' => 'TRK305', 'nama' => 'Ulumul Qur\'an', 'sks' => 2],
                ['kode' => 'TRK306', 'nama' => 'Teori Bermain AUD', 'sks' => 2],
                ['kode' => 'TRK307', 'nama' => 'Pendidikan Anak Dalam Masyarakat AUD', 'sks' => 2],
                ['kode' => 'TRK308', 'nama' => 'Kemampuan Dasar Mengajar AUD', 'sks' => 2],
                ['kode' => 'TRK309', 'nama' => 'Sejarah Pendidikan Islam di Indonesia', 'sks' => 2],
                ['kode' => 'TRK310', 'nama' => 'Manajemen Kelas AUD', 'sks' => 2],
            ]],
            4 => ['nama' => 'Semester 4 (2023/2024 Genap)', 'mk' => [
                ['kode' => 'TRK401', 'nama' => 'Pengembangan Media Pembelajaran AUD', 'sks' => 2],
                ['kode' => 'TRK402', 'nama' => 'Asesmen Perkembangan AUD', 'sks' => 2],
                ['kode' => 'TRK403', 'nama' => 'Pendidikan Jasmani AUD', 'sks' => 2],
                ['kode' => 'TRK404', 'nama' => 'Pengembangan Kreatifitas Anak AUD', 'sks' => 2],
                ['kode' => 'TRK405', 'nama' => 'Hukum Perjanjian Islam dan Keluarga (HKI)', 'sks' => 2],
                ['kode' => 'TRK406', 'nama' => 'Ilmu Hadits', 'sks' => 2],
                ['kode' => 'TRK407', 'nama' => 'Psikologi Pendidikan AUD', 'sks' => 3],
                ['kode' => 'TRK408', 'nama' => 'Manajemen Pendidikan AUD', 'sks' => 2],
                ['kode' => 'TRK409', 'nama' => 'Pengantar Mikro Ekonomi Syariah', 'sks' => 2],
                ['kode' => 'TRK410', 'nama' => 'Bahasa Arab AUD (Muhadatsah)', 'sks' => 2],
            ]],
            5 => ['nama' => 'Semester 5 (2024/2025 Ganjil)', 'mk' => [
                ['kode' => 'TRK501', 'nama' => 'Pengembangan Kurikulum AUD', 'sks' => 2],
                ['kode' => 'TRK502', 'nama' => 'Pendidikan Inklusi AUD', 'sks' => 2],
                ['kode' => 'TRK503', 'nama' => 'Metode Pengembangan Sosio-Emosional AUD', 'sks' => 2],
                ['kode' => 'TRK504', 'nama' => 'Manajemen Keuangan Sederhana TK/RA', 'sks' => 2],
                ['kode' => 'TRK505', 'nama' => 'Akuntansi Keuangan Syariah', 'sks' => 3],
                ['kode' => 'TRK506', 'nama' => 'Filsafat Pendidikan Islam', 'sks' => 2],
                ['kode' => 'TRK507', 'nama' => 'Neuro Sains dalam Pendidikan AUD', 'sks' => 2],
                ['kode' => 'TRK508', 'nama' => 'Met Peng. Agama, Moral, Disiplin, dan Efektif II', 'sks' => 2],
                ['kode' => 'TRK509', 'nama' => 'Penelitian Tindakan Kelas (PTK)', 'sks' => 2],
                ['kode' => 'TRK510', 'nama' => 'Evaluasi Pembelajaran AUD', 'sks' => 2],
            ]],
            6 => ['nama' => 'Semester 6 (2024/2025 Genap)', 'mk' => [
                ['kode' => 'TRK601', 'nama' => 'Fiqih II', 'sks' => 2],
                ['kode' => 'TRK602', 'nama' => 'Sosiologi Pendidikan', 'sks' => 2],
                ['kode' => 'TRK603', 'nama' => 'Entrepreneurship', 'sks' => 2],
                ['kode' => 'TRK604', 'nama' => 'KPM/KKL', 'sks' => 4],
                ['kode' => 'TRK605', 'nama' => 'Ujian Komprehensif', 'sks' => 2],
                ['kode' => 'TRK606', 'nama' => 'Pengembangan Model Pembelajaran AUD', 'sks' => 2],
                ['kode' => 'TRK607', 'nama' => 'Seminar Pendidikan dan Keilmuan', 'sks' => 2],
                ['kode' => 'TRK608', 'nama' => 'Parenting (MPP)', 'sks' => 2],
                ['kode' => 'TRK609', 'nama' => 'Konsentrasi PAUD Pilihan: Literasi Dini', 'sks' => 2],
                ['kode' => 'TRK610', 'nama' => 'Konsentrasi PAUD Pilihan: Sains dan Matematika Dini', 'sks' => 2],
            ]],
            7 => ['nama' => 'Semester 7 (2025/2026 Ganjil)', 'mk' => [
                ['kode' => 'TRK701', 'nama' => 'Bimbingan dan Konseling AUD', 'sks' => 2],
                ['kode' => 'TRK702', 'nama' => 'Dos Pilihan AUD', 'sks' => 2],
                ['kode' => 'TRK703', 'nama' => 'PPL I (Magang)', 'sks' => 2],
                ['kode' => 'TRK704', 'nama' => 'Metode Penelitian Pendidikan', 'sks' => 3],
                ['kode' => 'TRK705', 'nama' => 'Manajemen dan Supervisi Pendidikan RA/TK', 'sks' => 2],
            ]],
            8 => ['nama' => 'Semester 8 (2025/2026 Genap)', 'mk' => [
                ['kode' => 'TRK801', 'nama' => 'Ulumul Hadits', 'sks' => 2],
                ['kode' => 'TRK802', 'nama' => 'Permasalahan Anak Usia Dini', 'sks' => 2],
                ['kode' => 'TRK803', 'nama' => 'Kurikulum dan Pembelajaran II', 'sks' => 2],
                ['kode' => 'TRK804', 'nama' => 'Skripsi', 'sks' => 6],
            ]],
        ];

        $totalMk = 0;
        foreach ($semesterMk as $smt => $data) {
            $totalMk += count($data['mk']);
        }
        $this->command->info("Total mata kuliah direncanakan: {$totalMk}");

        $this->command->warn("(1/4) Reset KRS/KHS/KhsItem/KrsItem LAMA mahasiswa target (NPM {$mhs->npm}) agar tidak bentrok data lama (Nama MK cingcong Ekonomi Syariah / HKI)...");
        $khsIdsLama = Khs::query()->where('mahasiswa_id', $mhs->id)->pluck('id')->all();
        $krsIdsLama = Krs::query()->where('mahasiswa_id', $mhs->id)->pluck('id')->all();
        if (!empty($khsIdsLama)) {
            KhsItem::query()->whereIn('khs_id', $khsIdsLama)->delete();
            Khs::query()->whereIn('id', $khsIdsLama)->delete();
        }
        if (!empty($krsIdsLama)) {
            KrsItem::query()->whereIn('krs_id', $krsIdsLama)->delete();
            Krs::query()->whereIn('id', $krsIdsLama)->delete();
        }

        $this->command->warn("(2/4) REWRITE 75 MataKuliah (TRK101 s/d TRK804) → GANTI NAMA/PRODI/SKS (hapus semua nama cingcong \"Ekonomi Syariah Sem X (MK Y)\" diganti nama AUD asli)...");
        $forceUpdateMkJikaSudahAda = true;

        $bobotHuruf = [
            ['angka_min' => 92, 'huruf' => 'A'],
            ['angka_min' => 86, 'huruf' => 'A-'],
            ['angka_min' => 81, 'huruf' => 'B+'],
            ['angka_min' => 76, 'huruf' => 'B'],
            ['angka_min' => 71, 'huruf' => 'B-'],
            ['angka_min' => 66, 'huruf' => 'C+'],
            ['angka_min' => 60, 'huruf' => 'C'],
            ['angka_min' => 50, 'huruf' => 'D'],
            ['angka_min' => 0,  'huruf' => 'D'],
        ];
        $bobot = [
            'A' => 4.00, 'A-' => 3.70, 'B+' => 3.30, 'B' => 3.00,
            'B-' => 2.70, 'C+' => 2.30, 'C' => 2.00, 'D' => 1.00,
        ];
        $hurufOrder = ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D'];

        $totalSksKumulatif = 0;
        $totalMutuKumulatif = 0;
        $ipkTerakhir = 0;
        $ipsTerakhir = 0;

        $ujianKompetensi = [
            ['label' => 'Al Quran, Agama, dan Bahasa (Lisan)', 'mk' => 'Ujian Kompetensi Lisan Keagamaan', 'nilai' => null],
            ['label' => 'Al Quran, Agama, dan Bahasa (Lisan)', 'mk' => 'Ujian Kompetensi Lisan Bahasa Arab', 'nilai' => null],
            ['label' => 'Al Quran, Agama, dan Bahasa (Lisan)', 'mk' => 'Ujian Kompetensi Lisan Keintelektualan', 'nilai' => null],
            ['label' => 'Al Quran, Agama, dan Bahasa (Lisan)', 'mk' => 'Ujian Kompetensi Lisan Keintelektualan 2', 'nilai' => null],
        ];

        foreach ($semesterMk as $smt => $data) {
            $tahunAjaran = match (true) {
                $smt <= 2 => '2022/2023',
                $smt <= 4 => '2023/2024',
                $smt <= 6 => '2024/2025',
                default => '2025/2026',
            };

            $krs = Krs::query()->updateOrCreate(
                ['mahasiswa_id' => $mhs->id, 'semester' => $smt],
                ['tahun_ajaran' => $tahunAjaran, 'status_approval' => 'approved']
            );

            $khs = Khs::query()->updateOrCreate(
                ['mahasiswa_id' => $mhs->id, 'semester' => $smt],
                ['tahun_ajaran' => $tahunAjaran]
            );

            $totalSksSmt = 0;
            $totalMutuSmt = 0;

            foreach ($data['mk'] as $i => $mkRaw) {
                $kode = $mkRaw['kode'];
                $namaMk = $mkRaw['nama'];
                $sks = (int) $mkRaw['sks'];

                $mk = MataKuliah::query()->firstOrCreate(
                    ['kode' => $kode],
                    [
                        'nama' => $namaMk,
                        'jurusan' => $jurusan,
                        'sks' => $sks,
                        'semester' => $smt,
                        'dosen_id' => $dosenId,
                    ]
                );
                if ($forceUpdateMkJikaSudahAda || !$mk->wasRecentlyCreated) {
                    $changed = false;
                    if ($mk->nama !== $namaMk) { $mk->nama = $namaMk; $changed = true; }
                    if ($mk->jurusan !== $jurusan) { $mk->jurusan = $jurusan; $changed = true; }
                    if ((int) $mk->sks !== $sks) { $mk->sks = $sks; $changed = true; }
                    if ((int) $mk->semester !== $smt) { $mk->semester = $smt; $changed = true; }
                    if ($dosenId && !$mk->dosen_id) { $mk->dosen_id = $dosenId; $changed = true; }
                    if ($changed) $mk->save();
                }

                KrsItem::query()->firstOrCreate([
                    'krs_id' => $krs->id,
                    'mata_kuliah_id' => $mk->id,
                ]);

                $seed = crc32($mhs->npm . '-' . $smt . '-' . $kode);
                $mt_rand = function (int $min, int $max) use ($seed, $i, $smt) {
                    $val = ($seed + $i * 7919 + $smt * 104729) % ($max - $min + 1);
                    return $min + abs($val);
                };

                if ($smt <= 4 && $i < 4) {
                    $hurufIdx = $mt_rand(0, 2);
                } elseif ($smt >= 7 || $i >= 6) {
                    $hurufIdx = $mt_rand(0, 4);
                } else {
                    $hurufIdx = $mt_rand(0, 5);
                }
                if ($hurufIdx >= count($hurufOrder)) $hurufIdx = count($hurufOrder) - 1;
                $nilaiHuruf = $hurufOrder[$hurufIdx];
                $bobotAngka = $bobot[$nilaiHuruf];

                $nilaiAngkaMin = $bobotHuruf[$hurufIdx]['angka_min'];
                $nilaiAngkaMax = min(100, $nilaiAngkaMin + 6);
                if ($nilaiAngkaMax <= $nilaiAngkaMin) $nilaiAngkaMax = min(100, $nilaiAngkaMin + 5);
                $nilaiAngka = $mt_rand($nilaiAngkaMin, $nilaiAngkaMax);
                if ($nilaiAngka > 100) $nilaiAngka = 100;

                $ratio = [
                    'tm' => $mt_rand(25, 35) / 100,
                    'quis' => $mt_rand(10, 20) / 100,
                    'mid' => $mt_rand(20, 30) / 100,
                    'final' => $mt_rand(20, 30) / 100,
                ];
                $sumR = array_sum($ratio);
                foreach ($ratio as $k => $v) $ratio[$k] = $v / $sumR;

                $nilaiTm = min(100, max(0, (int) round($nilaiAngka + $mt_rand(-8, 8))));
                $nilaiQuis = min(100, max(0, (int) round($nilaiAngka + $mt_rand(-10, 6))));
                $nilaiMid = min(100, max(0, (int) round($nilaiAngka + $mt_rand(-7, 7))));
                $nilaiFinal = min(100, max(0, (int) round($nilaiAngka + $mt_rand(-9, 5))));

                KhsItem::query()->updateOrCreate(
                    ['khs_id' => $khs->id, 'mata_kuliah_id' => $mk->id],
                    [
                        'nilai_tm' => $nilaiTm,
                        'nilai_quis' => $nilaiQuis,
                        'nilai_mid' => $nilaiMid,
                        'nilai_final' => $nilaiFinal,
                        'nilai_angka' => $nilaiAngka,
                        'nilai_huruf' => $nilaiHuruf,
                    ]
                );

                if (str_starts_with($kode, 'TRK60') && in_array($kode, ['TRK601', 'TRK602', 'TRK603', 'TRK604'])) {
                    $idxUjian = $kode === 'TRK601' ? 0 : ($kode === 'TRK602' ? 1 : ($kode === 'TRK603' ? 2 : 3));
                    if (isset($ujianKompetensi[$idxUjian])) {
                        $ujianKompetensi[$idxUjian]['nilai'] = [
                            'jumlah' => '0',
                            'huruf' => $nilaiHuruf,
                            'mutu' => '0',
                        ];
                        $ujianKompetensi[$idxUjian]['sks'] = $sks;
                    }
                }

                $totalSksSmt += $sks;
                $totalMutuSmt += $sks * $bobotAngka;
                $totalSksKumulatif += $sks;
                $totalMutuKumulatif += $sks * $bobotAngka;
            }

            $ips = $totalSksSmt > 0 ? round($totalMutuSmt / $totalSksSmt, 2) : 0;
            $ipk = $totalSksKumulatif > 0 ? round($totalMutuKumulatif / $totalSksKumulatif, 2) : $ips;
            $khs->ips = $ips;
            $khs->ipk = $ipk;
            $khs->save();

            $ipsTerakhir = $ips;
            $ipkTerakhir = $ipk;

            $this->command->line("  Semester {$smt} ({$tahunAjaran}): MK=".count($data['mk']).", SKS={$totalSksSmt}, IPS={$ips}, IPK={$ipk}");
        }

        $predikat = match (true) {
            $ipkTerakhir >= 3.76 => 'Sangat Memuaskan (Pujian)',
            $ipkTerakhir >= 3.51 => 'Sangat Memuaskan',
            $ipkTerakhir >= 3.00 => 'Memuaskan',
            $ipkTerakhir >= 2.76 => 'Cukup Memuaskan',
            $ipkTerakhir >= 2.00 => 'Cukup',
            default => 'Kurang',
        };
        if ($ipkTerakhir >= 3.50 && $ipkTerakhir < 3.76) $predikat = 'Sangat Memuaskan';
        if ($ipkTerakhir >= 3.76) $predikat = 'Sangat Memuaskan (Dengan Pujian)';

        // Lengkapi Ujian Kompetensi kosong
        $namaJudul = 'Strategi Guru dalam Meningkatkan Permahaman Membaca pada Siswa SMA 2 Sidenreng Rappang';
        if ($mhs->program_studi && str_contains(strtolower($mhs->program_studi), 'anak usia dini')) {
            $namaJudul = 'Permainan Tradisional sebagai Media Pengembangan Motorik Kasar Anak Usia Dini di RA DDI 1 Sidrap';
        }

        foreach ($ujianKompetensi as $i => $item) {
            if (empty($item['nilai'])) {
                $hurufUji = $i === 0 ? 'A' : ($i === 1 ? 'A-' : ($i === 2 ? 'B+' : 'A'));
                $ujianKompetensi[$i]['nilai'] = ['jumlah' => '0', 'huruf' => $hurufUji, 'mutu' => '0'];
                if (!isset($ujianKompetensi[$i]['sks'])) $ujianKompetensi[$i]['sks'] = 2;
            }
        }

        $skBanptDefault = '337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026';
        $kodeProdi = (int) substr((string) $mhs->npm, -3) > 0 ? sprintf('00%02dS26', abs(crc32((string) $mhs->npm) % 99)) : '0012S26';

        $mhs->fakultas = $mhs->fakultas ?: (string) ($mhs->program_studi && str_contains(strtolower($mhs->program_studi), 'ekonomi') ? 'Fakultas Ekonomi dan Bisnis Islam' : 'Fakultas Tarbiyah dan Keguruan');
        $mhs->status_mahasiswa = 'Lulus';
        $mhs->tanggal_lulus = $mhs->tanggal_lulus ?? '2026-08-27';
        $mhs->judul_skripsi = $mhs->judul_skripsi ?: $namaJudul;
        $mhs->nomor_transkrip = $mhs->nomor_transkrip ?: 'TR' . $mhs->npm . date('Ymd');
        $mhs->ujian_kompre = $ujianKompetensi;
        $mhs->nomor_sk_banpt = $mhs->nomor_sk_banpt ?: $skBanptDefault;
        $mhs->kode_prodi_feeder = $mhs->kode_prodi_feeder ?? $kodeProdi;
        $mhs->ipk = $mhs->ipk ?? (string) $ipkTerakhir;
        $mhs->predikat_kelulusan = $mhs->predikat_kelulusan ?? $predikat;
        $mhs->jumlah_sks = $mhs->jumlah_sks ?? $totalSksKumulatif;
        $mhs->save();

        $this->command->info('');
        $this->command->info("✅ BERHASIL. Mahasiswa {$mhs->nama_lengkap} (NPM {$mhs->npm}) sekarang memiliki {$totalMk} MK = {$totalSksKumulatif} SKS, IPK {$ipkTerakhir} ({$predikat}).");
        $this->command->info("   Cek di: /admin/transkrip-nilai/{$mhs->id}");
        $this->command->info("   PDF:  /admin/transkrip-nilai/{$mhs->id}/pdf");
        $this->command->info("   Excel:/admin/transkrip-nilai/{$mhs->id}/excel");
    }
}
