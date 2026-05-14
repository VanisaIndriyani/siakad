<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pembayaran</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .kop { width: 100%; margin-bottom: 14px; }
        .kop-title-1 { color: #6b7280; font-size: 16px; font-weight: 700; margin: 0; }
        .kop-title-2 { color: #6b7280; font-size: 22px; font-weight: 900; margin: 2px 0 0; letter-spacing: 0.6px; }
        .kop-title-3 { color: #6b7280; font-size: 16px; font-weight: 800; margin: 1px 0 0; }
        .kop-meta { color: #6b7280; font-size: 10px; margin-top: 6px; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 10px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 4px; }
        .doc-title { text-align: center; font-size: 12px; font-weight: 900; margin: 14px 0 12px; letter-spacing: 0.4px; }

        .muted { color: #6b7280; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-box { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .info-box td { border: 1px solid #111827; padding: 12px 12px; vertical-align: top; }
        .info-box .split { border-left: 0; }
        .info-row { width: 100%; border-collapse: collapse; }
        .info-row td { border: 0; padding: 0 0 7px 0; }
        .info-row .k { width: 42%; font-size: 11px; }
        .info-row .v { width: 58%; text-align: right; font-weight: 800; font-size: 11px; }
        .info-row .v-left { text-align: left; font-weight: 800; }

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
        th, td { border: 1px solid #111827; padding: 8px 10px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    @php
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
        $kop6 = 'E-mail : iaiddisrapp@gmail.com  Website : www.yppddisrapp.ac.id';

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
                <td style="width: 120px; vertical-align: middle; padding-top: 2px;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 95px; height: auto;" />
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
                <td style="width: 120px;"></td>
            </tr>
        </table>
        <div class="kop-line-1"></div>
        <div class="kop-line-2"></div>
    </div>

    <div class="doc-title">PEMBAYARAN #{{ $pembayaran->id }}</div>

    <table class="info-box">
        <tr>
            <td style="width: 50%;">
                <table class="info-row">
                    <tr>
                        <td class="k">Nama</td>
                        <td class="v v-left">{{ $pembayaran->mahasiswa->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">NPM</td>
                        <td class="v v-left">{{ $pembayaran->mahasiswa->npm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Angkatan</td>
                        <td class="v v-left">{{ $pembayaran->mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="k">Jenis Tagihan</td>
                        <td class="v v-left">{{ $pembayaran->jenis_tagihan ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td class="split" style="width: 50%;">
                <table class="info-row">
                    <tr>
                        <td class="k">Semester / TA</td>
                        <td class="v">Semester {{ $pembayaran->semester }} / {{ $pembayaran->tahun_ajaran }}</td>
                    </tr>
                    <tr>
                        <td class="k">Status</td>
                        <td class="v"><span class="{{ $badgeClass }}">{{ strtoupper((string) $pembayaran->status_pembayaran) }}</span></td>
                    </tr>
                    <tr>
                        <td class="k">Dicetak</td>
                        <td class="v">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="k">Catatan</td>
                        <td class="v">{{ $pembayaran->catatan ?? '-' }}</td>
                    </tr>
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
    <table>
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
