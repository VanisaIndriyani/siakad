<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Pendaftaran KKN</title>
    <style>
        @page { margin: 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 18px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 26px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 18px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 2px; }
        .doc-sub { text-align: center; font-size: 11px; margin: 0 0 10px; color: #374151; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 6px 8px; vertical-align: top; }
        .tbl th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; background: #f3f4f6; }
        .center { text-align: center; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .badge { padding: 2px 6px; border-radius: 999px; font-weight: 700; font-size: 9px; text-align: center; display: inline-block; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
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

        $statusLabels = [
            'pending' => 'PENDING',
            'approved' => 'APPROVED',
            'rejected' => 'REJECTED',
        ];
        $statusClasses = [
            'pending' => 'badge-pending',
            'approved' => 'badge-approved',
            'rejected' => 'badge-rejected',
        ];
    @endphp

    <table>
        <tr>
            <td style="width: 120px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 115px; height: auto;" />
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

    <div class="doc-title">DAFTAR PENDAFTARAN KULIAH KERJA NYATA (KKN)</div>
    <div class="doc-sub">
        @if($programStudi) Program Studi: <b>{{ $programStudi }}</b> &nbsp;•&nbsp; @endif
        @if($q) Pencarian: <b>{{ $q }}</b> &nbsp;•&nbsp; @endif
        @if($status) Status: <b>{{ strtoupper($status) }}</b> &nbsp;•&nbsp; @endif
        Dicetak: <b>{{ now()->format('d F Y H:i') }}</b> &nbsp;•&nbsp; Oleh: <b>{{ $printedBy ?? '-' }}</b>
    </div>

    <table class="tbl">
        <thead>
        <tr>
            <th class="center" style="width: 34px;">No</th>
            <th style="width: 130px;">Mahasiswa</th>
            <th style="width: 140px;">Program Studi</th>
            <th class="center" style="width: 72px;">Status</th>
            <th style="width: 160px;">Posko / Lokasi</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($items as $row)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td>
                    <div style="font-weight: 700;">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                    <div style="font-size: 9px; color: #374151;">NPM. {{ $row->mahasiswa?->npm ?: '-' }}</div>
                </td>
                <td>
                    <div style="font-weight: 700;">{{ $row->mahasiswa?->program_studi ?: '-' }}</div>
                </td>
                <td class="center">
                    <span class="badge {{ $statusClasses[$row->status] ?? 'badge-pending' }}">
                        {{ $statusLabels[$row->status] ?? strtoupper((string) $row->status) }}
                    </span>
                </td>
                <td>
                    <div style="font-weight: 700;">{{ $row->posko?->nama_posko ?: 'Belum diplot' }}</div>
                    @if($row->posko?->lokasi)
                        <div style="font-size: 9px; color: #374151;">{{ $row->posko->lokasi }}</div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="center" style="padding: 16px;">Belum ada data pendaftaran KKN.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <table style="margin-top: 14mm;">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">
                <div style="font-size: 10px;">Sidrap, {{ now()->format('d F Y') }}</div>
                <div style="font-size: 10px; font-weight: 700;">
                    @if($kaprodi) Ketua Prodi {{ $kaprodi->program_studi ?? '' }} @else Mengetahui, @endif
                </div>
                <div style="height: 58px;"></div>
                @if($kaprodi)
                    <div style="font-size: 10px; font-weight: 800;">{{ trim($kaprodi->nama) }}</div>
                    <div style="font-size: 9px;">NUPTK. {{ $kaprodi->nuptk ?: ($kaprodi->nidn ?: '.....................................') }}</div>
                @else
                    <div style="font-size: 10px; font-weight: 800;">.....................................</div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
