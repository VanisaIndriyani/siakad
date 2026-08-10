<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Transkip Nilai {{ $mahasiswa->nama_lengkap }}</title>
    <style>
        @page { size: 210mm 330mm; margin: 14mm 12mm 14mm 14mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #111827; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { color: #000; font-size: 26px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 18px; font-weight: 900; margin: 1px 0 0; line-height: 1.1; }
        .kop-meta { color: #000; font-size: 11px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 13px; font-weight: 900; margin: 10px 0 1px; text-transform: uppercase; letter-spacing: 0.8px; }
        .doc-sub { text-align: center; font-size: 11px; margin-bottom: 10px; }
        .kv2 { width: 100%; border-collapse: collapse; }
        .kv2 td { padding: 2px 0; font-size: 10.5px; vertical-align: top; }
        .kv2 .label { width: 120px; color: #0f172a; }
        .kv2 .colon { width: 10px; text-align: center; color: #0f172a; }
        .kv2 .value { font-weight: 700; color: #0f172a; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 5px 7px; }
        .tbl th { font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.4px; background: #e2e8f0; }
        .tbl .sm { font-size: 9.5px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .sem-title { background: #064e3b; color: #fff !important; font-weight: 800; text-align: center; }
        .sem-summary { background: #f1f5f9; font-weight: 700; }
        .foto-box { border: 1px solid #0f172a; padding: 4px; background: #fff; width: 110px; height: 145px; vertical-align: top; text-align: center; font-size: 9px; color: #64748b; }
        .foto-box img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .ttd-wrap { margin-top: 16px; }
        .predikat-box { border: 1.5px solid #064e3b; background: #ecfdf5; padding: 8px 12px; border-radius: 4px; margin-top: 10px; }
        .predikat-label { font-size: 10px; font-weight: 700; color: #064e3b; text-transform: uppercase; letter-spacing: 0.5px; }
        .predikat-value { font-size: 13px; font-weight: 900; color: #064e3b; margin-top: 2px; }
    </style>
</head>
<body>
    @php
        $kop1 = 'INSTITUT AGAMA ISLAM';
        $kop2 = "DARUD DA'WAH WAL IRSYAD";
        $kop3 = 'SIDENRENG RAPPANG';
        $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kop6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';

        $ps = strtoupper((string) ($mahasiswa->program_studi ?? ''));
        $jenjang = str_contains($ps, 'S2') ? 'S2' : (str_contains($ps, 'S3') ? 'S3' : ($ps !== '' ? 'S1' : '-'));

        $kotaTtd = env('KAMPUS_KOTA') ?: 'Majelling Watang';
        $tanggalTtd = now()->format('d-m-Y');
        $kaprodiNuptk = $kaprodi->nuptk ?? ($kaprodi->nidn ?? ($kaprodi->nip ?? null));
    @endphp

    <table>
        <tr>
            <td style="width: 120px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 120px; height: auto;" />
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
            <td style="width: 130px; text-align: right;">
                <table style="width: 100%;">
                    <tr>
                        <td style="text-align: right;">
                        
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">Transkip Nilai</div>
    

    <table style="margin-bottom: 10px;">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <table class="kv2">
                    <tr>
                        <td class="label">Jenjang/Program</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $jenjang }}</td>
                    </tr>
                    <tr>
                        <td class="label">Fakultas</td>
                        <td class="colon">:</td>
                        <td class="value nowrap">{{ $mahasiswa->fakultas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->program_studi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 45%; vertical-align: top; padding-left: 12px;">
                <table class="kv2">
                    <tr>
                        <td class="label">Nama</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->nama_lengkap }}</td>
                    </tr>
                    <tr>
                        <td class="label">NPM</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->npm }}</td>
                    </tr>
                    <tr>
                        <td class="label">Angkatan</td>
                        <td class="colon">:</td>
                        <td class="value">{{ $mahasiswa->angkatan ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tbl">
        <thead>
        <tr>
            <th style="width: 32px;" class="center">No</th>
            <th style="width: 85px;">Kode MK</th>
            <th>Mata Kuliah</th>
            <th style="width: 40px;" class="center">SKS</th>
            <th style="width: 50px;" class="center">Nilai</th>
            <th style="width: 55px;" class="center">Bobot</th>
        </tr>
        </thead>
        <tbody>
        @php $no = 1; @endphp
        @foreach ($semesterRows as $sem)
            <tr>
                <td colspan="6" class="sem-title">
                    SEMESTER {{ $sem->semester }} &nbsp;•&nbsp; {{ $sem->tahun_ajaran }}
                </td>
            </tr>
            @if (count($sem->rows) === 0)
                <tr>
                    <td colspan="6" class="center" style="color: #64748b; padding: 10px;">Belum ada data nilai pada semester ini.</td>
                </tr>
            @else
                @foreach ($sem->rows as $row)
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td class="sm">{{ $row->kode }}</td>
                        <td class="sm">{{ $row->nama }}</td>
                        <td class="center">{{ $row->sks }}</td>
                        <td class="center" style="font-weight: 700;">{{ $row->nilai_huruf }}</td>
                        <td class="center">{{ number_format($row->bobot, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endif
            <tr class="sem-summary">
                <td colspan="3" class="sm">Jumlah SKS & Bobot Semester {{ $sem->semester }}</td>
                <td class="center" style="font-weight: 900;">{{ $sem->sks_semester }}</td>
                <td colspan="2" class="center" style="font-weight: 900;">{{ number_format($sem->bobot_semester, 2, ',', '.') }}</td>
            </tr>
            <tr class="sem-summary">
                <td colspan="3" class="sm">
                    IPS Semester {{ $sem->semester }} / IPK Kumulatif
                </td>
                <td class="center" style="color: #064e3b;">
                    <span style="font-weight: 900;">IPS: {{ $sem->ips !== null ? number_format($sem->ips, 2, ',', '.') : '-' }}</span>
                </td>
                <td colspan="2" class="right" style="color: #064e3b;">
                    <span style="font-weight: 900;">IPK: {{ $sem->ipk_kumulatif !== null ? number_format($sem->ipk_kumulatif, 2, ',', '.') : '-' }}</span>
                    &nbsp;&nbsp;
                    <span style="font-weight: 700;">SKS: {{ $sem->sks_kumulatif }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="predikat-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div class="predikat-label">Total SKS</div>
                    <div class="predikat-value">{{ $totalSks }} SKS</div>
                </td>
                <td style="width: 52%; vertical-align: top; padding-left: 12px; border-left: 1px dashed #064e3b;">
                    <div class="predikat-label">Indeks Prestasi Kumulatif (IPK)</div>
                    <div class="predikat-value">{{ number_format($ipkAkhir, 2, ',', '.') }} • {{ $predikat }}</div>
                </td>
            </tr>
        </table>
    </div>

  <div class="ttd-wrap">
    <table style="width:100%;">
        <tr>
            <!-- Mahasiswa -->
            <td style="width:50%; text-align:center; vertical-align:top;">
                <div style="font-size:10.5px;">
                    {{ $kotaTtd }}, {{ $tanggalTtd }}
                </div>

                <div style="font-size:10.5px; font-weight:700;">
                    Mahasiswa
                </div>

                <div style="height:70px;"></div>

                <div style="font-size:10.5px; font-weight:800;">
                    {{ $mahasiswa->nama_lengkap }}
                </div>

                <div style="font-size:9.5px;">
                    NPM. {{ $mahasiswa->npm }}
                </div>
            </td>

            <!-- Ketua Prodi -->
            <td style="width:50%; text-align:center; vertical-align:top;">
                <div style="font-size:10.5px;">
                    Mengetahui,
                </div>

                <div style="font-size:10.5px; font-weight:700;">
                    Ketua Program Studi
                </div>

                <div style="height:70px;"></div>

                <div style="font-size:10.5px; font-weight:800;">
                    {{ $kaprodi?->nama ?? '_________________________' }}
                </div>

                @if($kaprodiNuptk)
                    <div style="font-size:9.5px;">
                        NUPTK. {{ $kaprodiNuptk }}
                    </div>
                @endif
            </td>
        </tr>
    </table>
</div>
</body>
</html>
