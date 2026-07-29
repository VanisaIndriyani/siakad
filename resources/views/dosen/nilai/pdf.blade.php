<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Nilai</title>
    <style>
        @page { margin: 8mm 7mm 8mm 9mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 17px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { color: #000; font-size: 23px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 17px; font-weight: 900; margin: 1px 0 0; line-height: 1.1; }
        .kop-meta { color: #000; font-size: 10.5px; margin-top: 2px; line-height: 1.15; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 5px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 2px; }
        .doc-title { text-align: center; font-size: 12.5px; font-weight: 900; margin: 7px 0 4px; }
        .kv2 td { padding: 1.2px 0; font-size: 10px; vertical-align: top; }
        .kv2 .label { width: 140px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 3px 5px; font-size: 9px; }
        .tbl th { text-transform: uppercase; letter-spacing: 0.3px; font-size: 8.5px; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .sign-wrap { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .sign-box { width: 42%; margin-left: auto; text-align: center; }
        .sign-space { height: 38px; }
        .sign-name { font-weight: 800; font-size: 9px; }
        .page-break { page-break-after: always; }
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

        $relatedDosenNomor = $pickNomor($relatedDosen ?? null);
        $krsChunks = collect($krs ?? [])->chunk(15);
        if ($krsChunks->isEmpty()) {
            $krsChunks = collect([collect()]);
        }
        $totalChunks = $krsChunks->count();
        $chunkIndex = 0;
    @endphp

    @foreach ($krsChunks as $chunk)
        @php
            $chunkIndex++;
            $isLastChunk = $chunkIndex >= $totalChunks;
            $startNo = ($chunkIndex - 1) * 15 + 1;
        @endphp
        <div>
            <table>
                <tr>
                    <td style="width: 115px; vertical-align: middle; padding-top: 2px;">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 108px; height: auto;" />
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
                    <td style="width: 90px;"></td>
                </tr>
            </table>
            <div class="kop-line-1"></div>
            <div class="kop-line-2"></div>

            <div class="doc-title">DAFTAR INPUT NILAI MAHASISWA</div>

            <table style="margin-bottom: 8px;">
                <tr>
                    <td style="width: 50%; vertical-align: top;">
                        <table class="kv2">
                            <tr>
                                <td class="label">Kode Mata Kuliah</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $mataKuliah->kode }}</td>
                            </tr>
                            <tr>
                                <td class="label">Mata Kuliah</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $mataKuliah->nama }}</td>
                            </tr>
                            <tr>
                                <td class="label">Semester</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $semester }}</td>
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
                        </table>
                    </td>
                </tr>
            </table>

            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width: 34px;" class="center">No</th>
                        <th>Mahasiswa</th>
                        <th style="width: 120px;">NPM</th>
                        <th style="width: 90px;" class="center">Nilai Angka</th>
                        <th style="width: 90px;" class="center">Nilai Huruf</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($chunk->count())
                        @foreach ($chunk as $row)
                            @php
                                $mhs = $row->mahasiswa;
                                $existingItem = $existing->get($row->mahasiswa_id);
                            @endphp
                            <tr>
                                <td class="center">{{ $startNo + $loop->index }}</td>
                                <td>{{ $mhs?->nama_lengkap ?? '-' }}</td>
                                <td class="nowrap">{{ $mhs?->npm ?? '-' }}</td>
                                <td class="center">{{ $existingItem?->nilai_angka !== null ? number_format((float) $existingItem->nilai_angka, 2) : '-' }}</td>
                                <td class="center">{{ $existingItem?->nilai_huruf ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="center">Tidak ada mahasiswa pada mata kuliah ini.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($isLastChunk)
            <div class="sign-wrap">
                <div class="sign-box">
                    <div style="font-weight: 700; font-size: 9px;">Sidrap, {{ date('d F Y') }}</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $relatedDosen?->nama ?? '-' }}</div>
                    @if($relatedDosenNomor)
                        <div style="font-size: 8.5px; margin-top: 2px;">NUPTK. {{ $relatedDosenNomor }}</div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @if (!$isLastChunk)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
