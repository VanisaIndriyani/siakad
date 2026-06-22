<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Bimbingan Akademik</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 12px 0 6px; }
        .kv2 { width: 100%; border-collapse: collapse; }
        .kv2 td { padding: 2px 0; font-size: 11px; vertical-align: top; }
        .kv2 .label { width: 140px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 8px 10px; vertical-align: top; }
        .tbl th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .center { text-align: center; }
        .message { white-space: pre-line; }
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

        $kaprodiNuptk = $kaprodi?->nuptk ?: ($kaprodi?->nidn ?: ($kaprodi?->nip ?: null));
        $dosenPenasehatNuptk = $mahasiswa->dosenPenasehat?->nuptk ?: ($mahasiswa->dosenPenasehat?->nidn ?: ($mahasiswa->dosenPenasehat?->nip ?: null));
    @endphp

    <table>
        <tr>
            <td style="width: 130px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 125px; height: auto;" />
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

    <div class="doc-title" style="margin-bottom: 0;">RIWAYAT BIMBINGAN AKADEMIK</div>
    <div class="doc-title" style="margin-top: 2px; font-size: 13px;">MAHASISWA</div>

    <table style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv2">
                    <tr>
                        <td class="label">Nama Mahasiswa</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPM</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->npm ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fakultas</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->fakultas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->program_studi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 16px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Semester Aktif</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semesterAktif ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penasehat Akademik</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->dosenPenasehat?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor SK</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->nomor_sk_penasehat ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal SK</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->tanggal_sk_penasehat ? $mahasiswa->tanggal_sk_penasehat->format('d/m/Y') : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 40px;" class="center">No</th>
                <th style="width: 140px;">Tanggal</th>
                <th style="width: 180px;">Pengirim</th>
                <th>Pesan Bimbingan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $message)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $message->created_at?->format('d/m/Y H:i') ?: '-' }}</td>
                    <td>{{ $message->sender?->name ?: 'User' }}</td>
                    <td class="message">{{ $message->pesan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="center">Belum ada riwayat bimbingan akademik.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table style="margin-top: 16mm;">
        <tr>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Ketua Prodi</div>
                <div style="font-size: 11px; font-weight: 700;">{{ $mahasiswa->program_studi ?? '-' }}</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ $kaprodi?->nama ? trim($kaprodi->nama) : '-' }}</div>
                @if($kaprodiNuptk)
                    <div style="font-size: 10px;">NUPTK. {{ $kaprodiNuptk }}</div>
                @endif
            </td>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Penasehat Akademik</div>
                <div style="font-size: 11px; font-weight: 700;">&nbsp;</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ $mahasiswa->dosenPenasehat?->nama ? trim($mahasiswa->dosenPenasehat->nama) : '-' }}</div>
                @if($dosenPenasehatNuptk)
                    <div style="font-size: 10px;">NUPTK. {{ $dosenPenasehatNuptk }}</div>
                @endif
            </td>
            <td style="width: 33.33%; text-align: center; vertical-align: top;">
                <div style="font-size: 11px; font-weight: 700;">Mahasiswa</div>
                <div style="font-size: 11px; font-weight: 700;">&nbsp;</div>
                <div style="height: 64px;"></div>
                <div style="font-size: 11px; font-weight: 800;">{{ trim((string) ($mahasiswa->nama_lengkap ?? '-')) }}</div>
                @if($mahasiswa->npm)
                    <div style="font-size: 10px;">NPM. {{ $mahasiswa->npm }}</div>
                @endif
            </td>
        </tr>
    </table>

    @if(!empty($printedBy))
        <div style="margin-top: 8mm; font-size: 10px; color: #4b5563;">
            Dicetak oleh: {{ $printedBy }}
        </div>
    @endif

    @if(!empty($autoPrint))
        <script>
            window.print();
        </script>
    @endif
</body>
</html>
