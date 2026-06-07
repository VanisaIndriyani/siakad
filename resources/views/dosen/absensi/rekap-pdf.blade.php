<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Absensi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 8mm 10mm 8mm;
        }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        
        /* Kop Surat Styles */
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        
        .doc-title { text-align: center; font-size: 13px; font-weight: 900; margin: 10px 0 8px; text-transform: uppercase; }
        
        .kv2 { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .kv2 td { padding: 1px 0; font-size: 9px; vertical-align: top; border: none; text-align: left; }
        .kv2 .label { width: 100px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }

        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 4px 2px; text-align: center; }
        .tbl th { background-color: #f3f4f6; font-size: 8px; font-weight: 800; text-transform: uppercase; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        
        .sign-table { margin-top: 20px; width: 100%; border-collapse: collapse; }
        .sign-table td { border: none; width: 50%; text-align: center; vertical-align: top; padding: 0; }
        .sign-space { height: 50px; }
        .sign-name { font-weight: 800; text-decoration: underline; }
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
    @endphp

    <table style="border: none;">
        <tr>
            <td style="width: 130px; vertical-align: middle; padding-top: 2px; border: none;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 125px; height: auto;" />
                @endif
            </td>
            <td style="text-align: center; border: none;">
                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>
                <div class="kop-meta" style="font-weight: 700;">{{ $kop4 }}</div>
                <div class="kop-meta">{{ $kop5 }}</div>
                <div class="kop-meta">{{ $kop6 }}</div>
            </td>
            <td style="width: 100px; border: none;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">REKAPITULASI ABSENSI MAHASISWA</div>

    <table style="margin-bottom: 8px; border: none;">
        <tr>
            <td style="width: 55%; vertical-align: top; border: none;">
                <table class="kv2">
                    <tr>
                        <td class="label">Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mk->kode }} - {{ $mk->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dosen Pengampu</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dosenNama }} (NUPTK. {{ $dosen?->nuptk ?? '-' }})</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; border: none; padding-left: 20px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $jurusan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester / TA</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semester }} / {{ date('Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 75px;">NPM</th>
                <th rowspan="2" class="text-left" style="width: 200px;">Nama Mahasiswa</th>
                <th colspan="16">Pertemuan Ke-</th>
                <th colspan="4">Total</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th style="width: 18px; font-size: 8px;">{{ $i }}</th>
                @endfor
                <th style="width: 18px; font-size: 8px;">H</th>
                <th style="width: 18px; font-size: 8px;">I</th>
                <th style="width: 18px; font-size: 8px;">S</th>
                <th style="width: 18px; font-size: 8px;">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $row)
                @php
                    $map = [
                        'hadir' => 'H',
                        'izin' => 'I',
                        'sakit' => 'S',
                        'alpha' => 'A',
                    ];
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-size: 8px;">{{ $row['npm'] }}</td>
                    <td class="text-left" style="font-size: 8px;">{{ $row['nama'] }}</td>
                    @foreach (range(1, 16) as $p)
                        @php
                            $st = $row['pertemuan'][$p] ?? null;
                        @endphp
                        <td style="font-size: 7px;">{{ $st ? ($map[$st] ?? '-') : '-' }}</td>
                    @endforeach
                    <td style="font-size: 8px; font-weight: 700;">{{ (int) ($row['totals']['hadir'] ?? 0) }}</td>
                    <td style="font-size: 8px;">{{ (int) ($row['totals']['izin'] ?? 0) }}</td>
                    <td style="font-size: 8px;">{{ (int) ($row['totals']['sakit'] ?? 0) }}</td>
                    <td style="font-size: 8px;">{{ (int) ($row['totals']['alpha'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="page-break-inside: avoid; margin-top: 15px;">
        <div style="font-weight: 800; text-transform: uppercase; margin-bottom: 5px; font-size: 9px;">Jurnal Materi Perkuliahan</div>
        <table class="tbl" style="width: 100%;">
            <thead>
                <tr>
                    <th style="width: 30px;">Prt.</th>
                    <th style="text-align: left; padding-left: 5px;">Materi Pembelajaran</th>
                    <th style="width: 30px;">Prt.</th>
                    <th style="text-align: left; padding-left: 5px;">Materi Pembelajaran</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 1; $i <= 8; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        <td class="text-left" style="font-size: 8px;">{{ $materiList[$i] ?? '-' }}</td>
                        <td>{{ $i + 8 }}</td>
                        <td class="text-left" style="font-size: 8px;">{{ $materiList[$i + 8] ?? '-' }}</td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <table class="sign-table">
        <tr>
            <td>
                <div style="font-weight: 700;">Mengetahui,</div>
                <div style="font-weight: 700;">Ketua Program Studi</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $kaprodiNama ?? '........................................' }}</div>
                <div style="font-size: 9px; margin-top: 2px;">NUPTK. {{ $kaprodi?->nuptk ?? '.....................................' }}</div>
            </td>
            <td>
                <div style="font-weight: 700;">Sidrap, {{ date('d F Y') }}</div>
                <div style="font-weight: 700;">Dosen Pengampu,</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $dosenNama }}</div>
                <div style="font-size: 9px; margin-top: 2px;">NUPTK. {{ $dosen?->nuptk ?? '.....................................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
