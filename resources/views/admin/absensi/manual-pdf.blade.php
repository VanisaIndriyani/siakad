<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Absensi Manual</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 8mm 10mm 8mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #111827;
            line-height: 1.3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* =========================
           HEADER
        ========================= */

        .kop-title-1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }

        .kop-title-2 {
            font-size: 24px;
            font-weight: 900;
            margin: 1px 0 0;
            letter-spacing: 0.4px;
            line-height: 1;
        }

        .kop-title-3 {
            font-size: 16px;
            font-weight: 900;
            margin: 1px 0 0;
            line-height: 1.1;
        }

        .kop-meta {
            font-size: 10px;
            margin-top: 2px;
            line-height: 1.2;
        }

        .kop-line-1 {
            border-top: 3px solid #6b7280;
            margin-top: 6px;
        }

        .kop-line-2 {
            border-top: 1px solid #6b7280;
            margin-top: 2px;
        }

        .doc-title {
            text-align: center;
            font-size: 13px;
            font-weight: 900;
            margin: 10px 0 8px;
            text-transform: uppercase;
        }

        /* =========================
           INFO MATA KULIAH
        ========================= */

        .info-table td {
            padding: 1px 0;
            font-size: 9px;
            border: none;
            vertical-align: top;
        }

        .info-label {
            width: 100px;
        }

        .info-colon {
            width: 10px;
            text-align: center;
        }

        .info-value {
            font-weight: 700;
        }

        /* =========================
           TABEL ABSENSI
        ========================= */

 .attendance-table {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
}

.attendance-table th,
.attendance-table td {
    border: 1px solid #111827;
    text-align: center;
    vertical-align: middle;
    padding: 2px;
}

.attendance-table th {
    background: #f3f4f6;
    font-size: 7px;
    font-weight: 800;
}

.student-name-cell {
    text-align: left !important;
    padding-left: 5px !important;
    white-space: nowrap;
    overflow: hidden;
    font-size: 8.5px;
}

.student-name-header {
    font-size: 7px;
}

        .npm-column {
            font-weight: 700;
            font-size: 8.5px;
            white-space: nowrap;
        }

        /* =========================
           TANDA TANGAN
        ========================= */

        .signature-table {
            margin-top: 20px;
        }

        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0;
        }

        .signature-space {
            height: 50px;
        }

        .signature-name {
            font-weight: 800;
            text-decoration: underline;
            font-size: 10px;
        }

        .signature-label {
            font-weight: 700;
            font-size: 9px;
        }
  .student-name-cell {
    text-align: left !important;
    padding-left: 6px !important;
    font-size: 9px;
    font-weight: 600;
    white-space: nowrap; /* biar tidak turun */
}
.student-name-header {
    font-size: 8px;
}


    </style>
</head>

<body>

    @php
        $logoPath = public_path('img/lo.jpeg');
        $logoBase64 = null;

        if (is_string($logoPath) && file_exists($logoPath)) {
            $data = file_get_contents($logoPath);

            $logoBase64 =
                'data:image/' .
                pathinfo($logoPath, PATHINFO_EXTENSION) .
                ';base64,' .
                base64_encode($data);
        }

        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';

        $semesterLabel = ((int) $semester % 2 === 0) ? 'GENAP' : 'GANJIL';
        $ta = date('Y') . '/' . (date('Y') + 1);
    @endphp

    <!-- =========================
         HEADER
    ========================== -->

    <table style="border: none;">
        <tr>

            <td style="width: 100px; vertical-align: middle; border: none;">

                @if ($logoBase64)
                    <img
                        src="{{ $logoBase64 }}"
                        alt="Logo"
                        style="display:block; width:100px; height:auto;"
                    >
                @endif

            </td>

            <td style="text-align: center; border: none;">

                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>

                <div class="kop-meta" style="font-weight:700;">
                    {{ $kop4 }}
                </div>

                <div class="kop-meta">
                    {{ $kop5 }}
                </div>

                <div class="kop-meta">
                    {{ $kop6 }}
                </div>

            </td>

            <td style="width: 100px; border: none;"></td>

        </tr>
    </table>

    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">
        DAFTAR HADIR MAHASISWA (MANUAL)
    </div>

    <!-- =========================
         INFO MATA KULIAH
    ========================== -->

    <table style="margin-bottom: 8px; border: none;">
        <tr>

            <td style="width:55%; vertical-align:top; border:none;">

                <table class="info-table">

                    <tr>
                        <td class="info-label">Mata Kuliah</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">
                            {{ $mk->kode }} - {{ $mk->nama }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">Program Studi</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">
                            {{ $jurusan }}
                        </td>
                    </tr>

                </table>

            </td>

            <td style="width:45%; vertical-align:top; border:none; padding-left:20px;">

                <table class="info-table">

                    <tr>
                        <td class="info-label">Dosen Pengampu</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">
                            {{ $dosenNama }}
                        </td>
                    </tr>

                    <tr>
                        <td class="info-label">Semester / TA</td>
                        <td class="info-colon">:</td>
                        <td class="info-value">
                            {{ $semester }}
                            ({{ $semesterLabel }})
                            /
                            {{ $ta }}
                        </td>
                    </tr>

                </table>

            </td>

        </tr>
    </table>

    <!-- =========================
         TABEL ABSENSI
    ========================== -->

    <table class="attendance-table">

  <colgroup>
    <col style="width:35px;"> <!-- No -->
    <col style="width:75px;"> <!-- NPM -->
    <col style="width:180px;"> <!-- Nama Mahasiswa -->

    @for ($i = 1; $i <= 16; $i++)
        <col style="width:22px;"> <!-- Pertemuan sama rata -->
    @endfor

    <col style="width:40px;"> <!-- Ket -->
</colgroup>

        <thead>

            <tr>

                <th rowspan="2">
                    No
                </th>

                <th rowspan="2">
                    NPM
                </th>

                <th rowspan="2" class="student-name-header">
                    Nama Mahasiswa
                </th>

                <th colspan="16">
                    Pertemuan Ke-
                </th>

                <th rowspan="2">
                    Ket.
                </th>

            </tr>

            <tr>

                @for ($i = 1; $i <= 16; $i++)
                    <th>{{ $i }}</th>
                @endfor

            </tr>

        </thead>

        <tbody>

            @forelse ($mahasiswa as $i => $mhs)

                <tr>

                    <td>
                        {{ $i + 1 }}
                    </td>

                    <td class="npm-column">
                        {{ $mhs->npm }}
                    </td>

                    <td class="student-name-cell">
                        {{ $mhs->nama_lengkap }}
                    </td>

                    @for ($j = 1; $j <= 16; $j++)
                        <td></td>
                    @endfor

                    <td></td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="20"
                        style="padding:15px; text-align:center; color:#6b7280;"
                    >
                        Belum ada mahasiswa yang mengambil mata kuliah ini.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

    <!-- =========================
         TANDA TANGAN
    ========================== -->

    <table class="signature-table">

        <tr>

            <td>

                <div class="signature-label">
                    Mengetahui,
                </div>

                <div class="signature-label">
                    Ketua Program Studi
                </div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    {{ $kaprodiNama ?? '........................................' }}
                </div>

            </td>

            <td>

                <div class="signature-label">
                    Sidrap, {{ date('d F Y') }}
                </div>

                <div class="signature-label">
                    Dosen Pengampu,
                </div>

                <div class="signature-space"></div>

                <div class="signature-name">
                    {{ $dosenNama }}
                </div>

            </td>

        </tr>

    </table>

</body>
</html>