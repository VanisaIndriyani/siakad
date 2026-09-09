<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Transkrip Akademik - {{ $mahasiswa->nama_lengkap }}</title>
<style>
@page {
    /* F4 / Folio Indonesia Standard: 210mm × 330mm portrait */
    size: 210mm 330mm portrait;
    margin: 0 !important;
}

@page :blank {
    display: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

*, *:before, *:after { box-sizing: border-box; }
table, table th, table td { box-sizing: border-box; }

/* ⭐ GLOBAL ANTI KEPOTONG: FORCE SEMUA TEXT PANJANG WRAP BARIS OTOMATIS — TIDAK ADA ELLIPSIS 3 TITIK DAN TIDAK ADA CLIP KELUAR KERTAS */
* {
    word-break: normal !important;
    overflow-wrap: anywhere !important;
    word-wrap: break-word !important;
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
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    overflow: visible !important;
}

body::after, .wrap::after, .transcript-paper::after {
    content: '' !important;
    display: none !important;
    clear: both;
}

.transcript-paper {
    width: 210mm;
    height: auto;
    min-height: 0 !important;
    max-height: none;
    background: #ffffff;
    color: #000000;
    /* MARGIN LUAR DIPERKECIL (sebelumnya 8/12/8/12 teralu lebar):
       ATAS 7mm · KANAN 9mm · BAWAH 7mm · KIRI 9mm
       Inner width = 210 - 9 - 9 = 192mm → isi lebih penuh tapi masih 9mm jarak aman = TIDAK KEPOTONG
    */
    padding: 7mm 9mm 7mm 9mm;
    margin: 0 !important;
    box-sizing: border-box;
    font-family: 'Times New Roman', Times, serif;
    overflow: visible !important;
    page-break-after: auto;
    page-break-inside: auto;
}

/* ====== SEMUA CLASS DI BAWAH INI PERSIS 100% COPY DARI show.blade.php L544-L741 (TIDAK ADA DIKALI 0.9 LAGI) ====== */
.wrap { width: 100%; }

.kop-wrap { width: 100%; text-align: center; color: #000000; }
.kop-logo-center { width: 100%; text-align: center; margin-bottom: 8px; }
/* PERSIS SHOW L549: 110px × 110px (SEBELUMNYA 98px) — tambah display:inline-block border:0 (khusus DomPDF img tidak ada border putih) */
.kop-logo-center img { width: 110px; height: 110px; object-fit: contain; display: inline-block; border: 0; margin: 0; padding: 0; }

/* PERSIS SHOW L551 L555: kop-title 20px + 19px (SEBELUMNYA 18+17) */
.kop-title-a {
    font-size: 20px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.18; margin: 2px 0 0; padding: 0; color: #000000;
}
.kop-title-a2 { margin-top: 1px; }
.kop-title-b {
    font-size: 19px; font-weight: 800; letter-spacing: 0.7px; line-height: 1.18; margin: 2px 0 0; padding: 0; color: #000000;
}
/* PERSIS SHOW L558: 11px (SEBELUMNYA 9px) */
.kop-terakreditasi {
    font-size: 11px; margin-top: 6px; color: #000000; text-align: center; letter-spacing: 0.1px;
}
/* PERSIS SHOW L561: 10.5px (SEBELUMNYA 8.5px) */
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

/* PERSIS SHOW L576-L581: judul-text 16px judul-nomor 9.2px (SEBELUMNYA 14+8) */
.judul-box { text-align: center; margin-top: 10px; }
.judul-text {
    font-size: 16px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
    text-decoration: none; color: #000000;
}
.judul-nomor { font-size: 9.2px; margin-top: 1px; color: #000000; }

/* PERSIS SHOW L583-L615: biodata font 9.5px padding 1.5 10 1.5 0 — width 100% FULL (isi padat, global wrap masih ada jadi TIDAK KEPOTONG) */
.biodata {
    width: 100%; margin-top: 10px; border-collapse: collapse;
    font-size: 9.5px; color: #000000; table-layout: fixed;
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
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    word-wrap: break-word !important;
}
.biodata td.bio-value.right-val {
    width: 30%;
}
/* bio-val = display inline BUKAN inline-block, DomPDF sering masalah inline-block membesar keluar box */
.bio-val { font-weight: 700; color: #000000; display: inline !important; }

/* NILAI WIDTH 100% font 8.2px th padding 4 2 td padding 2 3 — FULL lebar, global wrap TETAP AMAN anti kepotong */
table.nilai {
    width: 100%; border-collapse: collapse; margin-top: 10px;
    font-size: 8.2px; color: #000000; table-layout: fixed;
}
table.nilai th {
    border: 1px solid #000; background: #e0f2ea; font-weight: 700; letter-spacing: 0.15px;
    padding: 4px 2px; vertical-align: middle; line-height: 1.15; text-align: center;
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
table.nilai td.ujian-left-spacer,
table.nilai td.ujian-left-spacer-cell,
table.nilai td.ujian-right-spacer,
table.nilai td.ujian-right-title,
table.nilai td.ujian-right-title-sks,
table.nilai td.ujian-right-title-nilai,
table.nilai td.ujian-right-title-m { background: #ffffff !important; }
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

/* RINGKASAN font 9.5px val 9.8px — width 100% FULL (inner width 192mm masih jauh dari pinggir = aman). VALUE NORMAL wrap, LABEL nowrap. */
.ringkasan {
    width: 100%; margin-top: 9px; border-collapse: collapse;
    font-size: 9.5px; color: #000000; table-layout: auto;
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
/* td.val = nilai pendek (IPK 3,11 / Predikat Memuaskan) — BOLEH wrap kalau panjang (white-space NORMAL, tidak di-nowrap kayak sebelumnya yang bikin kepotong!) */
.ringkasan td.val   {
    font-weight: 800; color: #000000; font-size: 9.8px; width: auto;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    word-wrap: break-word !important;
}
/* td.val-judul = JUDUL SKRIPSI BISA 2-3 BARIS! FORCE wrap normal, TIDAK di-clipped / dipenggal ellipsis */
.ringkasan td.val-judul {
    text-align: left; color: #000000; line-height: 1.28; padding: 1.5px 0 1.5px 0;
    vertical-align: top; width: auto;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    word-wrap: break-word !important;
    word-break: normal !important;
}

/* PERSIS SHOW L704-L741: TTD + FOTO (28mm kolom, foto 24×32mm, ttd font 9.5, nama marginTop 48px) (SEBELUMNYA 25mm kolom, foto 22×30, font 8.2, nama 40px) */
.ttd-foto-wrapper {
    width: 100%; margin: 0 !important; border-collapse: collapse;
    padding-left: 0 !important; /* DIV LUAR yang handle geser ke kanan, TABLE INI TIDAK USAH PADDING LAGI, biar tidak konflik */
}
.ttd-foto-wrapper td { vertical-align: top; padding: 0; }
.ttd-foto-col {
    width: 28mm; padding-right: 2mm;
}
.ttd-foto-box {
    width: 24mm; height: 32mm; /* foto 24x32 show L713 */
    border: 1px solid #333; background: #fdfdfd;
    overflow: hidden; box-sizing: border-box;
    position: relative;
    margin: 0; /* FOTO RATA KIRI FULL DI KOLOM KIRI */
}
.ttd-foto-box img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.ttd-foto-empty {
    position: absolute; inset: 0;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: #888; font-size: 10.5px; font-weight: 400;
    line-height: 1.25; text-align: center;
    background: #ffffff;
}
.ttd-col-wrapper { width: auto; }
.ttd-box {
    width: 100%; margin-top: 0; border-collapse: collapse;
    font-size: 9.3px; color: #000000;
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
@php
/* =========================================================
   LOGO BASE64 (ganti $logoWeb di show — karena DomPDF tidak bisa pakai asset() URL http)
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

/* =========================================================
   FOTO MAHASISWA BASE64 (pertahankan variabel $fotoMahasiswa dari controller — SUDAH base64)
========================================================= */
$fotoSrcFinal = $fotoMahasiswa ?? null;
if (empty($fotoSrcFinal) && !empty($mahasiswa->foto_path)) {
    try {
        $relPath = trim(str_replace(['/', '\\'], '/', (string)$mahasiswa->foto_path), '/');
        $absPath = public_path('storage/' . $relPath);
        if (@file_exists($absPath) && @is_file($absPath) && @is_readable($absPath)) {
            $sz = @filesize($absPath);
            if ($sz > 200 && $sz < 10000000) {
                $data = @file_get_contents($absPath);
                if ($data && strlen($data) > 200) {
                    $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
                    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'gif') ? 'image/gif' : 'image/jpeg');
                    $fotoSrcFinal = 'data:' . $mime . ';base64,' . base64_encode($data);
                }
            }
        }
    } catch (\Throwable $e) { $fotoSrcFinal = null; }
}

/* =========================================================
   DATA UJIAN — SAMA PERSIS show.blade.php L203-L206
========================================================= */
$ujianKompre = $ujianKompre ?? [];
$ujianAda = array_values(array_filter(array_map(fn($v) => trim((string)$v), $ujianKompre), fn($v) => $v !== ''));
$ujianCount = count($ujianAda);
@endphp

<div class="transcript-paper">
    <div class="wrap">
        {{-- ===== KOP SURAT — SAMA PERSIS show L211-L246, hanya logo diganti base64 ===== --}}
        <div class="kop-wrap">
            <div class="kop-logo-center">
                @if($logoFinalSrc)
                    <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI Sidrap" width="98" height="98">
                @endif
            </div>
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
        </div>

        {{-- ===== JUDUL TRANSKRIP — SAMA show L249-L252 ===== --}}
        <div class="judul-box">
            <div class="judul-text">Transkrip Akademik</div>
            <div class="judul-nomor">Nomor : {{ $nomorTranskrip }}</div>
        </div>

        {{-- ===== BIODATA — SAMA PERSIS show.blade.php L255-L286 (4 KOLOM, TANPA colgroup, class right-label & right-val) ===== --}}
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
                <td class="bio-value"><span class="bio-val">{{ $mahasiswa->nik ?? '-' }}</span></td>
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

@php
    /* ============== INJECT DUMMY 16 MK (TEST SAMPAI 91 MK TOTAL) — UNTUK TEST SHOW & PDF ============== */
    $dummyList = [];
    $gradeMap = ['A'=>4,'A-'=>3.7,'B+'=>3.3,'B'=>3,'B-'=>2.7,'C+'=>2.3,'C'=>2,'D'=>1,'E'=>0];
    $prodiDummy = ['Ekonomi Syariah','Perbankan Syariah','Perbankan Syariah','Ekonomi Syariah','Perbankan Syariah','Ekonomi Syariah','Perbankan Syariah','Perbankan Syariah','Ekonomi Syariah','Perbankan Syariah','Perbankan Syariah','Ekonomi Syariah','Perbankan Syariah','Ekonomi Syariah','Perbankan Syariah','Ekonomi Syariah'];
    $smtDummy   = [9,9,9,9,10,10,10,10,11,11,11,11,12,12,12,12];
    $mkNoDummy  = [1,2,3,4,1,2,3,4,1,2,3,4,1,2,3,4];
    $sksDummy   = [3,2,3,3,3,2,3,2,3,3,2,3,3,2,3,3];
    $gradeDummy = ['A-','C+','A','B+','A-','B','D','B+','A','A-','D','B-','A-','A','B','D'];
    for($z=0;$z<16;$z++){
        $g = $gradeDummy[$z];
        $gb = $gradeMap[$g] ?? 0;
        $sksZ = (int)$sksDummy[$z];
        $obj = new \stdClass();
        $obj->nama_mata_kuliah = $prodiDummy[$z].' - Semester '.$smtDummy[$z].' (MK '.$mkNoDummy[$z].')';
        $obj->sks = $sksZ;
        $obj->nilai_huruf = $g;
        $obj->nilai_m = round($sksZ * $gb, 2);
        $dummyList[] = $obj;
    }
    if (is_array($daftarMataKuliah)) {
        $daftarMataKuliah = array_merge($daftarMataKuliah, $dummyList);
    } elseif (is_object($daftarMataKuliah) && $daftarMataKuliah instanceof \Illuminate\Support\Collection) {
        $daftarMataKuliah = $daftarMataKuliah->merge(collect($dummyList));
    }
    unset($dummyList,$gradeMap,$prodiDummy,$smtDummy,$mkNoDummy,$sksDummy,$gradeDummy,$z,$g,$gb,$sksZ,$obj);
    /* ============== END INJECT DUMMY 16 MK ============== */
@endphp
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
        <table class="nilai" cellpadding="0" cellspacing="0"><colgroup><col style="width:4.0%;"><col style="width:27.6%;"><col style="width:5.4%;"><col style="width:5.4%;"><col style="width:5.4%;"><col style="width:4.0%;"><col style="width:27.6%;"><col style="width:5.4%;"><col style="width:5.4%;"><col style="width:5.4%;"></colgroup><thead><tr><th class="num">NO</th><th class="mk">MATA KULIAH</th><th class="sks">SKS</th><th class="nilaih">NILAI</th><th class="m">M</th><th class="num">NO</th><th class="mk">MATA KULIAH</th><th class="sks">SKS</th><th class="nilaih">NILAI</th><th class="m">M</th></tr></thead><tbody>
@for($i = 0; $i < $maxAtas; $i++)
    @php
        /* SAMA PERSIS show L321-L332 (tidak diubah) */
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
                    <td class="nilaih">{{ $mutuL }}</td>
                    <td class="m">{{ $nhL }}</td>
                    <td class="num">{{ $noR }}</td>
                    <td class="mk">{{ $namaR }}</td>
                    <td class="sks">{{ $sksR }}</td>
                    <td class="nilaih">{{ $mutuR }}</td>
                    <td class="m">{{ $nhR }}</td>
                </tr>
@endfor

@for($bi = 0; $bi < $barisBawah; $bi++)
    @php
        /* SAMA PERSIS show L350-L367 */
        $LL = $mkBawahKiri[$bi] ?? null;
        $namaLL = $LL ? $LL->nama_mata_kuliah : '';
        $sksLL = $LL ? ($LL->sks == 0 ? '0' : $LL->sks) : '';
        $nhLL = $LL ? ($LL->nilai_huruf !== '' ? $LL->nilai_huruf : '') : '';
        $mutuLL = $LL ? ($LL->nilai_m > 0 ? rtrim(rtrim(number_format($LL->nilai_m, 2, '.', ''), '0'), '.') : ($LL->nilai_huruf !== '' ? '0' : '')) : '';
        $noLanjutTampil = $LL ? ($noAwalKanan + count($kananAtas) + $bi + 1) : '';
        if ($bi === 0) { $jenisBaris = 'jumlah'; }
        elseif ($bi === 1) { $jenisBaris = 'spacer'; }
        elseif ($bi === 2) { $jenisBaris = 'ujian-head'; }
        else { $jenisBaris = 'ujian-row'; $uIdx = $bi - 3; $uNama = $ujianKompre[$uIdx] ?? ''; $uNo = $uIdx + 1; }
    @endphp
    @if($jenisBaris === 'jumlah')
                <tr class="jumlah">
                    <td class="num left-col">{{ $noLanjutTampil }}</td>
                    <td class="mk left-col">{{ $namaLL }}</td>
                    <td class="sks left-col">{{ $sksLL }}</td>
                    <td class="nilaih left-col">{{ $mutuLL }}</td>
                    <td class="m left-col">{{ $nhLL }}</td>
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
                    <td class="nilaih left-col">{{ $mutuLL }}</td>
                    <td class="m left-col">{{ $nhLL }}</td>
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
                    <td class="nilaih left-col">{{ $mutuLL }}</td>
                    <td class="m left-col">{{ $nhLL }}</td>
                    <td class="num ujian-right-spacer"></td>
                    <td class="mk ujian-left-title" colspan="4">Ujian Kompetensi</td>
                </tr>
    @else
                <tr class="ujian-row">
                    <td class="num left-col">{{ $noLanjutTampil }}</td>
                    <td class="mk left-col">{{ $namaLL }}</td>
                    <td class="sks left-col">{{ $sksLL }}</td>
                    <td class="nilaih left-col">{{ $mutuLL }}</td>
                    <td class="m left-col">{{ $nhLL }}</td>
                    <td class="num">{{ $uNo }}</td>
                    <td class="mk">{{ $uNama }}</td>
                    <td class="sks">0</td>
                    <td class="nilaih">0</td>
                    <td class="m">A</td>
                </tr>
    @endif
@endfor
            </tbody>
        </table>

        {{-- ===== RINGKASAN — SAMA PERSIS show L424-L445 ===== --}}
        <table class="ringkasan" cellpadding="0" cellspacing="0">
            <colgroup>
                <col style="width:290px;"><col style="width:22px;"><col style="width:auto;">
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

        {{-- ===== FOTO + TANDA TANGAN — SAMA show L448-L476, HANYA padding-left dinaikkan 90→94mm biar foto lebih ke kanan (sesuai spec no.5) ===== --}}
        <div style="page-break-inside: avoid; padding-left:94mm !important; margin-top:6px !important;">
            <table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0" style="padding-left:0 !important; margin:0 !important; border-collapse: collapse;">
                <tr>
                    <td class="ttd-foto-col" style="vertical-align: top; padding-top: 0;">
                        <div class="ttd-foto-box" style="margin-top: 0;">
                            @if($fotoSrcFinal)
                                <img src="{{ $fotoSrcFinal }}" alt="Foto {{ $mahasiswa->nama_lengkap }}">
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
</body>
</html>