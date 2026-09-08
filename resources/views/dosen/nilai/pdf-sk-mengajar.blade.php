<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SK Mengajar - {{ $mataKuliah->kode }}</title>
    <style>
        @page { margin: 18mm 18mm 22mm 18mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11.5px; color: #0f172a; line-height: 1.55; }
        table { width: 100%; border-collapse: collapse; }
        .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; letter-spacing: 0.2px; }
        .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.5px; line-height: 1.06; }
        .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; letter-spacing: 0.3px; }
        .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
        .kop-line-1 { border-top: 4px solid #000; margin-top: 9px; }
        .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }

        .doc-head { margin-top: 26px; }
        .doc-title { text-align: center; font-size: 15px; font-weight: 900; letter-spacing: 0.6px; text-transform: uppercase; }
        .doc-subtitle { text-align: center; font-size: 13px; font-weight: 700; margin-top: 1px; letter-spacing: 0.3px; text-transform: uppercase; }
        .doc-numline { margin-top: 20px; }
        .doc-numline td { font-size: 11.5px; padding: 0; vertical-align: top; }
        .num-left { text-align: left; }
        .num-right { text-align: right; }

        .section-head { margin: 22px 0 6px; font-weight: 900; text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.2px; }
        .section-head::after { content: ""; display: inline-block; margin-left: 6px; }
        .list-num { margin: 0; padding-left: 26px; }
        .list-num > li { margin-bottom: 3px; text-align: justify; }
        .memutuskan { margin-top: 14px; text-align: center; font-weight: 900; text-transform: uppercase; letter-spacing: 0.4px; font-size: 12px; }

        .ident { margin-top: 18px; }
        .ident td { padding: 2px 0; vertical-align: top; font-size: 11.5px; }
        .ident .lbl { width: 200px; color: #0f172a; }
        .ident .cl { width: 14px; padding: 0 4px; }
        .ident .val { font-weight: 500; text-align: left; }
        .ident .val b { font-weight: 800; }

        .sub-h { margin: 18px 0 6px; font-weight: 800; text-transform: uppercase; font-size: 11.5px; letter-spacing: 0.18px; border-bottom: 1px dashed #475569; padding-bottom: 3px; }

        .pasal-wrap { margin: 14px 0 0; }
        .pasal-num { font-weight: 900; display: inline-block; margin-right: 6px; }
        .pasal-body { margin: 0 0 6px; text-align: justify; }

        .catatan-box { margin-top: 18px; padding: 10px 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 4px; }
        .catatan-box .lbl { font-weight: 800; margin-bottom: 3px; color: #92400e; text-transform: uppercase; letter-spacing: 0.2px; }

        .penutup { margin-top: 20px; text-align: justify; }

        .sign-wrap { margin-top: 36px; }
        .sign-wrap td { width: 50%; vertical-align: top; font-size: 11.5px; padding: 0; }
        .sign-title { font-weight: 700; }
        .sign-space { height: 64px; }
        .sign-name { font-weight: 900; text-decoration: underline; text-underline-offset: 2px; margin-bottom: 2px; text-align: left; }
        .sign-nu { font-size: 10.5px; color: #334155; }

        .tembusan { margin-top: 30px; page-break-inside: avoid; }
        .tembusan .head { font-weight: 900; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 6px; font-size: 11.5px; }
        .tembusan ol { margin: 0; padding-left: 24px; font-size: 11px; color: #1e293b; }
        .tembusan ol li { margin-bottom: 1.5px; }

        .foot { margin-top: 28px; border-top: 1px dotted #94a3b8; padding-top: 6px; font-size: 9.5px; color: #64748b; text-align: center; }

        .muted { color: #475569; }
        .center { text-align: center !important; }
        .nowrap { white-space: nowrap; }
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
        $dosenNidn  = $dosen?->nidn ?? null;
        $dosenNuptk = $dosen?->nuptk ?? null;
        $dosenIdent = trim(implode(' / ', array_filter([$dosenNuptk ? 'NUPTK. '.$dosenNuptk : null, $dosenNidn ? 'NIDN. '.$dosenNidn : null])));
        if ($dosenIdent === '') { $dosenIdent = '-'; }
        $dosenJabatan = trim((string) ($skMengajar?->jabatan_dosen ?: ($dosen?->jabatan_struktural ?? 'Dosen')));
        $programStudi = trim((string) ($skMengajar?->program_studi ?: ($mataKuliah->jurusan ?? '-')));
        if ($programStudi === '' || $programStudi === '0') { $programStudi = '-'; }
        $kelas = trim((string) ($skMengajar?->kelas ?? '-'));
        if ($kelas === '' || $kelas === '0') { $kelas = '-'; }
        $bebanSks = $skMengajar?->beban_sks ?? ($mataKuliah->sks ?? 0);

        $nomorSk = trim((string) ($skMengajar?->nomor_sk ?? ''));
        if ($nomorSk === '') { $nomorSk = '-'; }
        $tanggalSk = $skMengajar?->tanggal_sk ? $skMengajar->tanggal_sk->format('d F Y') : $tglSekarang;
        $tanggalMulai = $skMengajar?->tanggal_mulai ? $skMengajar->tanggal_mulai->format('d F Y') : null;
        $tanggalSelesai = $skMengajar?->tanggal_selesai ? $skMengajar->tanggal_selesai->format('d F Y') : null;
        $masaBerlaku = ($tanggalMulai && $tanggalSelesai) ? ($tanggalMulai.' s/d '.$tanggalSelesai) : ($tanggalMulai ? ($tanggalMulai.' s/d selesai perkuliahan') : 'Sesuai jadwal akademik semester '.$semesterLabel);
        $tahunAjaran = trim((string) ($skMengajar?->tahun_ajaran ?? ''));
        if ($tahunAjaran === '') { $tahunAjaran = '-'; }
        $tugasTambahan = trim((string) ($skMengajar?->tugas_tambahan ?? ''));
        $catatan = trim((string) ($skMengajar?->catatan ?? ''));

        $tempatSk = 'Sidrap';
    @endphp

    <table>
        <tr>
            <td style="width: 132px; vertical-align: middle; padding-top: 2px;">
                @if($logoBase64) <img src="{{ $logoBase64 }}" alt="Logo" style="display:block;width:124px;height:auto;" /> @endif
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

    <div class="doc-head">
        <div class="doc-title">Surat Keputusan</div>
        <div class="doc-subtitle">Penugasan Dosen Pengampu Mata Kuliah</div>
    </div>

    <table class="doc-numline">
        <tr>
            <td class="num-left">Nomor &nbsp;: <b>{{ $nomorSk }}</b></td>
            <td class="num-right">{{ $tempatSk }}, {{ $tanggalSk }}</td>
        </tr>
    </table>

    <div class="section-head">MENIMBANG :</div>
    <ol class="list-num">
        <li>Bahwa untuk kelancaran pelaksanaan Tridharma Perguruan Tinggi pada Tahun Ajaran <b>{{ $tahunAjaran }}</b> Semester <b>{{ $semesterLabel }} ({{ $semester }})</b>, perlu ditetapkan tenaga pendidik (dosen) yang bertanggung jawab penuh atas penyelenggaraan perkuliahan pada setiap mata kuliah;</li>
        <li>Bahwa dosen pengampu mata kuliah harus memenuhi kualifikasi akademik sesuai ketentuan peraturan perundang-undangan yang berlaku di lingkungan Institut Agama Islam Darud Da'wah Wal Irsyad Sidrap;</li>
        <li>Bahwa berdasarkan usulan Ketua Program Studi {{ $programStudi }} dan pertimbangan Pembantu Ketua I Bidang Akademik, dipandang perlu untuk menetapkan penugasan dosen pengampu mata kuliah dalam bentuk Surat Keputusan.</li>
    </ol>

    <div class="section-head">MENGINGAT :</div>
    <ol class="list-num">
        <li>Undang-Undang Republik Indonesia Nomor 12 Tahun 2012 tentang Pendidikan Tinggi;</li>
        <li>Peraturan Menteri Pendidikan, Kebudayaan, Riset, dan Teknologi Republik Indonesia Nomor 53 Tahun 2023 tentang Penjaminan Mutu Pendidikan Tinggi;</li>
        <li>Statuta dan Peraturan Akademik Institut Agama Islam Darud Da'wah Wal Irsyad Sidrap yang berlaku.</li>
    </ol>

    <div class="memutuskan">MEMUTUSKAN :</div>

    <div class="pasal-wrap">
        <span class="pasal-num">PASAL 1</span>
        <p class="pasal-body">Menetapkan dan memberikan tugas kepada dosen yang namanya tercantum di bawah ini sebagai <b>Dosen Pengampu Mata Kuliah</b> pada Program Studi {{ $programStudi }} untuk Tahun Ajaran {{ $tahunAjaran }} Semester {{ $semesterLabel }} ({{ $semester }}), dengan rincian sebagai berikut:</p>

        <div class="sub-h">A. Identitas Mata Kuliah</div>
        <table class="ident">
            <tr><td class="lbl">Kode Mata Kuliah</td><td class="cl">:</td><td class="val"><b>{{ $mataKuliah->kode }}</b></td></tr>
            <tr><td class="lbl">Nama Mata Kuliah</td><td class="cl">:</td><td class="val"><b>{{ $mataKuliah->nama }}</b></td></tr>
            <tr><td class="lbl">Program Studi</td><td class="cl">:</td><td class="val">{{ $programStudi }}</td></tr>
            <tr><td class="lbl">Semester / Tahun Ajaran</td><td class="cl">:</td><td class="val">Semester {{ $semester }} ({{ $semesterLabel }}) / {{ $tahunAjaran }}</td></tr>
            <tr><td class="lbl">Kelas / Kelompok</td><td class="cl">:</td><td class="val">{{ $kelas }}</td></tr>
            <tr><td class="lbl">Beban Studi</td><td class="cl">:</td><td class="val"><b>{{ $bebanSks }} SKS</b> ({{ (int)$bebanSks * 50 }} menit / pertemuan)</td></tr>
            <tr><td class="lbl">Masa Berlaku</td><td class="cl">:</td><td class="val">{{ $masaBerlaku }}</td></tr>
        </table>

        <div class="sub-h">B. Identitas Dosen Pengampu</div>
        <table class="ident">
            <tr><td class="lbl">Nama Lengkap</td><td class="cl">:</td><td class="val"><b>{{ strtoupper($dosenNama) }}</b></td></tr>
            <tr><td class="lbl">NUPTK / NIDN</td><td class="cl">:</td><td class="val">{{ $dosenIdent }}</td></tr>
            <tr><td class="lbl">Jabatan Akademik / Struktural</td><td class="cl">:</td><td class="val">{{ $dosenJabatan }}</td></tr>
            @if ($tugasTambahan !== '')
            <tr><td class="lbl">Tugas Tambahan</td><td class="cl">:</td><td class="val">{{ $tugasTambahan }}</td></tr>
            @endif
            <tr><td class="lbl">Program Studi Keahlian</td><td class="cl">:</td><td class="val">{{ $programStudi }}</td></tr>
        </table>
    </div>

    <div class="pasal-wrap">
        <span class="pasal-num">PASAL 2</span>
        <p class="pasal-body">Dosen pengampu yang ditetapkan dalam Pasal 1 wajib melaksanakan kewajiban sebagai berikut:</p>
        <ol class="list-num" style="margin-left: 8px;">
            <li>Menyusun Rencana Pembelajaran Semester (RPS), Kontrak Perkuliahan, dan Roster Jadwal 16 (enam belas) pertemuan paling lambat 1 (satu) minggu sebelum perkuliahan dimulai dan mengunggahnya ke sistem SIAKAD IAI DDI Sidrap;</li>
            <li>Melaksanakan perkuliahan tatap muka secara teratur paling sedikit 16 (enam belas) kali pertemuan per semester, termasuk pertemuan Ujian Tengah Semester (UTS) dan Ujian Akhir Semester (UAS);</li>
            <li>Melaksanakan penilaian proses dan hasil belajar mahasiswa secara adil, transparan, dan berkesinambungan sesuai Bobot Nilai yang tercantum dalam RPS;</li>
            <li>Mengunggah seluruh nilai komponen (Tatap Muka, Tugas, MID, Final) beserta Total Angka dan Nilai Mutu mahasiswa ke dalam SIAKAD paling lambat 7 (tujuh) hari kalender setelah pelaksanaan Ujian Akhir Semester;</li>
            <li>Menyerahkan berkas administratif perkuliahan (RPS, absensi, daftar nilai, jurnal perkuliahan, dan berkas soal UTS/UAS) kepada Ketua Program Studi paling lambat 14 (empat belas) hari setelah UAS berakhir.</li>
        </ol>
    </div>

    <div class="pasal-wrap">
        <span class="pasal-num">PASAL 3</span>
        <p class="pasal-body">Hak, wewenang, dan tanggung jawab dosen pengampu sepenuhnya mengacu pada peraturan akademik yang berlaku di lingkungan Institut Agama Islam Darud Da'wah Wal Irsyad Sidrap, serta peraturan perundang-undangan terkait.</p>
    </div>

    <div class="pasal-wrap">
        <span class="pasal-num">PASAL 4</span>
        <p class="pasal-body">Hal-hal yang belum diatur dalam Surat Keputusan ini akan ditetapkan kemudian oleh Ketua Institut a.n. Pembantu Ketua I Bidang Akademik setelah berkonsultasi dengan Ketua Program Studi terkait.</p>
    </div>

    @if ($catatan !== '')
    <div class="catatan-box">
        <div class="lbl">Catatan Khusus :</div>
        <div style="margin: 0;">{{ $catatan }}</div>
    </div>
    @endif

    <p class="penutup">
        Demikian Surat Keputusan ini ditetapkan di Sidrap pada tanggal {{ $tanggalSk }} untuk dapat dipergunakan sebagaimana mestinya. Apabila di kemudian hari terdapat kekeliruan dalam penulisan maupun substansi, akan dilakukan perbaikan sesuai ketentuan yang berlaku.
    </p>

    <table class="sign-wrap">
        <tr>
            <td>
                <div class="sign-title">Mengetahui / Menyetujui,</div>
                <div class="muted" style="margin-bottom: 2px;">Ketua Program Studi {{ $programStudi }}</div>
                <div class="sign-space"></div>
                <div class="sign-name">.................................................</div>
                <div class="sign-nu">NUPTK. ........................................</div>
            </td>
            <td>
                <div class="sign-title">Ketua Institut Agama Islam</div>
                <div class="sign-title" style="margin-top: -1px;">Darud Da'wah Wal Irsyad Sidrap,</div>
                <div style="margin: 4px 0 2px; font-weight: 700; color: #334155;">a.n. Ketua Institut</div>
                <div class="muted" style="margin-bottom: 2px;">Pembantu Ketua I Bidang Akademik</div>
                <div class="sign-space"></div>
                <div class="sign-name">.................................................</div>
                <div class="sign-nu">NUPTK. ........................................</div>
            </td>
        </tr>
    </table>

    <div class="tembusan">
        <div class="head">Tembusan disampaikan kepada :</div>
        <ol>
            <li>Arsip Kepegawaian / Umum Institut;</li>
            <li>Ketua Program Studi {{ $programStudi }} (untuk diketahui dan ditindaklanjuti);</li>
            <li>Ybs. <b>{{ $dosenNama }}</b> — Dosen Pengampu Mata Kuliah;</li>
            <li>Biro Administrasi Akademik &amp; Kemahasiswaan (BAAK) untuk pengarsipan daftar nilai;</li>
            <li>Sub Bagian Keuangan sebagai dasar perhitungan remunerasi honor perkuliahan;</li>
            <li>SIAKAD IAI DDI Sidrap untuk verifikasi hak akses input nilai dosen.</li>
        </ol>
    </div>

    <div class="foot">
        SALINAN DARI SURAT KEPUTUSAN ASLI — Diarsipkan dan dicetak otomatis melalui Sistem Informasi Akademik (SIAKAD) IAI DDI Sidrap
    </div>
</body>
</html>
