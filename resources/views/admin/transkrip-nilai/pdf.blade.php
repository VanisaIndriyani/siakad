<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transkrip Akademik - {{ $mahasiswa->nama_lengkap }}</title>
    <style>
        *, *:before, *:after { box-sizing: border-box; }
        table, table th, table td { box-sizing: border-box; }

        /* =============================================================
           CSS UKURAN KERTAS & PAPER DIPAKSA 100% SAMA DENGAN
           @page + @media print DI show.blade.php (LINE 718-757)
           ============================================================= */
        @page {
            size: 210mm 330mm;
            margin: 0;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff;
            -webkit-text-size-adjust: 100%;
        }
        body {
            width: 210mm !important;
            height: 330mm !important;
            min-height: 330mm !important;
            max-height: 330mm !important;
            background: #ffffff !important;
            color: #000000;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            line-height: 1.18;
            font-size: 10pt;
            overflow: hidden !important;
        }

        /* ===== .transcript-paper PERSIS show.blade.php @media print L740-L749 (padding 8mm hemat tinggi untuk mencegah halaman 2) ===== */
        .transcript-paper {
            width: 210mm !important;
            height: 330mm !important;
            min-height: 330mm !important;
            max-height: 330mm !important;
            background: #ffffff !important;
            color: #000000;
            margin: 0 !important;
            padding: 8mm 13mm 8mm !important;
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            overflow: hidden !important;
            page-break-inside: auto;
            box-shadow: none !important;
            border-radius: 0 !important;
        }

        .no-break { page-break-inside: avoid !important; }

        /* ====== SEMUA CLASS DI BAWAH INI = PERSIS 1:1 DARI show.blade.php (USER LOCK LAYOUT) ====== */
        .kop-wrap { width: 100%; text-align: center; color: #000000; }
        .kop-logo-center { width: 100%; text-align: center; margin-bottom: 2px; }
        .kop-logo-center img { width: 78px; height: 78px; object-fit: contain; display: inline-block; }
        .kop-title-a {
            font-size: 18px; font-weight: 800; letter-spacing: 0.8px; line-height: 1.2; margin: 2px 0 0; padding: 0; color: #000000;
        }
        .kop-title-a2 { margin-top: 1px; }
        .kop-title-b {
            font-size: 17px; font-weight: 800; letter-spacing: 0.8px; line-height: 1.2; margin: 2px 0 0; padding: 0; color: #000000;
        }
        .kop-terakreditasi {
            font-size: 9px; margin-top: 3px; color: #000000; text-align: center; letter-spacing: 0.1px;
        }
        .kop-alamat-line {
            font-size: 9px; margin-top: 2px; line-height: 1.2; color: #000000; text-align: center;
        }
        .kop-email-web { margin-top: 1px; font-size: 9px; }
        .kop-line-double {
            margin-top: 2px;
            width: 100%;
            display: block;
        }
        .kop-line-double .kop-line-top {
            width: 100%; height: 2px; background: #000000;
        }
        .kop-line-double .kop-line-bottom {
            width: 100%; height: 1.5px; background: #000000; margin-top: 2px;
        }

        .judul-box { text-align: center; margin-top: 6px; }
        .judul-text {
            font-size: 13px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            text-decoration: none; color: #000000;
        }
        .judul-nomor { font-size: 8.5px; margin-top: 1px; color: #000000; }

        .biodata {
            width: 100%; margin-top: 6px; border-collapse: collapse;
            font-size: 8.5px; color: #000000; table-layout: fixed;
        }
        .biodata td { vertical-align: top; padding: 0; line-height: 1.2; }
        .biodata td.bio-label {
            width: 25%;
            padding: 1px 8px 1px 0;
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
            top: 1px;
            display: inline-block;
            color: #000000;
        }
        .biodata td.bio-value {
            width: 25%;
            padding: 1px 0 1px 4px;
            color: #000000;
        }
        .biodata td.bio-value.right-val {
            width: 30%;
        }
        .bio-val { font-weight: 700; color: #000000; display: inline-block; }

        table.nilai {
            width: 100%; border-collapse: collapse; margin-top: 6px;
            font-size: 7.8px; color: #000000; table-layout: fixed;
        }
        table.nilai th {
            border: 1px solid #000; background: #e0f2ea; font-weight: 700; letter-spacing: 0.15px;
            padding: 3px 2px; vertical-align: middle; line-height: 1.15; text-align: center;
        }
        table.nilai th.mk { text-align: left; padding: 3px 4px; width: 31%; }
        table.nilai th.num { width: 4%; padding: 3px 2px; }
        table.nilai th.sks { width: 5%; padding: 3px 2px; }
        table.nilai th.nilaih { width: 5%; padding: 3px 2px; }
        table.nilai th.m { width: 5%; padding: 3px 2px; }
        table.nilai td {
            border: 1px solid #000; padding: 2px 2px; vertical-align: middle;
            line-height: 1.15; text-align: center; color: #000000;
        }
        table.nilai td.mk { text-align: left; padding: 2px 4px; width: 31%; }
        table.nilai td.num { width: 4%; padding: 2px 2px; }
        table.nilai td.sks { width: 5%; padding: 2px 2px; }
        table.nilai td.nilaih { width: 5%; font-weight: 700; padding: 2px 2px; }
        table.nilai td.m { width: 5%; padding: 2px 2px; }
        table.nilai tr.jumlah td {
            background: #ffffff !important; font-weight: 700; padding: 2px 4px;
            letter-spacing: 0.15px; line-height: 1.15;
        }
        table.nilai tr.jumlah td.mk { text-align: center; }
        table.nilai tr.jumlah td.jumlah-dashed {
            background: #ffffff !important;
            border-top: 1px dashed #000000 !important;
            border-bottom: none !important;
        }
        table.nilai tr.ujian-head td {
            background: #ffffff !important; font-weight: 700; letter-spacing: 0.15px;
            padding: 2px 4px; line-height: 1.15; font-size: 7.8px;
        }
        table.nilai td.ujian-left-title { text-align: left; padding-left: 6px !important; }
        table.nilai td.ujian-left-spacer,
        table.nilai td.ujian-left-spacer-cell,
        table.nilai td.ujian-right-spacer,
        table.nilai td.ujian-right-title,
        table.nilai td.ujian-right-title-sks,
        table.nilai td.ujian-right-title-nilai,
        table.nilai td.ujian-right-title-m { background: #ffffff !important; }
        table.nilai tr.spacer-row td {
            background: #ffffff !important; border: 1px solid #000000;
            height: 12px; padding: 0;
        }
        table.nilai tr.ujian-row td { font-size: 7.8px; padding: 2px 4px; line-height: 1.15; }

        table.nilai tr.jumlah td.left-col,
        table.nilai tr.spacer-row td.left-col,
        table.nilai tr.ujian-head td.left-col,
        table.nilai tr.ujian-row td.left-col {
            background: #ffffff !important;
            font-weight: 400 !important;
            padding: 2px 2px !important;
            text-align: center !important;
            letter-spacing: 0 !important;
        }
        table.nilai tr.jumlah td.mk.left-col,
        table.nilai tr.spacer-row td.mk.left-col,
        table.nilai tr.ujian-head td.mk.left-col,
        table.nilai tr.ujian-row td.mk.left-col {
            text-align: left !important;
            padding: 2px 4px !important;
        }

        .ringkasan {
            width: 100%; margin-top: 4px; border-collapse: collapse;
            font-size: 8.5px; color: #000000; table-layout: auto;
        }
        .ringkasan td { vertical-align: top; padding: 1px 0; line-height: 1.2; }
        .ringkasan td.label {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 8px;
        }
        .ringkasan td.label-top {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding: 1px 8px 0 0;
        }
        .ringkasan td.sep   { width: auto; text-align: left; padding-right: 6px; }
        .ringkasan td.sep-top { width: auto; text-align: left; padding: 1px 6px 0 0; }
        .ringkasan td.val   { font-weight: 800; color: #000000; font-size: 8.8px; width: auto; white-space: nowrap; }
        .ringkasan td.val-judul {
            text-align: left; color: #000000; line-height: 1.2; padding: 1px 0 1px 0;
            vertical-align: top; width: auto;
        }

        /* Tabel TTD + FOTO: 2 kolom lebar 50% masing-masing → TIDAK GUNAKAN padding-left 90mm yang DOMPDF suka hitung beda */
        .ttd-foto-grid {
            width: 100%; border-collapse: collapse; margin-top: 4px !important;
            padding: 0 !important;
        }
        .ttd-foto-grid td {
            width: 50%; vertical-align: top; padding: 0;
        }
        .ttd-foto-col-inner {
            width: 28mm;
            margin-left: auto;
            margin-right: 0;
            padding: 0 !important;
            text-align: left;
        }
        .ttd-foto-wrapper {
            width: 100%; margin: 0 !important; border-collapse: collapse;
            padding-left: 0 !important;
        }
        .ttd-foto-wrapper td { vertical-align: top; padding: 0; }
        .ttd-foto-col {
            width: 21mm; padding-right: 1mm;
        }
        .ttd-foto-box {
            width: 18mm; height: 25mm;
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
            display: block;
            padding: 4px 2px;
            text-align: center;
            color: #888; font-size: 9px; font-weight: 400;
            line-height: 1.2;
            background: #ffffff;
            box-sizing: border-box;
        }
        .ttd-col-wrapper { width: auto; }
        .ttd-box {
            width: 100%; margin-top: 0; border-collapse: collapse;
            font-size: 8.3px; color: #000000;
        }
        .ttd-box td { vertical-align: top; }
        .ttd-spacer-l { width: 0%; }
        .ttd-spacer-r { width: 0%; }
        .ttd-col { width: 100%; text-align: left; line-height: 1.22; color: #000000; padding-left: 0; font-size: 8.3px; }
        .ttd-jabatan { margin-top: 1px; font-weight: 800; letter-spacing: 0.15px; }
        .ttd-nama    { margin-top: 22px; font-weight: 800; text-decoration: underline; font-size: 8.3px; }
        .ttd-nidk    { margin-top: 1px; font-size: 7.8px; letter-spacing: 0.1px; }
    </style>
</head>
<body>
@php
    function __logoGlobFind($baseDirs, $relDir, $patterns) {
        if (!is_array($baseDirs) || !is_array($patterns)) return null;
        foreach ($baseDirs as $bd) {
            $bd = rtrim(str_replace('\\', '/', (string)$bd), '/');
            if ($bd === '') continue;
            $dir = $bd . '/' . trim(str_replace('\\', '/', (string)$relDir), '/');
            if (!@is_dir($dir)) continue;
            foreach ($patterns as $p) {
                try {
                    $matches = @glob($dir . '/' . $p, GLOB_NOSORT | GLOB_BRACE);
                } catch (\Throwable $e) {
                    $matches = false;
                }
                if (is_array($matches) && count($matches) > 0) {
                    foreach ($matches as $m) {
                        if (@is_file($m) && @is_readable($m)) {
                            return ['path' => $m, 'rel' => trim(str_replace('\\', '/', (string)$relDir), '/') . '/' . basename($m)];
                        }
                    }
                }
            }
        }
        return null;
    }

    $logoAbsPath = null;
    try {
        $baseDirs = [];
        try { $baseDirs[] = public_path(); } catch (\Throwable $e) {}
        try {
            $bp = rtrim(str_replace('\\', '/', base_path()), '/');
            if ($bp !== '') {
                $baseDirs[] = $bp . '/public';
                $baseDirs[] = $bp . '/public_html';
                $baseDirs[] = $bp . '/../public_html';
                $baseDirs[] = $bp . '/../../public_html';
            }
        } catch (\Throwable $e) {}
        try {
            if (isset($_SERVER['DOCUMENT_ROOT']) && is_string($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
                $dr = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
                if ($dr !== '') {
                    $baseDirs[] = $dr;
                    $baseDirs[] = $dr . '/../siakad/public';
                    $baseDirs[] = $dr . '/../siakad/public_html';
                }
            }
        } catch (\Throwable $e) {}
        try {
            if (isset($_SERVER['SCRIPT_FILENAME']) && is_string($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] !== '') {
                $sf = rtrim(str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']), '/');
                if (dirname($sf) !== '') $baseDirs[] = dirname($sf);
            }
        } catch (\Throwable $e) {}
        try {
            if (function_exists('getcwd')) {
                $cwd = @getcwd();
                if (is_string($cwd) && $cwd !== '') {
                    $cwd = rtrim(str_replace('\\', '/', $cwd), '/');
                    if ($cwd !== '') {
                        $baseDirs[] = $cwd . '/public';
                        $baseDirs[] = $cwd . '/public_html';
                    }
                }
            }
        } catch (\Throwable $e) {}

        $patterns = [
            '[Ll][Oo].[Jj][Pp][Ee][Gg]',
            '[Ll][Oo].[Jj][Pp][Gg]',
            '[Ll][Oo][Gg][Oo].[Jj][Pp][Ee][Gg]',
            '[Ll][Oo][Gg][Oo].[Jj][Pp][Gg]',
            '[Ll][Oo][Gg][Oo].[Pp][Nn][Gg]',
            'lo.jpeg',
            'lo.jpg',
            'logo.jpeg',
            'logo.jpg',
            'logo.png',
        ];

        $found = __logoGlobFind(array_values(array_unique($baseDirs)), 'img', $patterns);
        if ($found && isset($found['path'])) {
            $logoAbsPath = $found['path'];
        } else {
            $tryList = [
                public_path('img/lo.jpeg'),
                public_path('img/lo.jpg'),
                public_path('img/logo.jpeg'),
                public_path('img/logo.jpg'),
                public_path('img/logo.png'),
            ];
            foreach ($tryList as $t) {
                try {
                    if (@file_exists($t) && @is_file($t) && @is_readable($t)) {
                        $logoAbsPath = $t;
                        break;
                    }
                } catch (\Throwable $e) {}
            }
        }
    } catch (\Throwable $e) {
        $logoAbsPath = null;
    }

    $logoSrcFinal = null;
    if ($logoAbsPath && @is_file($logoAbsPath) && @is_readable($logoAbsPath)) {
        try {
            $ext = strtolower(pathinfo($logoAbsPath, PATHINFO_EXTENSION));
            if ($ext === 'jpg' || $ext === 'jpeg') $mime = 'image/jpeg';
            elseif ($ext === 'png') $mime = 'image/png';
            else $mime = 'image/jpeg';
            $data = @file_get_contents($logoAbsPath);
            if ($data && is_string($data) && strlen($data) > 0) {
                $logoSrcFinal = 'data:' . $mime . ';base64,' . base64_encode($data);
            }
        } catch (\Throwable $e) {
            $logoSrcFinal = null;
        }
    }

    if (!$logoSrcFinal) {
        try {
            $remoteUrl = config('app.url') ? rtrim((string)config('app.url'), '/') . '/img/lo.jpeg' : 'https://siakadiaiddisidrap.com/img/lo.jpeg';
            try {
                $ctx = @stream_context_create(['http' => ['timeout' => 4, 'ignore_errors' => true, 'user_agent' => 'Mozilla/5.0 (DomPDF)']]);
                $data = @file_get_contents($remoteUrl, false, $ctx);
                if ($data && is_string($data) && strlen($data) > 200) {
                    $logoSrcFinal = 'data:image/jpeg;base64,' . base64_encode($data);
                }
            } catch (\Throwable $e) {
                $logoSrcFinal = null;
            }
        } catch (\Throwable $e) {
            $logoSrcFinal = null;
        }
    }

    if (!$logoSrcFinal) {
        try {
            $ctx = @stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true, 'user_agent' => 'Mozilla/5.0 (DomPDF)']]);
            $data = @file_get_contents('https://siakadiaiddisidrap.com/img/lo.jpeg', false, $ctx);
            if ($data && is_string($data) && strlen($data) > 200) {
                $logoSrcFinal = 'data:image/jpeg;base64,' . base64_encode($data);
            }
        } catch (\Throwable $e) {}
    }

    $fotoSrcFinal = null;
    if (!empty($fotoMahasiswa) && is_string($fotoMahasiswa)) {
        if (str_starts_with($fotoMahasiswa, 'data:')) {
            $fotoSrcFinal = $fotoMahasiswa;
        } elseif (str_starts_with($fotoMahasiswa, 'http://') || str_starts_with($fotoMahasiswa, 'https://')) {
            try {
                $ctx = @stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true, 'user_agent' => 'Mozilla/5.0 (DomPDF)']]);
                $data = @file_get_contents($fotoMahasiswa, false, $ctx);
                if ($data && is_string($data) && strlen($data) > 200) {
                    $fotoSrcFinal = 'data:image/jpeg;base64,' . base64_encode($data);
                }
            } catch (\Throwable $e) {
                $fotoSrcFinal = null;
            }
        } else {
            try {
                $abs = ltrim((string)$fotoMahasiswa, '/');
                foreach ([public_path($abs), base_path($abs)] as $cand) {
                    try {
                        if (@file_exists($cand) && @is_file($cand) && @is_readable($cand)) {
                            $data = @file_get_contents($cand);
                            if ($data && is_string($data) && strlen($data) > 200) {
                                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                                $mime = in_array($ext, ['png','jpg','jpeg']) ? ($ext === 'png' ? 'image/png' : 'image/jpeg') : 'image/jpeg';
                                $fotoSrcFinal = 'data:' . $mime . ';base64,' . base64_encode($data);
                                break;
                            }
                        }
                    } catch (\Throwable $e) {}
                }
            } catch (\Throwable $e) {
                $fotoSrcFinal = null;
            }
        }
    }

    $half = (int) ceil(count($items) / 2);
    $left = array_slice($items, 0, $half);
    $right = array_slice($items, $half);
    $maxRows = max(count($left), count($right));
    if ($maxRows < 1) $maxRows = 5;

    $ujianKompre = $ujianKompre ?? [];
    $ujianAda = array_values(array_filter(array_map(fn($v) => trim((string)$v), $ujianKompre), fn($v) => $v !== ''));
    $ujianCount = count($ujianAda);
@endphp

<div class="transcript-paper">
    {{-- ===== KOP SURAT (LOGO DI TENGAH, SESUAI CONTOH WILDAH ASLI) ===== --}}
    <div class="kop-wrap">
        <div class="kop-logo-center">
            @if($logoSrcFinal)
                <img src="{{ $logoSrcFinal }}"
                     alt="Logo IAI DDI Sidrap"
                     width="110" height="110">
            @endif
        </div>
        <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
        <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
        <div class="kop-title-b">SIDENRENG RAPPANG</div>
        <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
        <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
        <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
        <div class="kop-line-double">
            <div class="kop-line-top"></div>
            <div class="kop-line-bottom"></div>
        </div>
    </div>

    {{-- ===== JUDUL TRANSKRIP ===== --}}
    <div class="judul-box">
        <div class="judul-text">Transkrip Akademik</div>
        <div class="judul-nomor">Nomor : {{ $nomorTranskrip }}</div>
    </div>

    {{-- ===== BIODATA 50/50 URUTAN SESUAI CONTOH WILDAH ===== --}}
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
            <td class="bio-label">NO. Ijazah</td>
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

    {{-- ===== TABEL NILAI (MK BIASA 2 PANEL → JUMLAH → SUB-PANEL UJIAN KOMPETENSI NOMOR URUT SENDIRI) ===== --}}
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

    {{-- ===== RINGKASAN IPK, PREDIKAT, JUDUL SKRIPSI (SEJAJAR VERTIKAL + JUDUL INDENT) ===== --}}
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

    {{-- ===== FOTO MAHASISWA 24mm × 32mm (KIRI BAWAH) + TANDA TANGAN DEKAN FAKULTAS (KANAN) ===== --}}
    <table class="ttd-foto-grid no-break" cellpadding="0" cellspacing="0">
        <tr>
            <td><!-- kolom kiri: KOSONG (sesuai show yang pakai padding-left 90mm → area kiri dibiarkan kosong) --></td>
            <td>
                <div class="ttd-foto-col-inner">
                    <table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0" style="padding-left:0 !important; margin:0 !important;">
                        <tr>
                            <td class="ttd-foto-col">
                                <div class="ttd-foto-box">
                                    <div class="ttd-foto-empty">Foto<br>3 × 4</div>
                                </div>
                            </td>
                            <td class="ttd-col-wrapper">
                                <table class="ttd-box" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td class="ttd-spacer-l"></td>
                                        <td class="ttd-col">
                                            <div>Pangkajene, {{ $tanggalTtdPartBody ?? (($mahasiswa->tanggal_lulus ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus) : now())->translatedFormat('d F Y')) }}</div>
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
            </td>
        </tr>
    </table>
</div>
</body>
</html>
