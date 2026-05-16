<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            @page { margin: 18px 18px 22px 18px; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #0f172a; }
            .header { border: 1px solid #e5e7eb; padding: 12px 14px; border-radius: 10px; }
            .title { font-size: 14px; font-weight: 700; margin: 0; }
            .meta { margin-top: 6px; font-size: 10px; color: #334155; }
            .meta-row { margin-top: 3px; }
            .logo { width: 64px; vertical-align: top; }
            .logo img { height: 44px; width: auto; display: block; border: 1px solid #e5e7eb; padding: 6px 8px; border-radius: 10px; background: #fff; }
            .card { margin-top: 12px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            thead th { background: #f1f5f9; color: #0f172a; font-weight: 700; text-align: left; padding: 7px 8px; border-bottom: 1px solid #e5e7eb; }
            tbody td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
            tbody tr:nth-child(even) td { background: #fafafa; }
            .footer { margin-top: 10px; font-size: 10px; color: #64748b; }
            .muted { color: #64748b; }
            .sign { margin-top: 14px; }
            .sign table { width: 86%; margin: 0 auto; }
            .sign td { width: 50%; vertical-align: top; padding-top: 2px; }
            .sign .label { font-weight: 700; font-size: 10.5px; }
            .sign .sub { font-size: 10px; color: #334155; margin-top: 2px; }
            .sign .space { height: 54px; }
            .sign .name { font-weight: 700; font-size: 10.5px; }
        </style>
    </head>
    <body>
        @php
            $logoFile = public_path('img/lo.jpeg');
            $logoSrc = is_file($logoFile)
                ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoFile))
                : null;
            $mk = $absensi->mataKuliah;
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
                        <div class="title">Daftar Hadir (Absensi)</div>
                        <div class="meta">
                            <div class="meta-row"><span class="muted">Kampus:</span> {{ config('app.name') }}</div>
                            <div class="meta-row"><span class="muted">Jurusan:</span> {{ $absensi->jurusan }} • <span class="muted">Semester:</span> {{ $absensi->semester }}</div>
                            <div class="meta-row"><span class="muted">Mata Kuliah:</span> {{ $mk?->kode }} - {{ $mk?->nama }}</div>
                            <div class="meta-row"><span class="muted">Pertemuan:</span> {{ $absensi->pertemuan }} • <span class="muted">Tanggal:</span> {{ $absensi->tanggal?->format('d/m/Y') ?? '__________' }}</div>
                            <div class="meta-row"><span class="muted">Materi:</span> {{ $absensi->materi ?? '__________' }}</div>
                        </div>
                    </td>
                    <td style="width: 160px; text-align: right; vertical-align: top;">
                        <div class="meta">
                            <div class="meta-row"><span class="muted">Dicetak:</span> {{ now()->format('d/m/Y H:i') }}</div>
                            <div class="meta-row"><span class="muted">Role:</span> {{ strtoupper($role ?? '-') }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th style="width: 34px;">No</th>
                        <th style="width: 110px;">NPM</th>
                        <th>Nama</th>
                        <th style="width: 90px;">Status</th>
                        <th style="width: 140px;">Keterangan</th>
                        <th style="width: 80px;">Paraf</th>
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
                                default => '',
                            };
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->mahasiswa?->npm }}</td>
                            <td>{{ $item->mahasiswa?->nama_lengkap }}</td>
                            <td>{{ $statusText }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td></td>
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
                        <div class="sub">{{ $absensi->jurusan }}</div>
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
