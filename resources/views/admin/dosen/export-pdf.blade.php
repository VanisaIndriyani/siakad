<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            @page { margin: 22px 22px 26px 22px; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #0f172a; }
            .header { border: 1px solid #dbeafe; background: #0f172a; color: #eff6ff; padding: 14px 16px; border-radius: 10px; }
            .title { font-size: 16px; font-weight: 700; margin: 0; }
            .subtitle { font-size: 11px; margin-top: 4px; color: #bfdbfe; }
            .meta { font-size: 10px; color: #dbeafe; margin-top: 10px; }
            .logo { background: #ffffff; border-radius: 10px; padding: 8px 10px; border: 1px solid #e5e7eb; }
            .logo img { height: 36px; width: auto; display: block; }
            .card { margin-top: 14px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            thead th { background: #eff6ff; color: #1d4ed8; font-weight: 700; text-align: left; padding: 8px; border-bottom: 1px solid #e5e7eb; }
            tbody td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
            tbody tr:nth-child(even) td { background: #f8fafc; }
            .footer { margin-top: 10px; font-size: 10px; color: #64748b; }
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

            $logoFile = null;
            foreach ($logoCandidates as $candidate) {
                if (is_string($candidate) && is_file($candidate) && is_readable($candidate)) {
                    $logoFile = $candidate;
                    break;
                }
            }

            $logoSrc = null;
            if ($logoFile) {
                $logoData = @file_get_contents($logoFile);
                if ($logoData !== false) {
                    $ext = strtolower((string) pathinfo($logoFile, PATHINFO_EXTENSION));
                    $ext = $ext === 'jpg' ? 'jpeg' : $ext;
                    $logoSrc = 'data:image/'.$ext.';base64,'.base64_encode($logoData);
                }
            }
        @endphp

        <div class="header">
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 72px; vertical-align: middle;">
                        <div class="logo">
                            @if ($logoSrc)
                                <img src="{{ $logoSrc }}" alt="Logo" />
                            @endif
                        </div>
                    </td>
                    <td style="vertical-align: middle; padding-left: 12px;">
                        <div class="title">Data Dosen</div>
                        <div class="subtitle">{{ config('app.name') }} • Sistem Informasi Akademik</div>
                        <div class="meta">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
                    </td>
                    <td style="width: 160px; text-align: right; vertical-align: middle;">
                        <div style="font-size: 10px; color: #dbeafe;">Total</div>
                        <div style="font-size: 18px; font-weight: 700; color: #ffffff;">{{ number_format($rows->count()) }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>NIDN</th>
                        <th>Email Login</th>
                        <th>Nomor HP</th>
                        <th>Program Studi</th>
                        <th>Status Akademik</th>
                        <th>Mata Kuliah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->nik }}</td>
                            <td>{{ $row->nidn }}</td>
                            <td>{{ $row->user?->email }}</td>
                            <td>{{ $row->nomor_hp }}</td>
                            <td>{{ $row->program_studi }}</td>
                            <td>{{ $row->status_akademik }}</td>
                            <td>{{ $row->mata_kuliah }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer">Dokumen ini dihasilkan otomatis oleh {{ config('app.name') }}.</div>
    </body>
</html>
