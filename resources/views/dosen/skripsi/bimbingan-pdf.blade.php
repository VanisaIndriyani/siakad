<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Riwayat Bimbingan Skripsi - {{ $skripsi->mahasiswa?->nama_lengkap }}</title>
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
            color: #111827;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 2px;
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
        .kop-title-1 { font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { font-size: 24px; font-weight: 900; margin: 2px 0; line-height: 1; }
        .kop-title-3 { font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-meta { font-size: 10px; font-weight: 700; margin-top: 3px; }
        .kop-alamat { font-size: 10px; margin: 2px 0; }
        .kop-line-2 {
            border-top: 1px solid #000;
            margin-top: 2px;
            margin-bottom: 20px;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table .label {
            width: 120px;
            font-weight: bold;
        }
        .chat-container {
            margin-top: 20px;
        }
        .chat-row {
            margin-bottom: 12px;
            padding: 8px 10px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            page-break-inside: avoid;
        }
        .chat-meta {
            font-size: 9px;
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 4px;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 2px;
        }
        .chat-text {
            font-size: 11px;
            color: #111827;
            white-space: pre-line;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
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
                <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-title-3">SIDENRENG RAPPANG</div>
                <div class="kop-meta">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                <div class="kop-alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="kop-alamat">E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id</div>
            </td>
            <td style="width: 90px;"></td>
        </tr>
    </table>
    <div class="kop-line-2"></div>

    <div class="doc-title">Riwayat Bimbingan Skripsi</div>

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

    <div class="chat-container">
        <div style="font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 4px; margin-bottom: 15px; font-size: 12px;">DAFTAR KONSULTASI</div>
        
        @forelse ($skripsi->messages->sortBy('id') as $msg)
            <div class="chat-row">
                <div class="chat-meta">
                    {{ $msg->sender?->name ?: 'User' }} • {{ $msg->created_at?->format('d/m/Y H:i') }}
                </div>
                <div class="chat-text">{{ $msg->pesan }}</div>
            </div>
        @empty
            <div style="text-align: center; padding: 20px; color: #9ca3af;">Belum ada riwayat bimbingan.</div>
        @endforelse
    </div>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan secara otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>
