<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            @page { margin: 14px 14px 18px 14px; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #0f172a; }
            .header { border: 1px solid #e5e7eb; padding: 10px 12px; border-radius: 10px; }
            .title { font-size: 13px; font-weight: 700; margin: 0; }
            .meta { margin-top: 6px; font-size: 9px; color: #334155; }
            .meta-row { margin-top: 2px; }
            .logo { width: 62px; vertical-align: top; }
            .logo img { height: 42px; width: auto; display: block; border: 1px solid #e5e7eb; padding: 6px 8px; border-radius: 10px; background: #fff; }
            .card { margin-top: 10px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            thead th { background: #f1f5f9; color: #0f172a; font-weight: 700; text-align: center; padding: 6px 6px; border-bottom: 1px solid #e5e7eb; }
            thead th.left { text-align: left; }
            tbody td { padding: 6px 6px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
            tbody tr:nth-child(even) td { background: #fafafa; }
            .left { text-align: left; }
            .center { text-align: center; }
            .w-no { width: 26px; }
            .w-npm { width: 88px; }
            .w-p { width: 18px; }
            .w-t { width: 36px; }
            .muted { color: #64748b; }
            .sign { margin-top: 12px; }
            .sign table { width: 78%; margin: 0 auto; }
            .sign td { width: 50%; vertical-align: top; padding-top: 2px; }
            .sign .label { font-weight: 700; font-size: 10px; }
            .sign .sub { font-size: 9.5px; color: #334155; margin-top: 2px; }
            .sign .space { height: 52px; }
            .sign .name { font-weight: 700; font-size: 10px; }
            .footer { margin-top: 8px; font-size: 9px; color: #64748b; }
        </style>
    </head>
    <body>
        @php
            $logoFile = public_path('img/lo.jpeg');
            $logoSrc = is_file($logoFile)
                ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoFile))
                : null;
        @endphp

        <div class="header">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td class="logo">
                        @if ($logoSrc)
                            <img src="{{ $logoSrc }}" alt="Logo" />
                        @endif
                    </td>
                    <td style="padding-left: 10px; vertical-align: top;">
                        <div class="title">Rekap Absensi Mata Kuliah</div>
                        <div class="meta">
                            <div class="meta-row"><span class="muted">Kampus:</span> {{ config('app.name') }}</div>
                            <div class="meta-row"><span class="muted">Jurusan:</span> {{ $jurusan }} • <span class="muted">Semester:</span> {{ $semester }}</div>
                            <div class="meta-row"><span class="muted">Mata Kuliah:</span> {{ $mk?->kode }} - {{ $mk?->nama }}</div>
                            <div class="meta-row"><span class="muted">Dosen:</span> {{ $dosenNama ?: '-' }}</div>
                        </div>
                    </td>
                    <td style="width: 150px; text-align: right; vertical-align: top;">
                        <div class="meta">
                            <div class="meta-row"><span class="muted">Dicetak:</span> {{ now()->format('d/m/Y H:i') }}</div>
                            <div class="meta-row"><span class="muted">Keterangan:</span> H (Hadir) • I (Izin) • S (Sakit) • A (Alpha)</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th class="w-no">No</th>
                        <th class="w-npm left">NPM</th>
                        <th class="left">Nama</th>
                        @foreach (range(1, 16) as $p)
                            <th class="w-p">{{ $p }}</th>
                        @endforeach
                        <th class="w-t">H</th>
                        <th class="w-t">I</th>
                        <th class="w-t">S</th>
                        <th class="w-t">A</th>
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
                            <td class="center">{{ $i + 1 }}</td>
                            <td class="left">{{ $row['npm'] }}</td>
                            <td class="left">{{ $row['nama'] }}</td>
                            @foreach (range(1, 16) as $p)
                                @php
                                    $st = $row['pertemuan'][$p] ?? null;
                                @endphp
                                <td class="center">{{ $st ? ($map[$st] ?? '-') : '-' }}</td>
                            @endforeach
                            <td class="center">{{ (int) ($row['totals']['hadir'] ?? 0) }}</td>
                            <td class="center">{{ (int) ($row['totals']['izin'] ?? 0) }}</td>
                            <td class="center">{{ (int) ($row['totals']['sakit'] ?? 0) }}</td>
                            <td class="center">{{ (int) ($row['totals']['alpha'] ?? 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="sign">
            <table>
                <tr>
                    <td>
                        <div class="label">Ketua Prodi</div>
                        <div class="sub">{{ $jurusan }}</div>
                        <div class="space"></div>
                        <div class="name">{{ $kaprodiNama ?: '________________' }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="label">Dosen Pengampu</div>
                        <div class="sub">{{ $mk?->kode }} - {{ $mk?->nama }}</div>
                        <div class="space"></div>
                        <div class="name">{{ $dosenNama ?: '________________' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">Dokumen ini dihasilkan otomatis oleh {{ config('app.name') }}.</div>
    </body>
</html>
