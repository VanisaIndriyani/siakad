<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Data Akun User</title>
    <style>
        @page { margin: 1.5cm; }
        * { font-family: 'Helvetica', 'Arial', sans-serif; box-sizing: border-box; }
        body { font-size: 10px; color: #111827; line-height: 1.4; }
        .kop-surat { width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 2px; }
        .kop-logo { width: 110px; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 100px; height: auto; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-title-1 { font-size: 16px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { font-size: 22px; font-weight: 900; margin: 2px 0; line-height: 1; }
        .kop-title-3 { font-size: 16px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-meta { font-size: 9px; font-weight: 700; margin-top: 3px; }
        .kop-alamat { font-size: 9px; margin: 2px 0; }
        .kop-line-2 { border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table th { background: #f3f4f6; border: 1px solid #000; padding: 6px; font-weight: bold; text-align: center; }
        table.data-table td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        .footer { margin-top: 20px; text-align: right; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <table class="kop-surat">
        <tr>
            <td class="kop-logo">
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
                        $logoData = @file_get_contents($logoPath);
                        if ($logoData !== false) {
                            $ext = strtolower((string) pathinfo($logoPath, PATHINFO_EXTENSION));
                            $ext = $ext === 'jpg' ? 'jpeg' : $ext;
                            $logoBase64 = 'data:image/'.$ext.';base64,'.base64_encode($logoData);
                        }
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-title-3">SIDENRENG RAPPANG</div>
                <div class="kop-meta">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                <div class="kop-alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="kop-alamat">E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id</div>
            </td>
            <td style="width: 90px;"></td>
        </tr>
    </table>
    <div class="kop-line-2"></div>

    <div class="doc-title">Laporan Data Akun User</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama</th>
                <th>Email</th>
                <th style="width: 80px;">Role</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $i => $u)
                @php
                    $roleStyle = match($u->role) {
                        'admin' => 'color: #e11d48; font-weight: bold;',
                        'keuangan' => 'color: #d97706; font-weight: bold;',
                        'dosen' => 'color: #2563eb; font-weight: bold;',
                        'mahasiswa' => 'color: #059669; font-weight: bold;',
                        default => 'color: #4b5563;',
                    };
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $i + 1 }}</td>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td style="text-align: center; font-size: 8px; {{ $roleStyle }}">{{ strtoupper($u->role) }}</td>
                    <td style="font-family: monospace; font-size: 9px; word-break: break-all;">{{ $u->password_plain ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>
