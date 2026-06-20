<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kontrol Bimbingan KKN - {{ $posko->nama_posko }}</title>
    <style>
        @page { margin: 16mm 14mm 16mm 17mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5; color: #000; padding: 0; }
        
        /* Kop Surat Styles */
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 16pt; font-weight: 800; margin: 0; line-height: 1.12; text-align: center; }
        .kop-title-2 { color: #000; font-size: 22pt; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; text-align: center; }
        .kop-title-3 { color: #000; font-size: 16pt; font-weight: 900; margin: 1px 0 0; line-height: 1.12; text-align: center; }
        .kop-meta { color: #000; font-size: 10pt; margin-top: 3px; line-height: 1.2; text-align: center; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        
        .doc-title { text-align: center; font-size: 14pt; font-weight: 900; margin: 20px 0 15px; text-decoration: underline; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { vertical-align: top; padding: 3px 0; font-size: 11pt; }
        .info-table td:first-child { width: 150px; font-weight: bold; }
        .info-table td:nth-child(2) { width: 20px; text-align: center; }
        
        .revisi-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .revisi-table th, .revisi-table td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 10pt; }
        .revisi-table th { background-color: #f2f2f2; text-align: center; text-transform: uppercase; font-weight: bold; }
        
        .footer { margin-top: 50px; width: 100%; }
        .footer table { width: 100%; }
        .footer td { width: 50%; text-align: center; font-size: 11pt; }
        .signature-space { height: 80px; }
        
        @media print {
            .no-print { display: none; }
        }
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

    <div class="no-print" style="margin-bottom: 20px; text-align: right; padding: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #10b981; color: white; border: none; border-radius: 5px; font-weight: bold;">CETAK LAPORAN</button>
    </div>

    <table>
        <tr>
            <td style="width: 120px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 110px; height: auto;" />
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
            <td style="width: 100px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">Kartu Kontrol Bimbingan Kuliah Kerja Nyata (KKN)</div>

    <table class="info-table">
        <tr>
            <td>Nama Posko</td>
            <td>:</td>
            <td>{{ $posko->nama_posko }}</td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>:</td>
            <td>{{ $posko->lokasi ?: '-' }}</td>
        </tr>
        <tr>
            <td>DPL</td>
            <td>:</td>
            <td>
                @foreach ($posko->pembimbingS as $dpl)
                    {{ $loop->iteration }}. {{ $dpl->nama }}<br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Anggota Posko</td>
            <td>:</td>
            <td>
                @foreach ($posko->pengajuans as $p)
                    {{ $p->mahasiswa?->nama_lengkap }} ({{ $p->mahasiswa?->npm }}){{ !$loop->last ? ',' : '' }}
                @endforeach
            </td>
        </tr>
    </table>

    <table class="revisi-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="60%">Uraian Bimbingan / Revisi</th>
                <th width="20%">Paraf DPL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posko->revisis->sortBy('tanggal') as $rev)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td align="center">{{ $rev->tanggal->format('d/m/Y') }}</td>
                    <td>{!! nl2br(e($rev->uraian_revisi)) !!}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" align="center" style="padding: 30px; font-style: italic; color: #888;">Belum ada riwayat bimbingan/revisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>
                    @php
                        $pickNomor = function ($dosen) {
                            foreach ([$dosen?->nuptk, $dosen?->nidn, $dosen?->nip] as $nomor) {
                                $nomor = trim((string) $nomor);
                                if ($nomor !== '') {
                                    return $nomor;
                                }
                            }

                            return null;
                        };

                        $kaprodi = $kaprodi ?? null;
                        $kaprodiNama = $kaprodi?->nama ?: null;
                        $kaprodiNuptk = $pickNomor($kaprodi);
                        $dplTtd = $posko->pembimbingS->first();
                        $dplNuptk = $pickNomor($dplTtd);
                    @endphp
                    Mengetahui,<br>
                    Ketua Program Studi {{ $posko->pengajuans->first()?->mahasiswa?->program_studi ?? '................' }}
                    <div class="signature-space"></div>
                    <strong>( {{ $kaprodiNama ?: '...........................................' }} )</strong><br>
                    NUPTK. {{ $kaprodiNuptk ?: '.....................................' }}
                </td>
                <td>
                    Sidrap, {{ now()->translatedFormat('d F Y') }}<br>
                    Dosen Pembimbing Lapangan
                    <div class="signature-space"></div>
                    <strong>( {{ $dplTtd?->nama ?: '...........................................' }} )</strong><br>
                    NUPTK. {{ $dplNuptk ?: '.....................................' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
