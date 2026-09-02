<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>DAFTAR HADIR - {{ strtoupper($kegiatan->judul) }}</title>
<style>
@page {
    size: A4 landscape;
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
    width: 297mm;
    height: 210mm;
    overflow: hidden;
    position: relative;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.content {
    position: absolute;
    z-index: 10;
    top: 6mm;
    left: 8mm;
    right: 8mm;
    bottom: 7mm;
    padding: 0 1mm;
    overflow: hidden;
}
.kop-wrap { width: 100%; text-align: center; color: #000; margin-bottom: 1.5mm; }
.kop-logo-center { width: 100%; text-align: center; margin-bottom: 1mm; display: block; }
.kop-logo-center img { width: 20mm; height: 20mm; object-fit: contain; display: inline-block; border: 0; margin: 0; padding: 0; }
.kop-title-a { font-size: 18px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.15; margin: 0.3mm 0 0; padding: 0; color: #000; }
.kop-title-a2 { margin-top: 0.4mm; }
.kop-title-b { font-size: 17px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.15; margin: 0.3mm 0 0; padding: 0; color: #000; }
.kop-terakreditasi { font-size: 10px; margin-top: 1mm; color: #000; text-align: center; letter-spacing: 0.15px; }
.kop-alamat-line { font-size: 9.5px; margin-top: 0.4mm; line-height: 1.2; color: #000; text-align: center; }
.kop-email-web { margin-top: 0.4mm; }

.garis-tebal { border-top: 2.6pt solid #000; margin: 1.6mm 0 0.4mm 0; width: 100%; }
.garis-tipis { border-top: 0.8pt solid #000; margin: 0 0 2.5mm 0; width: 100%; }

.judul-box { text-align: center; margin-bottom: 2.5mm; }
.judul-text { font-size: 17px; font-weight: 800; letter-spacing: 1.6px; text-transform: uppercase;
    text-decoration: underline; text-underline-offset: 2.5px; color: #000; }
.judul-nomor { font-size: 10.5px; margin-top: 0.8mm; color: #000; font-style: italic; }

.info-kegiatan { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 1.8mm; font-size: 11px; color: #000; }
.info-kegiatan td { vertical-align: top; padding: 0.5mm 0; }
.info-kegiatan td.label { width: 32mm; font-weight: 400; position: relative; white-space: nowrap; }
.info-kegiatan td.label::after { content: ":"; position: absolute; left: 30mm; top: 0.5mm; display: inline-block; color: #000; }
.info-kegiatan td.value { padding-left: 5.5mm; font-weight: 700; }
.info-kegiatan td.value-soft { padding-left: 5.5mm; font-weight: 400; }
.info-row { display: flex; }
.info-col { width: 50%; }

table.daftar { border-collapse: collapse; width: 100%; font-size: 9.5px; color: #000; table-layout: fixed; margin-top: 0mm; }
table.daftar thead th {
    border: 1px solid #000; background: #e0f2ea; font-weight: 800; letter-spacing: 0.15px;
    padding: 1mm 0.9mm; vertical-align: middle; line-height: 1.15; text-align: center; font-size: 9px;
}
table.daftar tbody td {
    border: 1px solid #000; padding: 0.9mm 1mm; line-height: 1.2; vertical-align: middle; height: 5mm;
}
table.daftar .col-no { width: 10mm; text-align: center; }
table.daftar .col-nama { width: 63mm; }
table.daftar .col-npm { width: 28mm; text-align: center; }
table.daftar .col-prodi { width: 44mm; }
table.daftar .col-keterangan { width: 40mm; }
table.daftar .col-ttd { width: 40mm; height: 5mm; }
table.daftar tbody td.col-no { text-align: center; }
table.daftar tbody td.col-npm { text-align: center; }

.ttd-wrap-table { width: 100%; border-collapse: collapse; margin-top: 1.7mm; }
.ttd-wrap-table td { vertical-align: top; width: 33.33%; padding: 0 3mm; font-size: 11px; color: #000; }

.stat-box { padding: 1.5mm 2.2mm; border: 1px solid #000; background: #fff; font-size: 10px; width: 100%; }
.stat-title { font-weight: 800; text-align: center; margin-bottom: 0.4mm; border-bottom: 1px solid #000; padding-bottom: 0.7mm; font-size: 10px; }
.stat-row { display: flex; justify-content: space-between; padding: 0.55mm 0; border-bottom: 0.4px dotted #999; }
.stat-row:last-child { border-bottom: 0; }
.stat-label { font-weight: 400; }
.stat-val { font-weight: 800; }

.ttd-label { margin-bottom: 9mm; font-weight: 400; line-height: 1.3; }
.ttd-label b { display: block; margin-bottom: 0.4mm; font-size: 11px; }
.ttd-garis { border-top: 1px solid #000; margin: 0; padding-top: 0.5mm; }
.ttd-nama { font-weight: 800; text-decoration: underline; text-underline-offset: 1px; font-size: 11px; }
.ttd-nip { font-size: 10px; color: #000; }

.footer { clear: both; margin-top: 1mm; padding-top: 0.7mm; border-top: 0.4px dashed #888;
    font-size: 8.2px; color: #555; text-align: right; font-style: italic; }
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

$MAKS_BARIS = 16;
if ($totalPeserta >= $MAKS_BARIS) {
    $totalBlank = 0;
} else {
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
@endphp

<div class="content">
    <div class="kop-wrap">
        <div class="kop-logo-center">
            @if($logoFinalSrc)
                <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI Sidrap" width="80" height="80">
            @else
                <div style="width:20mm;height:20mm;display:inline-block;border-radius:50%;background:#0f6244;color:#fff;font-size:11px;font-weight:800;line-height:20mm;text-align:center;">IAI DDI</div>
            @endif
        </div>
        <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
        <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
        <div class="kop-title-b">SIDENRENG RAPPANG</div>
        <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
        <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
        <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
    </div>
    <div class="garis-tebal"></div>
    <div class="garis-tipis"></div>

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
                    <td class="label">Nama Kegiatan</td>
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
                            {{ \Illuminate\Support\Carbon::parse($kegiatan->tanggal_kegiatan)->isoFormat('dddd, D MMMM Y') }}
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
                                s/d {{ substr($kegiatan->waktu_selesai,0,5) }} WIB
                            @else WIB
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
                <tr>
                    <td class="label">Narasumber</td>
                    <td class="value-soft">{{ $kegiatan->narasumber ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

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
                <th>NPM / NIDN</th>
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
                    <td class="col-nama" style="font-weight: 700;">{{ strtoupper($p->nama_lengkap) }}<span style="font-weight: 400; color:#3a3a3a; font-size:7.8px; margin-left: 2mm;">[{{ strtoupper($p->jenis_peserta_label) }}]</span></td>
                    <td class="col-npm" style="font-family:'Courier New', monospace;">{{ $identitas }}</td>
                    <td class="col-prodi">{{ $prodiTampil }}</td>
                    <td class="col-keterangan" style="text-align:center; background: {{ $bgCell }}; color: {{ $ketColor }}; font-weight: 800;">
                        {{ $ket }}
                        @if($p->status_hadir && !empty($p->waktu_hadir))
                            <div style="font-weight: 400; color:#333; font-size: 7.2px; margin-top:0.3mm;">
                                {{ \Illuminate\Support\Carbon::parse($p->waktu_hadir)->format('H:i') }} WIB
                            </div>
                        @endif
                        @if(!empty($p->keterangan))
                            <div style="font-weight: 400; color:#555; font-size:7.2px; margin-top:0.3mm;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($p->keterangan), 42) }}
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

    <table class="ttd-wrap-table">
        <tr>
            <td style="padding: 0 2mm 0 0;">
                <div class="stat-box">
                    <div class="stat-title">REKAPITULASI KEHADIRAN</div>
                    <div class="stat-row">
                        <span class="stat-label">Total Peserta Terdaftar</span>
                        <span class="stat-val">{{ $totalPeserta }} orang</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">✓ Peserta Hadir</span>
                        <span class="stat-val" style="color:#059669;">{{ $totalHadir }} orang</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">✗ Tidak / Belum Hadir</span>
                        <span class="stat-val" style="color:#dc2626;">{{ $totalBelum }} orang</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Persentase Kehadiran</span>
                        <span class="stat-val">{{ $pct }}%</span>
                    </div>
                </div>
            </td>
            <td style="padding: 0 2mm;">
                <div class="ttd-label">
                    <b>Mengetahui / Menyetujui,</b>
                    Ketua {{ $keteranganPanitia }}
                </div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $namaPanitia }}</div>
                    <div class="ttd-nip">NIP. {{ $nipPanitia }}</div>
                </div>
            </td>
            <td style="padding: 0 0 0 2mm;">
                <div class="ttd-label">
                    <b>Sidrap, {{ \Illuminate\Support\Carbon::parse($kegiatan->tanggal_kegiatan ?? now())->isoFormat('D MMMM Y') }}</b>
                    Pimpinan / Penanggung Jawab
                </div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $namaRektor }}</div>
                    <div class="ttd-nip">Rektor IAI DDI Sidrap &nbsp;&nbsp; NIP. {{ $nipRektor }}</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Informasi Akademik (SIAKAD) IAI DDI Sidrap pada
        {{ \Illuminate\Support\Carbon::now()->isoFormat('dddd, D MMMM Y HH:mm:ss') }}
    </div>
</div>

</body>
</html>
