<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Absensi Manual</title>
    <style>
        @page { margin: 12mm 10mm 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        
        /* Kop Surat Styles */
        .kop-title-1 { color: #111827; font-size: 16px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { color: #111827; font-size: 24px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.0; }
        .kop-title-3 { color: #111827; font-size: 16px; font-weight: 900; margin: 1px 0 0; line-height: 1.1; }
        .kop-meta { color: #111827; font-size: 10px; margin-top: 2px; line-height: 1.2; }
        .kop-line-1 { border-top: 3px solid #6b7280; margin-top: 6px; }
        .kop-line-2 { border-top: 1px solid #6b7280; margin-top: 2px; }
        
        .doc-title { text-align: center; font-size: 13px; font-weight: 900; margin: 10px 0 8px; text-transform: uppercase; }
        
        .kv2 { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .kv2 td { padding: 2px 0; font-size: 10px; vertical-align: top; border: none; text-align: left; }
        .kv2 .label { width: 100px; }
        .kv2 .colon { width: 10px; text-align: center; }
        .kv2 .value { font-weight: 700; }

        .tbl { width: 100%; border-collapse: collapse; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 4px 2px; text-align: center; }
        .tbl th { background-color: #f3f4f6; font-size: 9px; font-weight: 800; text-transform: uppercase; }
        .text-left { text-align: left !important; padding-left: 5px !important; }
        
        .sign-table { margin-top: 20px; width: 100%; border-collapse: collapse; }
        .sign-table td { border: none; width: 50%; text-align: center; vertical-align: top; padding: 0; }
        .sign-space { height: 50px; }
        .sign-name { font-weight: 800; text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('img/lo.jpeg');
        $logoBase64 = null;
        if (is_string($logoPath) && file_exists($logoPath)) {
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
        }

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';
    @endphp

    <table style="border: none;">
        <tr>
            <td style="width: 100px; vertical-align: middle; padding-top: 2px; border: none;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 100px; height: auto;" />
                @endif
            </td>
            <td style="text-align: center; border: none;">
                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>
                <div class="kop-meta" style="font-weight: 700;">{{ $kop4 }}</div>
                <div class="kop-meta">{{ $kop5 }}</div>
                <div class="kop-meta">{{ $kop6 }}</div>
            </td>
            <td style="width: 100px; border: none;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">DAFTAR HADIR MAHASISWA (MANUAL)</div>

    <table style="margin-bottom: 10px; border: none;">
        <tr>
            <td style="width: 55%; vertical-align: top; border: none;">
                <table class="kv2">
                    <tr>
                        <td class="label">Mata Kuliah</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mk->kode }} - {{ $mk->nama }}</td>
                    </tr>
                    <tr>
                        <td class="label">Dosen Pengampu</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $dosenNama }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; border: none; padding-left: 20px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $jurusan }}</td>
                    </tr>
                    <tr>
                        <td class="label">Semester</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $semester }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 75px;">NPM</th>
                <th rowspan="2" class="text-left">Nama Mahasiswa</th>
                <th colspan="16">Pertemuan Ke-</th>
                <th rowspan="2" style="width: 50px;">Ket.</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th style="width: 18px; font-size: 7px;">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($mahasiswa as $i => $mhs)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-size: 8px;">{{ $mhs->npm }}</td>
                    <td class="text-left" style="font-size: 8px;">{{ $mhs->nama_lengkap }}</td>
                    @for ($j = 1; $j <= 16; $j++)
                        <td></td>
                    @endfor
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="padding: 15px; text-align: center; color: #6b7280;">Belum ada mahasiswa yang mengambil mata kuliah ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td>
                <div style="font-weight: 700;">Mengetahui,</div>
                <div style="font-weight: 700;">Ketua Program Studi</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $kaprodiNama ?? '........................................' }}</div>
            </td>
            <td>
                <div style="font-weight: 700;">Sidrap, {{ date('d F Y') }}</div>
                <div style="font-weight: 700;">Dosen Pengampu,</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $dosenNama }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
