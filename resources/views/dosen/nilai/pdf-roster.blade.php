<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Roster Kuliah - {{ $mataKuliah->kode }}</title>
    <style>
        @page { margin: 14mm 10mm 16mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 10px 0 2px; text-transform: uppercase; letter-spacing: 0.4px; }
        .doc-sub { text-align: center; font-size: 12px; font-weight: 700; margin: 0 0 10px; }
        .kv td { padding: 2px 0; font-size: 10.5px; vertical-align: top; }
        .kv .label { width: 125px; font-weight: 700; }
        .kv .colon { width: 10px; text-align: center; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 5px 6px; vertical-align: top; font-size: 10px; }
        .tbl th { background: #f3f4f6; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.4px; text-align: center; }
        .tbl th.left, .tbl td.left { text-align: left; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .sign-wrap { margin-top: 22px; }
        .sign-table { width: 100%; }
        .sign-table td { vertical-align: top; font-size: 10.5px; padding: 0; }
        .sign-space { height: 52px; }
        .sign-name { font-weight: 800; text-decoration: underline; margin-bottom: 2px; }
        .muted { color: #4b5563; font-size: 10px; }
        .page-break { page-break-after: always; }
        .mhs-col { width: 55px; }
    </style>
</head>
<body>
    @php
        $logoCandidates = [
            public_path('img/lo.jpeg'), public_path('img/logo.png'),
            base_path('../img/lo.jpeg'), base_path('../img/logo.png'),
            base_path('../public/img/lo.jpeg'), base_path('../public/img/logo.png'),
        ];
        $logoPath = null;
        foreach ($logoCandidates as $c) {
            if (is_string($c) && is_file($c) && is_readable($c)) { $logoPath = $c; break; }
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

        $semester = (int) ($semester ?? $mataKuliah->semester ?? 1);
        $semesterLabel = ($semester % 2 === 0) ? 'GENAP' : 'GANJIL';
        $dosen = $relatedDosen ?? ($mataKuliah->dosen ?? $mataKuliah->dosen2);
        $dosenNama = $dosen?->nama_lengkap ?? '-';
        $dosenNuptk = $dosen?->nuptk ?? ($dosen?->nidn ?? null);
        $programStudi = $mataKuliah->jurusan ?? '-';
        $sks = $mataKuliah->sks ?? 0;
        $tglSekarang = date('d F Y');

        if ($rosters->count() === 0) {
            $rosters = collect();
            for ($i = 1; $i <= 16; $i++) {
                $rosters->push((object) [
                    'pertemuan_ke' => $i, 'hari' => null, 'tanggal' => null,
                    'jam_mulai' => null, 'jam_selesai' => null,
                    'ruang' => null, 'materi' => null, 'metode_pembelajaran' => null,
                    'keterangan' => null,
                ]);
            }
        }

        $maxPertemuan = max(16, (int) $rosters->max('pertemuan_ke'));
        $mhs = $krsMahasiswa->map(fn ($k) => $k->mahasiswa)->filter(fn ($m) => $m !== null)->values();
        $chunkMhs = $mhs->chunk(18);
    @endphp

    <table>
        <tr>
            <td style="width: 115px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64) <img src="{{ $logoBase64 }}" alt="Logo" style="display:block;width:115px;height:auto;" /> @endif
            </td>
            <td style="text-align:center;">
                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>
                <div class="kop-meta" style="font-weight:700;">{{ $kop4 }}</div>
                <div class="kop-meta">{{ $kop5 }}</div>
                <div class="kop-meta">{{ $kop6 }}</div>
            </td>
            <td style="width:80px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">Roster / Daftar Jadwal Perkuliahan & Absensi Mahasiswa</div>
    <div class="doc-sub">Semester {{ $semesterLabel }} (Semester {{ $semester }}) - {{ $programStudi }}</div>

    <table class="kv" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv" style="width: 100%;">
                    <tr><td class="label">Kode / Mata Kuliah</td><td class="colon">:</td><td><strong>{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</strong></td></tr>
                    <tr><td>Dosen Pengampu</td><td class="colon">:</td><td>{{ $dosenNama }}</td></tr>
                    <tr><td>Program Studi / SKS</td><td class="colon">:</td><td>{{ $programStudi }} • {{ $sks }} SKS</td></tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv" style="width: 100%;">
                    <tr><td class="label">Semester / TA</td><td class="colon">:</td><td>{{ $semester }} ({{ $semesterLabel }})</td></tr>
                    <tr><td>NUPTK Dosen</td><td class="colon">:</td><td>{{ $dosenNuptk ?? '-' }}</td></tr>
                    <tr><td>Tanggal Cetak</td><td class="colon">:</td><td>{{ $tglSekarang }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="font-weight: 800; margin: 10px 0 6px;">BAGIAN 1 — Jadwal Pertemuan & Materi</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 30px;">Prt.</th>
                <th style="width: 56px;">Hari</th>
                <th style="width: 72px;">Tanggal</th>
                <th style="width: 62px;">Jam</th>
                <th style="width: 52px;">Ruang</th>
                <th class="left">Materi Pembelajaran</th>
                <th style="width: 80px;">Metode</th>
                <th class="left" style="width: 110px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rosters as $r)
                <tr>
                    <td class="center">{{ $r->pertemuan_ke }}</td>
                    <td class="center">{{ $r->hari ?? '-' }}</td>
                    <td class="center">
                        @if (!empty($r->tanggal))
                            {{ is_object($r->tanggal) ? $r->tanggal->format('d/m/Y') : $r->tanggal }}
                        @else - @endif
                    </td>
                    <td class="center">
                        @if (!empty($r->jam_mulai))
                            {{ (is_object($r->jam_mulai) ? $r->jam_mulai->format('H:i') : substr((string)$r->jam_mulai, 0, 5)) }}
                            -
                            {{ !empty($r->jam_selesai) ? (is_object($r->jam_selesai) ? $r->jam_selesai->format('H:i') : substr((string)$r->jam_selesai, 0, 5)) : '--:--' }}
                        @else - @endif
                    </td>
                    <td class="center">{{ $r->ruang ?? '-' }}</td>
                    <td class="left">{{ $r->materi ?? '-' }}</td>
                    <td class="center">{{ $r->metode_pembelajaran ?? '-' }}</td>
                    <td class="left">{{ $r->keterangan ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @foreach ($chunkMhs as $chunkIndex => $chunk)
        @if ($chunkIndex > 0) <div class="page-break"></div> @endif
        <div style="font-weight: 800; margin: 14px 0 6px;">
            BAGIAN 2 — Lembar Absensi Mahasiswa @if ($chunkMhs->count() > 1) <span class="muted">(Lembar {{ $chunkIndex + 1 }} / {{ $chunkMhs->count() }})</span> @endif
        </div>
        <table class="tbl">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 64px;">NPM</th>
                    <th class="left">Nama Mahasiswa</th>
                    @for ($p = 1; $p <= $maxPertemuan; $p++)
                        <th class="mhs-col">{{ $p }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($chunk as $i => $m)
                    @php $no = $chunkIndex * 18 + ($i + 1); @endphp
                    <tr>
                        <td class="center">{{ $no }}</td>
                        <td class="center nowrap">{{ $m->npm ?? '-' }}</td>
                        <td class="left">{{ $m->nama_lengkap ?? '-' }}</td>
                        @for ($p = 1; $p <= $maxPertemuan; $p++) <td>&nbsp;</td> @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <table class="sign-wrap sign-table">
        <tr>
            <td style="width: 50%;">
                <div class="muted">Mengetahui,</div>
                <div class="muted" style="margin-bottom: 4px;">Ketua Program Studi {{ $programStudi }}</div>
                <div class="sign-space"></div>
                <div class="sign-name">........................................</div>
                <div class="muted">NUPTK. .....................................</div>
            </td>
            <td style="width: 50%;">
                <div style="margin-bottom: 4px;">Sidrap, {{ $tglSekarang }}</div>
                <div style="font-weight: 800; margin-bottom: 4px;">Dosen Pengampu Mata Kuliah</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $dosenNama }}</div>
                <div class="muted">NUPTK. {{ $dosenNuptk ?? '.....................................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
