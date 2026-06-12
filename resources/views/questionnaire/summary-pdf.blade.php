<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Kuesioner</title>
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
        .summary-table td { padding: 4px 0; font-size: 10px; }
        .summary-label { width: 130px; }
        .summary-colon { width: 10px; text-align: center; }
        .summary-value { font-weight: 700; }
        .tbl { margin-top: 8px; table-layout: fixed; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 5px 6px; word-break: break-word; }
        .tbl th { font-size: 8.5px; text-transform: uppercase; letter-spacing: 0.2px; }
        .tbl td { font-size: 9px; }
        .center { text-align: center; }
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
    @endphp

    <table>
        <tr>
            <td style="width: 170px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 155px; height: auto;" />
                @endif
            </td>
            <td style="text-align: center;">
                <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-title-3">SIDENRENG RAPPANG</div>
                <div class="kop-meta" style="font-weight: 700;">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                <div class="kop-meta">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="kop-meta">E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id</div>
            </td>
            <td style="width: 110px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">REKAP KUESIONER MAHASISWA</div>

    <table class="summary-table" style="margin-bottom: 10px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Filter Pencarian</td>
                        <td class="summary-colon">:</td>
                        <td class="summary-value">{{ $q !== '' ? $q : 'Semua data' }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Total Respon</td>
                        <td class="summary-colon">:</td>
                        <td class="summary-value">{{ $summary['responses_count'] }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Mahasiswa Mengisi</td>
                        <td class="summary-colon">:</td>
                        <td class="summary-value">{{ $summary['students_count'] }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 16px;">
                <table class="summary-table">
                    <tr>
                        <td class="summary-label">Pertanyaan Aktif</td>
                        <td class="summary-colon">:</td>
                        <td class="summary-value">{{ $summary['questions_count'] }}</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Rata-rata Skor</td>
                        <td class="summary-colon">:</td>
                        <td class="summary-value">{{ $summary['average_score'] !== null ? number_format((float) $summary['average_score'], 2) : '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th style="width: 24px;" class="center">No</th>
                <th style="width: 70px;">Kode</th>
                <th>Mata Kuliah</th>
                <th style="width: 44px;" class="center">Smt</th>
                <th style="width: 120px;">Dosen 1</th>
                <th style="width: 120px;">Dosen 2</th>
                <th style="width: 48px;" class="center">Respon</th>
                <th style="width: 56px;" class="center">Rata2</th>
                <th style="width: 50px;" class="center">Krg</th>
                <th style="width: 50px;" class="center">Ckp</th>
                <th style="width: 50px;" class="center">Baik</th>
                <th style="width: 62px;" class="center">Sgt Baik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courseSummaries as $course)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $course->kode }}</td>
                    <td>{{ $course->nama }}</td>
                    <td class="center">{{ $course->semester }}</td>
                    <td>{{ $course->dosen_1 ?? '-' }}</td>
                    <td>{{ $course->dosen_2 ?? '-' }}</td>
                    <td class="center">{{ $course->responses_count }}</td>
                    <td class="center">{{ $course->average_score !== null ? number_format((float) $course->average_score, 2) : '-' }}</td>
                    <td class="center nowrap">{{ $course->score_1_pct !== null ? number_format((float) $course->score_1_pct, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $course->score_2_pct !== null ? number_format((float) $course->score_2_pct, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $course->score_3_pct !== null ? number_format((float) $course->score_3_pct, 2).'%' : '-' }}</td>
                    <td class="center nowrap">{{ $course->score_4_pct !== null ? number_format((float) $course->score_4_pct, 2).'%' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">Belum ada data rekap kuesioner.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
