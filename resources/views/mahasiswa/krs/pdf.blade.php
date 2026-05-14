<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KRS</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #6b7280; font-size: 18px; font-weight: 700; margin: 0; line-height: 1.15; }
        .kop-title-2 { color: #6b7280; font-size: 26px; font-weight: 900; margin: 2px 0 0; letter-spacing: 0.6px; line-height: 1.1; }
        .kop-title-3 { color: #6b7280; font-size: 18px; font-weight: 800; margin: 1px 0 0; line-height: 1.15; }
        .kop-meta { color: #6b7280; font-size: 11px; margin-top: 6px; line-height: 1.2; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 10px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 4px; }
        .doc-title { text-align: center; font-size: 12px; font-weight: 900; margin: 14px 0 10px; }
        .box { border: 1px solid #111827; }
        .box td { border: 1px solid #111827; padding: 10px 12px; vertical-align: top; }
        .kv { width: 100%; border-collapse: collapse; }
        .kv td { border: 0; padding: 0; }
        .kv .row { display: flex; justify-content: space-between; gap: 10px; font-size: 11px; margin-top: 6px; }
        .kv .row:first-child { margin-top: 0; }
        .kv .label { width: 45%; }
        .kv .value { width: 55%; text-align: right; font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 8px 10px; }
        .tbl th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .right { text-align: right; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $mahasiswa = auth()->user()->mahasiswa;
        $items = $krs->items->sortBy(fn ($item) => (string) ($item->mataKuliah?->kode ?? ''));
        $totalSks = $items->sum(fn ($item) => (int) ($item->mataKuliah?->sks ?? 0));

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

        $ps = strtoupper((string) ($mahasiswa?->program_studi ?? ''));
        $jenjang = str_contains($ps, 'S2') ? 'S2' : (str_contains($ps, 'S3') ? 'S3' : ($ps !== '' ? 'S1' : '-'));
    @endphp

    <table>
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

    <div class="doc-title">Kartu Rencana Studi (KRS)</div>

    <table class="box" style="margin-bottom: 16px;">
        <tr>
            <td style="width: 50%;">
                <table class="kv">
                    <tr><td>
                        <div class="row"><div class="label">Jenjang/Program</div><div class="value">{{ $jenjang }}</div></div>
                        <div class="row"><div class="label">Prodi</div><div class="value">{{ $mahasiswa?->program_studi ?? '-' }}</div></div>
                    </td></tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table class="kv">
                    <tr><td>
                        <div class="row"><div class="label">Nama</div><div class="value">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</div></div>
                        <div class="row"><div class="label">NPM</div><div class="value">{{ $mahasiswa?->npm ?? '-' }}</div></div>
                        <div class="row"><div class="label">Tahun Akademik</div><div class="value">{{ $krs->tahun_ajaran ?? '-' }}</div></div>
                        <div class="row"><div class="label">Semester</div><div class="value">{{ $krs->semester }}</div></div>
                    </td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
        <tr>
            <th style="width: 40px;" class="center">No</th>
            <th style="width: 120px;">Kode Mata Kuliah</th>
            <th>Mata Kuliah</th>
            <th style="width: 70px;" class="center">SKS</th>
            <th style="width: 90px;" class="center">Semester</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $item)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>{{ $item->mataKuliah?->kode ?? '-' }}</td>
                <td>{{ $item->mataKuliah?->nama ?? '-' }}</td>
                <td class="center">{{ $item->mataKuliah?->sks ?? '-' }}</td>
                <td class="center">{{ $krs->semester }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="3" style="font-weight: 900;">Total SKS</td>
            <td class="center" style="font-weight: 900;">{{ $totalSks }}</td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <table style="margin-top: 28mm;">
        <tr>
            <td class="box" style="width: 33.33%; text-align: center; padding: 10px 8px;">
                <div style="font-size: 11px; font-weight: 700;">Ketua Prodi</div>
                <div style="height: 60px;"></div>
                <div style="font-size: 10px; font-weight: 700;">TTD</div>
                <div style="margin-top: 10px; font-size: 11px;">Nama</div>
            </td>
            <td class="box" style="width: 33.33%; text-align: center; padding: 10px 8px;">
                <div style="font-size: 11px; font-weight: 700;">Sekretaris Prodi</div>
                <div style="height: 60px;"></div>
                <div style="font-size: 10px; font-weight: 700;">TTD</div>
                <div style="margin-top: 10px; font-size: 11px;">Nama</div>
            </td>
            <td class="box" style="width: 33.33%; text-align: center; padding: 10px 8px;">
                <div style="font-size: 11px; font-weight: 700;">Mahasiswa</div>
                <div style="height: 60px;"></div>
                <div style="font-size: 10px; font-weight: 700;">TTD</div>
                <div style="margin-top: 10px; font-size: 11px;">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</div>
            </td>
        </tr>
    </table>
</body>
</html>

