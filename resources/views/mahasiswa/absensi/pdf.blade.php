<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #111827; font-size: 19px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #111827; font-size: 27px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #111827; font-size: 19px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #111827; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 7px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 10px; text-transform: uppercase; }
        
        .kv2 { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kv2 td { padding: 2px 0; font-size: 11px; vertical-align: top; border: none; }
        .kv2 .label { width: 130px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }

        .tbl { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 7px 8px; vertical-align: top; }
        .tbl th { background-color: #f8fafc; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }
        .center { text-align: center; }
        .text-left { text-align: left !important; }
        .nowrap { white-space: nowrap; }

        .footer-note { margin-top: 20px; font-size: 10px; color: #64748b; font-style: italic; }
    </style>
</head>
<body>
    @php
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
        
        $semesterLabel = ((int) $semester % 2 === 0) ? 'GENAP' : 'GANJIL';
        $ta = date('Y') . '/' . (date('Y') + 1);
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

    <div class="doc-title">REKAP KEHADIRAN MAHASISWA</div>

    <table style="margin-bottom: 12px; border: none;">
        <tr>
            <td style="width: 55%; vertical-align: top; border: none;">
                <table class="kv2">
                    <tr>
                        <td class="label">Nama Mahasiswa</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPM</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->npm }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->program_studi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; border: none; padding-left: 20px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester / TA</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semester }} ({{ $semesterLabel }}) / {{ $ta }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dicetak Pada</td>
                        <td class="colon">:</td>
                        <td class="value" style="font-weight: 400; font-size: 10px;">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
        <tr>
            <th style="width: 35px;" class="center">No</th>
            <th style="width: 100px;">Pertemuan</th>
            <th style="width: 110px;">Tanggal</th>
            <th class="text-left">Materi Kuliah</th>
            <th style="width: 80px;" class="center">Status</th>
            <th style="width: 140px;" class="text-left">Keterangan</th>
        </tr>
        </thead>
        <tbody>
        @forelse ($items as $item)
            @php
                $status = (string) ($item->status ?? '');
                $label = match ($status) {
                    'hadir' => 'Hadir',
                    'izin' => 'Izin',
                    'sakit' => 'Sakit',
                    'alpha' => 'Alpha',
                    default => '-',
                };
                $isHadir = $status === 'hadir';
            @endphp
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center" style="font-weight: 700;">Ke-{{ $item->absensi->pertemuan }}</td>
                <td class="center">{{ $item->absensi->tanggal?->format('d F Y') ?? '-' }}</td>
                <td class="text-left" style="font-size: 10px;">{{ $item->absensi->materi ?: '-' }}</td>
                <td class="center" style="font-weight: 700; text-transform: uppercase; font-size: 10px; color: {{ $isHadir ? '#059669' : '#dc2626' }};">{{ $label }}</td>
                <td class="text-left" style="font-size: 10px;">{{ $item->keterangan ?: '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="center" style="padding: 20px; color: #64748b;">Belum ada riwayat kehadiran untuk mata kuliah ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        * Dokumen ini merupakan rekapitulasi kehadiran resmi yang dihasilkan oleh Sistem Informasi Akademik.
    </div>
</body>
</html>
