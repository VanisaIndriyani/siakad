<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Pengajuan Cuti</title>
    <style>
        @page { margin: 12mm 14mm 12mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 15px 0 5px; text-decoration: underline; text-transform: uppercase; }
        .doc-number { text-align: center; font-size: 11px; margin-bottom: 15px; }
        .content { margin-top: 5px; text-align: justify; }
        .kv { margin: 10px 0 10px 15px; }
        .kv td { padding: 3px 0; }
        .label { width: 180px; }
        .colon { width: 20px; text-align: center; }
        .value { font-weight: 700; }
        .alasan-box { margin: 5px 0 10px 15px; padding: 10px; border: 1px solid #000; background-color: #f9fafb; min-height: 40px; font-style: italic; }
        .footer-table { margin-top: 15mm; }
        .footer-table td { text-align: center; vertical-align: top; width: 33.33%; }
        .sig-space { height: 60px; }
        .sig-name { font-weight: 800; text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $mahasiswa = $cuti->mahasiswa;
        $logoPath = public_path('img/lo.jpeg');
        $logoBase64 = null;
        if (is_string($logoPath) && file_exists($logoPath)) {
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
        }

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';
    @endphp

    <table>
        <tr>
            <td style="width: 130px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 125px; height: auto;" />
                @endif
            </td>
            <td style="text-align: center;">
                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>
                <div class="kop-meta" style="font-weight: 700;">{{ $kop4 }}</div>
                <div class="kop-meta">{{ $kop5 }}</div>
                <div class="kop-meta">{{ $kop6 }}</div>
            </td>
            <td style="width: 90px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">SURAT PERMOHONAN CUTI AKADEMIK</div>
    @php
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $month = $romanMonths[(int)($cuti->created_at?->format('m') ?? date('m'))];
        $year = $cuti->created_at?->format('Y') ?? date('Y');
        $nomorUrut = str_pad($cuti->id, 4, '0', STR_PAD_LEFT);
        $nomorSurat = "Nomor : {$nomorUrut}/SIAKAD/IAI/DDI/SR/{$month}/{$year}";
    @endphp
    <div class="doc-number">{{ $nomorSurat }}</div>

    <div class="content">
        <p>Kepada Yth,<br>Rektor Institut Agama Islam DDI Sidrap<br>di -<br>&nbsp;&nbsp;&nbsp;&nbsp;Tempat</p>
        
        <p style="margin-top: 15px;">Assalamu'alaikum warahmatullahi wabarakatu.</p>
        <p>Yang bertanda tangan di bawah ini:</p>
        
        <table class="kv">
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td class="colon">:</td>
                <td class="value">{{ $mahasiswa->nama_lengkap }}</td>
            </tr>
            <tr>
                <td class="label">NIM / NPM</td>
                <td class="colon">:</td>
                <td class="value">{{ $mahasiswa->npm }}</td>
            </tr>
            <tr>
                <td class="label">Program Studi</td>
                <td class="colon">:</td>
                <td class="value">{{ $mahasiswa->program_studi }}</td>
            </tr>
            <tr>
                <td class="label">Semester</td>
                <td class="colon">:</td>
                <td class="value">{{ $cuti->semester }}</td>
            </tr>
            <tr>
                <td class="label">Tahun Ajaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $cuti->tahun_ajaran }}</td>
            </tr>
        </table>

        <p>Dengan ini mengajukan permohonan cuti akademik pada semester tersebut di atas dengan alasan sebagai berikut:</p>
        <div class="alasan-box">
            "{{ $cuti->alasan }}"
        </div>

        <p>Demikian surat permohonan ini saya buat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya. Atas perhatian dan persetujuan Bapak, saya ucapkan terima kasih.</p>
        <p style="margin-top: 10px;">Billahi taufiq Wadda'watu wal irsyad.</p>
        <p style="margin-top: 6px;">Wassalamu'alaikum warahmatullahi wabarakatu.</p>
    </div>

    <table class="footer-table">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div style="font-weight: 700;">Ketua Prodi</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $kaprodi?->nama ?? '........................................' }}</div>
                <div style="font-size: 10px;">NUPTK. {{ $kaprodi?->nuptk ?? '................................' }}</div>
            </td>
            <td>
                <div>Mengetahui,</div>
                <div style="font-weight: 700;">Sekretaris Prodi</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $sekprodi?->nama ?? '........................................' }}</div>
                <div style="font-size: 10px;">NUPTK. {{ $sekprodi?->nuptk ?? '................................' }}</div>
            </td>
            <td>
                <div>Sidrap, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: 700;">Mahasiswa Pemohon</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $mahasiswa->nama_lengkap }}</div>
                <div style="font-size: 10px;">NPM. {{ $mahasiswa->npm }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
