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
        $logoBase64 = null;
        try {
            $logoPath = public_path('img/lo.jpeg');
            if (file_exists($logoPath)) {
                $data = @file_get_contents($logoPath);
                if ($data !== false) {
                    $logoBase64 = 'data:image/jpeg;base64,'.base64_encode($data);
                }
            }
        } catch (\Exception $e) {}

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';
    @endphp

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

    <div class="doc-title" style="margin-top: 15px;">REKAP PEMBAYARAN</div>
    <div class="meta">
        <div>Filter: {{ $q ? 'q='.$q : '-' }} | Semester: {{ $semester ?: '-' }} | Angkatan: {{ $angkatan ?: '-' }} | Jurusan: {{ $jurusan ?: '-' }} | Tagihan: {{ $jenis_tagihan ?: '-' }}</div>
        <div>Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table class="tbl">
        <thead>
        <tr>
            <th style="width: 26px;">No</th>
            <th>Mahasiswa</th>
            <th class="nowrap">NPM</th>
            <th class="nowrap">Angkatan</th>
            <th class="nowrap">Semester</th>
            <th class="nowrap">TA</th>
            <th>Jenis Tagihan</th>
            <th class="right nowrap">Total</th>
            <th class="right nowrap">Dibayar</th>
            <th class="nowrap">Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $i => $p)
            <tr>
                <td class="nowrap">{{ $i + 1 }}</td>
                <td>{{ $p->mahasiswa?->nama_lengkap ?? '-' }}</td>
                <td class="nowrap">{{ $p->mahasiswa?->npm ?? '-' }}</td>
                <td class="nowrap">{{ $p->mahasiswa?->angkatan ?? '-' }}</td>
                <td class="nowrap">{{ $p->semester }}</td>
                <td class="nowrap">{{ $p->tahun_ajaran }}</td>
                <td>{{ $p->jenis_tagihan ?? '-' }}</td>
                <td class="right nowrap">Rp {{ number_format((float) ($p->total_biaya ?? 0), 0, ',', '.') }}</td>
                <td class="right nowrap">Rp {{ number_format((float) ($p->total_dibayar ?? 0), 0, ',', '.') }}</td>
                <td class="nowrap">{{ $p->status_pembayaran ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center; padding: 16px;">Tidak ada data.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
