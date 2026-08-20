<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Nilai</title>
    <style>
        @page { margin: 7mm 6mm 7mm 6mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 18px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 26px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 18px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .prodi-title { text-align: center; font-size: 14px; font-weight: 900; margin: 8px 0 3px; }
        .kv2 td { padding: 1.2px 0; font-size: 9.5px; vertical-align: top; }
        .kv2 .label { width: 120px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 3px 4px; font-size: 8.5px; vertical-align: middle; }
        .tbl th { text-transform: uppercase; letter-spacing: 0.2px; font-size: 7.8px; background: #f3f4f6; }
        .tbl .ghead { font-size: 9px; background: #e5e7eb; font-weight: 900; }
        .center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .sign-wrap { width: 100%; margin-top: 8px; page-break-inside: avoid; }
        .sign-box { width: 40%; margin-left: auto; text-align: center; }
        .sign-space { height: 38px; }
        .sign-name { font-weight: 800; font-size: 9px; }
        .page-break { page-break-after: always; }
        .total-cell { background: #fde68a; font-weight: 900; }
        .mutu-cell { background: #bbf7d0; font-weight: 900; text-transform: uppercase; }
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

        $hitungTertimbang = function (?float $nilai, float $bobot): ?string {
            if ($nilai === null) return '-';
            return number_format(round($nilai * $bobot, 2), 2, ',', '.');
        };

        $formatNilai = function (?float $nilai): string {
            if ($nilai === null) return '-';
            return number_format($nilai, 0, ',', '.');
        };

        $formatTotal = function (?float $nilai): string {
            if ($nilai === null) return '-';
            return number_format($nilai, 1, ',', '.');
        };

        $relatedDosenNomor = $pickNomor($relatedDosen ?? null);
        $programStudi = $mataKuliah?->jurusan ?? (optional(optional($krs->first()?->mahasiswa)->program_studi) ?: '-');
        $semesterGasalGenap = ((int) $semester % 2 === 1) ? 'GANJIL (I)' : 'GENAP (II)';
        $tahunAjaran = optional(optional($krs->first())->krs ?? null)->tahun_ajaran ?? date('Y').'/'.(date('Y') + 1);

        $krsChunks = collect($krs ?? [])->chunk(16);
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
            $startNo = ($chunkIndex - 1) * 16 + 1;
        @endphp
        <div>
            <table>
                <tr>
                    <td style="width: 110px; vertical-align: middle; padding-top: 2px;">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 100px; height: auto;" />
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
                    <td style="width: 80px;"></td>
                </tr>
            </table>
            <div class="kop-line-1"></div>
            <div class="kop-line-2"></div>

            <div class="prodi-title">PROGRAM STUDI {{ strtoupper($programStudi) }}</div>

            <table style="margin: 3px 0 8px;">
                <tr>
                    <td style="width: 40%; vertical-align: top;">
                        <table class="kv2">
                            <tr>
                                <td class="label">MATA KULIAH</td>
                                <td class="colon">:</td>
                                <td class="value">{{ strtoupper($mataKuliah?->nama ?? '-') }}</td>
                            </tr>
                            <tr>
                                <td class="label">SEMESTER</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $semesterGasalGenap }} TA. {{ $tahunAjaran }}</td>
                            </tr>
                            <tr>
                                <td class="label">JAM PERTEMUAN</td>
                                <td class="colon">:</td>
                                <td class="value">16 PERTEMUAN</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 30%; vertical-align: top; padding-left: 10px;">
                        <table class="kv2">
                            <tr>
                                <td class="label">Kode MK</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $mataKuliah?->kode ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Dosen 1</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $mataKuliah?->dosen?->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="label">Dosen 2</td>
                                <td class="colon">:</td>
                                <td class="value">{{ $mataKuliah?->dosen2?->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="tbl">
                <thead>
                    <tr>
                        <th rowspan="2" style="width: 28px;" class="center">No</th>
                        <th rowspan="2" style="width: 86px;">NIM/NPM</th>
                        <th rowspan="2" style="min-width: 140px;">NAMA MAHASISWA</th>
                        <th colspan="4" class="center ghead">NILAI</th>
                        <th colspan="4" class="center ghead">NILAI TERTIMBANG</th>
                        <th rowspan="2" style="width: 58px;" class="center">TOTAL</th>
                        <th rowspan="2" style="width: 58px;" class="center">NILAI MUTU</th>
                    </tr>
                    <tr>
                        <th style="width: 42px;" class="center">TATAP MUKA</th>
                        <th style="width: 42px;" class="center">QUIS</th>
                        <th style="width: 46px;" class="center">MID SEMESTER</th>
                        <th style="width: 42px;" class="center">FINAL</th>
                        <th style="width: 60px;" class="center">TATAP MUKA (50%)</th>
                        <th style="width: 52px;" class="center">QUIS (20%)</th>
                        <th style="width: 56px;" class="center">MID (15%)</th>
                        <th style="width: 52px;" class="center">FINAL (15%)</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($chunk->count())
                        @foreach ($chunk as $row)
                            @php
                                $mhs = $row->mahasiswa;
                                $existingItem = $existing->get($row->mahasiswa_id);
                                $tm = $existingItem?->nilai_tm;
                                $quis = $existingItem?->nilai_quis;
                                $mid = $existingItem?->nilai_mid;
                                $final = $existingItem?->nilai_final;
                                $angka = $existingItem?->nilai_angka;
                                $huruf = $existingItem?->nilai_huruf;
                            @endphp
                            <tr>
                                <td class="center">{{ $startNo + $loop->index }}</td>
                                <td class="nowrap">{{ $mhs?->npm ?? '-' }}</td>
                                <td>{{ strtoupper($mhs?->nama_lengkap ?? '-') }}</td>
                                <td class="center">{{ $formatNilai($tm) }}</td>
                                <td class="center">{{ $formatNilai($quis) }}</td>
                                <td class="center">{{ $formatNilai($mid) }}</td>
                                <td class="center">{{ $formatNilai($final) }}</td>
                                <td class="center">{{ $hitungTertimbang($tm, 0.50) }}</td>
                                <td class="center">{{ $hitungTertimbang($quis, 0.20) }}</td>
                                <td class="center">{{ $hitungTertimbang($mid, 0.15) }}</td>
                                <td class="center">{{ $hitungTertimbang($final, 0.15) }}</td>
                                <td class="center total-cell">{{ $formatTotal($angka) }}</td>
                                <td class="center mutu-cell">{{ $huruf ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="12" class="center">Tidak ada mahasiswa pada mata kuliah ini.</td>
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
