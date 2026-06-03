<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pembayaran</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .kop { width: 100%; margin-bottom: 14px; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 10px 0 10px; letter-spacing: 0.4px; }

        .muted { color: #6b7280; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-box { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .info-box td { border: 1px solid #111827; padding: 9px 10px; vertical-align: top; }
        .info-box .split { border-left: 0; }
        .kv2 { width: 100%; border-collapse: collapse; }
        .kv2 td { border: 0; padding: 2px 0; font-size: 11px; vertical-align: top; }
        .kv2 .label { width: 118px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 800; }

        .summary { width: 100%; border-collapse: collapse; margin: 12px 0 14px; }
        .summary td { border: 1px solid #111827; padding: 10px 12px; vertical-align: top; }
        .summary .num { font-size: 13px; font-weight: 900; margin-top: 6px; }
        .summary .good { color: #059669; }
        .summary .info { color: #0284c7; }
        .summary .bad { color: #dc2626; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 10px; font-weight: 900; letter-spacing: 0.5px; }
        .badge-lunas { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-cicil { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-belum { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

        table { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 8px 10px; vertical-align: top; }
        .tbl th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    @php
        $logoCandidates = [
            public_path('img/lo.jpeg'),
            public_path('img/logo.png'),
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

        $sisa = max(0, (float) $pembayaran->total_biaya - (float) $pembayaran->total_dibayar);
        $badgeClass = match ($pembayaran->status_pembayaran) {
            'Lunas' => 'badge badge-lunas',
            'Cicil' => 'badge badge-cicil',
            default => 'badge badge-belum',
        };
    @endphp

    <div class="kop">
        <table style="width: 100%; border-collapse: collapse;">
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
    </div>

    <div class="doc-title">PEMBAYARAN #{{ $pembayaran->id }}</div>

    <table class="info-box">
        <tr>
            <td style="width: 50%;">
                <table class="kv2">
                    <tr><td class="label">Nama</td><td class="colon">:</td><td class="value">{{ $pembayaran->mahasiswa?->nama_lengkap ?? '-' }}</td></tr>
                    <tr><td class="label">NPM</td><td class="colon">:</td><td class="value">{{ $pembayaran->mahasiswa?->npm ?? '-' }}</td></tr>
                    <tr><td class="label">Angkatan</td><td class="colon">:</td><td class="value">{{ $pembayaran->mahasiswa?->angkatan ?? '-' }}</td></tr>
                    <tr><td class="label">Jenis Tagihan</td><td class="colon">:</td><td class="value">{{ $pembayaran->jenis_tagihan ?? '-' }}</td></tr>
                </table>
            </td>
            <td class="split" style="width: 50%;">
                <table class="kv2">
                    <tr><td class="label">Semester / TA</td><td class="colon">:</td><td class="value">Semester {{ $pembayaran->semester }} / {{ $pembayaran->tahun_ajaran }}</td></tr>
                    <tr><td class="label">Status</td><td class="colon">:</td><td class="value"><span class="{{ $badgeClass }}">{{ strtoupper((string) $pembayaran->status_pembayaran) }}</span></td></tr>
                    <tr><td class="label">Dicetak</td><td class="colon">:</td><td class="value">{{ now()->format('d/m/Y H:i') }}</td></tr>
                    <tr><td class="label">Catatan</td><td class="colon">:</td><td class="value">{{ $pembayaran->catatan ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td>
                <div class="muted">Total Tagihan</div>
                <div class="num good">Rp {{ number_format((float) $pembayaran->total_biaya, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="muted">Total Dibayar</div>
                <div class="num info">Rp {{ number_format((float) $pembayaran->total_dibayar, 0, ',', '.') }}</div>
            </td>
            <td>
                <div class="muted">Sisa</div>
                <div class="num bad">Rp {{ number_format((float) $sisa, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <div style="margin: 10px 0 8px; font-size: 11px; font-weight: 900; letter-spacing: 0.4px;">RIWAYAT TRANSAKSI</div>
    <table class="tbl">
        <thead>
        <tr>
            <th style="width: 26px;">No</th>
            <th class="nowrap">Tanggal</th>
            <th>Keterangan</th>
            <th class="right nowrap">Jumlah</th>
            <th class="nowrap">Bukti</th>
        </tr>
        </thead>
        <tbody>
        @forelse($pembayaran->details as $i => $d)
            <tr>
                <td class="nowrap">{{ $i + 1 }}</td>
                <td class="nowrap">{{ $d->tanggal_bayar?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $d->keterangan ?? '-' }}</td>
                <td class="right nowrap">Rp {{ number_format((float) $d->jumlah_bayar, 0, ',', '.') }}</td>
                <td class="nowrap">{{ $d->bukti_pembayaran ? 'Ada' : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align:center; padding: 16px;">Belum ada transaksi.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top: 16px; color: #6b7280; font-size: 10px;">
        Dokumen ini dihasilkan otomatis oleh sistem.
    </div>
</body>
</html>
