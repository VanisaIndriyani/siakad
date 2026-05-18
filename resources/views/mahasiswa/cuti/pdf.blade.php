<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Pengajuan Cuti</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #111827; font-size: 19px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #111827; font-size: 27px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #111827; font-size: 19px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #111827; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 7px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 20px 0 15px; text-decoration: underline; }
        .content { margin-top: 20px; text-align: justify; }
        .kv { margin: 15px 0; }
        .kv td { padding: 4px 0; }
        .label { width: 160px; }
        .colon { width: 20px; text-align: center; }
        .value { font-weight: 700; }
        .footer-table { margin-top: 30mm; }
        .footer-table td { text-align: center; vertical-align: top; width: 33.33%; }
        .sig-space { height: 70px; }
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

    <div class="content">
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

        <p>Mengajukan permohonan cuti akademik pada semester tersebut di atas dengan alasan sebagai berikut:</p>
        <div style="padding: 10px; border: 1px solid #e5e7eb; background-color: #f9fafb; min-height: 60px;">
            {{ $cuti->alasan }}
        </div>

        <p style="margin-top: 20px;">Demikian surat permohonan ini saya buat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <table class="footer-table">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div style="font-weight: 700;">Ketua Prodi</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $cuti->approvedByProdi?->name ?? '........................................' }}</div>
            </td>
            <td>
                <div>Mengetahui,</div>
                <div style="font-weight: 700;">Sekretaris Prodi</div>
                <div class="sig-space"></div>
                <div class="sig-name">........................................</div>
            </td>
            <td>
                <div>Sidrap, {{ now()->translatedFormat('d F Y') }}</div>
                <div style="font-weight: 700;">Mahasiswa</div>
                <div class="sig-space"></div>
                <div class="sig-name">{{ $mahasiswa->nama_lengkap }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
