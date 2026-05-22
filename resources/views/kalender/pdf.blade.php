<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Akademik</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; line-height: 1.35; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 8px; text-transform: uppercase; }
        .sub { text-align: center; font-size: 11px; margin-bottom: 12px; color: #111827; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 8px 10px; vertical-align: top; }
        .tbl th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
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

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';

        $q = trim((string) ($q ?? ''));
        $printedAt = now()->format('d-m-Y H:i');
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

    <div class="doc-title">KALENDER AKADEMIK</div>
    <div class="sub">
        Dicetak: {{ $printedAt }}@if($q !== '') • Filter: {{ $q }}@endif
    </div>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 40px;" class="center">No</th>
                <th style="width: 140px;" class="center">Tanggal</th>
                <th>Judul</th>
                <th style="width: 140px;" class="center">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($events as $event)
                @php
                    $start = $event->tanggal_mulai?->format('d/m/Y') ?: '-';
                    $end = $event->tanggal_selesai?->format('d/m/Y');
                    $range = $end && $end !== $start ? ($start.' s/d '.$end) : $start;
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="center nowrap">{{ $range }}</td>
                    <td>{{ $event->judul }}</td>
                    <td class="center">{{ $event->kategori ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center" style="padding: 18px;">Belum ada kegiatan kalender akademik.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
