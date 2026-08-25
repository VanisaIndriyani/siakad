<x-portal-layout :title="'Transkrip '.$mahasiswa->nama_lengkap.' - '.config('app.name')" subtitle="Transkrip Akademik">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Transkrip Akademik</div>
            <div class="text-sm text-emerald-100/70 mt-0.5">
                {{ $mahasiswa->nama_lengkap }}
                @if($mahasiswa->npm) • <span class="font-mono">{{ $mahasiswa->npm }}</span> @endif
                @if(count($items) > 0) • <span class="text-emerald-300 font-semibold">{{ count($items) }} Mata Kuliah · IPK {{ str_replace('.', ',', number_format($ipk, 2)) }}</span> @endif
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.transkrip-nilai.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Daftar Mahasiswa
            </a>
            <a href="{{ route('admin.transkrip-nilai.edit', $mahasiswa) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                <i class="fa-solid fa-pen-to-square"></i>
                Edit Data Transkrip
            </a>
            <button onclick="window.print()" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                <i class="fa-solid fa-print"></i>
                Cetak Halaman
            </button>
          
        </div>
    </div>

    @if(session('success'))
        <div class="no-print mb-5 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(count($items) === 0)
        <div class="no-print mb-5 rounded-2xl border border-yellow-500/20 bg-yellow-500/10 p-5 text-yellow-100">
            <div class="font-semibold mb-1 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                Mahasiswa ini belum memiliki nilai transkrip.
            </div>
            <div class="text-sm text-yellow-100/80">
                Silakan lakukan proses pengisian nilai oleh Dosen melalui menu Nilai. Nilai akan muncul di transkrip setelah disimpan.
            </div>
        </div>
    @endif

    <div class="no-print grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="flex items-center gap-3">
                <span class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-white/10 border border-white/10 text-emerald-200">
                    <i class="fa-solid fa-user-graduate"></i>
                </span>
                <div>
                    <div class="text-sm text-emerald-100/70">Program Studi</div>
                    <div class="mt-0.5 font-semibold">{{ $mahasiswa->program_studi ?? '-' }}</div>
                    <div class="text-xs text-emerald-100/60 mt-1">Angkatan {{ $mahasiswa->angkatan ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="flex items-center gap-3">
                <span class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-white/10 border border-white/10 text-emerald-200">
                    <i class="fa-solid fa-book"></i>
                </span>
                <div>
                    <div class="text-sm text-emerald-100/70">Total SKS & Mata Kuliah</div>
                    <div class="mt-0.5 font-semibold">{{ $totalSks }} SKS · {{ count($items) }} MK</div>
                    <div class="text-xs text-emerald-100/60 mt-1">Total Nilai Mutu: {{ str_replace('.', ',', number_format($totalMutu, 2)) }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-5">
            <div class="flex items-center gap-3">
                <span class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-emerald-600/20 border border-emerald-500/20 text-emerald-200">
                    <i class="fa-solid fa-trophy"></i>
                </span>
                <div>
                    <div class="text-sm text-emerald-100/80">IPK Akhir</div>
                    <div class="mt-0.5 text-2xl font-extrabold leading-none tracking-tight">{{ str_replace('.', ',', number_format($ipk, 2)) }}</div>
                    <div class="text-xs text-emerald-100/70 mt-1">Predikat: {{ $predikat }}</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $logoSrc = public_path('img/lo.jpeg');
        $logoData = null;
        if (file_exists($logoSrc)) {
            $logoData = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoSrc));
        }

        $half = (int) ceil(count($items) / 2);
        $left = array_slice($items, 0, $half);
        $right = array_slice($items, $half);
        $maxRows = max(count($left), count($right));
        if ($maxRows < 1) $maxRows = 5;

        $ujianKompre = $ujianKompre ?? [];
        $ujianAda = array_values(array_filter(array_map(fn($v) => trim((string)$v), $ujianKompre), fn($v) => $v !== ''));
        $ujianCount = count($ujianAda);

        $tglLahir = $mahasiswa->tanggal_lahir ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lahir)->translatedFormat('d F Y') : '-';
        $tempatTgl = trim(($mahasiswa->tempat_lahir ? $mahasiswa->tempat_lahir.', ' : '') . $tglLahir);
        $tanggalLulus = $mahasiswa->tanggal_lulus ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus)->translatedFormat('d F Y') : '-';
        $skBanpt = trim($mahasiswa->nomor_sk_banpt ?: '-');
        $judulSkripsi = trim($mahasiswa->judul_skripsi ?: '-');
    @endphp

    <div class="transcript-preview">
        <div class="transcript-paper">
            {{-- ===== KOP SURAT ===== --}}
            <table class="kop-surat" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="kop-logo">
                        @if($logoData)
                            <img src="{{ $logoData }}" alt="Logo">
                        @endif
                    </td>
                    <td class="kop-text">
                        <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                        <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
                        <div class="kop-title-3">SIDENRENG RAPPANG</div>
                        <div class="kop-meta">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                        <div class="kop-alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                        <div class="kop-alamat">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
                    </td>
                </tr>
            </table>
            <div class="kop-line-tebal"></div>
            <div class="kop-line-tipis"></div>

            {{-- ===== JUDUL TRANSKRIP ===== --}}
            <div class="judul-box">
                <div class="judul-text">Transkrip Akademik</div>
                <div class="judul-nomor">Nomor : {{ $nomorTranskrip }}</div>
            </div>

            {{-- ===== BIODATA 50-50 KIRI KANAN ===== --}}
            <table class="biodata" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="bio-kiri">
                        <div><span class="bio-label">Nama</span><span class="bio-sep">:</span><span class="bio-val">{{ $mahasiswa->nama_lengkap }}</span></div>
                        <div><span class="bio-label">No. Pokok Mahasiswa</span><span class="bio-sep">:</span><span class="bio-val">{{ $mahasiswa->npm ?? '-' }}</span></div>
                        <div><span class="bio-label">NIK</span><span class="bio-sep">:</span><span class="bio-val">{{ $mahasiswa->nik ?? '-' }}</span></div>
                        <div><span class="bio-label">Tempat / Tanggal Lahir</span><span class="bio-sep">:</span><span class="bio-val">{{ $tempatTgl }}</span></div>
                        <div><span class="bio-label">Tanggal, Bulan dan Tahun Lulus</span><span class="bio-sep">:</span><span class="bio-val">{{ $tanggalLulus }}</span></div>
                    </td>
                    <td class="bio-kanan">
                        <div><span class="bio-label">Program Pendidikan</span><span class="bio-sep">:</span><span class="bio-val">Strata Satu (S1)</span></div>
                        <div><span class="bio-label">Fakultas</span><span class="bio-sep">:</span><span class="bio-val">{{ $mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan' }}</span></div>
                        <div><span class="bio-label">Program Studi</span><span class="bio-sep">:</span><span class="bio-val">{{ $mahasiswa->program_studi ?? '-' }}</span></div>
                        <div><span class="bio-label">No. SK BAN-PT</span><span class="bio-sep">:</span><span class="bio-val">{{ $skBanpt }}</span></div>
                        <div></div>
                    </td>
                </tr>
            </table>

            {{-- ===== TABEL NILAI ===== --}}
            <table class="nilai" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <th class="num">NO</th>
                        <th class="mk">MATA KULIAH</th>
                        <th class="sks">SKS</th>
                        <th class="nilaih">NILAI</th>
                        <th class="m">M</th>
                        <th class="num">NO</th>
                        <th class="mk">MATA KULIAH</th>
                        <th class="sks">SKS</th>
                        <th class="nilaih">NILAI</th>
                        <th class="m">M</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i < $maxRows; $i++)
                        @php
                            $L = $left[$i] ?? null;
                            $R = $right[$i] ?? null;
                            $noL = $L ? ($i + 1) : '';
                            $noR = $R ? ($half + $i + 1) : '';
                            $namaL = $L ? ($L->mataKuliah?->nama ?? '') : '';
                            $sksL = $L ? ($L->_sks ?? 0) : '';
                            $nhL = $L ? ($L->_nilai_huruf !== '' ? $L->_nilai_huruf : '-') : '';
                            $mutuL = $L ? ($L->_nilai_m > 0 ? rtrim(rtrim(number_format($L->_nilai_m, 2, '.', ''), '0'), '.') : ($L->_nilai_huruf !== '' ? '0' : '')) : '';
                            $namaR = $R ? ($R->mataKuliah?->nama ?? '') : '';
                            $sksR = $R ? ($R->_sks ?? 0) : '';
                            $nhR = $R ? ($R->_nilai_huruf !== '' ? $R->_nilai_huruf : '-') : '';
                            $mutuR = $R ? ($R->_nilai_m > 0 ? rtrim(rtrim(number_format($R->_nilai_m, 2, '.', ''), '0'), '.') : ($R->_nilai_huruf !== '' ? '0' : '')) : '';
                        @endphp
                        <tr>
                            <td class="num">{{ $noL }}</td>
                            <td class="mk">{{ $namaL }}</td>
                            <td class="sks">{{ $sksL }}</td>
                            <td class="nilaih">{{ $nhL }}</td>
                            <td class="m">{{ $mutuL }}</td>
                            <td class="num">{{ $noR }}</td>
                            <td class="mk">{{ $namaR }}</td>
                            <td class="sks">{{ $sksR }}</td>
                            <td class="nilaih">{{ $nhR }}</td>
                            <td class="m">{{ $mutuR }}</td>
                        </tr>
                    @endfor

                    <tr class="jumlah">
                        <td colspan="2" class="mk">Jumlah</td>
                        <td class="sks">{{ $totalSks }}</td>
                        <td></td>
                        <td class="m">{{ rtrim(rtrim(number_format($totalMutu, 2, '.', ''), '0'), '.') }}</td>
                        <td colspan="2"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    @if($ujianCount > 0)
                        <tr class="ujian-head">
                            <td colspan="10">UJIAN KOMPETENSI</td>
                        </tr>
                        <tr class="ujian-row">
                            <td class="num">1</td>
                            <td class="mk">{{ $ujianAda[0] }}</td>
                            <td class="sks">0</td>
                            <td class="nilaih">A</td>
                            <td class="m">0</td>
                            <td colspan="5" class="ujian-inner">
                                @if($ujianCount > 1)
                                    <table class="inner-ujian" cellpadding="0" cellspacing="0">
                                        @for($u = 1; $u < min($ujianCount, 4); $u++)
                                            <tr>
                                                <td class="num">{{ $u }}</td>
                                                <td class="mk">{{ $ujianAda[$u] }}</td>
                                                <td class="sks">0</td>
                                                <td class="nilaih">A</td>
                                                <td class="m">0</td>
                                            </tr>
                                        @endfor
                                    </table>
                                @endif
                            </td>
                        </tr>
                        @if($ujianCount > 4)
                            <tr class="ujian-row">
                                <td colspan="5"></td>
                                <td colspan="5" class="ujian-inner">
                                    <table class="inner-ujian" cellpadding="0" cellspacing="0">
                                        @for($u = 4; $u < $ujianCount; $u++)
                                            <tr>
                                                <td class="num">{{ $u }}</td>
                                                <td class="mk">{{ $ujianAda[$u] }}</td>
                                                <td class="sks">0</td>
                                                <td class="nilaih">A</td>
                                                <td class="m">0</td>
                                            </tr>
                                        @endfor
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>

            {{-- ===== RINGKASAN IPK, PREDIKAT, JUDUL SKRIPSI ===== --}}
            <table class="ringkasan" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="label">INDEKS PRESTASI KUMULATIF (IPK)</td>
                    <td class="sep">:</td>
                    <td class="val">{{ str_replace('.', ',', number_format($ipk, 2)) }}</td>
                </tr>
                <tr>
                    <td class="label">PREDIKAT KELULUSAN</td>
                    <td class="sep">:</td>
                    <td class="val">{{ $predikat }}</td>
                </tr>
                <tr>
                    <td class="label">JUDUL SKRIPSI</td>
                    <td class="sep">:</td>
                    <td class="justify">{{ $judulSkripsi }}</td>
                </tr>
            </table>

            {{-- ===== TANDA TANGAN ===== --}}
            <table class="ttd-box" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="ttd-spacer-l"></td>
                    <td class="ttd-spacer-m"></td>
                    <td class="ttd-col">
                        <div>Pangkajene, {{ ($mahasiswa->tanggal_lulus ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus) : now())->translatedFormat('d F Y') }}</div>
                        <div class="ttd-jabatan">WAKIL REKTOR BIDANG AKADEMIK</div>
                        <div class="ttd-nama">Dr. H. UMAR YAHYA, M.Ag.</div>
                        <div class="ttd-nidk">NIDK. 8932610021</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <style>
        *, *:before, *:after { box-sizing: border-box; }
        table, table th, table td { box-sizing: border-box; }

        .transcript-preview {
            min-height: 100vh;
            background: #e5e7eb;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            box-sizing: border-box;
            margin: -1.25rem -1.25rem -2.5rem;
            width: calc(100% + 2.5rem);
        }
        .transcript-paper {
            width: 210mm;
            height: 330mm;
            max-height: 330mm;
            background: #ffffff;
            color: #000000;
            padding: 10mm 13mm 10mm;
            box-shadow: 0 8px 30px rgba(0,0,0,.15);
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            overflow: hidden;
        }
        @media (max-width: 1200px) {
            .transcript-paper { width: min(210mm, 95%); height: auto; max-height: none; overflow: visible; }
        }
        @media (max-width: 900px) {
            .transcript-preview { padding: 20px 10px; }
            .transcript-paper { padding: 10mm 8mm; width: 100%; height: auto; }
        }

        /* ====== SEMUA CLASS DI BAWAH INI = PERSIS SAMA DENGAN pdf.blade.php ====== */
        .wrap { width: 100%; }

        .kop-surat { width: 100%; border-collapse: collapse; }
        .kop-logo { width: 86px; vertical-align: top; padding-top: 2px; padding-right: 14px; }
        .kop-logo img { width: 76px; height: auto; display: block; }
        .kop-text { text-align: center; vertical-align: middle; color: #000000; }
        .kop-title-1 { font-size: 15px; font-weight: 700; letter-spacing: 0.5px; line-height: 1.1; margin: 0; padding: 0; }
        .kop-title-2 { font-size: 20px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.12; margin: 3px 0 0; padding: 0; }
        .kop-title-3 { font-size: 16px; font-weight: 800; letter-spacing: 0.5px; line-height: 1.1; margin: 3px 0 0; padding: 0; }
        .kop-meta { font-size: 9px; margin-top: 6px; line-height: 1.2; color: #000000; }
        .kop-alamat { font-size: 9px; margin-top: 3px; line-height: 1.2; color: #000000; }
        .kop-line-tebal { border-top: 3px solid #000; margin-top: 11px; }
        .kop-line-tipis { border-top: 1px solid #000; margin-top: 2px; }

        .judul-box { text-align: center; margin-top: 18px; }
        .judul-text {
            font-size: 16px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            text-decoration: underline; text-underline-offset: 5px; text-decoration-thickness: 1.2px; color: #000000;
        }
        .judul-nomor { font-size: 9.2px; margin-top: 9px; color: #000000; }

        .biodata {
            width: 100%; margin-top: 18px; border-collapse: collapse;
            font-size: 9.5px; color: #000000;
        }
        .biodata td { vertical-align: top; padding: 0; line-height: 1.3; }
        .bio-kiri, .bio-kanan { width: 50%; }
        .bio-label { width: 130px; display: inline-block; padding: 1.5px 0; }
        .bio-sep   { width: 10px;  display: inline-block; padding: 1.5px 0; text-align: center; }
        .bio-val   { display: inline-block; padding: 1.5px 0; font-weight: 700; color: #000000; }

        table.nilai {
            width: 100%; border-collapse: collapse; margin-top: 14px;
            font-size: 9px; color: #000000; table-layout: fixed;
        }
        table.nilai th {
            border: 1px solid #000; background: #e0f2ea; font-weight: 700; letter-spacing: 0.2px;
            padding: 5px 3px; vertical-align: middle; line-height: 1.22; text-align: center;
        }
        table.nilai th.mk { text-align: left; padding: 5px 6px; width: 31%; }
        table.nilai th.num { width: 4%; padding: 5px 2px; }
        table.nilai th.sks { width: 5%; padding: 5px 2px; }
        table.nilai th.nilaih { width: 5%; padding: 5px 2px; }
        table.nilai th.m { width: 5%; padding: 5px 2px; }
        table.nilai td {
            border: 1px solid #000; padding: 3px 4px; vertical-align: middle;
            line-height: 1.22; text-align: center; color: #000000;
        }
        table.nilai td.mk { text-align: left; padding: 3px 6px; width: 31%; }
        table.nilai td.num { width: 4%; padding: 3px 2px; }
        table.nilai td.sks { width: 5%; padding: 3px 2px; }
        table.nilai td.nilaih { width: 5%; font-weight: 700; padding: 3px 2px; }
        table.nilai td.m { width: 5%; padding: 3px 2px; }
        table.nilai tr.jumlah td {
            background: #e0f2ea; font-weight: 700; padding: 3.5px 6px;
            letter-spacing: 0.2px; line-height: 1.22;
        }
        table.nilai tr.jumlah td.mk { text-align: left; }
        table.nilai tr.ujian-head td {
            background: #f7fef9; font-weight: 700; padding: 3.5px 6px;
            letter-spacing: 0.3px; font-size: 8.5px; line-height: 1.22;
        }
        table.nilai tr.ujian-row td { font-size: 8.3px; padding: 3px 5px; line-height: 1.22; }
        table.nilai td.ujian-inner { padding: 1px 0; vertical-align: top; }
        table.inner-ujian { width: 100%; border-collapse: collapse; }
        table.inner-ujian td { border: none; padding: 2px 3px; }
        table.inner-ujian td.mk { padding: 2px 5px; }
        table.inner-ujian td.num    { width: 28px; }
        table.inner-ujian td.sks    { width: 32px; }
        table.inner-ujian td.nilaih { width: 34px; }
        table.inner-ujian td.m      { width: 32px; }

        .ringkasan {
            width: 100%; margin-top: 9px; border-collapse: collapse;
            font-size: 9.2px; color: #000000;
        }
        .ringkasan td { vertical-align: top; padding: 1.8px 0; line-height: 1.32; }
        .ringkasan td.label { width: 190px; font-weight: 700; color: #000000; }
        .ringkasan td.sep   { width: 10px;  text-align: center; }
        .ringkasan td.val   { font-weight: 800; color: #000000; font-size: 9.8px; }
        .ringkasan td.justify { text-align: justify; color: #000000; }

        .ttd-box {
            width: 100%; margin-top: 12px; border-collapse: collapse;
            font-size: 9px; color: #000000;
        }
        .ttd-box td { vertical-align: top; }
        .ttd-spacer-l { width: 44%; }
        .ttd-spacer-m { width: 14%; }
        .ttd-col { width: 42%; text-align: center; line-height: 1.32; color: #000000; }
        .ttd-jabatan { margin-top: 2px; font-weight: 800; }
        .ttd-nama    { margin-top: 40px; font-weight: 800; text-decoration: underline; font-size: 9.8px; }
        .ttd-nidk    { margin-top: 2px; font-size: 8.6px; }

        @page {
            size: 210mm 330mm;
            margin: 0;
        }
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 330mm !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .transcript-preview {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                width: 100% !important;
                min-height: auto !important;
            }
            .transcript-paper {
                width: 210mm !important;
                height: 330mm !important;
                max-height: 330mm !important;
                margin: 0 !important;
                padding: 10mm 13mm 10mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: hidden !important;
            }
            .no-print, aside, nav, header, [x-data] > aside, nav.sidebar { display: none !important; }
            main {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
            }
        }
    </style>
</x-portal-layout>
