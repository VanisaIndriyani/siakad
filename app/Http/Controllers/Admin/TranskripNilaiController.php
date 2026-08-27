<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TranskripNilaiController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));
        $prodi = trim((string) $request->get('prodi', ''));

        $query = Mahasiswa::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%");
            });
        }

        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }

        if ($prodi !== '') {
            $query->where('program_studi', $prodi);
        }

        $mahasiswa = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $angkatanList = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->sortDesc()
            ->values()
            ->all();

        $prodiList = [
            'Pendidikan Agama Islam',
            'Pendidikan Islam Anak Usia Dini',
            'Hukum Keluarga Islam',
            'Hukum Tata Negara',
            'Perbankan Syariah',
            'Ekonomi Syariah',
        ];

        return view('admin.transkrip-nilai.index', [
            'mahasiswa' => $mahasiswa,
            'q' => $q,
            'angkatan' => $angkatan,
            'prodi' => $prodi,
            'angkatanList' => $angkatanList,
            'prodiList' => $prodiList,
        ]);
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        return view('admin.transkrip-nilai.show', $this->buildTranskripData($mahasiswa));
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $ujian = $mahasiswa->ujian_kompre;
        if (!is_array($ujian)) {
            $ujian = [];
        }

        while (count($ujian) < 7) {
            $ujian[] = '';
        }

        return view('admin.transkrip-nilai.edit', [
            'mahasiswa' => $mahasiswa,
            'ujian' => array_slice($ujian, 0, 7),
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_transkrip' => ['nullable', 'string', 'max:100'],
            'tanggal_lulus' => ['nullable', 'date'],
            'nomor_sk_banpt' => ['nullable', 'string', 'max:100'],
            'judul_skripsi' => ['nullable', 'string', 'max:500'],
            'ujian' => ['nullable', 'array'],
            'ujian.*' => ['nullable', 'string', 'max:255'],
        ]);

        $ujian = $validated['ujian'] ?? [];
        $ujian = array_values(array_filter(array_map(fn($v) => trim((string) $v), $ujian), fn($v) => $v !== ''));

        $mahasiswa->update([
            'nomor_transkrip' => $validated['nomor_transkrip'] !== '' ? $validated['nomor_transkrip'] : null,
            'tanggal_lulus' => $validated['tanggal_lulus'] ?? null,
            'nomor_sk_banpt' => $validated['nomor_sk_banpt'] !== '' ? $validated['nomor_sk_banpt'] : null,
            'judul_skripsi' => $validated['judul_skripsi'] !== '' ? $validated['judul_skripsi'] : null,
            'ujian_kompre' => count($ujian) > 0 ? $ujian : null,
        ]);

        return redirect()->route('admin.transkrip-nilai.show', $mahasiswa)
            ->with('success', 'Data transkrip berhasil disimpan.');
    }

    public function pdf(Mahasiswa $mahasiswa)
    {
        $data = $this->buildTranskripData($mahasiswa);

        $html = view('admin.transkrip-nilai.pdf', $data)->render();

        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('Folio', 'portrait');
        $dompdf->render();

        $namafile = 'Transkrip-' . ($mahasiswa->npm ?: $mahasiswa->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $mahasiswa->nama_lengkap) . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namafile . '"',
        ]);
    }

    private function nilaiMutuHuruf(string $huruf): float
    {
        return match ($huruf) {
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            default => 0.00,
        };
    }

    private function predikat(float $ipk): string
    {
        if ($ipk >= 3.75) return 'Dengan Pujian';
        if ($ipk >= 3.50) return 'Sangat Memuaskan';
        if ($ipk >= 3.00) return 'Memuaskan';
        if ($ipk >= 2.00) return 'Cukup';
        return 'Kurang';
    }

    private function formatTanggalID($tanggal, string $fallback = '-'): string
    {
        try {
            if ($tanggal === null || $tanggal === '') {
                return $fallback;
            }
            $c = $tanggal instanceof \Illuminate\Support\Carbon || $tanggal instanceof \DateTimeInterface
                ? \Illuminate\Support\Carbon::instance($tanggal)
                : \Illuminate\Support\Carbon::parse($tanggal);
            if (!$c) return $fallback;

            $bulanID = [
                1  => 'Januari',
                2  => 'Februari',
                3  => 'Maret',
                4  => 'April',
                5  => 'Mei',
                6  => 'Juni',
                7  => 'Juli',
                8  => 'Agustus',
                9  => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $hari   = (int) $c->format('j');
            $bulan  = (int) $c->format('n');
            $tahun  = (int) $c->format('Y');
            $namaBulan = $bulanID[$bulan] ?? $c->format('F');

            return sprintf('%02d %s %04d', $hari, $namaBulan, $tahun);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function defaultUjianKompre(): array
    {
        return [
            'Ujian Komprehensif',
            'Al-Qur\'an, Agama, dan Bahasa (Lisan)',
            'Ilmu Pendidikan Islam',
            'Pengembangan Kurikulum Anak Usia Dini',
            'Metodik Khusus Pendidikan Anak Usia Dini',
            'Pengembangan Moral dan Agama',
            'Psikologi Perkembangan Anak',
        ];
    }

    private function buildTranskripData(Mahasiswa $mahasiswa): array
    {
        $mahasiswa->load([
            'khs.items.mataKuliah',
            'dosenPenasehat',
            'dekanFakultas',
        ]);

        $items = [];

        foreach ($mahasiswa->khs as $khs) {
            foreach ($khs->items as $khsItem) {
                if ($khsItem->mataKuliah) {
                    $items[] = $khsItem;
                }
            }
        }

        usort($items, function ($a, $b) {
            $sa = (int) ($a->mataKuliah->semester ?? 0);
            $sb = (int) ($b->mataKuliah->semester ?? 0);
            if ($sa !== $sb) return $sa <=> $sb;
            return strnatcasecmp((string) ($a->mataKuliah->kode ?? ''), (string) ($b->mataKuliah->kode ?? ''));
        });

        $totalSks = 0;
        $totalMutu = 0;
        $sksLulus = 0;
        $mutuLulus = 0;

        $daftarMataKuliah = [];

        foreach ($items as $it) {
            $mk = $it->mataKuliah;
            $sks = (int) ($mk->sks ?? 0);
            $huruf = (string) ($it->nilai_huruf ?? '');
            $nilaiM = $this->nilaiMutuHuruf($huruf);
            $m = $sks * $nilaiM;

            $namaMk = (string) ($mk->nama ?? '');
            if ($namaMk !== '') {
                $daftarMataKuliah[] = (object) [
                    'nama_mata_kuliah' => $namaMk,
                    'sks' => $sks,
                    'nilai_huruf' => $huruf,
                    'nilai_m' => $m,
                ];
            }

            $it->_sks = $sks;
            $it->_nilai_huruf = $huruf;
            $it->_nilai_m = $m;
            $it->_semester = $mk->semester ?? 0;

            $totalSks += $sks;
            $totalMutu += $m;

            if ($huruf !== '' && $huruf !== 'E') {
                $sksLulus += $sks;
                $mutuLulus += $m;
            }
        }

        $ujianKompre = is_array($mahasiswa->ujian_kompre) ? $mahasiswa->ujian_kompre : [];
        $ujianKompre = array_values(array_filter(array_map(fn($v) => trim((string) $v), $ujianKompre), fn($v) => $v !== ''));
        if (count($ujianKompre) === 0) {
            $ujianKompre = $this->defaultUjianKompre();
        }

        $totalItem = count($daftarMataKuliah);
        $jumlahKiri = (int) ceil($totalItem / 2);
        $bagianKiri = array_slice($daftarMataKuliah, 0, $jumlahKiri);
        $bagianKanan = array_slice($daftarMataKuliah, $jumlahKiri);

        $ipk = 0;
        if ($sksLulus > 0) {
            $ipk = round($mutuLulus / $sksLulus, 2);
        }

        $nomorTranskrip = $mahasiswa->nomor_transkrip
            ? $mahasiswa->nomor_transkrip
            : ('TR' . ($mahasiswa->npm ? $mahasiswa->npm : '0000' . $mahasiswa->id) . now()->format('Ym'));

        $judulSkripsi = (string) ($mahasiswa->judul_skripsi ?? '-');
        if ($judulSkripsi === '') $judulSkripsi = '-';

        $tempatLahir = (string) ($mahasiswa->tempat_lahir ?? '-');
        if ($tempatLahir === '') $tempatLahir = '-';
        $tglLahir = $this->formatTanggalID($mahasiswa->tanggal_lahir, '-');
        if ($tempatLahir !== '-' && $tglLahir !== '-') {
            $tempatTgl = "{$tempatLahir}, {$tglLahir}";
        } else {
            $gabung = ($tempatLahir !== '-' ? $tempatLahir : '') . ($tglLahir !== '-' ? $tglLahir : '');
            $tempatTgl = $gabung !== '' ? $gabung : '-';
        }

        $tanggalLulus = $this->formatTanggalID($mahasiswa->tanggal_lulus, '-');

        $tglTtd = $mahasiswa->tanggal_lulus
            ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus)
            : now();
        $tanggalTtd = 'Pangkajene, ' . $this->formatTanggalID($tglTtd, now()->format('d F Y'));

        $skBanpt = (string) ($mahasiswa->nomor_sk_banpt ?? '');
        if ($skBanpt === '') {
            $tahunLulus = $mahasiswa->tanggal_lulus
                ? (int) \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus)->format('Y')
                : (int) date('Y');
            $skBanpt = "281/SK/LAMDIK/AK/V/III/{$tahunLulus}";
        }

        $ujianAda = $ujianKompre;
        $ujianCount = count($ujianAda);

        $dekan = $mahasiswa->dekanFakultas;
        if (!$dekan) {
            $fakultasMhs = (string) ($mahasiswa->fakultas ?? '');
            if ($fakultasMhs !== '') {
                $dekan = \App\Models\Dosen::query()
                    ->where(function ($s) {
                        $s->whereNotNull('jabatan_struktural')
                            ->whereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%fakultas%'])
                            ->orWhereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%']);
                    })
                    ->where('fakultas', $fakultasMhs)
                    ->first();
            }
            if (!$dekan) {
                $dekan = \App\Models\Dosen::query()
                    ->where(function ($s) {
                        $s->whereNotNull('jabatan_struktural')
                            ->whereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%fakultas%'])
                            ->orWhereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%']);
                    })
                    ->first();
            }
        }

        if ($dekan) {
            $ttdJabatan = trim((string) ($dekan->jabatan_struktural ?? ''));
            if ($ttdJabatan === '') {
                $fakultasTtd = (string) ($dekan->fakultas ?: ($mahasiswa->fakultas ?? ''));
                $ttdJabatan = $fakultasTtd !== '' ? "DEKAN FAKULTAS " . strtoupper($fakultasTtd) : "DEKAN FAKULTAS";
            }
            $ttdNama = (string) ($dekan->nama ?? '');
            $nomorInduk = (string) ($dekan->nidn ?? '');
            $labelInduk = 'NIDN';
            if ($nomorInduk === '' && !empty($dekan->nidk)) {
                $nomorInduk = (string) $dekan->nidk;
                $labelInduk = 'NIDK';
            }
            if ($nomorInduk === '' && !empty($dekan->nip)) {
                $nomorInduk = (string) $dekan->nip;
                $labelInduk = 'NIP';
            }
            if ($nomorInduk === '' && !empty($dekan->nuptk)) {
                $nomorInduk = (string) $dekan->nuptk;
                $labelInduk = 'NUPTK';
            }
            if ($ttdNama === '') {
                $ttdNama = 'Dr. H. UMAR YAHYA, M.Ag.';
                $nomorInduk = '8932610021';
                $labelInduk = 'NIDK';
            }
        } else {
            $fakultasTtd = (string) ($mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan');
            $ttdJabatan = "DEKAN FAKULTAS " . strtoupper($fakultasTtd);
            $ttdNama = 'Dr. H. UMAR YAHYA, M.Ag.';
            $nomorInduk = '8932610021';
            $labelInduk = 'NIDK';
        }
        $ttdNomor = $nomorInduk;
        $ttdNomorLabel = $labelInduk;

        $fotoMahasiswa = null;
        if (!empty($mahasiswa->foto_path)) {
            try {
                $relPath = trim(str_replace(['/', '\\'], '/', (string) $mahasiswa->foto_path), '/');
                $absPath = public_path('storage/' . $relPath);
                $absPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absPath);

                if (\Illuminate\Support\Facades\File::exists($absPath) && \Illuminate\Support\Facades\File::isFile($absPath)) {
                    $size = (int) \Illuminate\Support\Facades\File::size($absPath);
                    if ($size > 0 && $size < 10_000_000) {
                        $ext = strtolower((string) \Illuminate\Support\Facades\File::extension($absPath));
                        $mimeMap = [
                            'jpg'  => 'image/jpeg',
                            'jpeg' => 'image/jpeg',
                            'png'  => 'image/png',
                            'gif'  => 'image/gif',
                        ];
                        if (isset($mimeMap[$ext])) {
                            $content = \Illuminate\Support\Facades\File::get($absPath);
                            if ($content !== false && $content !== '') {
                                $fotoMahasiswa = 'data:' . $mimeMap[$ext] . ';base64,' . base64_encode($content);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                $fotoMahasiswa = null;
            }
        }

        return [
            'mahasiswa' => $mahasiswa,
            'items' => $items,
            'daftarMataKuliah' => $daftarMataKuliah,
            'bagianKiri' => $bagianKiri,
            'bagianKanan' => $bagianKanan,
            'left' => $bagianKiri,
            'right' => $bagianKanan,
            'half' => count($bagianKiri),
            'maxRows' => $maxRows ?? max(count($bagianKiri), count($bagianKanan)),
            'totalSks' => $totalSks,
            'totalMutu' => round($totalMutu, 2),
            'sksLulus' => $sksLulus,
            'mutuLulus' => round($mutuLulus, 2),
            'ipk' => $ipk,
            'predikat' => $this->predikat($ipk),
            'nomorTranskrip' => $nomorTranskrip,
            'ujianKompre' => $ujianKompre,
            'ujianAda' => $ujianAda,
            'ujianCount' => $ujianCount,
            'judulSkripsi' => $judulSkripsi,
            'tempatTgl' => $tempatTgl,
            'tanggalLulus' => $tanggalLulus,
            'tanggalTtd' => $tanggalTtd,
            'skBanpt' => $skBanpt,
            'fotoMahasiswa' => $fotoMahasiswa,
            'ttdJabatan' => $ttdJabatan,
            'ttdNama' => $ttdNama,
            'ttdNomor' => $ttdNomor,
            'ttdNomorLabel' => $ttdNomorLabel,
        ];
    }
}
