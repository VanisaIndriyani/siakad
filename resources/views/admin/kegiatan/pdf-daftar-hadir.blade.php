<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>DAFTAR HADIR - {{ strtoupper($kegiatan->judul) }}</title>
<style>
@page {
    size: A4 portrait;
    margin: 0 !important;
}
*, *:before, *:after { box-sizing: border-box; }
table, table th, table td { box-sizing: border-box; }
html, body {
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
    color: #000 !important;
    font-family: 'Times New Roman', Times, serif;
    width: 210mm;
    height: 297mm;
    overflow: hidden;
    position: relative;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.content {
    position: absolute;
    z-index: 10;
    top: 6mm;
    left: 7mm;
    right: 7mm;
    bottom: 7mm;
    padding: 0 0.5mm;
    overflow: hidden;
}

/* BARIS KOP ATAS: KIRI = LOGO, KANAN = TEKS KOP (LURUS VERTIKAL - DI ATAS GARIS) */
.kop-wrap {
    width: 100%;
    color: #000;
    margin-bottom: 1.2mm;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 4mm;
}
.kop-logo-side {
    flex: 0 0 auto;
    width: 22mm;
    height: 22mm;
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: center;
    margin-top: 0;
    padding-top: 0;
}
.kop-logo-side img {
    width: 20mm;
    height: 20mm;
    object-fit: contain;
    display: block;
    margin: 0 auto;
    border: 0;
    padding: 0;
}
.kop-text {
    flex: 1 1 auto;
    display: block;
    width: calc(100% - 22mm - 4mm);
    margin: 0 auto;
    text-align: center;
}
.kop-title-a {
    display: block;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    font-size: 15.5px;
    font-weight: 800;
    letter-spacing: 0.5px;
    line-height: 1.15;
    margin-top: 0;
    margin-bottom: 0;
    padding: 0;
    color: #000;
}
.kop-title-a2 { margin-top: 0.25mm; }
.kop-title-b {
    display: block;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 0.5px;
    line-height: 1.15;
    margin-top: 0.25mm;
    margin-bottom: 0;
    padding: 0;
    color: #000;
}
.kop-terakreditasi {
    display: block;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    font-size: 8.5px;
    margin-top: 0.8mm;
    color: #000;
    letter-spacing: 0.1px;
}
.kop-alamat-line {
    display: block;
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    font-size: 8px;
    margin-top: 0.3mm;
    line-height: 1.2;
    color: #000;
}
.kop-email-web { margin-top: 0.3mm; }

.garis-tebal { border-top: 2.6pt solid #000; margin: 1.2mm 0 0.3mm 0; width: 100%; }
.garis-tipis { border-top: 0.8pt solid #000; margin: 0 0 2mm 0; width: 100%; }

/* AREA DI BAWAH GARIS KOP: FULL WIDTH JUDUL + INFO KEGIATAN (TANPA LOGO) */
.info-with-logo {
    display: block;
    width: 100%;
    margin-bottom: 0.5mm;
}
.info-right {
    display: block;
    width: 100%;
    margin: 0 auto;
}

.judul-box {
    display: block;
    width: 100%;
    text-align: center;
    margin-bottom: 2mm;
}
.judul-text {
    display: block;
    width: 100%;
    text-align: center;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 1.1px;
    text-transform: uppercase;
    color: #000;
}
.judul-nomor {
    display: block;
    width: 100%;
    text-align: center;
    font-size: 9px;
    margin: 0.6mm auto 0;
    color: #000;
    font-style: italic;
}

.info-kegiatan {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 1.5mm;
    font-size: 10px;
    color: #000;
}
.info-kegiatan td { vertical-align: top; padding: 0.35mm 0; }
.info-kegiatan td.label {
    width: 26mm;
    font-weight: 400;
    position: relative;
    white-space: nowrap;
}
.info-kegiatan td.label::after {
    content: ":";
    position: absolute;
    left: 24.5mm;
    top: 0.35mm;
    display: inline-block;
    color: #000;
}
.info-kegiatan td.value { padding-left: 5mm; font-weight: 700; }
.info-kegiatan td.value-soft { padding-left: 5mm; font-weight: 400; }
.info-row { display: flex; }
.info-col { width: 50%; }

table.daftar {
    border-collapse: collapse;
    width: 100%;
    font-size: 8.5px;
    color: #000;
    table-layout: fixed;
    margin-top: 0.5mm;
}
table.daftar thead th {
    border: 1px solid #000;
    background: #e0f2ea;
    font-weight: 800;
    letter-spacing: 0.1px;
    padding: 0.8mm 0.4mm;
    vertical-align: middle;
    line-height: 1.15;
    text-align: center;
    font-size: 8.5px;
}
table.daftar tbody td {
    border: 1px solid #000;
    padding: 0.6mm 0.5mm;
    line-height: 1.15;
    vertical-align: middle;
    height: 6mm;
}
table.daftar .col-no {
    width: 6.5mm;
    max-width: 6.5mm;
    text-align: center;
    padding: 0.6mm 0;
}
table.daftar .col-nama { width: 54mm; }
table.daftar .col-npm { width: 25mm; text-align: center; }
table.daftar .col-prodi { width: 36mm; }
table.daftar .col-keterangan { width: 35mm; }
table.daftar .col-ttd { width: 35mm; height: 6mm; }
table.daftar tbody td.col-no { text-align: center; }
table.daftar tbody td.col-npm { text-align: center; }

.ttd-wrap-table { width: 100%; border-collapse: collapse; margin-top: 1.5mm; }
.ttd-wrap-table td { vertical-align: top; width: 50%; padding: 0 2mm; font-size: 10px; color: #000; }
.ttd-label { margin-bottom: 8mm; font-weight: 400; line-height: 1.3; }
.ttd-label b { display: block; margin-bottom: 0.3mm; font-size: 10px; }
.ttd-garis { border-top: 1px solid #000; margin: 0; padding-top: 0.4mm; }
.ttd-nama { font-weight: 800; text-decoration: underline; text-underline-offset: 1px; font-size: 10px; }
.ttd-nip { font-size: 9px; color: #000; }

.footer {
    clear: both;
    margin-top: 0.8mm;
    padding-top: 0.5mm;
    border-top: 0.4px dashed #888;
    font-size: 7.5px;
    color: #555;
    text-align: right;
    font-style: italic;
}
</style>
</head>
<body>
@php
$logoFinalSrc = null;
$logoCandidates = [];

try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'lo.jpeg'; } catch (\Throwable $e) {}
try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'lo.jpg'; } catch (\Throwable $e) {}
try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'lo.png'; } catch (\Throwable $e) {}
try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.png'; } catch (\Throwable $e) {}
try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'logo.jpeg'; } catch (\Throwable $e) {}

try {
    $bp = rtrim(base_path(), '\\/');
    if ($bp !== '') {
        $logoCandidates[] = $bp . '/public/img/lo.jpeg';
        $logoCandidates[] = $bp . '/public_html/img/lo.jpeg';
        $logoCandidates[] = $bp . '/../public_html/img/lo.jpeg';
        $logoCandidates[] = $bp . '/../public/img/lo.jpeg';
        $logoCandidates[] = $bp . '/public/img/lo.jpg';
        $logoCandidates[] = $bp . '/public_html/img/lo.jpg';
        $logoCandidates[] = $bp . '/public/img/logo.png';
        $logoCandidates[] = $bp . '/public_html/img/logo.png';
    }
} catch (\Throwable $e) {}

try {
    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '\\/');
    if ($docRoot !== '') {
        $logoCandidates[] = $docRoot . '/img/lo.jpeg';
        $logoCandidates[] = $docRoot . '/img/lo.jpg';
        $logoCandidates[] = $docRoot . '/img/lo.png';
        $logoCandidates[] = $docRoot . '/img/logo.png';
        $logoCandidates[] = $docRoot . '/public/img/lo.jpeg';
        $logoCandidates[] = $docRoot . '/../public/img/lo.jpeg';
    }
} catch (\Throwable $e) {}

$logoCandidates = array_values(array_unique(array_filter($logoCandidates, static fn($p) => !empty($p) && is_string($p))));

$logoNames = ['lo.jpeg', 'lo.jpg', 'lo.png', 'logo.jpeg', 'logo.jpg', 'logo.png'];
foreach ($logoNames as $lname) {
    try {
        if (function_exists('resource_path')) { $logoCandidates[] = resource_path($lname); }
    } catch (\Throwable $e) {}
}

foreach ($logoCandidates as $logoPath) {
    try {
        if (is_string($logoPath) && file_exists($logoPath) && is_file($logoPath) && is_readable($logoPath)) {
            $data = @file_get_contents($logoPath);
            if ($data && strlen($data) > 100 && strlen($data) < 400000) {
                $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
                $logoFinalSrc = 'data:' . $mime . ';base64,' . base64_encode($data);
                break;
            }
        }
    } catch (\Throwable $e) { $logoFinalSrc = null; }
}
unset($logoPath, $data, $ext, $mime, $logoCandidates, $logoNames, $bp, $docRoot);

$totalPeserta = $peserta->count();
$totalHadir   = $peserta->where('status_hadir', true)->count();
$totalBelum   = $totalPeserta - $totalHadir;

$MAKS_BARIS = 24;
$totalBlank = 0;
if ($totalPeserta < $MAKS_BARIS) {
    $totalBlank = $MAKS_BARIS - $totalPeserta;
}
$pct = $totalPeserta > 0 ? round(($totalHadir / $totalPeserta) * 100, 1) : 0;

$namaPanitia = trim($kegiatan->ketua_panitia_nama ?? '');
if ($namaPanitia === '') $namaPanitia = trim($kegiatan->penyelenggara ?? 'Panitia Kegiatan');
$nipPanitia  = trim($kegiatan->ketua_panitia_nip ?? '');
if ($nipPanitia === '') $nipPanitia = '-';
$keteranganPanitia = trim($kegiatan->penyelenggara ?? 'Panitia Penyelenggara');

$namaRektor = trim($kegiatan->rektor_nama ?? '');
if ($namaRektor === '') $namaRektor = 'Dr. H. Muh. Anshar, M.Ag.';
$nipRektor  = trim($kegiatan->rektor_nip ?? '');
if ($nipRektor === '') $nipRektor = '-';

function hariIndo($inggris): string {
    $map = [
        'Monday' => 'Senin', 'Mon' => 'Senin',
        'Tuesday' => 'Selasa', 'Tue' => 'Selasa',
        'Wednesday' => 'Rabu', 'Wed' => 'Rabu',
        'Thursday' => 'Kamis', 'Thu' => 'Kamis',
        'Friday' => 'Jumat', 'Fri' => 'Jumat',
        'Saturday' => 'Sabtu', 'Sat' => 'Sabtu',
        'Sunday' => 'Minggu', 'Sun' => 'Minggu',
    ];
    return $map[$inggris] ?? $inggris;
}
function bulanIndo($inggris): string {
    $map = [
        'January' => 'Januari', 'Jan' => 'Januari',
        'February' => 'Februari', 'Feb' => 'Februari',
        'March' => 'Maret', 'Mar' => 'Maret',
        'April' => 'April', 'Apr' => 'April',
        'May' => 'Mei',
        'June' => 'Juni', 'Jun' => 'Juni',
        'July' => 'Juli', 'Jul' => 'Juli',
        'August' => 'Agustus', 'Aug' => 'Agustus',
        'September' => 'September', 'Sep' => 'Sept',
        'October' => 'Oktober', 'Oct' => 'Okt',
        'November' => 'November', 'Nov' => 'Nov',
        'December' => 'Desember', 'Dec' => 'Des',
    ];
    return $map[$inggris] ?? $inggris;
}
function formatTglIndo($tanggal): string {
    try {
        if (empty($tanggal)) return '-';
        $c = \Illuminate\Support\Carbon::parse($tanggal);
        $hariInggris = $c->format('l');
        $hari = hariIndo($hariInggris);
        $tgl = $c->format('j');
        $bulanInggris = $c->format('F');
        $bulan = bulanIndo($bulanInggris);
        $tahun = $c->format('Y');
        return "{$hari}, {$tgl} {$bulan} {$tahun}";
    } catch (\Throwable $e) {
        return is_string($tanggal) ? $tanggal : '-';
    }
}
function formatTglIndoSingkat($tanggal): string {
    try {
        if (empty($tanggal)) return '-';
        $c = \Illuminate\Support\Carbon::parse($tanggal);
        $tgl = $c->format('j');
        $bulanInggris = $c->format('F');
        $bulan = bulanIndo($bulanInggris);
        $tahun = $c->format('Y');
        return "{$tgl} {$bulan} {$tahun}";
    } catch (\Throwable $e) {
        return is_string($tanggal) ? $tanggal : '-';
    }
}
@endphp

<div class="content">
    {{-- BARIS PERTAMA (ATAS GARIS): KIRI = LOGO, KANAN = TEKS KOP 6 BARIS LURUS VERTIKAL --}}
    <div class="kop-wrap">
        <div class="kop-logo-side">
            @if($logoFinalSrc)
                <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI Sidrap" width="80" height="80">
            @else
                <div style="width:20mm;height:20mm;margin:0 auto;border-radius:50%;background:#0f6244;color:#fff;font-size:9px;font-weight:800;line-height:20mm;text-align:center;">IAI DDI</div>
            @endif
        </div>
        <div class="kop-text">
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
        </div>
    </div>
    <div class="garis-tebal"></div>
    <div class="garis-tipis"></div>

    {{-- BARIS KEDUA (DI BAWAH GARIS KOP): FULL WIDTH JUDUL + INFO KEGIATAN (TANPA LOGO LAGI) --}}
    <div class="info-with-logo">
        <div class="info-right">
            <div class="judul-box">
                <div class="judul-text">DAFTAR HADIR PESERTA</div>
                <div class="judul-nomor">
                    {{ strtoupper($kegiatan->jenis_kegiatan) }} &mdash;
                    {{ strtoupper($kegiatan->judul) }}
                </div>
            </div>

            <div class="info-row">
                <div class="info-col">
                    <table class="info-kegiatan" style="margin-bottom: 0;">
                        <tr>
                            <td class="label">Tema Kegiatan</td>
                            <td class="value">{{ $kegiatan->judul }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kegiatan</td>
                            <td class="value-soft">{{ $kegiatan->jenis_kegiatan }}</td>
                        </tr>
                        <tr>
                            <td class="label">Hari / Tanggal</td>
                            <td class="value-soft">
                                @if(!empty($kegiatan->tanggal_kegiatan))
                                    {{ formatTglIndo($kegiatan->tanggal_kegiatan) }}
                                @else -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Waktu</td>
                            <td class="value-soft">
                                @if(!empty($kegiatan->waktu_mulai))
                                    {{ substr($kegiatan->waktu_mulai,0,5) }}
                                    @if(!empty($kegiatan->waktu_selesai))
                                        s/d {{ substr($kegiatan->waktu_selesai,0,5) }} WITA
                                    @else WITA
                                    @endif
                                @else -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="info-col">
                    <table class="info-kegiatan" style="margin-bottom: 0;">
                        <tr>
                            <td class="label">Tempat / Lokasi</td>
                            <td class="value-soft">{{ $kegiatan->lokasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Penyelenggara</td>
                            <td class="value-soft">{{ $kegiatan->penyelenggara ?? 'IAI DDI Sidrap' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DAFTAR HADIR --}}
    <table class="daftar">
        <colgroup>
            <col class="col-no">
            <col class="col-nama">
            <col class="col-npm">
            <col class="col-prodi">
            <col class="col-keterangan">
            <col class="col-ttd">
        </colgroup>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAMA LENGKAP</th>
                <th>NPM / NUPTK</th>
                <th>PROGRAM STUDI</th>
                <th>STATUS KEHADIRAN</th>
                <th>TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $i => $p)
                @if($i >= $MAKS_BARIS) @break @endif
                @php
                    $no = $i + 1;
                    $prodiTampil = trim((string)($p->program_studi ?? ''));
                    if ($prodiTampil === '') $prodiTampil = $p->jenis_peserta === 'dosen' ? 'Dosen IAI DDI' : '-';
                    $ket = $p->status_hadir ? 'HADIR' : 'TIDAK HADIR';
                    $ketColor = $p->status_hadir ? '#065f46' : '#991b1b';
                    $bgCell = $p->status_hadir ? '#ecfdf5' : '#fef2f2';
                    $identitas = $p->jenis_peserta === 'dosen' ? ($p->nidn ?? '-') : ($p->npm ?? '-');
                @endphp
                <tr>
                    <td class="col-no">{{ $no }}</td>
                    <td class="col-nama" style="font-weight: 700;">{{ strtoupper($p->nama_lengkap) }}</td>
                    <td class="col-npm" style="font-family:'Courier New', monospace;">{{ $identitas }}</td>
                    <td class="col-prodi">{{ $prodiTampil }}</td>
                    <td class="col-keterangan" style="text-align:center; background: {{ $bgCell }}; color: {{ $ketColor }}; font-weight: 800;">
                        {{ $ket }}
                        @if($p->status_hadir && !empty($p->waktu_hadir))
                            <div style="font-weight: 400; color:#333; font-size: 6.6px; margin-top:0.25mm;">
                                {{ \Illuminate\Support\Carbon::parse($p->waktu_hadir)->format('H:i') }} WITA
                            </div>
                        @endif
                        @if(!empty($p->keterangan))
                            <div style="font-weight: 400; color:#555; font-size:6.6px; margin-top:0.25mm;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($p->keterangan), 38) }}
                            </div>
                        @endif
                    </td>
                    <td class="col-ttd"></td>
                </tr>
            @endforeach

            @for($b = 1; $b <= $totalBlank; $b++)
                <tr>
                    <td class="col-no">{{ $totalPeserta + $b }}</td>
                    <td class="col-nama">&nbsp;</td>
                    <td class="col-npm">&nbsp;</td>
                    <td class="col-prodi">&nbsp;</td>
                    <td class="col-keterangan">&nbsp;</td>
                    <td class="col-ttd"></td>
                </tr>
            @endfor
        </tbody>
    </table>

    {{-- TANDA TANGAN 2 KOLOM --}}
 

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Akademik (SIAKAD) IAI DDI Sidrap pada
        {{ formatTglIndo(now()) }} {{ \Illuminate\Support\Carbon::now()->format('H:i:s') }} WITA
    </div>
</div>

</body>
</html>
