<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KRS</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 6px; }
        .kv2 { width: 100%; border-collapse: collapse; }
        .kv2 td { padding: 2px 0; font-size: 11px; vertical-align: top; }
        .kv2 .label { width: 140px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 8px 10px; }
        .tbl th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .right { text-align: right; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    @php
        $mahasiswa = $krs->mahasiswa;
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
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';

        $ps = strtoupper((string) ($mahasiswa?->program_studi ?? ''));
        $jenjang = str_contains($ps, 'S2') ? 'S2' : (str_contains($ps, 'S3') ? 'S3' : ($ps !== '' ? 'S1' : '-'));
        $semesterLabel = ((int) $krs->semester % 2 === 0) ? 'GENAP' : 'GANJIL';
        $tahunAjaran = trim((string) ($krs->tahun_ajaran ?? ''));
        $semesterHeader = $semesterLabel.($tahunAjaran !== '' ? '-'.$tahunAjaran : '');

        $kaprodiNama = $kaprodiNama ?? null;
        $sekprodiNama = $sekprodiNama ?? null;
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

    <div class="doc-title" style="margin-bottom: 0;">KARTU RENCANA STUDI</div>
    <div class="doc-title" style="margin-top: 2px; font-size: 13px;">SEMESTER : {{ $semesterHeader }}</div>

    <table style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv2">
                    <tr>
                        <td class="label">Jenjang/Program</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $jenjang }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fakultas</td>
                        <td class="colon">:</td>
                        <td class="value nowrap" style="font-size: 10.5px;">{{ $mahasiswa?->fakultas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa?->program_studi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 16px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPM</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa?->npm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tahun Akademik</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $tahunAjaran !== '' ? $tahunAjaran : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semesterHeader }}</td>
                    </tr>
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

    <table style="margin-top: 16mm;">
        <tr>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Ketua Prodi</div>
                <div style="font-size: 11px; font-weight: 700;">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ $kaprodiNama ? trim($kaprodiNama) : '-' }}</div>
            </td>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Sekretaris Prodi</div>
                <div style="font-size: 11px; font-weight: 700;">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ $sekprodiNama ? trim($sekprodiNama) : '-' }}</div>
            </td>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Mahasiswa</div>
                <div style="font-size: 11px; font-weight: 700;">&nbsp;</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ trim((string) ($mahasiswa?->nama_lengkap ?? auth()->user()->name)) }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
