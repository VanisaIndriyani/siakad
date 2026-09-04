<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>DAFTAR HADIR - {{ strtoupper($kegiatan->judul ?? '-') }}</title>
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
    min-height: 297mm;
    position: relative;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.page {
    position: relative;
    width: 210mm;
    min-height: 297mm;
    page-break-after: always;
    page-break-inside: avoid;
}
.page-last { page-break-after: auto !important; }
.content {
    position: absolute;
    z-index: 10;
    top: 6mm;
    left: 7mm;
    right: 7mm;
    bottom: 7mm;
    padding: 0 0.5mm;
}

/* BARIS KOP ATAS */
.kop-wrap {
    position: relative;
    width: 100%;
    height: 27mm;
    color: #000;
    margin-bottom: 1.2mm;
}
.kop-logo-side {
    position: absolute;
    left: 0;
    top: 1mm;
    width: 23mm;
    height: 23mm;
    text-align: left;
}
.kop-logo-side img {
    width: 20mm;
    height: 20mm;
    object-fit: contain;
    display: block;
    margin: 0;
    border: 0;
    padding: 0;
}
.kop-text {
    position: absolute;
    left: 23mm;
    right: 0;
    top: 0;
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
.judul-lanjutan-info {
    margin-top: 0.4mm;
    font-size: 10px;
    font-weight: 700;
    line-height: 1.1;
    text-align: center;
}
.judul-lanjutan-hal {
    margin-top: 0.2mm;
    font-size: 8.5px;
    color: #333;
    font-style: italic;
    line-height: 1.1;
    text-align: center;
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

/* HEADER */
table.daftar thead th {
    border: 1px solid #000;
    background: #d6f2e3;
    font-weight: 800;
    letter-spacing: 0.1px;
    padding: 0.8mm 0.2mm;
    vertical-align: middle;
    line-height: 1.15;
    text-align: center;
    font-size: 8.5px;
}

/* ISI */
table.daftar tbody td {
    border: 1px solid #000;
    padding: 0.6mm 0.5mm;
    line-height: 1.15;
    vertical-align: middle;
    height: 6mm;
}

table.daftar tbody td.col-npm {
    text-align: center;
    font-family: 'Courier New', monospace;
}

/* TANDA TANGAN */

table.daftar td.col-ttd {
    position: relative;
    padding: 0.6mm 0.5mm !important;
    vertical-align: middle;
    height: 6mm;
}

.ttd-wrap-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 3mm;
    page-break-inside: avoid;
    break-inside: avoid;
}
.ttd-wrap-table tr { page-break-inside: avoid; break-inside: avoid; }
.ttd-wrap-table td {
    vertical-align: top;
    width: 50%;
    padding: 0 4mm;
    font-size: 10px;
    color: #000;
    text-align: center;
}
.ttd-label { font-weight: 700; margin-bottom: 0.2mm; text-align: center; }
.ttd-sub   { font-weight: 600; margin-bottom: 10mm; text-align: center; }
.ttd-garis { height: 0.5mm; width: 75%; margin: 0 auto 0.6mm auto; border-bottom: 1.2pt solid #000; }
.ttd-nama  { font-weight: 800; text-align: center; margin-bottom: 0.3mm; word-wrap: break-word; }
.ttd-nip   { font-style: italic; text-align: center; font-size: 9px; }

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

function hariIndo($d) {
    $map = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    return $map[$d] ?? '';
}
function bulanIndo($m) {
    $map = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return $map[(int)$m] ?? '';
}
function formatTglIndo($tanggal) {
    if (empty($tanggal)) return '-';
    try {
        $c = \Illuminate\Support\Carbon::parse($tanggal)->setTimezone('Asia/Makassar');
        return hariIndo($c->dayOfWeek).', '.$c->format('j').' '.bulanIndo($c->month).' '.$c->format('Y');
    } catch (\Throwable $e) { return '-'; }
}
function formatTglIndoCetak($tanggal) {
    if (empty($tanggal)) return '-';
    try {
        $c = \Illuminate\Support\Carbon::parse($tanggal)->setTimezone('Asia/Makassar');
        return $c->format('j').' '.bulanIndo($c->month).' '.$c->format('Y');
    } catch (\Throwable $e) { return '-'; }
}

$peserta = $peserta->sortBy([
    ['nama_lengkap', SORT_NATURAL | SORT_FLAG_CASE],
    ['id', SORT_NUMERIC],
])->values();

$totalPeserta = $peserta->count();
$totalHadir   = $peserta->where('status_hadir', true)->count();
$totalBelum   = $totalPeserta - $totalHadir;
$pct = $totalPeserta > 0 ? round(($totalHadir / $totalPeserta) * 100, 1) : 0;

$PAGE_SIZE = 18;
$chunks = $peserta->chunk($PAGE_SIZE);
$totalHalaman = $chunks->count();

$tempatVal = trim((string)($kegiatan->tempat ?? $kegiatan->lokasi ?? ''));
if ($tempatVal === '') $tempatVal = '-';
$penyelenggaraVal = trim((string)($kegiatan->penyelenggara ?? ''));
if ($penyelenggaraVal === '') $penyelenggaraVal = 'IAI DDI SIDRAP';
$ketuaPanitiaNama = trim((string)($kegiatan->ketua_panitia_nama ?? ''));
if ($ketuaPanitiaNama === '') $ketuaPanitiaNama = trim((string)($kegiatan->penyelenggara ?? 'Panitia Kegiatan'));
$ketuaPanitiaNip  = trim((string)($kegiatan->ketua_panitia_nip ?? ''));
if ($ketuaPanitiaNip === '') $ketuaPanitiaNip = '-';
$rektorNama       = trim((string)($kegiatan->rektor_nama ?? ''));
if ($rektorNama === '') $rektorNama = 'Dr. H. Muh. Anshar, M.Ag.';
$rektorNip        = trim((string)($kegiatan->rektor_nip ?? ''));
if ($rektorNip === '') $rektorNip = '-';
$narasumberVal    = trim((string)($kegiatan->narasumber ?? ''));
if ($narasumberVal === '') $narasumberVal = '-';
$jenisKegiatanVal = trim((string)($kegiatan->jenis_kegiatan ?? ''));
if ($jenisKegiatanVal === '') $jenisKegiatanVal = 'KEGIATAN';
$tanggalKegiatanVal = !empty($kegiatan->tanggal_mulai) ? $kegiatan->tanggal_mulai : (!empty($kegiatan->tanggal_kegiatan) ? $kegiatan->tanggal_kegiatan : (!empty($kegiatan->tanggal) ? $kegiatan->tanggal : ''));
$nowCetak = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Makassar');
@endphp

@foreach($chunks as $chunkIdx => $pagePeserta)
    @php
        $halaman = $chunkIdx + 1;
        $isFirst = $halaman === 1;
        $isLast  = $halaman === $totalHalaman;
        $chunkCount = $pagePeserta->count();
        $blankNeeded = 0;
    @endphp
    <div class="page {{ $isLast ? 'page-last' : '' }}">
        <div class="content">
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

            @if($isFirst)
                <div class="info-with-logo">
                    <div class="info-right">
                        <div class="judul-box">
                            <div class="judul-text">DAFTAR HADIR PESERTA</div>
                            <div class="judul-nomor">
                                {{ strtoupper($jenisKegiatanVal) }} &mdash;
                                {{ strtoupper($kegiatan->judul ?? '-') }}
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-col">
                                <table class="info-kegiatan" style="margin-bottom: 0;">
                                    <tr>
                                        <td class="label">Tema Kegiatan</td>
                                        <td class="value">{{ $kegiatan->judul ?? '-' }}</td>
                                    </tr>
                                  
                                    <tr>
                                        <td class="label">Hari / Tanggal</td>
                                        <td class="value-soft">{{ formatTglIndo($tanggalKegiatanVal) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Waktu</td>
                                        <td class="value-soft">
                                            @php
                                                $wm = $kegiatan->waktu_mulai ?? '';
                                                $ws = $kegiatan->waktu_selesai ?? '';
                                                if ($wm !== '') $wm = substr(trim((string)$wm), 0, 5);
                                                if ($ws !== '') $ws = substr(trim((string)$ws), 0, 5);
                                                if ($wm !== '' && $ws !== '') { echo $wm.' s/d '.$ws.' WITA'; }
                                                elseif ($wm !== '') { echo $wm.' WITA'; } else { echo '-'; }
                                            @endphp
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="info-col">
                                <table class="info-kegiatan" style="margin-bottom: 0;">
                                    <tr>
                                        <td class="label">Tempat / Lokasi</td>
                                        <td class="value-soft">{{ $tempatVal }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Penyelenggara</td>
                                        <td class="value-soft">{{ $penyelenggaraVal }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Narasumber</td>
                                        <td class="value-soft">{{ $narasumberVal }}</td>
                                    </tr>
                                   
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="judul-box">
                    <div class="judul-text">DAFTAR HADIR PESERTA (LANJUTAN)</div>
                    <div class="judul-lanjutan-info">{{ strtoupper($kegiatan->judul ?? '-') }}</div>
                    <div class="judul-lanjutan-hal">Halaman {{ $halaman }} / {{ $totalHalaman }} &bull; Total: {{ $totalPeserta }} orang</div>
                </div>
            @endif

            {{-- TABEL DAFTAR HADIR DENGAN PERSENTASE PERSISI UNTUK DOMPDF/MPDF --}}
            <table class="daftar">
                <thead>
                    <tr>
                        <th width="3%" style="padding-left:0; padding-right:0; text-align:center;">NO</th>
                        <th width="33%">NAMA LENGKAP</th>
                        <th width="15%">NPM / NUPTK</th>
                        <th width="19%">PROGRAM STUDI</th>
                        <th width="15%">STATUS KEHADIRAN</th>
                        <th width="15%">TANDA TANGAN</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($pagePeserta as $i => $p)
                        @php
                            $no = $chunkIdx * $PAGE_SIZE + $i + 1;

                            $prodiTampil = trim((string)($p->program_studi ?? ''));
                            if ($prodiTampil === '') {
                                $prodiTampil = $p->jenis_peserta === 'dosen'
                                    ? 'Dosen IAI DDI'
                                    : '-';
                            }

                            $hadir = (bool)$p->status_hadir;
                            $ket = $hadir ? 'HADIR' : 'TIDAK HADIR';

                            $ketColor = $hadir ? '#047857' : '#b91c1c';
                            $bgCell = $hadir ? '#dcfce7' : '#fee2e2';

                            try {
                                $identitas = (string)$p->nomor_identitas;
                            } catch (\Throwable $e) {
                                $identitas = $p->jenis_peserta === 'dosen'
                                    ? (string)($p->nidn ?? '-')
                                    : (string)($p->npm ?? '-');
                            }
                        @endphp

                        <tr>
                            <td width="3%" style="text-align:center !important; padding-left:0 !important; padding-right:0 !important; font-size:7.5px;">{{ $no }}</td>

                            <td width="33%" class="col-nama" style="font-weight:700;">
                                {{ strtoupper($p->nama_lengkap) }}
                            </td>

                            <td width="15%" class="col-npm">
                                {{ $identitas }}
                            </td>

                            <td width="19%" class="col-prodi">
                                {{ $prodiTampil }}
                            </td>

                            <td width="15%" class="col-keterangan"
                                style="
                                    text-align:center;
                                    background:{{ $bgCell }};
                                    color:{{ $ketColor }};
                                    font-weight:800;
                                ">
                                {{ $ket }}

                                @if($hadir && !empty($p->waktu_hadir))
                                    <div style="
                                        font-weight:400;
                                        color:{{ $ketColor }};
                                        font-size:6.8px;
                                        margin-top:0.2mm;
                                    ">
                                        {{ \Illuminate\Support\Carbon::parse($p->waktu_hadir)->setTimezone('Asia/Makassar')->format('H:i') }}
                                        WITA
                                    </div>
                                @endif

                                @if(!empty($p->keterangan))
                                    <div style="
                                        font-weight:400;
                                        color:{{ $ketColor }};
                                        font-size:6.6px;
                                        margin-top:0.2mm;
                                    ">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($p->keterangan), 38) }}
                                    </div>
                                @endif
                            </td>

                            <td width="15%" class="col-ttd">&nbsp;</td>
                        </tr>
                    @endforeach

                    @for($b = 1; $b <= $blankNeeded; $b++)
                        <tr>
                            <td width="3%" style="text-align:center !important; padding-left:0 !important; padding-right:0 !important; font-size:7.5px;">
                                {{ $chunkIdx * $PAGE_SIZE + $chunkCount + $b }}
                            </td>

                            <td width="33%" class="col-nama">&nbsp;</td>
                            <td width="15%" class="col-npm">&nbsp;</td>
                            <td width="19%" class="col-prodi">&nbsp;</td>
                            <td width="15%" class="col-keterangan">&nbsp;</td>

                            <td width="15%" class="col-ttd">&nbsp;</td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            @if($isLast)
                <table class="ttd-wrap-table">
                    <tr>
                        <td style="width:50%;">
                            <div class="ttd-label">Mengetahui / Menyetujui,</div>
                            <div class="ttd-sub">Ketua Panitia</div>
                            <div class="ttd-garis">&nbsp;</div>
                            <div class="ttd-nama">{{ strtoupper($ketuaPanitiaNama) }}</div>
                            <div class="ttd-nip">
                                @if($ketuaPanitiaNip !== '-') NIP. {{ $ketuaPanitiaNip }} @else Panitia Penyelenggara @endif
                            </div>
                        </td>
                        <td style="width:50%;">
                            <div class="ttd-label">Sidrap, {{ formatTglIndoCetak($nowCetak) }}</div>
                            <div class="ttd-sub">Pimpinan / Penanggung Jawab</div>
                            <div class="ttd-garis">&nbsp;</div>
                            <div class="ttd-nama">{{ strtoupper($rektorNama) }}</div>
                            <div class="ttd-nip">
                                @if($rektorNip !== '-') NIP. {{ $rektorNip }} @else Rektor IAI DDI Sidrap @endif
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="footer">
                    Dicetak otomatis oleh sistem SIAKAD IAI DDI Sidrap pada {{ $nowCetak->format('d/m/Y H:i') }} WITA &bull; Halaman {{ $halaman }} / {{ $totalHalaman }}
                </div>
            @endif

        </div>
    </div>
@endforeach
</body>
</html>