<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jurnal Kegiatan KKN</title>
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
        .info-box { border: 1px solid #111827; padding: 8px 10px; margin: 6px 0 10px; }
        .info-row { display: table; width: 100%; font-size: 10px; }
        .info-row > div { display: table-row; }
        .info-row > div > span { display: table-cell; padding: 2px 0; }
        .info-row > div > span:first-child { width: 120px; color: #374151; font-weight: 600; }
        .catatan-box { background: #eff6ff; border: 1px solid #93c5fd; padding: 6px 8px; margin-top: 4px; font-size: 9px; color: #1e3a8a; border-radius: 4px; }
        .catatan-label { font-weight: 800; margin-bottom: 2px; }
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

        $pembimbingList = $kkn->posko?->pembimbingS ?? collect();
        $pembimbingStr = $pembimbingList->pluck('nama')->filter()->implode(' • ');
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

    <div class="doc-title">JURNAL KEGIATAN KULIAH KERJA NYATA (KKN)</div>
    <div class="doc-sub">
        Dicetak: <b>{{ now()->format('d F Y H:i') }}</b> &nbsp;•&nbsp; Oleh: <b>{{ $printedBy ?? '-' }}</b>
    </div>

    <div class="info-box">
        <div class="info-row">
            <div><span>Nama Mahasiswa</span><span>: <b>{{ $kkn->mahasiswa?->nama_lengkap ?: '-' }}</b></span></div>
            <div><span>NPM</span><span>: {{ $kkn->mahasiswa?->npm ?: '-' }}</span></div>
            <div><span>Program Studi</span><span>: {{ $kkn->mahasiswa?->program_studi ?: '-' }}</span></div>
            <div><span>Posko KKN</span><span>: <b>{{ $kkn->posko?->nama_posko ?: '-' }}</b></span></div>
            @if($kkn->posko?->alamat)
                <div><span>Lokasi Posko</span><span>: {{ $kkn->posko?->alamat }}</span></div>
            @endif
            <div><span>Dosen Pembimbing</span><span>: {{ $pembimbingStr ?: '-' }}</span></div>
        </div>
    </div>

    <table class="tbl">
        <thead>
        <tr>
            <th class="center" style="width: 34px;">No</th>
            <th class="center" style="width: 80px;">Tanggal</th>
            <th>Kegiatan</th>
            <th style="width: 110px;">Lokasi / Pihak Terkait</th>
            <th class="center" style="width: 60px;">Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($jurnals as $row)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center nowrap">{{ $row->tanggal?->format('d/m/Y') }}</td>
                <td>
                    <div style="font-weight: 700;">{{ $row->kegiatan ?: '-' }}</div>
                    @if($row->deskripsi)
                        <div style="font-size: 9px; color: #374151; margin-top: 3px; white-space: pre-wrap;">{{ $row->deskripsi }}</div>
                    @endif
                    @if($row->catatan_pembimbing)
                        <div class="catatan-box">
                            <div class="catatan-label">Catatan Pembimbing:</div>
                            <div style="white-space: pre-wrap;">{{ $row->catatan_pembimbing }}</div>
                        </div>
                    @endif
                </td>
                <td style="font-size: 9px;">
                    @if($row->lokasi)
                    <div><b>Lokasi:</b> {{ $row->lokasi }}</div>
                    @endif
                    @if($row->pihak_terkait)
                    <div style="margin-top: 2px;"><b>Pihak:</b> {{ $row->pihak_terkait }}</div>
                    @endif
                    @if(!$row->lokasi && !$row->pihak_terkait)
                    -
                    @endif
                </td>
                <td class="center">
                    <span class="badge {{ $statusClasses[$row->status] ?? 'badge-pending' }}">
                        {{ $statusLabels[$row->status] ?? strtoupper((string) $row->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="center" style="padding: 16px;">Belum ada data jurnal kegiatan.</td>
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
                    @if($kaprodi ?? null) Ketua Prodi {{ $kaprodi->program_studi ?? '' }} @else Mengetahui, @endif
                </div>
                <div style="height: 58px;"></div>
                @if($kaprodi ?? null)
                    <div style="font-size: 10px; font-weight: 800;">{{ trim($kaprodi->nama) }}</div>
                    <div style="font-size: 9px;">
                        @php
                            $_label = null;
                            $_val = null;
                            if (!empty(trim((string) $kaprodi->nuptk))) {
                                $_label = 'NUPTK';
                                $_val = trim((string) $kaprodi->nuptk);
                            } elseif (!empty(trim((string) $kaprodi->nidn))) {
                                $_label = 'NIDN';
                                $_val = trim((string) $kaprodi->nidn);
                            } elseif (!empty(trim((string) $kaprodi->nip))) {
                                $_label = 'NIP';
                                $_val = trim((string) $kaprodi->nip);
                            }
                        @endphp
                        @if ($_label && $_val)
                            {{ $_label }}. {{ $_val }}
                        @else
                            .....................................
                        @endif
                    </div>
                @else
                    <div style="font-size: 10px; font-weight: 800;">.....................................</div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
