<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Input Nilai Dosen</title>
    <style>
        @page { margin: 12mm 10mm 12mm 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 18px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 24px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 18px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 13px; font-weight: 900; margin: 10px 0 6px; }
        .meta { width: 100%; margin-bottom: 10px; }
        .meta td { padding: 2px 0; vertical-align: top; }
        .meta .label { width: 90px; }
        .meta .colon { width: 10px; text-align: center; }
        .meta .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 6px 7px; }
        .tbl th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
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

    $statusLabel = function (string $s) use ($statusOptions) {
        return $statusOptions[$s] ?? $s;
    };
@endphp

<table>
    <tr>
        <td style="width: 115px; vertical-align: middle; padding-top: 2px;">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 110px; height: auto;" />
            @endif
        </td>
        <td style="text-align: center;">
            <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
            <div class="kop-title-3">SIDENRENG RAPPANG</div>
            <div class="kop-meta" style="font-weight: 700;">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
            <div class="kop-meta">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-meta">E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id</div>
        </td>
        <td style="width: 70px;"></td>
    </tr>
</table>
<div class="kop-line-1"></div>
<div class="kop-line-2"></div>

<div class="doc-title">REKAP INPUT NILAI DOSEN</div>

<table class="meta">
    <tr>
        <td style="width: 50%; vertical-align: top;">
            <table>
                <tr>
                    <td class="label">Semester</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $semester }}</td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $status !== '' ? $statusLabel($status) : 'Semua' }}</td>
                </tr>
            </table>
        </td>
        <td style="width: 50%; vertical-align: top;">
            <table>
                <tr>
                    <td class="label">Pencarian</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $q !== '' ? $q : 'Semua data' }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="colon">:</td>
                    <td class="value">{{ date('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="tbl">
    <thead>
    <tr>
        <th style="width: 34px;" class="center">No</th>
        <th style="width: 130px;">Kode</th>
        <th>Mata Kuliah</th>
        <th style="width: 220px;">Dosen</th>
        <th style="width: 70px;" class="center">Peserta</th>
        <th style="width: 90px;" class="center">Terisi</th>
        <th style="width: 160px;">Status</th>
    </tr>
    </thead>
    <tbody>
    @forelse ($rows as $i => $row)
        @php
            $peserta = (int) ($row->peserta_approved ?? 0);
            $terisi = (int) ($row->nilai_terisi ?? 0);
        @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td class="nowrap">{{ $row->kode }}</td>
            <td>{{ $row->nama }}</td>
            <td>
                <div>{{ $row->dosen_1 ?: '-' }}</div>
                @if ($row->dosen_2)
                    <div style="font-size: 9px; margin-top: 2px;">{{ $row->dosen_2 }}</div>
                @endif
            </td>
            <td class="center">{{ $peserta }}</td>
            <td class="center">{{ $peserta > 0 ? $terisi.' / '.$peserta : '-' }}</td>
            <td>{{ $statusLabel($row->status_input) }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="center">Data tidak ditemukan.</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>

