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
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        .kop-logo {
            width: 110px;
            text-align: left;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 100px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text .instansi {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }
        .kop-text .nama-kampus {
            font-size: 24px;
            font-weight: 900;
            margin: 2px 0;
            line-height: 1;
        }
        .kop-text .lokasi {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }
        .kop-text .akreditasi {
            font-size: 10px;
            font-weight: 700;
            margin-top: 3px;
        }
        .kop-text .alamat {
            font-size: 10px;
            margin: 2px 0;
        }
        .kop-line-2 {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 20px;
        }
        .title-box {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-box h2 {
            font-size: 16px;
            text-decoration: underline;
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
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }
        .data-table td {
            border: 1px solid #d1d5db;
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
                    $logoPath = public_path('img/lo.jpeg');
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $logoData = file_get_contents($logoPath);
                        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <div class="instansi">INSTITUT AGAMA ISLAM</div>
                <div class="nama-kampus">DARUD DA'WAH WAL IRSYAD</div>
                <div class="lokasi">SIDENRENG RAPPANG</div>
                <div class="akreditasi">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                <div class="alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="alamat">E-mail : iaiddisrapp@gmail.com Website : www.yppddisrapp.ac.id</div>
            </td>
            <td style="width: 90px;"></td>
        </tr>
    </table>
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

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>

