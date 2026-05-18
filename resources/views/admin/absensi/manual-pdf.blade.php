<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 12mm 10mm 12mm 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 4px 2px; text-align: center; }
        .text-left { text-align: left; padding-left: 5px; }
        
        .kop { margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .kop-logo { width: 80px; text-align: left; border: none; }
        .kop-text { text-align: center; border: none; }
        .kop-title-1 { font-size: 14px; font-weight: bold; margin: 0; }
        .kop-title-2 { font-size: 18px; font-weight: bold; margin: 0; }
        .kop-meta { font-size: 9px; margin: 2px 0; }

        .doc-title { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 10px; text-decoration: underline; }
        
        .info-table { margin-bottom: 10px; }
        .info-table td { border: none; text-align: left; padding: 1px 0; font-size: 10px; }
        .info-label { width: 100px; }
        .info-colon { width: 10px; }

        .sign-table { margin-top: 20px; }
        .sign-table td { border: none; width: 50%; text-align: center; }
        .sign-space { height: 60px; }
        .sign-name { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
    @php
        $logoFile = public_path('img/lo.jpeg');
        $logoSrc = is_file($logoFile)
            ? 'data:image/jpeg;base64,'.base64_encode(file_get_contents($logoFile))
            : null;
    @endphp

    <div class="kop">
        <table>
            <tr>
                <td class="kop-logo" style="width: 100px;">
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" style="width: 80px; height: auto;" />
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                    <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD (DDI)</div>
                    <div class="kop-title-1">SIDENRENG RAPPANG</div>
                    <div class="kop-meta">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                    <div class="kop-meta">E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id</div>
                </td>
                <td style="width: 100px; border: none;"></td>
            </tr>
        </table>
    </div>

    <div class="doc-title">DAFTAR HADIR MAHASISWA (MANUAL)</div>

    <table class="info-table">
        <tr>
            <td class="info-label">Mata Kuliah</td>
            <td class="info-colon">:</td>
            <td class="value"><strong>{{ $mk->kode }} - {{ $mk->nama }}</strong></td>
            <td class="info-label">Program Studi</td>
            <td class="info-colon">:</td>
            <td class="value">{{ $jurusan }}</td>
        </tr>
        <tr>
            <td class="info-label">Dosen Pengampu</td>
            <td class="info-colon">:</td>
            <td class="value">{{ $dosenNama }}</td>
            <td class="info-label">Semester</td>
            <td class="info-colon">:</td>
            <td class="value">{{ $semester }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2" style="width: 80px;">NPM</th>
                <th rowspan="2">Nama Mahasiswa</th>
                <th colspan="16">Pertemuan Ke-</th>
                <th rowspan="2" style="width: 60px;">Ket.</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th style="width: 20px; font-size: 8px;">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($mahasiswa as $i => $mhs)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-size: 9px;">{{ $mhs->npm }}</td>
                    <td class="text-left" style="font-size: 9px;">{{ $mhs->nama_lengkap }}</td>
                    @for ($j = 1; $j <= 16; $j++)
                        <td></td>
                    @endfor
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="20" style="padding: 20px;">Belum ada mahasiswa yang mengambil mata kuliah ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign-table">
        <tr>
            <td>
                <div>Mengetahui,</div>
                <div>Ketua Program Studi</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $kaprodiNama ?? '........................................' }}</div>
            </td>
            <td>
                <div>Sidrap, ............................ {{ date('Y') }}</div>
                <div>Dosen Pengampu,</div>
                <div class="sign-space"></div>
                <div class="sign-name">{{ $dosenNama }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
