<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi Pertemuan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 10mm 10mm 10mm;
        }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        
        /* Kop Surat Styles */
        .kop-title-1 { color: #111827; font-size: 19px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #111827; font-size: 27px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #111827; font-size: 19px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #111827; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 7px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 3px; }
        
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 6px; text-transform: uppercase; }
        .doc-subtitle { text-align: center; font-size: 12px; font-weight: 800; margin-bottom: 12px; }
        
        .kv2 { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kv2 td { padding: 2px 0; font-size: 11px; vertical-align: top; border: none; text-align: left; }
        .kv2 .label { width: 120px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }

        .tbl { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 7px 8px; text-align: center; }
        .tbl th { background-color: #f8fafc; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .text-left { text-align: left !important; padding-left: 10px !important; }
        
        .sign-table { margin-top: 25px; width: 100%; border-collapse: collapse; }
        .sign-table td { border: none; width: 50%; text-align: center; vertical-align: top; padding: 0; }
        .sign-space { height: 64px; }
        .sign-name { font-weight: 900; text-decoration: underline; font-size: 11.5px; }
        .sign-label { font-weight: 800; font-size: 11px; margin-bottom: 2px; }
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

        $pickNomor = function ($dosen) {
            foreach ([$dosen?->nuptk, $dosen?->nidn, $dosen?->nip] as $nomor) {
                $nomor = trim((string) $nomor);
                if ($nomor !== '') {
                    return $nomor;
                }
            }

            return null;
        };

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';
        $mk = $absensi->mataKuliah;
        
        $semesterLabel = ((int) $absensi->semester % 2 === 0) ? 'GENAP' : 'GANJIL';
        $ta = date('Y') . '/' . (date('Y') + 1);
        $kaprodiNuptk = $pickNomor($kaprodi ?? null);
        $dosenNuptk = $pickNomor($dosen ?? null);
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
            <td style="width: 90px; border: none;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">DAFTAR HADIR MAHASISWA</div>
    <div class="doc-subtitle">PERTEMUAN KE : {{ $absensi->pertemuan }}</div>

    <table style="margin-bottom: 12px; border: none;">
        <tr>
            <td style="width: 55%; vertical-align: top; border: none;">
                <table class="kv2">
                    <tr>
                        <td class="label">Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mk?->kode }} - {{ $mk?->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $absensi->jurusan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester / TA</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $absensi->semester }} ({{ $semesterLabel }}) / {{ $ta }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; border: none; padding-left: 20px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Dosen Pengampu</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dosenNama ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $absensi->tanggal?->format('d F Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Waktu Cetak</td>
                        <td class="colon">:</td>
                        <td class="value" style="font-weight: 400; font-size: 10px;">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($absensi->materi)
        <div style="margin-bottom: 14px;">
            <div style="font-weight: 800; font-size: 11px; margin-bottom: 4px; text-transform: uppercase; color: #374151;">Materi Perkuliahan:</div>
            <div style="padding: 10px 12px; border: 1px solid #d1d5db; background: #f9fafb; font-size: 11px; border-radius: 4px; line-height: 1.5;">
                {{ $absensi->materi }}
            </div>
        </div>
    @endif

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 35px;">No</th>
                <th style="width: 110px;">NPM</th>
                <th class="text-left">Nama Lengkap Mahasiswa</th>
                <th style="width: 85px;">Status</th>
                <th style="width: 130px;">Keterangan</th>
                <th style="width: 90px;">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $i => $item)
                @php
                    $statusText = match ($item->status) {
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                        default => '-',
                    };
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight: 700; font-size: 10.5px;">{{ $item->mahasiswa?->npm }}</td>
                    <td class="text-left">{{ $item->mahasiswa?->nama_lengkap }}</td>
                    <td style="font-weight: 700; text-transform: uppercase; font-size: 10px;">{{ $statusText }}</td>
                    <td style="font-size: 10px;">{{ $item->keterangan ?: '-' }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-label">Ketua Program Studi</div>
                <div class="sign-label">{{ $absensi->jurusan }}</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $kaprodiNama ?: '________________________' }}</div>
                @if($kaprodiNuptk)
                    <div style="font-size: 10px;">NUPTK. {{ $kaprodiNuptk }}</div>
                @endif
            </td>
            <td>
                <div class="sign-label">Sidrap, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="sign-label">Dosen Pengampu,</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $dosenNama ?: '________________________' }}</div>
                @if($dosenNuptk)
                    <div style="font-size: 10px;">NUPTK. {{ $dosenNuptk }}</div>
                @endif
            </td>
        </tr>
    </table>
</body>
</html>
