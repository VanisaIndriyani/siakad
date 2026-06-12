<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Kuesioner</title>
    <style>
        @page { margin: 12mm 10mm 12mm 12mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 24px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { color: #000; font-size: 36px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.04; }
        .kop-title-3 { color: #000; font-size: 24px; font-weight: 900; margin: 1px 0 0; line-height: 1.1; }
        .kop-meta { color: #000; font-size: 13px; margin-top: 4px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 9px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 4px; }
        .doc-title { text-align: center; font-size: 13px; font-weight: 900; margin: 12px 0 5px; }
        .kv2 { width: 100%; border-collapse: collapse; }
        .kv2 td { padding: 2px 0; font-size: 10px; vertical-align: top; }
        .kv2 .label { width: 110px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; word-break: break-word; }
        .tbl { margin-top: 8px; table-layout: fixed; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 5px 6px; word-break: break-word; }
        .tbl th { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.2px; }
        .tbl td { font-size: 9px; }
        .center { text-align: center; }
        .section-title { margin-top: 12px; font-size: 11px; font-weight: 900; }
        .nowrap { white-space: nowrap; }
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

    <table>
        <tr>
            <td style="width: 170px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 155px; height: auto;" />
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
            <td style="width: 110px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title" style="margin-bottom: 0;">LAPORAN HASIL KUESIONER</div>
    <div class="doc-title" style="margin-top: 2px; font-size: 11px;">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</div>

    <table style="margin-bottom: 14px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="kv2">
                    <tr>
                        <td class="label">Kode Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->kode }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->semester }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 16px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Dosen 1</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->dosen?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dosen 2</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mataKuliah->dosen2?->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Total Respon</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $responses->count() }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">Statistik Per Pertanyaan</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 24px;" class="center">No</th>
                <th>Pertanyaan</th>
                <th style="width: 52px;" class="center">Jawab</th>
                <th style="width: 58px;" class="center">Rata2</th>
                <th style="width: 50px;" class="center">Krg</th>
                <th style="width: 50px;" class="center">Ckp</th>
                <th style="width: 50px;" class="center">Baik</th>
                <th style="width: 62px;" class="center">Sgt Baik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($questionStats as $stat)
                @php
                    $totalAnswers = (int) $stat->answers_count;
                @endphp
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $stat->question }}</td>
                    <td class="center">{{ $totalAnswers }}</td>
                    <td class="center">{{ $stat->average_score !== null ? number_format((float) $stat->average_score, 2) : '-' }}</td>
                    <td class="center nowrap">{{ $totalAnswers > 0 ? number_format(($stat->score_1_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $totalAnswers > 0 ? number_format(($stat->score_2_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $totalAnswers > 0 ? number_format(($stat->score_3_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $totalAnswers > 0 ? number_format(($stat->score_4_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Belum ada statistik kuesioner.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Komentar Mahasiswa</div>
    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 24px;" class="center">No</th>
                <th style="width: 130px;">Nama Mahasiswa</th>
                <th style="width: 80px;">NPM</th>
                <th style="width: 58px;" class="center">Rata-rata</th>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($responses as $response)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $response->mahasiswa?->nama_lengkap ?? '-' }}</td>
                    <td>{{ $response->mahasiswa?->npm ?? '-' }}</td>
                    <td class="center">{{ number_format((float) $response->answers->avg('score'), 2) }}</td>
                    <td>{{ $response->komentar ?: 'Tidak ada komentar.' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="center">Belum ada komentar atau hasil kuesioner.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
