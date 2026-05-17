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
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #064e3b;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
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
            color: #374151;
        }
        .chat-container {
            margin-top: 20px;
        }
        .chat-row {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        .chat-meta {
            font-size: 10px;
            font-weight: bold;
            color: #6b7280;
            margin-bottom: 5px;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 3px;
        }
        .chat-text {
            font-size: 11px;
            color: #111827;
            white-space: pre-line;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
            padding-top: 10px;
        }
        .me {
            background-color: #f0fdf4;
            border-color: #dcfce7;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="title">Riwayat Bimbingan Skripsi</div>
                <div class="subtitle">SIAKAD {{ config('app.name') }}</div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div style="font-size: 10px; color: #6b7280;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

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
        <h3 style="color: #064e3b; border-bottom: 1px solid #10b981; padding-bottom: 5px; margin-bottom: 15px;">Daftar Konsultasi</h3>
        
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
        Dokumen ini dihasilkan secara otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>
