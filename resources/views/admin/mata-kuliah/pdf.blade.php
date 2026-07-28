<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Mata Kuliah</title>
    <style>
        @page { margin: 12mm 10mm 12mm 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 10px 0 6px; }
        .kv2 td { padding: 2px 0; font-size: 11px; vertical-align: top; }
        .kv2 .label { width: 140px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 7px 8px; }
        .tbl th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.4px; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .sign-wrap { width: 100%; margin-top: 18px; }
        .sign-box { width: 42%; margin-left: auto; text-align: center; }
        .sign-space { height: 62px; }
        .sign-name { font-weight: 800; }
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

        $totalSks = 0;
        foreach ($items as $item) {
            $totalSks += (int) ($item->sks ?? 0);
        }
    @endphp

    <table>
        <tr>
            <td style="width: 130px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 125px; height: auto;" />
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
            <td style="width: 90px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">DAFTAR MATA KULIAH</div>

    <table style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv2">
                    <tr>
                        <td class="label">Jurusan / Prodi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $jurusan ?: 'Semua Jurusan' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semester && $semester >= 1 && $semester <= 8 ? $semester : 'Semua Semester' }}</td>
                    </tr>
                    @if($q)
                        <tr>
                            <td class="label">Pencarian</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $q }}</td>
                        </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 16px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Jumlah Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $items->count() }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total SKS</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $totalSks }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Cetak</td>
                        <td class="colon">:</td>
                        <td class="value">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 34px;" class="center">No</th>
                <th style="width: 100px;">Kode</th>
                <th>Mata Kuliah</th>
                <th style="width: 80px;" class="center">Semester</th>
                <th style="width: 55px;" class="center">SKS</th>
                <th>Dosen 1</th>
                <th>Dosen 2</th>
                <th style="width: 90px;" class="center">RPS Admin</th>
                <th style="width: 90px;" class="center">RPS Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td class="nowrap">{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="center">{{ $item->semester }}</td>
                    <td class="center">{{ $item->sks }}</td>
                    <td>{{ $item->dosen?->nama ?? '-' }}</td>
                    <td>{{ $item->dosen2?->nama ?? '-' }}</td>
                    <td class="center">{{ $item->rps_admin_path ? 'Ada' : '-' }}</td>
                    <td class="center">{{ $item->rps_dosen_path ? 'Ada' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center">Belum ada mata kuliah pada pilihan ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="sign-wrap">
        <div class="sign-box">
            <div style="font-weight: 700;">Sidrap, {{ date('d F Y') }}</div>
            <div style="font-weight: 700;">Ketua Program Studi</div>
            <div class="sign-space"></div>
            <div class="sign-name">___________________________</div>
            <div style="font-size: 9px; margin-top: 2px;">NUPTK. ___________________________</div>
        </div>
    </div>
</body>
</html>
