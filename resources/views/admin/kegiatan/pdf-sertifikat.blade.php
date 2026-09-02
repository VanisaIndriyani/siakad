<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SERTIFIKAT - {{ $peserta->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { margin: 0; }
        html, body {
            font-family: 'Times New Roman', Times, serif;
            width: 297mm;
            height: 210mm;
            background: #ffffff;
            color: #1a1a1a;
            overflow: hidden;
        }
        body { position: relative; }

        /* =========================================================
           BINGKAI 3 BARIS (EMAS - HIJAU - EMAS) — POSISI TEPAT
           ========================================================= */
        .frame-1 {
            position: absolute;
            top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
            border: 0.9mm solid #b08500;
            pointer-events: none;
        }
        .frame-2 {
            position: absolute;
            top: 7.5mm; left: 7.5mm; right: 7.5mm; bottom: 7.5mm;
            border: 0.18mm solid #0f6244;
            pointer-events: none;
        }
        .frame-3 {
            position: absolute;
            top: 8.3mm; left: 8.3mm; right: 8.3mm; bottom: 8.3mm;
            border: 0.35mm solid #caa02e;
            pointer-events: none;
        }

        /* =========================================================
           WATERMARK LOGO — KECIL, SANGAT TRANSPARAN, DI BAWAH TENGAH
           ========================================================= */
        .watermark {
            position: absolute;
            left: 50%;
            top: 56%;
            width: 50mm;
            height: 50mm;
            margin-left: -25mm;
            margin-top: -25mm;
            opacity: 0.022;
            background-repeat: no-repeat;
            background-position: center center;
            background-size: contain;
            z-index: 0;
            pointer-events: none;
        }

        /* =========================================================
           KONTEN UTAMA — 1 WRAPPER POSITION ABSOLUTE (100% DOM PDF OK!)
           AREA TEPAT:  di DALAM frame-3 (antara 8.3mm ~ 201.7mm → tingginya ~193.4mm)
           Kita set  top:9.5mm bottom:9mm → 191.5mm, overflow:hidden.
           Konten TIDAK PERNAH keluar dari sini, TIDAK PERNAH bikin halaman-2.
           ========================================================= */
        .content {
            position: absolute;
            z-index: 10;
            top: 9.5mm;
            left: 9.5mm;
            right: 9.5mm;
            bottom: 9mm;
            padding: 0.5mm 5mm 0.5mm 5mm;
            overflow: hidden;
        }

        /* ===== KOP SURAT ===== */
        .kop {
            width: 100%;
            border-collapse: collapse;
            padding-bottom: 1.3mm;
            margin-bottom: 1.8mm;
        }
        .kop td { vertical-align: middle; }
        .kop .logo-cell { width: 18mm; padding-right: 3.5mm; }
        .kop img { width: 16mm; height: 16mm; }
        .kop .text-cell { text-align: center; }
        .kop-n1 { font-size: 10.5pt; font-weight: 700; color: #0f6244; letter-spacing: 0.65pt; font-family: Georgia, 'Times New Roman', serif; }
        .kop-n2 { font-size: 7.8pt; color: #b08500; font-weight: 600; letter-spacing: 2.8pt; margin-top: 0.45mm; }
        .kop-n3 { font-size: 9pt; font-weight: 700; color: #0f6244; letter-spacing: 0.45pt; margin-top: 0.45mm; }
        .kop-ak { font-size: 7pt; color: #3f3f46; margin-top: 0.7mm; display: inline-block; }
        .kop-al { font-size: 7pt; color: #52525b; margin-top: 0.35mm; font-style: italic; }

        /* ===== TEMPAT TANGGAL ===== */
        .tempat-row { width: 100%; text-align: right; margin-top: 1mm; margin-bottom: 1.1mm; }
        .tempat-box {
            display: inline-block;
            background: linear-gradient(90deg, #caa02e0f, #caa02e28, #caa02e0f);
            padding: 0.4mm 3mm;
            font-weight: 600;
            color: #7c5e00;
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 8.3pt;
        }

        /* =========================================================
           JUDUL SERTIFIKAT — TANPA GARIS, TANPA DIAMOND, POLOS.
           ========================================================= */
        .judul { text-align: center; margin-top: 0mm; }
        .judul-besar {
            font-size: 23pt;
            font-weight: 900;
            letter-spacing: 6.5pt;
            color: #0f6244;
            font-family: Georgia, 'Times New Roman', serif;
            display: inline-block;
            padding: 0 2mm;
        }
        .judul-kecil {
            font-size: 8.2pt;
            letter-spacing: 5.2pt;
            color: #b08500;
            margin-top: 0.9mm;
            font-weight: 600;
        }

        /* ===== NOMOR SERTIFIKAT ===== */
        .nomor-wrap { margin: 1.4mm auto 1.2mm; text-align: center; }
        .nomor-label { font-size: 7.1pt; color: #6b7280; letter-spacing: 1.4pt; text-transform: uppercase; }
        .nomor-val {
            display: inline-block;
            margin-top: 0.4mm;
            padding: 0.4mm 5.5mm;
            background: linear-gradient(90deg, #fff, #fffbeb, #fff);
            font-family: 'Courier New', monospace;
            font-size: 9.2pt;
            font-weight: 700;
            color: #7c2d12;
            letter-spacing: 0.55pt;
        }

        /* ===== DIBERIKAN KEPADA ===== */
        .kepada { text-align: center; margin-top: 0.6mm; margin-bottom: 0.2mm; }
        .kepada-text {
            display: inline-block;
            padding: 0.25mm 5.5mm;
            background: linear-gradient(90deg, #ecfdf5, #d1fae5, #ecfdf5);
            color: #065f46;
            font-weight: 600;
            border-radius: 60mm;
            font-size: 8.3pt;
            letter-spacing: 1.1pt;
        }

        /* ===== NAMA PESERTA ===== */
        .nama-wrap {
            margin: 1.1mm auto 0.2mm;
            text-align: center;
            width: 100%;
        }
        .nama-text {
            display: inline-block;
            padding: 0.5mm 10mm 0.8mm;
            font-size: 21pt;
            font-weight: 900;
            color: #0f6244;
            letter-spacing: 0.95pt;
            font-family: Georgia, 'Times New Roman', serif;
            background: linear-gradient(90deg, #fff7cc 0%, #ffffff 20%, #ffffff 80%, #fff7cc 100%);
            max-width: 220mm;
            word-wrap: break-word;
        }
        .npm-wrap { text-align: center; margin-top: 0.3mm; }
        .npm-text {
            display: inline-block;
            background: #f8fafc;
            padding: 0.35mm 4mm;
            font-size: 8.1pt;
            color: #334155;
            font-family: 'Courier New', monospace;
        }

        /* ===== PEMBUKA ===== */
        .pembuka {
            text-align: center;
            margin: 1.4mm 16mm 0.5mm;
            font-size: 8.9pt;
            line-height: 1.4;
            color: #1f2937;
        }
        .kegiatan-judul {
            display: block;
            text-align: center;
            font-size: 11.5pt;
            font-weight: 800;
            color: #0f6244;
            padding: 0.6mm 9mm;
            margin: 0.3mm 12mm;
            letter-spacing: 0.22pt;
            font-style: italic;
            font-family: Georgia, 'Times New Roman', serif;
        }

        /* ===== INFO KEGIATAN TABLE ===== */
        .info-table {
            width: 100%;
            margin: 1mm auto 0.2mm;
            border-collapse: collapse;
            max-width: 185mm;
        }
        .info-table td {
            padding: 0.22mm 0;
            vertical-align: top;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .info-table .col-label {
            width: 62mm;
            color: #374151;
            padding-right: 1.8mm;
            padding-left: 2.5mm;
        }
        .info-table .col-sep {
            width: 3mm;
            color: #b08500;
            font-weight: 700;
            text-align: center;
        }
        .info-table .col-val {
            color: #111827;
            font-weight: 600;
            text-align: left;
        }

        /* ===== PENUTUP ===== */
        .penutup {
            text-align: center;
            margin: 2.5mm 16mm 1.2mm;
            font-size: 8.7pt;
            line-height: 1.5;
            color: #1f2937;
            font-style: italic;
        }

        /* =========================================================
           TANDA TANGAN — SPASI TAMBAH BANYAK (spacer 11mm + margin bawah posisi TTD)
           ========================================================= */
        .ttd-table {
            width: 100%;
            margin-top: 4.5mm;
            border-collapse: collapse;
        }
        .ttd-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 2mm;
            font-size: 8.1pt;
        }
        .ttd-pos {
            font-weight: 700;
            color: #0f6244;
            line-height: 1.35;
            margin-bottom: 1.4mm;
        }
        .ttd-pos small {
            display: block;
            color: #52525b;
            font-weight: 400;
            font-size: 7.3pt;
            margin-top: 0.25mm;
        }
        .ttd-spacer { height: 11mm; }
        .ttd-garis {
            border-top: 0.14mm solid #111827;
            width: 92%;
            margin: 0 auto;
            padding-top: 0.55mm;
        }
        .ttd-nama {
            font-weight: 800;
            color: #0f6244;
            font-size: 9pt;
            text-decoration: underline;
            text-underline-offset: 0.4mm;
        }
        .ttd-nip {
            font-size: 7.2pt;
            color: #52525b;
            margin-top: 0.3mm;
        }

        /* ===== FOOTER ===== */
        .footer-wrap {
            width: 100%;
            margin-top: 2mm;
            padding-top: 0.6mm;
            border-collapse: collapse;
        }
        .footer-wrap td {
            font-size: 7pt;
            color: #52525b;
            vertical-align: middle;
        }
        .footer-left { text-align: left; width: 70%; padding-left: 1.8mm; }
        .footer-right { text-align: right; width: 30%; padding-right: 1.8mm; }
        .verify-box {
            display: inline-block;
            font-family: 'Courier New', monospace;
            color: #0f6244;
            background: #f8fafc;
            padding: 0.35mm 1.8mm;
            font-weight: 600;
        }
    </style>
</head>
<body>

@php
/* =========================================================
   LOGO BASE64
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

$nomorSertif = $peserta->nomor_sertifikat
    ? $peserta->nomor_sertifikat
    : $kegiatan->generateNomorSertifikat($peserta);

$tanggalKegiatan = !empty($kegiatan->tanggal_kegiatan)
    ? \Illuminate\Support\Carbon::parse($kegiatan->tanggal_kegiatan)
    : null;

$waktu = '';
if (!empty($kegiatan->waktu_mulai)) {
    $waktu = substr($kegiatan->waktu_mulai,0,5);
    if (!empty($kegiatan->waktu_selesai)) $waktu .= ' s/d '.substr($kegiatan->waktu_selesai,0,5);
    $waktu .= ' WIB';
}

$verifyCode = strtoupper(substr(md5(($peserta->id ?? 'X').'|'.($kegiatan->id ?? 'Y').'|'.$nomorSertif),0,12));
$verifyCode = substr($verifyCode,0,4).'-'.substr($verifyCode,4,4).'-'.substr($verifyCode,8,4);

$jenis = Str::lower(trim($kegiatan->jenis_kegiatan ?? ''));
if ($jenis == 'workshop') {
    $judulKecil = 'CERTIFICATE OF COMPLETION';
} elseif ($jenis == 'pelatihan') {
    $judulKecil = 'CERTIFICATE OF TRAINING';
} elseif ($jenis == 'seminar' || $jenis == 'seminar nasional' || $jenis == 'webinar') {
    $judulKecil = 'CERTIFICATE OF ATTENDANCE';
} else {
    $judulKecil = 'CERTIFICATE OF PARTICIPATION';
}
@endphp

{{-- ============ BINGKAI (TANPA SUDUT / CORNER — SUMBER GARIS ANEH!) ============ --}}
<div class="frame-1"></div>
<div class="frame-2"></div>
<div class="frame-3"></div>

@if($logoFinalSrc)
    <div class="watermark" style="background-image: url('{{ $logoFinalSrc }}');"></div>
@endif

{{-- ============ KONTEN UTAMA: 1 WRAPPER ABSOLUTE EXACT SIZE ============ --}}
<div class="content">

    {{-- ===== KOP SURAT ===== --}}
    <table class="kop">
        <tr>
            <td class="logo-cell">
                @if($logoFinalSrc)
                    <img src="{{ $logoFinalSrc }}" alt="Logo IAI DDI">
                @else
                    <div style="width: 16mm; height: 16mm; border-radius: 50%;
                        background: linear-gradient(135deg,#0f6244,#059669);
                        display:flex;align-items:center;justify-content:center;
                        color:#fff;font-family:Georgia;font-weight:900;font-size:11pt;">IAI</div>
                @endif
            </td>
            <td class="text-cell">
                <div class="kop-n1">INSTITUT AGAMA ISLAM DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-n2">D D I</div>
                <div class="kop-n3">SIDENRENG RAPPANG</div>
                <div class="kop-ak">TERAKREDITASI INSTITUSI &mdash; SK. BAN-PT No. 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
                <div class="kop-al">Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang &bull; E-mail: iaiddisidrap@gmail.com &bull; Website: www.yppddisrapp.ac.id</div>
            </td>
        </tr>
    </table>

    {{-- ===== TEMPAT TANGGAL ===== --}}
    <div class="tempat-row">
        <span class="tempat-box">
            Sidrap, {{ $tanggalKegiatan ? $tanggalKegiatan->isoFormat('D MMMM Y') : now()->isoFormat('D MMMM Y') }}
        </span>
    </div>

    {{-- ===== JUDUL SERTIFIKAT (POLOS, TANPA GARIS APAPUN) ===== --}}
    <div class="judul">
        <div class="judul-besar">SERTIFIKAT</div>
        <div class="judul-kecil">{{ $judulKecil }}</div>
    </div>

    {{-- ===== NOMOR SERTIFIKAT ===== --}}
    <div class="nomor-wrap">
        <div class="nomor-label">Nomor Sertifikat</div>
        <br />
        <div class="nomor-val">{{ $nomorSertif }}</div>
    </div>

    {{-- ===== DIBERIKAN KEPADA ===== --}}
    <div class="kepada">
        <span class="kepada-text">Dengan ini Diberikan Kepada :</span>
    </div>

    {{-- ===== NAMA PESERTA ===== --}}
    <div class="nama-wrap">
        <div class="nama-text">{{ strtoupper($peserta->nama_lengkap) }}</div>
    </div>
    <div class="npm-wrap">
        @if(trim($peserta->npm ?? '') !== '' || trim($peserta->program_studi ?? '') !== '' || trim($peserta->fakultas ?? '') !== '')
            <span class="npm-text">
                @if(trim($peserta->npm ?? '') !== '') NPM. {{ $peserta->npm }} @endif
                @if(trim($peserta->npm ?? '') !== '' && trim($peserta->program_studi ?? '') !== '') &nbsp;&bull;&nbsp; @endif
                @if(trim($peserta->program_studi ?? '') !== '') {{ $peserta->program_studi }} @endif
                @if(trim($peserta->fakultas ?? '') !== '' && trim($peserta->program_studi ?? '') !== '') &mdash; {{ $peserta->fakultas }} @endif
            </span>
        @endif
    </div>

    {{-- ===== PEMBUKA ===== --}}
    <div class="pembuka">
        Atas partisipasi aktif dan kehadirannya sebagai peserta dalam kegiatan
    </div>
    <div class="kegiatan-judul">&ldquo;{{ strtoupper($kegiatan->judul) }}&rdquo;</div>

    {{-- ===== INFO KEGIATAN ===== --}}
    <table class="info-table">
        <tr>
            <td class="col-label">Jenis Kegiatan</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $kegiatan->jenis_kegiatan ? ucwords(strtolower($kegiatan->jenis_kegiatan)) : 'Kegiatan Akademik' }}</td>
        </tr>
        <tr>
            <td class="col-label">Penyelenggara</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $kegiatan->penyelenggara ?? 'IAI DDI Sidrap' }}</td>
        </tr>
        <tr>
            <td class="col-label">Hari, Tanggal</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $tanggalKegiatan ? $tanggalKegiatan->isoFormat('dddd, D MMMM Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Waktu</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $waktu !== '' ? $waktu : '-' }}</td>
        </tr>
        <tr>
            <td class="col-label">Tempat / Lokasi</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $kegiatan->lokasi ?? 'Kampus IAI DDI Sidrap' }}</td>
        </tr>
        @if(!empty($kegiatan->narasumber))
        <tr>
            <td class="col-label">Narasumber</td>
            <td class="col-sep">:</td>
            <td class="col-val">{{ $kegiatan->narasumber }}</td>
        </tr>
        @endif
    </table>

    {{-- ===== PENUTUP ===== --}}
    <div class="penutup">
        &ldquo;Barang siapa yang keluar dalam rangka menuntut ilmu, maka ia berada di jalan Allah sampai ia kembali.&rdquo;
        <br />(HR. Tirmidzi) &mdash; Demikian sertifikat ini diberikan dengan sebenarnya.
    </div>

    {{-- ===== TANDA TANGAN (SPACER 11mm + SEMUA DATA DARI DATABASE) ===== --}}
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-pos">
                    KETUA PANITIA
                    <small>{{ $kegiatan->penyelenggara ?? 'Panitia Penyelenggara' }}</small>
                </div>
                <div class="ttd-spacer">&nbsp;</div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $kegiatan->ketua_panitia_nama ?? ($kegiatan->penyelenggara ?? 'Panitia Kegiatan') }}</div>
                    <div class="ttd-nip">NIP. {{ $kegiatan->ketua_panitia_nip ?? '-' }}</div>
                </div>
            </td>

            @if(!empty($kegiatan->narasumber))
            <td>
                <div class="ttd-pos">
                    NARASUMBER
                    <small>{{ $kegiatan->jenis_kegiatan ?? 'Kegiatan' }}</small>
                </div>
                <div class="ttd-spacer">&nbsp;</div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $kegiatan->narasumber }}</div>
                    <div class="ttd-nip">NIP. {{ $kegiatan->narasumber_nip ?? '-' }}</div>
                </div>
            </td>
            @else
            <td>&nbsp;</td>
            @endif

            <td>
                <div class="ttd-pos">
                    MENGETAHUI,<br />
                    REKTOR
                    <small>IAI DDI Sidrap</small>
                </div>
                <div class="ttd-spacer">&nbsp;</div>
                <div class="ttd-garis">
                    <div class="ttd-nama">{{ $kegiatan->rektor_nama ?? 'Dr. H. Muh. Anshar, M.Ag.' }}</div>
                    <div class="ttd-nip">
                        Rektor IAI DDI Sidrap &bull; NIP. {{ $kegiatan->rektor_nip ?? '-' }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

   

</div>

</body>
</html>
