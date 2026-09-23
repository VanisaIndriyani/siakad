<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Akademik</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 10mm 8mm 10mm;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            margin: 0;
            padding: 0;
            font-size: 9pt;
            line-height: 1.2;
        }

        .wrap { width: 100%; }

        /* KOP SURAT */
        .kop-wrap { width: 100%; text-align: center; margin-bottom: 5px; }
        .kop-title-a { font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-title-b { font-size: 13pt; font-weight: bold; margin-top: 1px; text-transform: uppercase; }
        .kop-terakreditasi { font-size: 8.5pt; margin-top: 2px; font-weight: bold; }
        .kop-alamat-line { font-size: 8pt; margin-top: 1px; }
        .kop-line-double { margin-top: 6px; width: 100%; margin-bottom: 20px; }
        .kop-line-top { width: 100%; height: 2px; background-color: #000; }
        .kop-line-bottom { width: 100%; height: 2px; background-color: #000; margin-top: 2px; }

        /* JUDUL */
        .judul-box { text-align: center; margin: 0; margin-bottom: 20px; }
        .judul-text { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .judul-nomor { font-size: 9pt; margin-top: 14px; }

        /* BIODATA */
        .biodata { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 8px; }
        .biodata td { vertical-align: top; padding: 1px 0; }
        .bio-label { width: 22%; }
        .bio-sep { width: 2%; text-align: center; }
        .bio-val { width: 26%; font-weight: bold; }
        .bio-label-r { width: 20%; padding-left: 5px; }
        .bio-val-r { width: 30%; font-weight: bold; }

        /* TABEL NILAI */
        table.nilai {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-spacing: 0;
            font-size: 8pt;
            box-sizing: border-box;
        }
        table.nilai th { border: 1px solid #000; background-color: #e0f2ea; font-weight: bold; padding: 3px 1px; text-align: center; }
        table.nilai td { border: 1px solid #000; padding: 2px; vertical-align: middle; text-align: center; box-sizing: border-box; }
        table.nilai td.mk { text-align: left; padding-left: 4px; }
        table.nilai td.nilaih { font-weight: bold; }
        table.nilai tr.jumlah td { background: #ffffff !important; font-weight: 700; padding: 2.5px 3px; letter-spacing: 0.2px; line-height: 1.15; border: 1px solid #000000 !important; }
        table.nilai tr.jumlah td.mk { text-align: center; }
        table.nilai tr.jumlah td.jumlah-dashed {
            background: #ffffff !important;
            border: 1px solid #000000 !important;
            border-top: 1px dashed #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: none !important;
            border-bottom: 1px solid #000000 !important;
        }
        table.nilai tr.spacer-row td {
            background: #ffffff !important; border: 1px solid #000000 !important;
            height: 10px; padding: 0;
        }
        table.nilai tr.ujian-head td { background: #ffffff !important; font-weight: 700; padding: 3px 3px; letter-spacing: 0.2px; line-height: 1.2; border: 1px solid #000000 !important; }
        table.nilai td.ujian-left-title { text-align: left; padding-left: 7px !important; border: 1px solid #000 !important; border-top: 1px solid #000 !important; }
        table.nilai tr.ujian-head td.ujian-right-spacer {
            background: #ffffff !important;
            border: 1px solid #000000 !important;
            border-top: 1px solid #000000 !important;
            border-left: 1px solid #000000 !important;
            border-right: none !important;
            border-bottom: none !important;
        }
        table.nilai tr.jumlah td.left-col,
        table.nilai tr.spacer-row td.left-col,
        table.nilai tr.ujian-head td.left-col,
        table.nilai tr.ujian-row td.left-col {
            background: #ffffff !important;
            border: 1px solid #000000 !important;
            font-weight: 400 !important;
            padding: 3px 4px !important;
            text-align: center !important;
            letter-spacing: 0 !important;
        }
        table.nilai tr.jumlah td.mk.left-col,
        table.nilai tr.spacer-row td.mk.left-col,
        table.nilai tr.ujian-head td.mk.left-col,
        table.nilai tr.ujian-row td.mk.left-col {
            text-align: left !important;
            padding: 3px 6px !important;
        }
        table.nilai tr.ujian-row td { background: #ffffff !important; padding: 2px 3px; line-height: 1.15; }
        table.nilai tr.ujian-row td:nth-child(6) {
            border-left: 1px solid #000000 !important;
        }
        table.nilai tr.jumlah td:nth-child(10),
        table.nilai tr.ujian-head td:nth-child(9),
        table.nilai tr.ujian-row td:nth-child(10) {
            border-right: 1px solid #000000 !important;
        }

        /* RINGKASAN IPK */
        .ringkasan { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-top: 16px; font-weight: bold; }
        .ringkasan td { padding: 1px 0; vertical-align: top; }

        /* FOOTER: FOTO & TANDA TANGAN (LURUS SESUAI SCREENSHOT) */
        .footer-wrapper { margin-top: 24px; text-align: right; }
        .footer-table {
            display: inline-table;
            width: auto;
            margin: 0;
            border-collapse: collapse;
            text-align: right;
        }
        .footer-table td { vertical-align: top; }
        .footer-table .foto-col { padding: 0 8px 0 0; }
        .footer-table .ttd-col { padding: 0; }

        .foto-box {
            width: 28mm;
            height: 38mm;
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            margin: 0;
        }
        .foto-box img { width: 100%; height: 100%; object-fit: cover; }

        .ttd-box { text-align: left; font-size: 8.5pt; width: auto; display: inline-block; }
        .ttd-jabatan-text { font-weight: bold; letter-spacing: 0.2px; }
        .ttd-nama { font-weight: bold; text-decoration: underline; margin-top: 80px; }
    </style>
</head>
<body>
    <div class="wrap">
        @php
            /* =========================================================
               EXTRACT SEMUA DATA DARI CONTROLLER ($data) KE VARIABLE
               Supaya blade bisa panggil langsung: {{ $nama }} dll.
               ========================================================= */
            $mahasiswa = $data['mahasiswa'] ?? null;
            $nomorTranskrip = $data['nomorTranskrip'] ?? '-';
            $noIjazah = $data['noIjazah'] ?? '-';
            $tempatTgl = $data['tempatTgl'] ?? '-';
            $tanggalLulus = $data['tanggalLulus'] ?? '-';
            $skBanpt = $data['skBanpt'] ?? '-';
            $daftarMataKuliah = $data['daftarMataKuliah'] ?? [];
            if (!is_array($daftarMataKuliah) && !($daftarMataKuliah instanceof \Traversable)) {
                $daftarMataKuliah = [];
            }
            $totalSks = $data['totalSks'] ?? 0;
            $totalMutu = $data['totalMutu'] ?? 0;
            if (is_float($totalMutu) || is_numeric($totalMutu)) {
                $fmt = number_format((float)$totalMutu, 2, '.', '');
                $fmt = rtrim($fmt, '0');
                $fmt = rtrim($fmt, '.');
                $totalMutu = $fmt === '' ? '0' : $fmt;
            }
            $ipk = $data['ipk'] ?? 0;
            $predikat = $data['predikat'] ?? '-';
            $judulSkripsi = $data['judulSkripsi'] ?? '-';
            $tanggalTtd = $data['tanggalTtd'] ?? 'Sidenreng Rappang, .............. 2026';
            $ttdJabatan = $data['ttdJabatan'] ?? 'DEKAN FAKULTAS';
            $ttdNama = $data['ttdNama'] ?? ' Nama Pejabat & Gelar ';
            $ttdNomorLabel = $data['ttdNomorLabel'] ?? 'NIDN';
            $ttdNomor = $data['ttdNomor'] ?? '........................';
            $fotoMahasiswa = $data['fotoMahasiswa'] ?? null;
            $ujianKompre = $data['ujianKompre'] ?? [];
            if (!is_array($ujianKompre) && !($ujianKompre instanceof \Traversable)) {
                $ujianKompre = [];
            }
            $ujianKompre = array_values($ujianKompre);
            $ujianAda = array_values(array_filter(array_map(static fn($v): string => trim((string)($v ?? '')), $ujianKompre), static fn($v): bool => $v !== ''));
            $ujianCount = count($ujianAda);
        @endphp

        {{-- KOP SURAT --}}
        <div class="kop-wrap">
            @if(!empty($logoSrc))
                <div style="margin-bottom: 4px; text-align: center;">
                    <img src="{{ $logoSrc }}" alt="Logo" style="width: 68px; height: 68px; object-fit: contain; display: inline-block;">
                </div>
            @endif
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
            <div class="kop-line-double">
                <div class="kop-line-top"></div>
                <div class="kop-line-bottom"></div>
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="judul-box">
            <div class="judul-text">TRANSKRIP AKADEMIK</div>
            <div class="judul-nomor">Nomor : {{ $nomorTranskrip ?? '-' }}</div>
        </div>

        {{-- BIODATA MAHASISWA --}}
        <table class="biodata">
            <tr>
                <td class="bio-label">Nama</td>
                <td class="bio-sep">:</td>
                <td class="bio-val">{{ $mahasiswa->nama_lengkap ?? ($mahasiswa->nama ?? '-') }}</td>
                <td class="bio-label-r">Program Pendidikan</td>
                <td class="bio-sep">:</td>
                <td class="bio-val-r">Strata Satu (S1)</td>
            </tr>
            <tr>
                <td class="bio-label">No. Pokok Mahasiswa</td>
                <td class="bio-sep">:</td>
                <td class="bio-val">{{ $mahasiswa->npm ?? '-' }}</td>
                <td class="bio-label-r">Fakultas</td>
                <td class="bio-sep">:</td>
                <td class="bio-val-r">{{ $mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan' }}</td>
            </tr>
            <tr>
                <td class="bio-label">No. Ijazah</td>
                <td class="bio-sep">:</td>
                <td class="bio-val">{{ $noIjazah ?? '-' }}</td>
                <td class="bio-label-r">Program Studi</td>
                <td class="bio-sep">:</td>
                <td class="bio-val-r">{{ $mahasiswa->program_studi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bio-label">Tempat / Tanggal Lahir</td>
                <td class="bio-sep">:</td>
                <td class="bio-val">{{ $tempatTgl ?? '-' }}</td>
                <td class="bio-label-r">No. SK BAN-PT</td>
                <td class="bio-sep">:</td>
                <td class="bio-val-r">{{ $skBanpt ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bio-label">Tanggal, Bulan dan Tahun Lulus</td>
                <td class="bio-sep">:</td>
                <td class="bio-val">{{ $tanggalLulus ?? '-' }}</td>
                <td class="bio-label-r"></td>
                <td class="bio-sep"></td>
                <td class="bio-val-r"></td>
            </tr>
        </table>

        {{-- TABEL MATA KULIAH --}}
        <table class="nilai">
            <thead>
                <tr>
                    <th style="width: 4%;">NO</th>
                    <th style="width: 29%;">MATA KULIAH</th>
                    <th style="width: 5%;">SKS</th>
                    <th style="width: 6%;">NILAI</th>
                    <th style="width: 6%;">M</th>
                    <th style="width: 4%;">NO</th>
                    <th style="width: 29%;">MATA KULIAH</th>
                    <th style="width: 5%;">SKS</th>
                    <th style="width: 6%;">NILAI</th>
                    <th style="width: 6%;">M</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // PERSIS SAMA DENGAN show.blade.php L319-332 (kaak reference SAMAIN KAYA GINI)
                    $semuaMK = $daftarMataKuliah;
                    $totalMK = count($semuaMK);
                    $barisBawah = 3 + $ujianCount;
                    $sisa = max(0, $totalMK - $barisBawah);
                    $mkAtas = array_slice($semuaMK, 0, $sisa);
                    $mkBawahKiri = array_slice($semuaMK, $sisa);
                    while (count($mkBawahKiri) < $barisBawah) { $mkBawahKiri[] = null; }
                    $halfAtas = (int) ceil(count($mkAtas) / 2);
                    $kiriAtas = array_slice($mkAtas, 0, $halfAtas);
                    $kananAtas = array_slice($mkAtas, $halfAtas);
                    $maxAtas = max(count($kiriAtas), count($kananAtas));
                    $noAwalKanan = count($kiriAtas);

                    $fnNama = static fn($m): string => $m === null ? '' : (($m->nama_mata_kuliah ?? $m['nama_mata_kuliah'] ?? ''));
                    $fnSks = static fn($m): string => $m === null ? '' : (string)((($m->sks ?? $m['sks'] ?? 0) == 0) ? '0' : ($m->sks ?? $m['sks'] ?? 0));
                    $fnNh = static fn($m): string => $m === null ? '' : (string)(($m->nilai_huruf ?? $m['nilai_huruf'] ?? ''));
                    $fnMutu = static function ($m): string {
                        if ($m === null) return '';
                        $nm = $m->nilai_m ?? $m['nilai_m'] ?? 0;
                        $nh = $m->nilai_huruf ?? $m['nilai_huruf'] ?? '';
                        if ($nm > 0) {
                            $f = number_format((float)$nm, 2, '.', '');
                            $f = rtrim($f, '0');
                            $f = rtrim($f, '.');
                            return $f === '' ? '0' : $f;
                        }
                        if ($nh !== '') return '0';
                        return '';
                    };
                    $fnFormatMutuStr = static function ($v): string {
                        $f = number_format((float)$v, 2, '.', '');
                        $f = rtrim($f, '0');
                        $f = rtrim($f, '.');
                        return $f === '' ? '0' : $f;
                    };
                @endphp
                @for($i = 0; $i < $maxAtas; $i++)
                    @php
                        $L = $kiriAtas[$i] ?? null;
                        $R = $kananAtas[$i] ?? null;
                        $noL = $L ? ($i + 1) : '';
                        $noR = $R ? ($noAwalKanan + $i + 1) : '';
                        $namaL = $fnNama($L);
                        $sksL = $fnSks($L);
                        $nhL = $fnNh($L);
                        $mutuL = $fnMutu($L);
                        $namaR = $fnNama($R);
                        $sksR = $fnSks($R);
                        $nhR = $fnNh($R);
                        $mutuR = $fnMutu($R);
                    @endphp
                    <tr>
                        <td>{{ $noL }}</td>
                        <td class="mk">{{ $namaL }}</td>
                        <td>{{ $sksL }}</td>
                        <td class="nilaih">{{ $nhL }}</td>
                        <td>{{ $mutuL }}</td>
                        <td>{{ $noR }}</td>
                        <td class="mk">{{ $namaR }}</td>
                        <td>{{ $sksR }}</td>
                        <td class="nilaih">{{ $nhR }}</td>
                        <td>{{ $mutuR }}</td>
                    </tr>
                @endfor

                    @for($bi = 0; $bi < $barisBawah; $bi++)
                        @php
                            // PERSIS show.blade L364-L382
                            $LL = $mkBawahKiri[$bi] ?? null;
                            $namaLL = $fnNama($LL);
                            $sksLL = $fnSks($LL);
                            $nhLL = $fnNh($LL);
                            $mutuLL = $fnMutu($LL);
                            // Formula NO LANJUT PERSIS show.blade L369
                            $noLanjutTampil = $LL ? ($noAwalKanan + count($kananAtas) + $bi + 1) : '';
                            if ($bi === 0) {
                                $jenisBaris = 'jumlah';
                            } elseif ($bi === 1) {
                                $jenisBaris = 'spacer';
                            } elseif ($bi === 2) {
                                $jenisBaris = 'ujian-head';
                            } else {
                                $jenisBaris = 'ujian-row';
                                $uIdx = $bi - 3;
                                $uNama = $ujianAda[$uIdx] ?? '';
                                $uNo = $uIdx + 1;
                            }
                        @endphp
                        @if($jenisBaris === 'jumlah')
                    <tr class="jumlah">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num jumlah-dashed"></td>
                        <td class="mk">Jumlah</td>
                        <td class="sks">{{ $totalSks }}</td>
                        <td class="nilaih"></td>
                        <td class="m">{{ $fnFormatMutuStr($totalMutu) }}</td>
                    </tr>
                        @elseif($jenisBaris === 'spacer')
                    <tr class="spacer-row">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num"></td>
                        <td class="mk"></td>
                        <td class="sks"></td>
                        <td class="nilaih"></td>
                        <td class="m"></td>
                    </tr>
                        @elseif($jenisBaris === 'ujian-head')
                    <tr class="ujian-head">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num ujian-right-spacer"></td>
                        <td class="mk ujian-left-title" colspan="4">Ujian Kompetensi</td>
                    </tr>
                        @else
                    <tr class="ujian-row">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num">{{ $uNo }}</td>
                        <td class="mk">{{ $uNama }}</td>
                        <td class="sks">0</td>
                        <td class="nilaih">A</td>
                        <td class="m">0</td>
                    </tr>
                        @endif
                    @endfor
            </tbody>
        </table>

        {{-- RINGKASAN IPK --}}
        <table class="ringkasan">
            <tr>
                <td style="width: 230px;">INDEKS PRESTASI KUMULATIF (IPK)</td>
                <td style="width: 15px;">:</td>
                <td>{{ number_format($ipk ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>PREDIKAT KELULUSAN</td>
                <td>:</td>
                <td>{{ $predikat ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold !important;">JUDUL SKRIPSI</td>
                <td style="font-weight: bold !important;">:</td>
                <td style="font-weight: normal !important;">{{ $judulSkripsi ?? '-' }}</td>
            </tr>
        </table>

        {{-- FOOTER: FOTO & TANDA TANGAN (LURUS SESUAI SCREENSHOT) --}}
        <div class="footer-wrapper">
            <table class="footer-table">
                <tr>
                    {{-- SISI KIRI: FOTO (SEJAJAR ATAS DENGAN TANGGAL TTD) --}}
                    <td class="foto-col">
                        <div class="foto-box">
                            @if(!empty($fotoSrc))
                                <img src="{{ $fotoSrc }}" alt="Foto">
                            @else
                                <br><br>Foto<br>3 × 4
                            @endif
                        </div>
                    </td>

                    {{-- SISI KANAN: TANDA TANGAN (text-align:left, persis seperti screenshot) --}}
                    <td class="ttd-col">
                        <div class="ttd-box">
                            <div>{{ $tanggalTtd ?? 'Sidenreng Rappang, .............. 2026' }}</div>
                            <div style="margin-top: 3px;" class="ttd-jabatan-text">{{ $ttdJabatan ?? 'Rektor / Dekan,' }}</div>

                            <div class="ttd-nama">{{ $ttdNama ?? ' Nama Pejabat & Gelar ' }}</div>
                            <div style="margin-top: 2px;">{{ $ttdNomorLabel ?? 'NIP/NIDN' }}. {{ $ttdNomor ?? '........................' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>