<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Pembayaran</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 10px 0 8px; }
        .meta { font-size: 10px; color: #6b7280; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; }
        .tbl th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    @php
        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
    @endphp

    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
        <div style="font-size: 16px; font-weight: bold;">{{ $kop1 }}</div>
        <div style="font-size: 20px; font-weight: bold;">{{ $kop2 }}</div>
        <div style="font-size: 16px; font-weight: bold;">{{ $kop3 }}</div>
        <div style="font-size: 12px; margin-top: 5px;">REKAP PEMBAYARAN MAHASISWA</div>
    </div>

    <div class="meta" style="margin-bottom: 10px; font-size: 9px;">
        <div>Filter: {{ $q ? 'q='.$q : '-' }} | Sem: {{ $semester ?: '-' }} | Angk: {{ $angkatan ?: '-' }} | Jur: {{ $jurusan ?: '-' }}</div>
        <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="tbl" style="font-size: 10px;">
        <thead>
        <tr>
            <th style="width: 20px;">No</th>
            <th>Nama Mahasiswa</th>
            <th style="width: 80px;">NPM</th>
            <th style="width: 50px;">Angk</th>
            <th style="width: 40px;">Sem</th>
            <th>Jenis Tagihan</th>
            <th class="right" style="width: 80px;">Total</th>
            <th class="right" style="width: 80px;">Dibayar</th>
            <th style="width: 70px;">Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $i => $p)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ $p->mahasiswa?->nama_lengkap ?? '-' }}</td>
                <td>{{ $p->mahasiswa?->npm ?? '-' }}</td>
                <td style="text-align: center;">{{ $p->mahasiswa?->angkatan ?? '-' }}</td>
                <td style="text-align: center;">{{ $p->semester }}</td>
                <td>{{ $p->jenis_tagihan ?? '-' }}</td>
                <td class="right">Rp{{ number_format((float)($p->total_biaya ?? 0), 0, ',', '.') }}</td>
                <td class="right">Rp{{ number_format((float)($p->total_dibayar ?? 0), 0, ',', '.') }}</td>
                <td>{{ $p->status_pembayaran ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; padding: 10px;">Tidak ada data.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
