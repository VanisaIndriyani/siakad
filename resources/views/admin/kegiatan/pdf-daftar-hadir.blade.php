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

    $logoPaths = [
        public_path('img/lo.jpeg'), public_path('img/lo.jpg'),
        public_path('img/logo.jpeg'), public_path('img/logo.jpg'),
        base_path('public_html/img/lo.jpeg'), base_path('public_html/img/lo.jpg'),
        base_path('public/img/lo.jpeg'), base_path('public/img/lo.jpg'),
    ];
    $logoFinalSrc = '';
    foreach ($logoPaths as $lp) { if (@file_exists($lp)) { $logoFinalSrc = $lp; break; } }
    $logoBase64 = '';
    if ($logoFinalSrc !== '') {
        $ext = strtolower(pathinfo($logoFinalSrc, PATHINFO_EXTENSION));
        $mime = in_array($ext, ['jpeg','jpg']) ? 'image/jpeg' : 'image/png';
        $raw = @file_get_contents($logoFinalSrc);
        if ($raw !== false) $logoBase64 = 'data:'.$mime.';base64,'.base64_encode($raw);
    }

    $PAGE_SIZE = 15;
    $totalPeserta = $peserta->count();
    $chunks = $peserta->chunk($PAGE_SIZE);
    $totalHalaman = $chunks->count();

    $tempatVal = trim((string)($kegiatan->tempat ?? ''));
    if ($tempatVal === '') $tempatVal = '-';
    $penyelenggaraVal = trim((string)($kegiatan->penyelenggara ?? ''));
    if ($penyelenggaraVal === '') $penyelenggaraVal = 'IAI DDI SIDRAP';
    $ketuaPanitiaNama = trim((string)($kegiatan->ketua_panitia_nama ?? ''));
    $ketuaPanitiaNip  = trim((string)($kegiatan->ketua_panitia_nip ?? ''));
    $rektorNama       = trim((string)($kegiatan->rektor_nama ?? ''));
    $rektorNip        = trim((string)($kegiatan->rektor_nip ?? ''));
    $narasumberVal    = trim((string)($kegiatan->narasumber ?? ''));
    if ($narasumberVal === '') $narasumberVal = '-';
    $nowCetak = \Illuminate\Support\Carbon::now()->setTimezone('Asia/Makassar');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>DAFTAR HADIR PESERTA</title>
