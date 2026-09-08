<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Mengajar - {{ $mataKuliah->kode }}</title>
    <style>
        @page { margin: 18mm 16mm 22mm 16mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
        .doc-title { text-align: center; font-size: 16px; font-weight: 900; margin: 18px 0 2px; letter-spacing: 0.4px; text-transform: uppercase; }
        .doc-sub { text-align: center; font-size: 13px; font-weight: 700; margin: 0 0 16px; }
        .doc-number { text-align: center; font-size: 12px; margin: 0 0 22px; }
        .preamble { margin: 0 0 20px; line-height: 1.55; text-indent: 30px; text-align: justify; }
        .pasal { margin: 0 0 18px; line-height: 1.55; }
        .pasal-title { font-weight: 800; margin-bottom: 6px; }
        .tbl th, .tbl td { border: 1px solid #111827; padding: 7px 10px; vertical-align: top; font-size: 11px; }
        .tbl th { background: #f3f4f6; font-size: 10.5px; text-transform: uppercase; letter-spacing: 0.4px; }
        .center { text-align: center; }
        .label { width: 180px; font-weight: 700; }
        .colon { width: 10px; }
        .sign-wrap { margin-top: 36px; }
        .sign-table { width: 100%; }
        .sign-table td { width: 50%; vertical-align: top; font-size: 11px; padding: 0; }
        .sign-space { height: 58px; }
        .sign-name { font-weight: 800; text-decoration: underline; margin-bottom: 2px; }
        .muted { color: #4b5563; }
    </style>
</head>
<body>
    @php
        $logoCandidates = [
            public_path('img/lo.jpeg'), public_path('img/logo.png'),
            base_path('../img/lo.jpeg'), base_path('../img/logo.png'),
            base_path('../public/img/lo.jpeg'), base_path('../public/img/logo.png'),
        ];
        $logoPath = null;
        foreach ($logoCandidates as $c) {
            if (is_string($c) && is_file($c) && is_readable($c)) { $logoPath = $c; break; }
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

        $semester = (int) ($semester ?? $mataKuliah->semester ?? 1);
        $tglSekarang = date('d F Y');
        $semesterLabel = ($semester % 2 === 0) ? 'GENAP' : 'GANJIL';

        $dosen = $skMengajar?->dosen ?? $relatedDosen;
        $dosenNama = $dosen?->nama ?? ($mataKuliah->dosen?->nama ?? '-');
        $dosenNuptk = $dosen?->nuptk ?? ($dosen?->nidn ?? null);
        $dosenJabatan = trim((string) ($skMengajar?->jabatan_dosen ?: ($dosen?->jabatan_struktural ?? 'Dosen')));
        $programStudi = trim((string) ($skMengajar?->program_studi ?: ($mataKuliah->jurusan ?? '-')));
        $kelas = trim((string) ($skMengajar?->kelas ?? '-'));
        $bebanSks = $skMengajar?->beban_sks ?? ($mataKuliah->sks ?? 0);

        $nomorSk = $skMengajar?->nomor_sk ?: '-';
        $tanggalSk = $skMengajar?->tanggal_sk ? $skMengajar->tanggal_sk->format('d F Y') : $tglSekarang;
        $tanggalMulai = $skMengajar?->tanggal_mulai ? $skMengajar->tanggal_mulai->format('d F Y') : null;
        $tanggalSelesai = $skMengajar?->tanggal_selesai ? $skMengajar->tanggal_selesai->format('d F Y') : null;
        $tahunAjaran = trim((string) ($skMengajar?->tahun_ajaran ?? '-'));
        $tugasTambahan = trim((string) ($skMengajar?->tugas_tambahan ?? ''));
        $catatan = trim((string) ($skMengajar?->catatan ?? ''));

        $tempatSk = 'Sidrap';
    @endphp

    <table>
        <tr>
            <td style="width: 130px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64) <img src="{{ $logoBase64 }}" alt="Logo" style="display:block;width:125px;height:auto;" /> @endif
            </td>
            <td style="text-align:center;">
                <div class="kop-title-1">{{ $kop1 }}</div>
                <div class="kop-title-2">{{ $kop2 }}</div>
                <div class="kop-title-3">{{ $kop3 }}</div>
                <div class="kop-meta" style="font-weight:700;">{{ $kop4 }}</div>
                <div class="kop-meta">{{ $kop5 }}</div>
                <div class="kop-meta">{{ $kop6 }}</div>
            </td>
            <td style="width:90px;"></td>
        </tr>
    </table>
    <div class="kop-line-1"></div>
    <div class="kop-line-2"></div>

    <div class="doc-title">Surat Keputusan</div>
    <div class="doc-sub">Penugasan Dosen Pengampu Mata Kuliah</div>
    <div class="doc-number">Nomor : {{ $nomorSk }} &nbsp;&nbsp; | &nbsp;&nbsp; {{ $tempatSk }}, {{ $tanggalSk }}</div>

    <p class="preamble">
        Ketua Institut Agama Islam Darud Da'wah Wal Irsyad Sidrap, berdasarkan mandat yang diberikan untuk menyelenggarakan pendidikan tinggi pada tahun ajaran <strong>{{ $tahunAjaran }}</strong>
        semester <strong>{{ $semesterLabel }}</strong> (Semester {{ $semester }}), dengan ini memutuskan untuk menetapkan dan menugaskan tenaga pendidik (dosen) sebagai pengampu mata kuliah sebagaimana diatur dalam ketentuan berikut:
    </p>

    <div class="pasal">
        <div class="pasal-title">KETENTUAN PERTAMA</div>
        Menetapkan dosen pengampu mata kuliah dengan rincian sebagai berikut:
        <table class="tbl" style="margin-top: 8px;">
            <tbody>
                <tr>
                    <td class="label">Kode / Mata Kuliah</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</strong></td>
                </tr>
                <tr>
                    <td>Program Studi</td>
                    <td class="colon">:</td>
                    <td>{{ $programStudi }}</td>
                </tr>
                <tr>
                    <td>Semester / Tahun Ajaran</td>
                    <td class="colon">:</td>
                    <td>Semester {{ $semester }} ({{ $semesterLabel }}) / {{ $tahunAjaran }}</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td class="colon">:</td>
                    <td>{{ $kelas }}</td>
                </tr>
                <tr>
                    <td>Beban SKS</td>
                    <td class="colon">:</td>
                    <td>{{ $bebanSks }} SKS</td>
                </tr>
                <tr>
                    <td>Jadwal Berlaku</td>
                    <td class="colon">:</td>
                    <td>{{ $tanggalMulai ?: '-' }} s/d {{ $tanggalSelesai ?: '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="pasal">
        <div class="pasal-title">KETENTUAN KEDUA</div>
        Menugaskan kepada:
        <table class="tbl" style="margin-top: 8px;">
            <tbody>
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $dosenNama }}</strong></td>
                </tr>
                <tr>
                    <td>NUPTK / NIDN</td>
                    <td class="colon">:</td>
                    <td>{{ $dosenNuptk ?? ($dosen?->nidn ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Jabatan Akademik</td>
                    <td class="colon">:</td>
                    <td>{{ $dosenJabatan }}</td>
                </tr>
                @if ($tugasTambahan !== '')
                <tr>
                    <td>Tugas Tambahan</td>
                    <td class="colon">:</td>
                    <td>{{ $tugasTambahan }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="pasal">
        <div class="pasal-title">KETENTUAN KETIGA</div>
        <p style="margin: 0; text-align: justify;">
            Dosen pengampu yang ditunjuk wajib melaksanakan seluruh Tri Dharma Perguruan Tinggi pada mata kuliah yang diamanatkan, meliputi: (a) menyusun RPS, kontrak perkuliahan, dan jadwal roster; (b) melaksanakan perkuliahan tatap muka paling sedikit 16 (enam belas) pertemuan; (c) menilai hasil belajar mahasiswa secara adil dan transparan; (d) mengunggah nilai akhir mahasiswa ke sistem SIAKAD paling lambat 7 (tujuh) hari setelah Ujian Akhir Semester.
        </p>
    </div>

    @if ($catatan !== '')
    <div class="pasal">
        <div class="pasal-title">CATATAN</div>
        <p style="margin: 0;">{{ $catatan }}</p>
    </div>
    @endif

    <div class="pasal">
        <div class="pasal-title">PENUTUP</div>
        <p style="margin: 0; text-align: justify;">
            Surat Keputusan ini berlaku sejak tanggal ditetapkan. Apabila di kemudian hari terdapat kekeliruan, akan diadakan perbaikan sebagaimana mestinya. Demikian surat keputusan ini ditetapkan untuk dapat dipergunakan sebagaimana mestinya.
        </p>
    </div>

    <table class="sign-wrap sign-table">
        <tr>
            <td>
                <div class="muted">Mengetahui,</div>
                <div class="muted" style="margin-bottom: 4px;">Ketua Program Studi {{ $programStudi }}</div>
                <div class="sign-space"></div>
                <div class="sign-name">........................................</div>
                <div class="muted" style="font-size: 10px;">NUPTK. .....................................</div>
            </td>
            <td>
                <div style="margin-bottom: 4px;">{{ $tempatSk }}, {{ $tanggalSk }}</div>
                <div style="font-weight: 800; margin-bottom: 4px;">Ketua Institut Agama Islam Darud Da'wah Wal Irsyad Sidrap,</div>
                <div style="font-weight: 700; margin-bottom: 2px;">a.n. Ketua</div>
                <div style="margin-bottom: 4px;" class="muted">Pembantu Ketua I Bidang Akademik</div>
                <div class="sign-space"></div>
                <div class="sign-name">........................................</div>
                <div class="muted" style="font-size: 10px;">NUPTK. .....................................</div>
            </td>
        </tr>
    </table>
</body>
</html>
