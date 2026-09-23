<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" 
      xmlns:w="urn:schemas-microsoft-com:office:word" 
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Akademik</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page WordSection1 {
            size: 21.0cm 29.7cm;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
            mso-header-margin: .5in;
            mso-footer-margin: .5in;
            mso-paper-source: 0;
        }

        div.WordSection1 {
            page: WordSection1;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            color: #000000;
            margin: 0;
            padding: 0;
            font-size: 9pt;
            line-height: 1.15;
        }
        p.MsoNormal, li.MsoNormal, div.MsoNormal {
            mso-style-parent: "";
            margin: 0;
            margin-bottom: .0001pt;
            mso-pagination: widow-orphan;
            font-size: 12.0pt;
            mso-fareast-font-family: "Times New Roman";
        }

        /* KOP SURAT */
        .kop-wrap { width: 100%; text-align: center; margin-bottom: 5px; }
        .kop-title-a { font-size: 14pt; font-weight: bold; margin: 0; text-transform: uppercase; }
        .kop-title-b { font-size: 13pt; font-weight: bold; margin-top: 1px; text-transform: uppercase; }
        .kop-terakreditasi { font-size: 8.5pt; margin-top: 2px; font-weight: bold; }
        .kop-alamat-line { font-size: 8pt; margin-top: 1px; }

        /* GARIS DOUBLE KOP PERSIS PDF */
        .kop-line-double { margin-top: 6px; width: 100%; margin-bottom: 20px; }
        .kop-line-top { width: 100%; border-bottom: 2pt solid #000000; line-height: 2pt; font-size: 1pt; }
        .kop-line-bottom { width: 100%; border-bottom: 2pt solid #000000; margin-top: 2pt; line-height: 2pt; font-size: 1pt; }

        /* JUDUL PERSIS PDF */
        .judul-box { text-align: center; margin: 0; margin-bottom: 20px; }
        .judul-text { font-size: 14pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .judul-nomor { font-size: 9pt; margin-top: 14px; }

        /* BIODATA (Colon INLINE krn Word TIDAK support pseudo-element) */
        table.biodata { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 8px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        table.biodata td { vertical-align: top; padding: 1px 0; }
        .bio-label { width: 22%; }
        .bio-sep { width: 2%; text-align: center; }
        .bio-val { width: 26%; font-weight: bold; }
        .bio-label-r { width: 20%; padding-left: 5px; }
        .bio-val-r { width: 30%; font-weight: bold; }

        /* TABEL NILAI 10 KOLOM PERSIS PDF */
        table.nilai {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        table.nilai th { border: 1.0pt solid #000; background-color: #e0f2ea; font-weight: bold; padding: 3px 1px; text-align: center; }
        table.nilai td { border: 1.0pt solid #000; padding: 2px; vertical-align: middle; text-align: center; box-sizing: border-box; }
        table.nilai td.mk { text-align: left; padding-left: 4px; }
        table.nilai td.nilaih { font-weight: bold; }

        table.nilai tr.jumlah td { background: #ffffff !important; font-weight: 700; padding: 2.5px 3px; letter-spacing: 0.2px; line-height: 1.15; border: 1pt solid #000000 !important; }
        table.nilai tr.jumlah td.mk { text-align: center; }
        table.nilai tr.jumlah td.jumlah-dashed {
            background: #ffffff !important;
            border: 1pt solid #000000 !important;
            border-top: 1pt dashed #000000 !important;
            border-left: 1pt solid #000000 !important;
            border-right: none !important;
            border-bottom: 1pt solid #000000 !important;
        }
        table.nilai tr.spacer-row td { background: #ffffff !important; border: 1pt solid #000000 !important; height: 10px; padding: 0; }
        table.nilai tr.ujian-head td { background: #ffffff !important; font-weight: 700; padding: 3px 3px; letter-spacing: 0.2px; line-height: 1.2; border: 1pt solid #000000 !important; }
        table.nilai td.ujian-left-title { text-align: left; padding-left: 7px !important; border: 1pt solid #000 !important; border-top: 1pt solid #000 !important; }
        table.nilai tr.ujian-head td.ujian-right-spacer {
            background: #ffffff !important;
            border: 1pt solid #000000 !important;
            border-top: 1pt solid #000000 !important;
            border-left: 1pt solid #000000 !important;
            border-right: none !important;
            border-bottom: none !important;
        }
        table.nilai tr.jumlah td.left-col,
        table.nilai tr.spacer-row td.left-col,
        table.nilai tr.ujian-head td.left-col,
        table.nilai tr.ujian-row td.left-col {
            background: #ffffff !important;
            border: 1pt solid #000000 !important;
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
        table.nilai tr.ujian-row td:nth-child(6) { border-left: 1pt solid #000000 !important; }
        table.nilai tr.jumlah td:nth-child(10),
        table.nilai tr.ujian-head td:nth-child(9),
        table.nilai tr.ujian-row td:nth-child(10) { border-right: 1pt solid #000000 !important; }

        /* RINGKASAN IPK PERSIS PDF */
        table.ringkasan { width: 100%; border-collapse: collapse; font-size: 8.5pt; margin-top: 16px; font-weight: bold; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        table.ringkasan td { padding: 1px 0; vertical-align: top; }

        /* FOOTER: FOTO & TANDA TANGAN (LURUS KANAN - KHUSUS WORD: LEBAR EXPLICIT CM AGAR TIDAK BINGUNG RENDER) */
        .footer-wrapper { margin-top: 30px; page-break-inside: avoid; }
        table.footer-table {
            width: 18.0cm;   /* LEBAR EXPLICIT = A4 21cm - margin 1.5cm*2 = 18cm (TIDAK PAKAI PERSEN!) */
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border: none;
            margin-left: 0;
        }
        table.footer-table td { border: none; padding: 0; vertical-align: top; }
        td.footer-spacer { width: 10.5cm; border: none; }  /* KOLOM KIRI KOSONG (10.5 cm) → DORONG TTD KE KANAN */
        td.footer-ttd-area { width: 7.5cm; border: none; }   /* KOLOM KANAN TOTAL 7.5cm AREA TTD + FOTO */

        table.footer-inner {
            width: 7.5cm;
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border: none;
        }
        table.footer-inner td { border: none; padding: 0; vertical-align: top; }
        td.footer-foto-col { width: 2.8cm; padding-right: 0.4cm; }  /* FOTO 2.8cm + jarak 4mm ke TTD */
        td.footer-ttd-col { width: 4.3cm; }  /* TTD TEKS 4.3cm (LEBIH LEBAR dr sblmnya ~3cm!) */

        .foto-box {
            width: 2.8cm;
            height: 3.8cm;
            border: 1.0pt solid #000000;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            line-height: 1.2;
            margin: 0;
            text-decoration: none !important;
        }

        .ttd-box {
            text-align: left;
            font-size: 8.5pt;   /* TURUNKAN SEDIKIT: AGAR JABATAN PANJANG TIDAK WRAP 4x */
            line-height: 1.2;
        }
        .ttd-tanggal { line-height: 1.25; }
        .ttd-jabatan { font-weight: bold; margin-top: 2pt; line-height: 1.25; }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 68px;   /* KECILKAN DARI 80px → 68px (CUKUP BUAT TANDA TANGAN BASAH TAPI TIDAK PINDAH HALAMAN) */
            line-height: 1.25;
        }
    </style>
</head>
<body>
    <div class="WordSection1">
        @php
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
            $logoSrc = $data['logoSrc'] ?? null;
            $fotoSrc = $data['fotoSrc'] ?? null;
            $ujianKompre = $data['ujianKompre'] ?? [];
            if (!is_array($ujianKompre)) $ujianKompre = [];

            /* =========================================================
               SPLIT TABLE PERSIS show.blade L319-332 (10 KOLOM SIMETRIS)
               ========================================================= */
            $semuaMK = $daftarMataKuliah;
            $totalMK = count($semuaMK);
            $ujianCount = count($ujianKompre);
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
                return ($nh !== '' && $nh !== null) ? '0' : '';
            };
            $fnFormatMutuStr = static function ($v): string {
                if ($v === null || $v === '') return '';
                $f = number_format((float)$v, 2, '.', '');
                $f = rtrim($f, '0');
                $f = rtrim($f, '.');
                return $f === '' ? '0' : $f;
            };
        @endphp

        {{-- KOP SURAT --}}
        <div class="kop-wrap">
            @if(!empty($logoSrc))
                <div style="margin-bottom: 4px; text-align: center;">
                    <img src="{{ $logoSrc }}" alt="Logo" style="width: 68px; height: 68px; display: inline-block;">
                </div>
            @endif
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>

            {{-- DOUBLE LINE PERSIS PDF --}}
            <div class="kop-line-double">
                <div class="kop-line-top">&nbsp;</div>
                <div class="kop-line-bottom">&nbsp;</div>
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="judul-box">
            <div class="judul-text">TRANSKRIP AKADEMIK</div>
            <div class="judul-nomor">Nomor : {{ $nomorTranskrip ?? '-' }}</div>
        </div>

        {{-- BIODATA MAHASISWA (Colon INLINE krn Word TIDAK render ::after) --}}
        <table class="biodata">
            <tr>
                <td class="bio-label">Nama:</td>
                <td class="bio-sep"></td>
                <td class="bio-val">{{ $mahasiswa->nama_lengkap ?? ($mahasiswa->nama ?? '-') }}</td>
                <td class="bio-label-r">Program Pendidikan:</td>
                <td class="bio-sep"></td>
                <td class="bio-val-r">Strata Satu (S1)</td>
            </tr>
            <tr>
                <td class="bio-label">No. Pokok Mahasiswa:</td>
                <td class="bio-sep"></td>
                <td class="bio-val">{{ $mahasiswa->npm ?? '-' }}</td>
                <td class="bio-label-r">Fakultas:</td>
                <td class="bio-sep"></td>
                <td class="bio-val-r">{{ $mahasiswa->fakultas ?? 'Fakultas Ekonomi dan Bisnis Islam' }}</td>
            </tr>
            <tr>
                <td class="bio-label">No. Ijazah:</td>
                <td class="bio-sep"></td>
                <td class="bio-val">{{ $noIjazah ?? '-' }}</td>
                <td class="bio-label-r">Program Studi:</td>
                <td class="bio-sep"></td>
                <td class="bio-val-r">{{ $mahasiswa->program_studi ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bio-label">Tempat / Tanggal Lahir:</td>
                <td class="bio-sep"></td>
                <td class="bio-val">{{ $tempatTgl ?? '-' }}</td>
                <td class="bio-label-r">No. SK BAN-PT:</td>
                <td class="bio-sep"></td>
                <td class="bio-val-r">{{ $skBanpt ?? '-' }}</td>
            </tr>
            <tr>
                <td class="bio-label">Tanggal, Bulan dan Tahun Lulus:</td>
                <td class="bio-sep"></td>
                <td class="bio-val">{{ $tanggalLulus ?? '-' }}</td>
                <td class="bio-label-r"></td>
                <td class="bio-sep"></td>
                <td class="bio-val-r"></td>
            </tr>
        </table>

        {{-- TABEL NILAI 10 KOLOM PERSIS PDF + show.blade L303-435 --}}
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
                        <td class="num">{{ $noL }}</td>
                        <td class="mk">{{ $namaL }}</td>
                        <td class="sks">{{ $sksL }}</td>
                        <td class="nilaih">{{ $nhL }}</td>
                        <td class="m">{{ $mutuL }}</td>
                        <td class="num">{{ $noR }}</td>
                        <td class="mk">{{ $namaR }}</td>
                        <td class="sks">{{ $sksR }}</td>
                        <td class="nilaih">{{ $nhR }}</td>
                        <td class="m">{{ $mutuR }}</td>
                    </tr>
                @endfor

                @for($bi = 0; $bi < $barisBawah; $bi++)
                    @php
                        $LL = $mkBawahKiri[$bi] ?? null;
                        $namaLL = $fnNama($LL);
                        $sksLL = $fnSks($LL);
                        $nhLL = $fnNh($LL);
                        $mutuLL = $fnMutu($LL);
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
                            $uNama = $ujianKompre[$uIdx] ?? '';
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
                <td style="font-weight: normal !important; text-decoration: none !important;">{{ $judulSkripsi ?? '-' }}</td>
            </tr>
        </table>

        {{-- PEMISAH JELAS: AGAR TABLE RINGKASAN DAN TABLE FOOTER TIDAK OVERLAP / TABRAK (WORD RENDER LEBIH AMAN) --}}
        <div style="clear: both; width: 100%; height: 0pt; line-height: 0pt; font-size: 0pt; border: none;">&nbsp;</div>

        {{-- FOOTER: FOTO & TANDA TANGAN (LURUS KANAN - KHUSUS WORD STRUKTUR 2 LEVEL TABLE AGAR TIDAK LONCAT) --}}
        <div class="footer-wrapper">
            <table class="footer-table">
                <tr>
                    {{-- KOLOM KIRI (55%): KOSONG TOTAL - agar keseluruhan TTD DORONG KE KANAN --}}
                    <td class="footer-spacer"></td>

                    {{-- KOLOM KANAN (45%): SUB-TABLE FOTO + TTD LURUS VERTIKAL --}}
                    <td class="footer-ttd-area">
                        <table class="footer-inner">
                            <tr>
                                {{-- FOTO BOX (LURUS TOP DENGAN TANGGAL TTD) --}}
                                <td class="footer-foto-col">
                                    <div class="foto-box">
                                        @if(!empty($fotoSrc))
                                            <img src="{{ $fotoSrc }}" alt="Foto" style="width: 100%; height: 100%;">
                                        @else
                                            <br><br>Foto<br>3 × 4
                                        @endif
                                    </div>
                                </td>

                                {{-- TANGGAL + JABATAN BOLD + NAMA DEKAN (UNDERLINE 68px BAWAH) + NIDN --}}
                                <td class="footer-ttd-col">
                                    <div class="ttd-box">
                                        <div class="ttd-tanggal">{{ $tanggalTtd ?? 'Sidenreng Rappang, .............. 2026' }}</div>
                                        <div class="ttd-jabatan">{{ $ttdJabatan ?? 'DEKAN FAKULTAS' }}</div>
                                        <div class="ttd-nama">{{ $ttdNama ?? ' Nama Pejabat & Gelar ' }}</div>
                                        <div style="margin-top: 2pt;">{{ $ttdNomorLabel ?? 'NIDN' }}. {{ $ttdNomor ?? '........................' }}</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ================================================================
       XML sectPr AKHIR BODY - OVERRIDE DEFAULT MARGIN MS WORD (A4 12/15mm)
       ================================================================ --}}
    <!--[if gte mso 9]>
    <xml>
        <w:sectPr>
            <w:headerReference w:type="default" r:id="-1"/>
            <w:footerReference w:type="default" r:id="-1"/>
            <w:pgSz w:w="11906" w:h="16838"/>
            <w:pgMar w:top="680" w:right="850" w:bottom="680" w:left="850" w:header="360" w:footer="360" w:gutter="0"/>
            <w:cols w:space="425"/>
            <w:docGrid w:line-pitch="256"/>
        </w:sectPr>
    </xml>
    <![endif]-->
</body>
</html>
