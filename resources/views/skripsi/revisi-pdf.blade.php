<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
        @page {
            margin: 1.5cm;
        }
        * {
            font-family: 'Helvetica', 'Arial', sans-serif;
            box-sizing: border-box;
        }
        body {
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            width: 100%;
            padding-bottom: 5px;
        }
        .kop-logo {
            width: 130px;
            text-align: left;
            vertical-align: middle;
            padding-top: 2px;
        }
        .kop-logo img {
            width: 125px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-box h2 {
            font-size: 16px;
            text-decoration: none;
            border-bottom: 3px double #000;
            display: inline-block;
            padding-bottom: 2px;
            margin: 0;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 120px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background-color: #f9fafb;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <table class="kop-surat">
        <tr>
            <td class="kop-logo">
                @php
                    $logoCandidates = [
                        public_path('img/lo.jpeg'),
                        public_path('img/logo.png'),
                        base_path('../img/lo.jpeg'),
                        base_path('../img/logo.png'),
                        base_path('../public/img/lo.jpeg'),
                        base_path('../public/img/logo.png'),
                    ];

                    $logoPath = null;
                    foreach ($logoCandidates as $candidate) {
                        if (is_string($candidate) && is_file($candidate) && is_readable($candidate)) {
                            $logoPath = $candidate;
                            break;
                        }
                    }

                    $logoBase64 = null;
                    if ($logoPath) {
                        $data = @file_get_contents($logoPath);
                        if ($data !== false) {
                            $ext = strtolower((string) pathinfo($logoPath, PATHINFO_EXTENSION));
                            $ext = $ext === 'jpg' ? 'jpeg' : $ext;
                            $logoBase64 = 'data:image/'.$ext.';base64,'.base64_encode($data);
                        }
                    }

                    $kop1 = 'INSTITUT AGAMA ISLAM';
                    $kop2 = "DARUD DA'WAH WAL IRSYAD";
                    $kop3 = 'SIDENRENG RAPPANG';
                    $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
                    $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
                    $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 125px; height: auto;" />
                @endif
            </td>
            <td class="kop-text">
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

    <div class="title-box">
        <h2>Lembar Revisi Skripsi</h2>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Nama Mahasiswa</td>
            <td>: {{ $skripsi->mahasiswa?->nama_lengkap }} ({{ $skripsi->mahasiswa?->npm }})</td>
        </tr>
        <tr>
            <td class="label">Judul Skripsi</td>
            <td>: {{ $skripsi->judul }}</td>
        </tr>
        <tr>
            <td class="label">Pembimbing 1</td>
            <td>: {{ $skripsi->dosenPembimbing?->nama ?: '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pembimbing 2</td>
            <td>: {{ $skripsi->dosenPembimbing2?->nama ?: '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 120px;">Tanggal</th>
                <th style="width: 150px;">Pembimbing</th>
                <th>Catatan Revisi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revisis as $i => $row)
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td style="text-align: center;">{{ ($row->tanggal ?: $row->created_at)?->format('d/m/Y H:i') }}</td>
                    <td>{{ $row->creator?->name ?: 'User' }}</td>
                    <td style="white-space: pre-line;">{{ $row->revisi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 20px;">Belum ada riwayat revisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-sign" style="margin-top: 50px; width: 100%;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; text-align: center; border: none;">
                    Mengetahui,<br>
                    Ketua Program Studi {{ $skripsi->mahasiswa?->program_studi ?? '................' }}
                    <div style="height: 80px;"></div>
                    ( ........................................... )<br>
                    NIDN. .....................................
                </td>
                <td style="width: 50%; text-align: center; border: none;">
                    Sidrap, {{ now()->translatedFormat('d F Y') }}<br>
                    Dosen Pembimbing Skripsi,
                    <div style="height: 80px;"></div>
                    <strong>( {{ $skripsi->dosenPembimbing?->nama ?: '...........................................' }} )</strong><br>
                    NIDN. {{ $skripsi->dosenPembimbing?->nidn ?: '.....................................' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
