<html xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Transkrip Akademik - {{ $mahasiswa->nama_lengkap }}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <meta name="Originator" content="Microsoft Word 15">
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:Compatibility>
                <w:UseFELayout/>
            </w:Compatibility>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        /* KEBUTUHAN MSWORD SAJA: SET PAPER SIZE F4 (210 × 330mm) margin 0 */
        @page WordSection1 {
            size: 210mm 330mm;
            margin: 0 0 0 0;
            mso-page-orientation: portrait;
            mso-header-margin: 0;
            mso-footer-margin: 0;
        }
        div.WordSection1 { page: WordSection1; }
        *, *:before, *:after { box-sizing: border-box !important; mso-box-shadow: none; }
        table, table th, table td { box-sizing: border-box !important; mso-cellspacing: 0; }
        * {
            word-wrap: break-word !important;
            overflow-wrap: anywhere !important;
            white-space: normal !important;
            text-overflow: clip !important;
            overflow: visible !important;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 210mm !important;
            height: auto !important;
            min-height: 0 !important;
            background: #fff !important;
            color: #000 !important;
            font-family: 'Times New Roman', Times, serif;
            mso-default-props: yes;
            mso-ascii-font-family: 'Times New Roman';
            mso-hansi-font-family: 'Times New Roman';
            mso-bidi-font-family: 'Times New Roman';
            mso-font-kerning: 1.0pt;
            overflow: hidden !important;
        }
        body::after, .wrap::after, .transcript-paper::after {
            content: '' !important;
            display: none !important;
            clear: both;
        }
        .transcript-paper {
            width: 210mm !important;
            height: auto !important;
            min-height: 0 !important;
            max-height: none !important;
            background: #ffffff;
            color: #000000;
            /* PERSIS @media print show.blade.php L756-L768 YANG UDAH BENER: 8mm atas, 11mm kiri, 8mm bawah (kanan otomatis 11mm dari shorthand CSS) — SAMA PERSIS PDF */
            padding: 8mm 11mm 8mm 11mm !important;
            margin: 0 !important;
            box-sizing: border-box !important;
            font-family: 'Times New Roman', Times, serif;
            overflow: visible !important;
            mso-padding-alt: 8mm 11mm 8mm 11mm;
        }
        .wrap { width: 100%; }
        .kop-wrap { width: 100%; text-align: center; color: #000000; }
        .kop-logo-center { width: 100%; text-align: center; margin-bottom: 8px; }
        .kop-logo-center img { width: 110px; height: 110px; object-fit: contain; display: inline-block; }
        .kop-title-a {
            font-size: 20px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.18; margin: 2px 0 0; padding: 0; color: #000000;
        }
        .kop-title-a2 { margin-top: 1px; }
        .kop-title-b {
            font-size: 19px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.18; margin: 2px 0 0; padding: 0; color: #000000;
        }
        .kop-terakreditasi {
            font-size: 11px; margin-top: 6px; color: #000000; text-align: center; letter-spacing: 0.1px;
        }
        .kop-alamat-line {
            font-size: 10.5px; margin-top: 4px; line-height: 1.25; color: #000000; text-align: center;
        }
        .kop-email-web { margin-top: 2px; }
        .kop-line-double {
            margin-top: 3px;
            width: 100%;
            display: block;
        }
        .kop-line-double .kop-line-top {
            width: 100%; height: 2px; background: #000000;
        }
        .kop-line-double .kop-line-bottom {
            width: 100%; height: 1.5px; background: #000000; margin-top: 2px;
        }
        .judul-box { text-align: center; margin-top: 10px; }
        .judul-text {
            font-size: 16px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            text-decoration: none; color: #000000;
        }
        .judul-nomor { font-size: 9.2px; margin-top: 1px; color: #000000; }
        .biodata {
            width: 100%; margin-top: 10px; border-collapse: collapse;
            font-size: 9.5px; color: #000000; table-layout: fixed;
            mso-cellspacing: 0;
        }
        .biodata td { vertical-align: top; padding: 0; line-height: 1.3; }
        .biodata td.bio-label {
            width: 25%;
            padding: 1.5px 10px 1.5px 0;
            text-align: left;
            font-weight: 400;
            color: #000000;
            position: relative;
        }
        .biodata td.bio-label.right-label {
            width: 20%;
        }
        .biodata td.bio-label:after {
            content: ":";
            position: absolute;
            right: 0px;
            top: 1.5px;
            display: inline-block;
            color: #000000;
        }
        .biodata td.bio-value {
            width: 25%;
            padding: 1.5px 0 1.5px 6px;
            color: #000000;
        }
        .biodata td.bio-value.right-val {
            width: 30%;
        }
        .bio-val { font-weight: 700; color: #000000; display: inline !important; }
        table.nilai {
            width: 100%; border-collapse: collapse; margin-top: 10px;
            font-size: 8.2px; color: #000000; table-layout: fixed;
            mso-cellspacing: 0;
        }
        table.nilai th {
            border: 1px solid #000; background: #e0f2ea; font-weight: 700; letter-spacing: 0.15px;
            padding: 4px 2px; vertical-align: middle; line-height: 1.15; text-align: center;
            mso-background-themecolor: accent3;
            mso-background-tint: 40;
        }
        table.nilai th.mk { text-align: left; padding: 4px 5px; width: 29%; }
        table.nilai th.num { width: 4.5%; padding: 4px 2px; }
        table.nilai th.sks { width: 5.5%; padding: 4px 2px; }
        table.nilai th.nilaih { width: 5.5%; padding: 4px 2px; }
        table.nilai th.m { width: 5.5%; padding: 4px 2px; }
        table.nilai td {
            border: 1px solid #000; padding: 2px 3px; vertical-align: middle;
            line-height: 1.15; text-align: center; color: #000000;
        }
        table.nilai td.mk { text-align: left; padding: 2px 5px; width: 29%; }
        table.nilai td.num { width: 4.5%; padding: 2px 2px; }
        table.nilai td.sks { width: 5.5%; padding: 2px 2px; }
        table.nilai td.nilaih { width: 5.5%; font-weight: 700; padding: 2px 2px; }
        table.nilai td.m { width: 5.5%; padding: 2px 2px; }
        table.nilai tr.jumlah td {
            background: #ffffff !important; font-weight: 700; padding: 2.5px 5px;
            letter-spacing: 0.2px; line-height: 1.15;
        }
        table.nilai tr.jumlah td.mk { text-align: center; }
        table.nilai tr.jumlah td.jumlah-dashed {
            background: #ffffff !important;
            border-top: 1px dashed #000000 !important;
            border-bottom: none !important;
        }
        table.nilai tr.ujian-head td {
            background: #ffffff !important; font-weight: 700; letter-spacing: 0.15px;
            padding: 2.5px 5px; line-height: 1.15; font-size: 8.2px;
        }
        table.nilai td.ujian-left-title { text-align: left; padding-left: 7px !important; }
        table.nilai tr.spacer-row td {
            background: #ffffff !important; border: 1px solid #000000;
            height: 15px; padding: 0;
        }
        table.nilai tr.ujian-row td { font-size: 8.2px; padding: 2px 3px; line-height: 1.15; }
        table.nilai tr.jumlah td.left-col,
        table.nilai tr.spacer-row td.left-col,
        table.nilai tr.ujian-head td.left-col,
        table.nilai tr.ujian-row td.left-col {
            background: #ffffff !important;
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
        .ringkasan {
            width: 100%; margin-top: 9px; border-collapse: collapse;
            font-size: 9.5px; color: #000000; table-layout: auto;
            mso-cellspacing: 0;
        }
        .ringkasan td { vertical-align: top; padding: 1.5px 0; line-height: 1.28; }
        .ringkasan td.label {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 12px;
        }
        .ringkasan td.label-top {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding: 1.5px 12px 0 0;
        }
        .ringkasan td.sep   { width: auto; text-align: left; padding-right: 10px; }
        .ringkasan td.sep-top { width: auto; text-align: left; padding: 1.5px 10px 0 0; }
        .ringkasan td.val   { font-weight: 800; color: #000000; font-size: 9.8px; width: auto; white-space: nowrap; }
        .ringkasan td.val-judul {
            text-align: left; color: #000000; line-height: 1.28; padding: 1.5px 0 1.5px 0;
            vertical-align: top; width: auto;
        }
        .ttd-foto-wrapper {
            width: 100%; margin: 0 !important; border-collapse: collapse;
            padding-left: 0 !important;
            mso-cellspacing: 0;
        }
        .ttd-foto-wrapper td { vertical-align: top; padding: 0; }
        .ttd-foto-col {
            width: 28mm; padding-right: 2mm;
        }
        .ttd-foto-box {
            width: 24mm; height: 32mm;
            border: 1px solid #333; background: #fdfdfd;
            overflow: hidden; box-sizing: border-box;
            position: relative;
            margin: 0;
        }
        .ttd-foto-box img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .ttd-foto-empty {
            position: absolute; inset: 0;
            display: block; text-align: center;
            color: #888; font-size: 10.5px; font-weight: 400;
            line-height: 1.25;
            background: #ffffff;
            padding-top: 9mm;
        }
        .ttd-col-wrapper { width: auto; }
        .ttd-box {
            width: 100%; margin-top: 0; border-collapse: collapse;
            font-size: 9.3px; color: #000000;
            mso-cellspacing: 0;
        }
        .ttd-box td { vertical-align: top; }
        .ttd-spacer-l { width: 0%; }
        .ttd-spacer-r { width: 0%; }
        .ttd-col { width: 100%; text-align: left; line-height: 1.32; color: #000000; padding-left: 0; font-size: 9.5px; }
        .ttd-jabatan { margin-top: 3px; font-weight: 800; letter-spacing: 0.2px; }
        .ttd-nama    { margin-top: 48px; font-weight: 800; text-decoration: underline; font-size: 9.5px; }
        .ttd-nidk    { margin-top: 1px; font-size: 8.5px; letter-spacing: 0.1px; }
    </style>
</head>
<body>
<div class="WordSection1">
@php
/* =========================================================
   LOGO BASE64 (MS Word lebih stabil pakai base64 daripada URL asset hosting)
========================================================= */
$logoFinalSrc = null;
$logoCandidates = [
    public_path('img/lo.jpeg'),
    public_path('img/lo.jpg'),
    public_path('img/lo.png'),
    public_path('img/logo.jpeg'),
    public_path('img/logo.jpg'),
    public_path('img/logo.png'),
];
foreach ($logoCandidates as $lp) {
    if ($logoFinalSrc !== null) break;
    try {
        if (!$lp || !is_file($lp) || !is_readable($lp)) continue;
        $sizeRaw = @getimagesize($lp);
        $mimeRaw = is_array($sizeRaw) && !empty($sizeRaw['mime']) ? $sizeRaw['mime'] : '';
        $extRaw = strtolower(pathinfo($lp, PATHINFO_EXTENSION));
        $mime = '';
        if ($mimeRaw) {
            $mime = $mimeRaw;
        } else {
            if ($extRaw === 'png') $mime = 'image/png';
            elseif ($extRaw === 'gif') $mime = 'image/gif';
            else $mime = 'image/jpeg';
        }
        $contents = @file_get_contents($lp);
        if ($contents === false || $contents === '') continue;
        $logoFinalSrc = 'data:'.$mime.';base64,'.base64_encode($contents);
    } catch (\Throwable $e) { $logoFinalSrc = null; }
}
/* =========================================================
   FOTO MAHASISWA BASE64
========================================================= */
$fotoFinalSrc = null;
$fotoMahasiswa = $fotoMahasiswa ?? null;
if (!empty($fotoMahasiswa) && is_string($fotoMahasiswa)) {
    try {
        $fotoCandidates = [];
        $trim = ltrim($fotoMahasiswa, '/');
        if (strpos($fotoMahasiswa, '://') !== false) {
            $fotoCandidates[] = $fotoMahasiswa;
        }
        $fotoCandidates[] = public_path($trim);
        $fotoCandidates[] = public_path($fotoMahasiswa);
        foreach ($fotoCandidates as $fc) {
            if ($fotoFinalSrc !== null) break;
            try {
                $contents = null;
                $mime = null;
                if (strpos($fc, '://') !== false) {
                    $ch = @curl_init($fc);
                    if ($ch) {
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 SIAKAD-WORD');
                        $buf = curl_exec($ch);
                        $httpCode = 0;
                        $ct = '';
                        try { $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); } catch (\Throwable $e) {}
                        try { $ct = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE); } catch (\Throwable $e) {}
                        try { curl_close($ch); } catch (\Throwable $e) {}
                        if ($buf !== false && $buf !== '' && $httpCode >= 200 && $httpCode < 300) {
                            $contents = $buf;
                            if ($ct && strpos($ct, 'image/') === 0) $mime = trim(explode(';', $ct, 2)[0]);
                        }
                    }
                } else {
                    if (is_file($fc) && is_readable($fc)) {
                        $si = @getimagesize($fc);
                        if ($si && !empty($si['mime']) && strpos($si['mime'], 'image/') === 0) {
                            $buf = @file_get_contents($fc);
                            if ($buf !== false && $buf !== '') {
                                $contents = $buf;
                                $mime = $si['mime'];
                            }
                        }
                    }
                }
                if ($contents === null || $contents === '') continue;
                if ($mime === null || $mime === '') {
                    $off = null;
                    try { $off = @getimagesizefromstring($contents); } catch (\Throwable $e) { $off = null; }
                    if (is_array($off) && !empty($off['mime']) && strpos($off['mime'], 'image/') === 0) $mime = $off['mime'];
                    else $mime = 'image/jpeg';
                }
                $fotoFinalSrc = 'data:'.$mime.';base64,'.base64_encode($contents);
            } catch (\Throwable $e) { $fotoFinalSrc = null; }
        }
    } catch (\Throwable $e) { $fotoFinalSrc = null; }
}
/* =========================================================
   UJIAN KOMPETENSI LIST
========================================================= */
$ujianKompre = $ujianKompre ?? [];
if (!is_array($ujianKompre)) $ujianKompre = [];
$ujianCount = count($ujianKompre);
@endphp
<div class="transcript-paper">
    <div class="wrap">
        {{-- KOP SURAT — 100% SAMA PERSIS show.blade.php L221-L260 — LOGO $logoFinalSrc --}}
        <div class="kop-wrap">
            <div class="kop-logo-center">
                @if($logoFinalSrc)
                    <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI Sidrap" width="110" height="110">
                @endif
            </div>
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
            <div class="kop-line-double">
                <div class="kop-line-top"></div>
                <div class="kop-line-bottom"></div>
            </div>
        </div>
        {{-- JUDUL TRANSKRIP — 100% show.blade.php --}}
        <div class="judul-box">
            <div class="judul-text">Transkrip Akademik</div>
            <div class="judul-nomor">Nomor : {{ $nomorTranskrip }}</div>
        </div>
        {{-- BIODATA — 100% show.blade.php --}}
        <table class="biodata" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bio-label">Nama</td>
                <td class="bio-value"><span class="bio-val">{{ $mahasiswa->nama_lengkap }}</span></td>
                <td class="bio-label right-label">Program Pendidikan</td>
                <td class="bio-value right-val"><span class="bio-val">Strata Satu (S1)</span></td>
            </tr>
            <tr>
                <td class="bio-label">No. Pokok Mahasiswa</td>
                <td class="bio-value"><span class="bio-val">{{ $mahasiswa->npm ?? '-' }}</span></td>
                <td class="bio-label right-label">Fakultas</td>
                <td class="bio-value right-val"><span class="bio-val">{{ $mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan' }}</span></td>
            </tr>
            <tr>
                <td class="bio-label">No. Ijazah</td>
                <td class="bio-value"><span class="bio-val">{{ $noIjazah }}</span></td>
                <td class="bio-label right-label">Program Studi</td>
                <td class="bio-value right-val"><span class="bio-val">{{ $mahasiswa->program_studi ?? '-' }}</span></td>
            </tr>
            <tr>
                <td class="bio-label">Tempat / Tanggal Lahir</td>
                <td class="bio-value"><span class="bio-val">{{ $tempatTgl }}</span></td>
                <td class="bio-label right-label">No. SK BAN-PT</td>
                <td class="bio-value right-val"><span class="bio-val">{{ $skBanpt }}</span></td>
            </tr>
            <tr>
                <td class="bio-label">Tanggal, Bulan dan Tahun Lulus</td>
                <td class="bio-value"><span class="bio-val">{{ $tanggalLulus }}</span></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        {{-- TABEL NILAI 2 PANEL — 100% show.blade.php --}}
        <table class="nilai" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="num">NO</th>
                    <th class="mk">MATA KULIAH</th>
                    <th class="sks">SKS</th>
                    <th class="nilaih">NILAI</th>
                    <th class="m">M</th>
                    <th class="num">NO</th>
                    <th class="mk">MATA KULIAH</th>
                    <th class="sks">SKS</th>
                    <th class="nilaih">NILAI</th>
                    <th class="m">M</th>
                </tr>
            </thead>
            <tbody>
                @php
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
                @endphp
                @for($i = 0; $i < $maxAtas; $i++)
                    @php
                        $L = $kiriAtas[$i] ?? null;
                        $R = $kananAtas[$i] ?? null;
                        $noL = $L ? ($i + 1) : '';
                        $noR = $R ? ($noAwalKanan + $i + 1) : '';
                        $namaL = $L ? $L->nama_mata_kuliah : '';
                        $sksL = $L ? ($L->sks == 0 ? '0' : $L->sks) : '';
                        $nhL = $L ? ($L->nilai_huruf !== '' ? $L->nilai_huruf : '') : '';
                        $mutuL = $L ? ($L->nilai_m > 0 ? rtrim(rtrim(number_format($L->nilai_m, 2, '.', ''), '0'), '.') : ($L->nilai_huruf !== '' ? '0' : '')) : '';
                        $namaR = $R ? $R->nama_mata_kuliah : '';
                        $sksR = $R ? ($R->sks == 0 ? '0' : $R->sks) : '';
                        $nhR = $R ? ($R->nilai_huruf !== '' ? $R->nilai_huruf : '') : '';
                        $mutuR = $R ? ($R->nilai_m > 0 ? rtrim(rtrim(number_format($R->nilai_m, 2, '.', ''), '0'), '.') : ($R->nilai_huruf !== '' ? '0' : '')) : '';
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
                        $namaLL = $LL ? $LL->nama_mata_kuliah : '';
                        $sksLL = $LL ? ($LL->sks == 0 ? '0' : $LL->sks) : '';
                        $nhLL = $LL ? ($LL->nilai_huruf !== '' ? $LL->nilai_huruf : '') : '';
                        $mutuLL = $LL ? ($LL->nilai_m > 0 ? rtrim(rtrim(number_format($LL->nilai_m, 2, '.', ''), '0'), '.') : ($LL->nilai_huruf !== '' ? '0' : '')) : '';
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
                    <td class="m">{{ rtrim(rtrim(number_format($totalMutu, 2, '.', ''), '0'), '.') }}</td>
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
        {{-- RINGKASAN IPK/PREDIKAT/SKRIPSI — 100% show.blade.php --}}
        <table class="ringkasan" cellpadding="0" cellspacing="0">
            <colgroup>
                <col style="width:290px;">
                <col style="width:22px;">
                <col style="width:auto;">
            </colgroup>
            <tr>
                <td class="label">INDEKS PRESTASI KUMULATIF (IPK)</td>
                <td class="sep">:</td>
                <td class="val">{{ str_replace('.', ',', number_format($ipk, 2)) }}</td>
            </tr>
            <tr>
                <td class="label">PREDIKAT KELULUSAN</td>
                <td class="sep">:</td>
                <td class="val">{{ $predikat }}</td>
            </tr>
            <tr>
                <td class="label-top">JUDUL SKRIPSI</td>
                <td class="sep-top">:</td>
                <td class="val-judul">{{ $judulSkripsi }}</td>
            </tr>
        </table>
        {{-- FOTO + TANDA TANGAN — 100% show.blade.php L462 — FOTO pakai $fotoFinalSrc --}}
        <div style="page-break-inside: avoid; padding-left:90mm !important; margin-top:10px !important;">
            <table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0" style="padding-left:0 !important; margin:0 !important; border-collapse: collapse;">
                <tr>
                    <td class="ttd-foto-col" style="vertical-align: top; padding-top: 0;">
                        <div class="ttd-foto-box" style="margin-top: 0;">
                            @if($fotoFinalSrc)
                                <img src="{{ $fotoFinalSrc }}" alt="Foto {{ $mahasiswa->nama_lengkap }}">
                            @else
                                <div class="ttd-foto-empty">Foto<br>3 × 4</div>
                            @endif
                        </div>
                    </td>
                    <td class="ttd-col-wrapper">
                        <table class="ttd-box" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="ttd-spacer-l"></td>
                                <td class="ttd-col">
                                    <div>{{ $tanggalTtd }}</div>
                                    <div class="ttd-jabatan">{{ $ttdJabatan }}</div>
                                    <div class="ttd-nama">{{ $ttdNama }}</div>
                                    <div class="ttd-nidk">{{ $ttdNomorLabel }}. {{ $ttdNomor }}</div>
                                </td>
                                <td class="ttd-spacer-r"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</div>
</body>
</html>