<style>
@page { size: A4 portrait; margin: 0; padding: 0; }
*, *::before, *::after { box-sizing: border-box; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; }
html, body {
    margin: 0; padding: 0;
    font-family: "Times New Roman", Times, serif;
    color: #000; background: #fff; font-size: 10px;
}
.page {
    position: relative; width: 210mm; min-height: 297mm;
    padding: 5mm 4mm 5mm 4mm; margin: 0 auto; page-break-after: always;
}
.kop-wrap { position: relative; width: 100%; height: 27mm; margin: 0; padding: 0; }
.kop-logo-side { position: absolute; left: 0; top: 1mm; width: 23mm; height: 23mm; text-align: left; }
.kop-logo-side img { width: 20mm; height: 20mm; display: block; object-fit: contain; margin: 0; padding: 0; }
.kop-text {
    position: absolute; left: 23mm; right: 0; top: 0;
    text-align: center; margin: 0; padding: 0;
}
.kop-text .kop-institusi    { font-size: 19px; font-weight: 700; letter-spacing: 0.6px; line-height: 1.05; margin: 0 0 0.5mm 0; text-align: center; }
.kop-text .kop-institusi-2  { font-size: 18px; font-weight: 700; letter-spacing: 0.6px; line-height: 1.05; margin: 0 0 0.5mm 0; text-align: center; }
.kop-text .kop-kota         { font-size: 18px; font-weight: 700; letter-spacing: 0.6px; line-height: 1.05; margin: 0 0 0.7mm 0; text-align: center; }
.kop-text .kop-akreditasi   { font-size: 10px; font-weight: 600; line-height: 1.1; margin: 0 0 0.3mm 0; text-align: center; }
.kop-text .kop-alamat       { font-size: 9.5px; line-height: 1.1; margin: 0 0 0.2mm 0; text-align: center; }
.kop-text .kop-kontak       { font-size: 9.5px; line-height: 1.1; margin: 0; text-align: center; }
.garis-tebal { width: 100%; height: 2.4pt; background: #000; margin: 0.5mm 0 0.5mm 0; }
.garis-tipis { width: 100%; height: 0.8pt; background: #000; margin: 0 0 1.5mm 0; }
.judul-box { width: 100%; display: block; text-align: center; margin: 0 0 0.5mm 0; padding: 0; }
.judul-box .judul-utama { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; line-height: 1.1; margin: 0; text-align: center; }
.info-table { width: 100%; border-collapse: collapse; margin: 0 0 0.5mm 0; table-layout: fixed; }
.info-table td { border: none; padding: 0.1mm 0; vertical-align: top; font-size: 9px; line-height: 1.05; }
.info-table td.kiri    { width: 26mm; white-space: nowrap; font-weight: 400; }
.info-table td.titik-2 { width: 3mm; text-align: center; padding: 0.1mm 0.5mm; }
.info-table td.kanan   { padding: 0.1mm 0; white-space: normal; word-wrap: break-word; }
table.daftar-hadir {
    width: 100% !important;
    max-width: 100% !important;
    border-collapse: collapse;
    table-layout: fixed !important;
    font-size: 8.5px;
    color: #000;
    margin: 0.5mm 0 0 0;
}

table.daftar-hadir th,
table.daftar-hadir td {
    box-sizing: border-box !important;
    overflow: hidden;
}

.col-no     { width: 9mm !important; }
.col-nama   { width: 61mm !important; }
.col-npm    { width: 28mm !important; }
.col-prodi  { width: 40mm !important; }
.col-status { width: 28mm !important; }
.col-ttd    { width: 36mm !important; }
table.daftar-hadir, table.daftar-hadir th, table.daftar-hadir td {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
table.daftar-hadir thead { display: table-header-group; }
table.daftar-hadir thead th {
    border: 1px solid #000; background: #e0f2ea;
    font-weight: 800; padding: 0.5mm 0.1mm;
    line-height: 1.1; vertical-align: middle; text-align: center; font-size: 8.5px;
}
table.daftar-hadir tbody td {
    border: 1px solid #000; padding: 0.3mm 0.1mm;
    line-height: 1.15; vertical-align: middle; height: 4.5mm; font-size: 8.5px;
}
.ttd-wrap-table {
    width: 100% !important; max-width: 100% !important;
    border-collapse: collapse; table-layout: fixed;
    margin-top: 2mm; margin-bottom: 0mm;
    page-break-inside: avoid; break-inside: avoid;
}
.ttd-wrap-table tr { page-break-inside: avoid; break-inside: avoid; }
.ttd-col {
    width: 50%; vertical-align: top; padding: 0 4mm;
    font-size: 9px; line-height: 1.15; text-align: center;
}
.ttd-label { font-weight: 700; margin-bottom: 0.2mm; text-align: center; }
.ttd-sub   { font-weight: 600; margin-bottom: 2mm; text-align: center; }
.ttd-garis { height: 0.5mm; width: 75%; margin: 0 auto 0.6mm auto; border-bottom: 1.2pt solid #000; }
.ttd-nama  { font-weight: 700; text-align: center; margin-bottom: 0.3mm; word-wrap: break-word; }
.ttd-nip   { font-style: italic; text-align: center; font-size: 8.5px; }
</style>
</head>
<body>
@foreach($chunks as $chunkIdx => $pagePeserta)
    @php
        $halaman = $chunkIdx + 1;
        $isFirst = $halaman === 1;
        $isLast  = $halaman === $totalHalaman;
        $chunkCount = $pagePeserta->count();
        $blankNeeded = $PAGE_SIZE - $chunkCount;
        if ($blankNeeded > 2) $blankNeeded = 2;
    @endphp
    <div class="page" style="page-break-after: {{ $isLast ? 'auto' : 'always' }};">
        <div class="kop-wrap">
            <div class="kop-logo-side">
                @if($logoBase64 !== '') <img src="{{ $logoBase64 }}" alt="Logo IAI DDI"> @endif
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
            <div class="judul-box"><div class="judul-utama">DAFTAR HADIR PESERTA</div></div>
            <table class="info-table">
                <tr>
                    <td class="kiri">Tema Kegiatan</td>
                    <td class="titik-2">:</td>
                    <td class="kanan" style="font-weight:700;">{{ strtoupper($kegiatan->judul ?? '-') }}</td>
                    <td class="kiri" style="width:34mm;">Tempat / Lokasi</td>
                    <td class="titik-2">:</td>
                    <td class="kanan">{{ $tempatVal }}</td>
                </tr>
                <tr>
                    <td class="kiri">Hari, Tanggal</td>
                    <td class="titik-2">:</td>
                    <td class="kanan">{{ formatTglIndo($kegiatan->tanggal_mulai ?? $kegiatan->tanggal ?? now()) }}</td>
                    <td class="kiri" style="width:34mm;">Penyelenggara</td>
                    <td class="titik-2">:</td>
                    <td class="kanan">{{ $penyelenggaraVal }}</td>
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
                    <td class="kiri" style="width:34mm;">Narasumber</td>
                    <td class="titik-2">:</td>
                    <td class="kanan">{{ $narasumberVal }}</td>
                </tr>
            </table>
        @else
            <div class="judul-box" style="margin-bottom:0.5mm;">
                <div class="judul-utama">DAFTAR HADIR PESERTA (LANJUTAN)</div>
                <div style="margin-top:0.4mm; font-size:10px; font-weight:600; line-height:1.1;">{{ strtoupper($kegiatan->judul ?? '-') }}</div>
                <div style="margin-top:0.2mm; font-size:8.5px; color:#333; font-style:italic; line-height:1.1;">Halaman {{ $halaman }} / {{ $totalHalaman }} &bull; Total: {{ $totalPeserta }} orang</div>
            </div>
        @endif

      <table class="daftar-hadir">

    <colgroup>
        <col style="width: 9mm;">
        <col style="width: 61mm;">
        <col style="width: 28mm;">
        <col style="width: 40mm;">
        <col style="width: 28mm;">
        <col style="width: 36mm;">
    </colgroup>

    <thead>
        <tr>
            <th>NO</th>
            <th>NAMA LENGKAP</th>
            <th>NPM / NUPTK</th>
            <th>PROGRAM STUDI</th>
            <th>STATUS</th>
            <th>TANDA TANGAN</th>
        </tr>
    </thead>

    <tbody>

        @foreach($pagePeserta as $i => $p)

            @php
                $no = $chunkIdx * $PAGE_SIZE + $i + 1;

                $prodiTampil = trim((string)($p->program_studi ?? ''));

                if ($prodiTampil === '') {
                    $prodiTampil =
                        ($p->jenis_peserta === 'dosen')
                        ? 'Dosen IAI DDI'
                        : '-';
                }

                $hadir = (bool)$p->status_hadir;

                $ket = $hadir
                    ? 'HADIR'
                    : 'TIDAK HADIR';

                $ketColor = $hadir
                    ? '#065f46'
                    : '#991b1b';

                $bgCell = $hadir
                    ? '#ecfdf5'
                    : '#fef2f2';

                try {
                    $id = (string)$p->nomor_identitas;
                } catch (\Throwable $e) {
                    $id =
                        $p->jenis_peserta === 'dosen'
                        ? (string)($p->nidn ?? '-')
                        : (string)($p->npm ?? '-');
                }
            @endphp

            <tr>

                {{-- NO --}}
                <td style="
                    text-align:center;
                    font-size:7px;
                    line-height:1;
                    white-space:nowrap;
                ">
                    {{ $no }}.
                </td>


                {{-- NAMA --}}
                <td style="
                    padding:0.3mm 0.8mm;
                    font-weight:700;
                    word-wrap:break-word;
                ">
                    {{ strtoupper($p->nama_lengkap) }}
                </td>


                {{-- NPM --}}
                <td style="
                    text-align:center;
                    padding:0.3mm 0.3mm;
                    font-family:'Courier New', monospace;
                    font-size:8px;
                ">
                    {{ $id }}
                </td>


                {{-- PRODI --}}
                <td style="
                    padding:0.3mm 0.5mm;
                    word-wrap:break-word;
                ">
                    {{ $prodiTampil }}
                </td>


                {{-- STATUS --}}
                <td style="
                    text-align:center;
                    padding:0.3mm 0.2mm;
                    font-weight:800;
                    background:{{ $bgCell }};
                    color:{{ $ketColor }};
                ">

                    {{ $ket }}

                    @if($hadir && !empty($p->waktu_hadir))

                        <div style="
                            font-weight:400;
                            color:#333;
                            font-size:6.5px;
                            margin-top:0.2mm;
                        ">
                            {{
                                \Illuminate\Support\Carbon::parse($p->waktu_hadir)
                                ->setTimezone('Asia/Makassar')
                                ->format('H:i')
                            }}
                            WITA
                        </div>

                    @endif

                    @if(!empty($p->keterangan))

                        <div style="
                            font-weight:400;
                            color:#555;
                            font-size:6.5px;
                            margin-top:0.2mm;
                        ">
                            {{
                                \Illuminate\Support\Str::limit(
                                    strip_tags($p->keterangan),
                                    40
                                )
                            }}
                        </div>

                    @endif

                </td>


                {{-- TANDA TANGAN --}}
                <td style="
                    width:36mm;
                    height:4.5mm;
                    text-align:center;
                    vertical-align:middle;
                    padding:0 0.8mm 0 0.8mm;
                    position:relative;
                ">
                    <div style="
                        width:92%;
                        border-bottom:1pt solid #000;
                        margin: 0 auto;
                        position: absolute;
                        bottom: 1.2mm;
                        left: 4%;
                    "></div>
                </td>

            </tr>

        @endforeach


        {{-- BARIS KOSONG --}}
        @for($b = 1; $b <= $blankNeeded; $b++)

            <tr>

                <td style="
                    text-align:center;
                    font-size:7px;
                ">
                    {{ $chunkIdx * $PAGE_SIZE + $chunkCount + $b }}.
                </td>

                <td>&nbsp;</td>

                <td>&nbsp;</td>

                <td>&nbsp;</td>

                <td>&nbsp;</td>

                <td style="
                    height:4.5mm;
                    text-align:center;
                    vertical-align:middle;
                    padding:0 0.8mm 0 0.8mm;
                    position:relative;
                ">
                    <div style="
                        width:92%;
                        border-bottom:1pt solid #000;
                        margin: 0 auto;
                        position: absolute;
                        bottom: 1.2mm;
                        left: 4%;
                    "></div>
                </td>

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
                        <div class="ttd-garis">&nbsp;</div>
                        <div class="ttd-nama">{!! $ketuaPanitiaNama !== '' ? e(strtoupper($ketuaPanitiaNama)) : '_______________________________' !!}</div>
                        <div class="ttd-nip">{!! $ketuaPanitiaNip !== '' ? 'NIP. '.e($ketuaPanitiaNip) : 'NIP. _____________________' !!}</div>
                    </td>
                    <td class="ttd-col" style="width:50%;">
                        <div class="ttd-label">Sidrap, {{ $nowCetak->format('j') }} {{ bulanIndo($nowCetak->month) }} {{ $nowCetak->format('Y') }}</div>
                        <div class="ttd-sub">Pimpinan / Penanggung Jawab</div>
                        <div class="ttd-garis">&nbsp;</div>
                        <div class="ttd-nama">{!! $rektorNama !== '' ? e(strtoupper($rektorNama)) : '_______________________________' !!}</div>
                        <div class="ttd-nip">{!! $rektorNip !== '' ? 'NIP. '.e($rektorNip) : 'Rektor IAI DDI Sidrap' !!}</div>
                    </td>
                </tr>
            </table>
        @endif
    </div>
@endforeach
</body>
</html>
