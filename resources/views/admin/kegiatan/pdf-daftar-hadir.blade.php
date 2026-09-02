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
    top: 8mm;
    left: 10mm;
    right: 10mm;
    bottom: 9mm;
    padding: 0 1mm;
    overflow: hidden;
}
.kop-wrap { width: 100%; text-align: center; color: #000; margin-bottom: 1.5mm; }
.kop-logo-center { width: 100%; text-align: center; margin-bottom: 1.2mm; display: block; }
.kop-logo-center img { width: 16mm; height: 16mm; object-fit: contain; display: inline-block; border: 0; margin: 0; padding: 0; }
.kop-title-a { font-size: 16px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.15; margin: 0.4mm 0 0; padding: 0; color: #000; }
.kop-title-a2 { margin-top: 0.5mm; }
.kop-title-b { font-size: 15px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.15; margin: 0.4mm 0 0; padding: 0; color: #000; }
.kop-terakreditasi { font-size: 9px; margin-top: 1.1mm; color: #000; text-align: center; letter-spacing: 0.1px; }
.kop-alamat-line { font-size: 8.5px; margin-top: 0.5mm; line-height: 1.2; color: #000; text-align: center; }
.kop-email-web { margin-top: 0.5mm; }

.garis-tebal { border-top: 2.4pt solid #000; margin: 1.8mm 0 0.5mm 0; width: 100%; }
.garis-tipis { border-top: 0.7pt solid #000; margin: 0 0 2.8mm 0; width: 100%; }

.judul-box { text-align: center; margin-bottom: 2.8mm; }
.judul-text { font-size: 15px; font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;
    text-decoration: underline; text-underline-offset: 2.2px; color: #000; }
.judul-nomor { font-size: 9.5px; margin-top: 1mm; color: #000; font-style: italic; }

.info-kegiatan { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 2mm; font-size: 10px; color: #000; }
.info-kegiatan td { vertical-align: top; padding: 0.4mm 0; }
.info-kegiatan td.label { width: 30mm; font-weight: 400; position: relative; white-space: nowrap; }
.info-kegiatan td.label::after { content: ":"; position: absolute; left: 28mm; top: 0.4mm; display: inline-block; color: #000; }
.info-kegiatan td.value { padding-left: 5mm; font-weight: 700; }
.info-kegiatan td.value-soft { padding-left: 5mm; font-weight: 400; }
.info-row { display: flex; }
.info-col { width: 50%; }

table.daftar { border-collapse: collapse; width: 100%; font-size: 8px; color: #000; table-layout: fixed; margin-top: 0mm; }
table.daftar thead th {
    border: 1px solid #000; background: #e0f2ea; font-weight: 800; letter-spacing: 0.1px;
    padding: 0.9mm 0.8mm; vertical-align: middle; line-height: 1.1; text-align: center; font-size: 7.8px;
}
table.daftar tbody td {
    border: 1px solid #000; padding: 0.6mm 0.9mm; line-height: 1.15; vertical-align: middle; height: 4.4mm;
}
table.daftar .col-no { width: 8mm; text-align: center; }
table.daftar .col-nama { width: 58mm; }
table.daftar .col-npm { width: 21mm; text-align: center; }
table.daftar .col-prodi { width: 34mm; }
table.daftar .col-keterangan { width: 32mm; }
table.daftar .col-sertifikat { width: 37mm; text-align: left; font-family: 'Courier New', monospace; font-size: 7.5px; }
table.daftar .col-ttd { width: 32mm; height: 4.4mm; }
table.daftar tbody td.col-no { text-align: center; }
table.daftar tbody td.col-npm { text-align: center; }

.ttd-wrap-table { width: 100%; border-collapse: collapse; margin-top: 1.8mm; }
.ttd-wrap-table td { vertical-align: top; width: 33.33%; padding: 0 3mm; font-size: 10px; color: #000; }

.stat-box { padding: 1.4mm 2mm; border: 1px solid #000; background: #fff; font-size: 9px; width: 100%; }
.stat-title { font-weight: 800; text-align: center; margin-bottom: 0.3mm; border-bottom: 1px solid #000; padding-bottom: 0.6mm; font-size: 9px; }
.stat-row { display: flex; justify-content: space-between; padding: 0.45mm 0; border-bottom: 0.4px dotted #999; }
.stat-row:last-child { border-bottom: 0; }
.stat-label { font-weight: 400; }
.stat-val { font-weight: 800; }

.ttd-label { margin-bottom: 10mm; font-weight: 400; line-height: 1.3; }
.ttd-label b { display: block; margin-bottom: 0.4mm; font-size: 10px; }
.ttd-garis { border-top: 1px solid #000; margin: 0; padding-top: 0.4mm; }
.ttd-nama { font-weight: 800; text-decoration: underline; text-underline-offset: 1px; font-size: 10px; }
.ttd-nip { font-size: 9px; color: #000; }

.footer { clear: both; margin-top: 1.2mm; padding-top: 0.8mm; border-top: 0.4px dashed #888;
    font-size: 7.4px; color: #555; text-align: right; font-style: italic; }
</style>
</head>
<body>
@php
$logoFinalSrc = null;
$logoCandidates = [
    public_path('img/lo.jpeg'),
    public_path('img/lo.jpg'),
    public_path('img/lo.png'),
    public_path('img/logo.jpeg'),
    public_path('img/logo.jpg'),
    public_path('img/logo.png'),
];
foreach ($logoCandidates as $logoPath) {
    try {
        if (file_exists($logoPath) && is_file($logoPath) && is_readable($logoPath)) {
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

$totalPeserta = $peserta->count();
$totalHadir   = $peserta->where('status_hadir', true)->count();
$totalBelum   = $totalPeserta - $totalHadir;

$MAKS_BARIS = 17;
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
                <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI Sidrap" width="64" height="64">
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
                <tr>
                    <td class="label">Prefix Sertifikat</td>
                    <td class="value-soft">
                        @if(!empty($kegiatan->nomor_sertifikat_prefix))
                            <span style="font-family:'Courier New', monospace; font-weight: 700;">{{ $kegiatan->nomor_sertifikat_prefix }}/xxxx/MM/YYYY</span>
                        @else
                            <span style="color:#555;">(Default: SERT/xxxx/MM/YYYY)</span>
                        @endif
                    </td>
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
            <col class="col-sertifikat">
            <col class="col-ttd">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA LENGKAP</th>
                <th rowspan="2">NPM / NIK</th>
                <th rowspan="2">PRODI / FAKULTAS</th>
                <th colspan="2">STATUS KEHADIRAN</th>
                <th rowspan="2">TANDA TANGAN</th>
            </tr>
            <tr>
                <th>KET.</th>
                <th>NO. SERTIFIKAT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peserta as $i => $p)
                @if($i >= $MAKS_BARIS) @break @endif
                @php
                    $no = $i + 1;
                    $prodiTampil = trim(($p->program_studi ?? '').($p->fakultas ? ' - '.$p->fakultas : ''));
                    if ($prodiTampil === '') $prodiTampil = '-';
                    $ket = $p->status_hadir ? 'HADIR' : 'TIDAK HADIR';
                    $ketColor = $p->status_hadir ? '#065f46' : '#991b1b';
                    $bgCell = $p->status_hadir ? '#ecfdf5' : '#fef2f2';
                @endphp
                <tr>
                    <td class="col-no">{{ $no }}</td>
                    <td class="col-nama" style="font-weight: 700;">{{ strtoupper($p->nama_lengkap) }}</td>
                    <td class="col-npm" style="font-family:'Courier New', monospace;">{{ $p->npm ?? '-' }}</td>
                    <td class="col-prodi" style="font-size:7.5px;">{{ $prodiTampil }}</td>
                    <td class="col-keterangan" style="text-align:center; background: {{ $bgCell }}; color: {{ $ketColor }}; font-weight: 800;">
                        {{ $ket }}
                        @if($p->status_hadir && !empty($p->waktu_hadir))
                            <div style="font-weight: 400; color:#333; font-size: 7px; margin-top:0.3mm;">
                                {{ \Illuminate\Support\Carbon::parse($p->waktu_hadir)->format('H:i') }} WIB
                            </div>
                        @endif
                    </td>
                    <td class="col-sertifikat">
                        @if($p->status_hadir && !empty($p->nomor_sertifikat))
                            {{ $p->nomor_sertifikat }}
                        @else
                            <span style="color: #888;">-</span>
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
                    <td class="col-sertifikat">&nbsp;</td>
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
