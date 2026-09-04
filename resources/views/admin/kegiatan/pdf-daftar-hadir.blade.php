@php
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
    function formatTglIndoSingkat($tanggal) {
        if (empty($tanggal)) return '-';
        try {
            $c = \Illuminate\Support\Carbon::parse($tanggal)->setTimezone('Asia/Makassar');
            return $c->format('j').' '.substr(bulanIndo($c->month),0,3).' '.$c->format('Y');
        } catch (\Throwable $e) { return '-'; }
    }

    $logoPaths = [
        public_path('img/lo.jpeg'), public_path('img/lo.jpg'), public_path('img/logo.jpeg'), public_path('img/logo.jpg'),
        base_path('public_html/img/lo.jpeg'), base_path('public_html/img/lo.jpg'),
        base_path('public/img/lo.jpeg'), base_path('public/img/lo.jpg'),
    ];
    $logoFinalSrc = '';
    foreach ($logoPaths as $lp) { if (@file_exists($lp)) { $logoFinalSrc = $lp; break; } }
    $logoBase64 = '';
    if ($logoFinalSrc !== '') {
        $ext = strtolower(pathinfo($logoFinalSrc, PATHINFO_EXTENSION));
        $mime = in_array($ext,['jpeg','jpg']) ? 'image/jpeg' : 'image/png';
        $raw = @file_get_contents($logoFinalSrc);
        if ($raw !== false) $logoBase64 = 'data:'.$mime.';base64,'.base64_encode($raw);
    }

    $PAGE_SIZE = 30;
    $totalPeserta = $peserta->count();
    $chunks = $peserta->chunk($PAGE_SIZE);
    $totalHalaman = $chunks->count();

    $tempatVal = trim((string)($kegiatan->tempat ?? ''));
    if ($tempatVal === '') $tempatVal = '-';
    $penyelenggaraVal = trim((string)($kegiatan->penyelenggara ?? ''));
    if ($penyelenggaraVal === '') $penyelenggaraVal = 'IAI DDI SIDRAP';
    $ketuaPanitiaNama = trim((string)($kegiatan->ketua_panitia_nama ?? ''));
    $ketuaPanitiaNip = trim((string)($kegiatan->ketua_panitia_nip ?? ''));
    $rektorNama = trim((string)($kegiatan->rektor_nama ?? ''));
    $rektorNip = trim((string)($kegiatan->rektor_nip ?? ''));

    $nowCetak = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Makassar');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DAFTAR HADIR PESERTA</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
            padding: 0;
        }
        * { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
        html, body { margin:0; padding:0; font-family:'Times New Roman', Times, serif; color:#000; background:#fff; font-size:10px; }
        .page {
            position: relative;
            width: 210mm;
            min-height: 297mm;
            padding: 5mm 10mm 6mm 10mm;
            margin: 0 auto;
            page-break-after: always;
        }
        .kop-wrap {
            display: table;
            table-layout: fixed;
            width: 100%;
            padding: 0;
            margin: 0 0 0.5mm 0;
        }
        .kop-logo-side {
            display: table-cell;
            vertical-align: middle;
            width: 22mm;
            padding: 0;
            margin: 0;
            text-align: left;
        }
        .kop-logo-side img {
            width: 20mm;
            height: 20mm;
            display: block;
            margin: 0;
            transform: none;
            float: left;
        }
        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 1mm 0 0;
            margin: 0;
        }
        .kop-text .kop-institusi {
            font-size: 19px;
            font-weight: 700;
            letter-spacing: 0.6px;
            line-height: 1.1;
            margin: 0 0 0.5mm 0;
            text-align: center;
        }
        .kop-text .kop-institusi-2 {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.6px;
            line-height: 1.1;
            margin: 0 0 0.5mm 0;
            text-align: center;
        }
        .kop-text .kop-kota {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.6px;
            line-height: 1.1;
            margin: 0 0 0.7mm 0;
            text-align: center;
        }
        .kop-text .kop-akreditasi {
            font-size: 10px;
            font-weight: 600;
            line-height: 1.15;
            margin: 0 0 0.3mm 0;
            text-align: center;
        }
        .kop-text .kop-alamat {
            font-size: 9.5px;
            line-height: 1.15;
            margin: 0 0 0.2mm 0;
            text-align: center;
        }
        .kop-text .kop-kontak {
            font-size: 9.5px;
            line-height: 1.15;
            margin: 0;
            text-align: center;
        }
        .garis-tebal { 
    width: 100%;
    height: 2.4pt;
    background: #000;
    margin: 1.5mm 0 0.6mm 0;
}
       .garis-tipis { 
    width:100%; 
    height: 0.8pt; 
    background:#000; 
    margin: 0 0 2mm 0; 
}

        .info-with-logo { display:block; width: 100%; margin: 0; padding: 0; }
       .judul-box { 
    width: 100%;
    display: block;
    text-align: center;
    margin: 0 0 2.5mm 0;
    padding: 0;
}
        .judul-box .judul-utama {
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            line-height: 1.2;
            text-decoration: none;
            color: #000;
            margin: 0;
        }

       .info-table { 
    width: 100%; 
    border-collapse: collapse; 
    margin: 0;
    padding: 0;
    table-layout: auto;
}

.info-table td {
    border: none;
    padding: 0.3mm 0;
    vertical-align: top;
    font-size: 9.5px;
    line-height: 1.25;
}

.info-table td.kiri {
    width: 29mm;
    white-space: nowrap;
    font-weight: 400;
}

.info-table td.titik-2 {
    width: 3mm;
    text-align: center;
    padding: 0.3mm 1mm;
}

.info-table td.kanan {
    padding-left: 1mm;
    padding-right: 2mm;
    white-space: normal;
    word-wrap: break-word;
}
        table.daftar-hadir {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse;
            table-layout: fixed !important;
            font-size: 8.5px;
            color: #000;
            margin: 1mm 0 0 0;
            padding: 0;
        }
        table.daftar-hadir thead th {
            border: 1px solid #000;
            background: #e0f2ea;
            font-weight: 800;
            padding: 0.5mm 0.1mm;
            line-height: 1.15;
            vertical-align: middle;
            text-align: center;
            font-size: 8.5px;
        }
        table.daftar-hadir tbody td {
            border: 1px solid #000;
            padding: 0.3mm 0.1mm;
            line-height: 1.15;
            vertical-align: middle;
            height: 5mm;
            font-size: 8.5px;
        }

        .no-cell { width:5% !important; min-width:5% !important; max-width:5% !important; text-align:center; font-size:6.8px !important; line-height:1 !important; white-space:nowrap !important; padding: 0.3mm 0 !important; }
        .nama-cell { width:30% !important; min-width:30% !important; max-width:30% !important; padding-left:0.6mm !important; padding-right:0.6mm !important; font-weight:700; }
        .npm-cell { width:16% !important; min-width:16% !important; max-width:16% !important; text-align:center; padding-left:0.4mm !important; padding-right:0.4mm !important; font-family:'Courier New', monospace; }
        .prodi-cell { width:20% !important; min-width:20% !important; max-width:20% !important; padding-left:0.5mm !important; padding-right:0.5mm !important; }
        .status-cell { width:14% !important; min-width:14% !important; max-width:14% !important; text-align:center; padding-left:0.3mm !important; padding-right:0.3mm !important; font-weight:800; }
        .ttd-cell { width:15% !important; min-width:15% !important; max-width:15% !important; text-align:center; height:5mm; }

        .ttd-wrap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3mm;
            table-layout: fixed;
        }
        .ttd-col {
            width: 50%;
            vertical-align: top;
            padding: 0 4mm;
            font-size: 10px;
            line-height: 1.25;
            text-align: center;
        }
        .ttd-label { font-weight:700; margin-bottom: 0.2mm; text-align:center; }
        .ttd-sub { font-weight:600; margin-bottom: 8mm; text-align:center; }
        .ttd-space { height: 0.2mm; line-height: 0.2mm; margin: 0; padding: 0; }
        .ttd-garis {
            height: 0.5mm;
            width: 70%;
            margin: 0 auto 0.8mm auto;
            border-bottom: 1.2pt solid #000;
        }
        .ttd-nama { font-weight:700; text-align:center; margin-bottom: 0.5mm; }
        .ttd-nip { font-style: italic; text-align:center; }

        .footer-dashed {
            width:100%; border-top:1px dashed #555; margin: 1.2mm 0 0.8mm 0; padding-top: 0.3mm;
        }
        .footer-text { font-size: 8px; text-align: right; color: #444; font-style: italic; }
    </style>
</head>
<body>
@foreach($chunks as $chunkIdx => $pagePeserta)
    @php
        $halaman = $chunkIdx + 1;
        $isFirst = $halaman === 1;
        $isLast = $halaman === $totalHalaman;
        $chunkCount = $pagePeserta->count();
        $blankNeeded = $PAGE_SIZE - $chunkCount;
        if ($blankNeeded > 15) $blankNeeded = 15;
    @endphp
<div class="page" style="page-break-after: {{ $isLast ? 'auto' : 'always' }};">
    <div class="kop-wrap">
        <div class="kop-logo-side">
            @if($logoBase64 !== '')
                <img src="{{ $logoBase64 }}" alt="Logo IAI DDI">
            @endif
        </div>
        <div class="kop-text">
            <div class="kop-institusi">INSTITUT AGAMA ISLAM</div>
            <div class="kop-institusi-2">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-kota">SIDENRENG RAPPANG</div>
            <div class="kop-akreditasi">TERAKREDITASI INSTITUSI &bull; SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-kontak">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
        </div>
    </div>

    <div class="garis-tebal"></div>
    <div class="garis-tipis"></div>

    @if($isFirst)
    <div class="info-with-logo">
        <div class="judul-box">
            <div class="judul-utama">DAFTAR HADIR PESERTA</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="kiri">Tema Kegiatan</td>
                <td class="titik-2">:</td>
                <td class="kanan" style="font-weight:700;">{{ strtoupper($kegiatan->judul ?? '-') }}</td>
                <td class="kiri" style="width:38mm;">Tempat / Lokasi</td>
                <td class="titik-2">:</td>
                <td class="kanan">{{ $tempatVal }}</td>
            </tr>
            <tr>
                <td class="kiri">Jenis Kegiatan</td>
                <td class="titik-2">:</td>
                <td class="kanan">{{ strtoupper($kegiatan->jenis_kegiatan ?? '-') }}</td>
                <td class="kiri" style="width:38mm;">Penyelenggara</td>
                <td class="titik-2">:</td>
                <td class="kanan">{{ $penyelenggaraVal }}</td>
            </tr>
            <tr>
                <td class="kiri">Hari, Tanggal</td>
                <td class="titik-2">:</td>
                <td class="kanan">{{ formatTglIndo($kegiatan->tanggal_mulai ?? $kegiatan->tanggal ?? now()) }}</td>
                <td class="kiri" style="width:38mm;">Jumlah Peserta</td>
                <td class="titik-2">:</td>
                <td class="kanan"><b>{{ $totalPeserta }}</b> orang &nbsp;•&nbsp; Halaman {{ $halaman }} dari {{ $totalHalaman }}</td>
            </tr>
            <tr>
                <td class="kiri">Waktu (WITA)</td>
                <td class="titik-2">:</td>
                <td class="kanan">
                    @php
                        $wm = $kegiatan->waktu_mulai ?? '';
                        $ws = $kegiatan->waktu_selesai ?? '';
                        if ($wm !== '') $wm = substr(trim((string)$wm), 0, 5);
                        if ($ws !== '') $ws = substr(trim((string)$ws), 0, 5);
                        if ($wm !== '' && $ws !== '') { echo $wm.' s/d '.$ws.' WITA'; }
                        elseif ($wm !== '') { echo $wm.' WITA'; } else { echo '-'; }
                    @endphp
                </td>
                <td class="kiri" style="width:38mm;"></td>
                <td class="titik-2"></td>
                <td class="kanan"></td>
            </tr>
        </table>
    </div>
    @else
    <div class="info-with-logo">
        <div class="judul-box" style="margin-bottom:1.5mm;">
            <div class="judul-utama">DAFTAR HADIR PESERTA (LANJUTAN)</div>
            <div style="margin-top:1mm; font-size:10px; font-weight:600; line-height:1.2;">
                {{ strtoupper($kegiatan->judul ?? '-') }}
            </div>
            <div style="margin-top:0.5mm; font-size:9px; color:#333; font-style:italic; line-height:1.2;">
                Halaman {{ $halaman }} dari {{ $totalHalaman }} &nbsp;•&nbsp; Total peserta: {{ $totalPeserta }} orang
            </div>
        </div>
    </div>
    @endif

    <table class="daftar-hadir">
        <thead>
            <tr>
                <th style="width:5% !important; min-width:5% !important; max-width:5% !important; font-size:7px !important; line-height:1 !important;">NO</th>
                <th style="width:30% !important; min-width:30% !important; max-width:30% !important;">NAMA LENGKAP</th>
                <th style="width:16% !important; min-width:16% !important; max-width:16% !important;">NPM / NUPTK</th>
                <th style="width:20% !important; min-width:20% !important; max-width:20% !important;">PROGRAM STUDI</th>
                <th style="width:14% !important; min-width:14% !important; max-width:14% !important;">STATUS KEHADIRAN</th>
                <th style="width:15% !important; min-width:15% !important; max-width:15% !important;">TANDA TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagePeserta as $i => $p)
                @php
                    $no = $chunkIdx * $PAGE_SIZE + $i + 1;
                    $prodiTampil = trim((string)($p->program_studi ?? ''));
                    if ($prodiTampil === '') {
                        $prodiTampil = ($p->jenis_peserta === 'dosen') ? 'Dosen IAI DDI' : '-';
                    }
                    $ket = $p->status_hadir ? 'HADIR' : 'TIDAK HADIR';
                    $ketColor = $p->status_hadir ? '#065f46' : '#991b1b';
                    $bgCell = $p->status_hadir ? '#ecfdf5' : '#fef2f2';
                    try {
                        $identitas = (string)$p->nomor_identitas;
                    } catch (\Throwable $e) {
                        $identitas = $p->jenis_peserta === 'dosen' ? (string)($p->nidn ?? '-') : (string)($p->npm ?? '-');
                    }
                @endphp
                <tr>
                    <td class="no-cell" style="width:5% !important; min-width:5% !important; max-width:5% !important;">{{ $no }}.</td>
                    <td class="nama-cell" style="width:30% !important; min-width:30% !important; max-width:30% !important;">{{ strtoupper($p->nama_lengkap) }}</td>
                    <td class="npm-cell" style="width:16% !important; min-width:16% !important; max-width:16% !important;">{{ $identitas }}</td>
                    <td class="prodi-cell" style="width:20% !important; min-width:20% !important; max-width:20% !important;">{{ $prodiTampil }}</td>
                    <td class="status-cell" style="width:14% !important; min-width:14% !important; max-width:14% !important; background:{{ $bgCell }}; color:{{ $ketColor }};">
                        {{ $ket }}
                        @if($p->status_hadir && !empty($p->waktu_hadir))
                            <div style="font-weight:400; color:#333; font-size:6.6px; margin-top:0.25mm;">
                                {{ \Illuminate\Support\Carbon::parse($p->waktu_hadir)->setTimezone('Asia/Makassar')->format('H:i') }} WITA
                            </div>
                        @endif
                        @if(!empty($p->keterangan))
                            <div style="font-weight:400; color:#555; font-size:6.6px; margin-top:0.25mm;">
                                {{ \Illuminate\Support\Str::limit(strip_tags($p->keterangan), 38) }}
                            </div>
                        @endif
                    </td>
                    <td class="ttd-cell" style="width:15% !important; min-width:15% !important; max-width:15% !important;"></td>
                </tr>
            @endforeach

            @for($b = 1; $b <= $blankNeeded; $b++)
                @php $blankNo = $chunkIdx * $PAGE_SIZE + $chunkCount + $b; @endphp
                <tr>
                    <td class="no-cell" style="width:5% !important; min-width:5% !important; max-width:5% !important;">{{ $blankNo }}.</td>
                    <td class="nama-cell" style="width:30% !important; min-width:30% !important; max-width:30% !important;">&nbsp;</td>
                    <td class="npm-cell" style="width:16% !important; min-width:16% !important; max-width:16% !important;">&nbsp;</td>
                    <td class="prodi-cell" style="width:20% !important; min-width:20% !important; max-width:20% !important;">&nbsp;</td>
                    <td class="status-cell" style="width:14% !important; min-width:14% !important; max-width:14% !important;">&nbsp;</td>
                    <td class="ttd-cell" style="width:15% !important; min-width:15% !important; max-width:15% !important;"></td>
                </tr>
            @endfor
        </tbody>
    </table>

    @if($isLast)
    <table class="ttd-wrap-table">
        <tr>
            <td class="ttd-col" style="width:50%;">
                <div class="ttd-label">Mengetahui / Menyetujui,</div>
                <div class="ttd-sub">Ketua Panitia</div>
                <div class="ttd-space">&nbsp;</div>
                <div class="ttd-garis">&nbsp;</div>
                <div class="ttd-nama">{!! $ketuaPanitiaNama !== '' ? e(strtoupper($ketuaPanitiaNama)) : '_______________________________' !!}</div>
                <div class="ttd-nip">{!! $ketuaPanitiaNip !== '' ? 'NIP. '.e($ketuaPanitiaNip) : 'NIP. _____________________' !!}</div>
            </td>
            <td class="ttd-col" style="width:50%;">
                <div class="ttd-label">Sidrap, {{ $nowCetak->format('j') }} {{ bulanIndo($nowCetak->month) }} {{ $nowCetak->format('Y') }}</div>
                <div class="ttd-sub">Pimpinan / Penanggung Jawab</div>
                <div class="ttd-space">&nbsp;</div>
                <div class="ttd-garis">&nbsp;</div>
                <div class="ttd-nama">{!! $rektorNama !== '' ? e(strtoupper($rektorNama)) : '_______________________________' !!}</div>
                <div class="ttd-nip">{!! $rektorNip !== '' ? 'NIP. '.e($rektorNip) : 'Rektor IAI DDI Sidrap' !!}</div>
            </td>
        </tr>
    </table>

    <div class="footer-dashed"></div>
    <div class="footer-text">
        Dicetak otomatis oleh Sistem Informasi Akademik (SIAKAD) IAI DDI Sidrap
        pada {{ hariIndo($nowCetak->dayOfWeek) }}, {{ $nowCetak->format('j') }} {{ bulanIndo($nowCetak->month) }} {{ $nowCetak->format('Y') }}
        {{ $nowCetak->format('H:i') }} WITA
    </div>
    @endif
</div>
@endforeach
</body>
</html>
