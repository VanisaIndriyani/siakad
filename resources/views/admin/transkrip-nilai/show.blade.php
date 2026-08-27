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
           
            @php
                $namaFilePdf = 'Transkrip-' . ($mahasiswa->npm ?: $mahasiswa->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$mahasiswa->nama_lengkap) . '.pdf';
                $namaFileExcel = 'Transkrip-' . ($mahasiswa->npm ?: $mahasiswa->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$mahasiswa->nama_lengkap) . '.xlsx';
            @endphp
         
            <a href="{{ route('admin.transkrip-nilai.excel', $mahasiswa) }}"
               download="{{ $namaFileExcel }}"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-green-700 hover:bg-green-600 border border-green-500/40 transition text-sm font-semibold shadow-lg shadow-green-900/30 text-white">
                <i class="fa-solid fa-file-excel"></i>
                Download Excel
            </a>
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
        $logoWebPrefer = 'img/lo.jpeg';
        $logoWebBaseCandidates = [
            'img/lo.jpeg',
            'img/lo.jpg',
            'img/logo.jpeg',
            'img/logo.jpg',
            'img/logo.png',
            'img/Logo.png',
            'img/LOGO.png',
            'img/Lo.jpeg',
            'img/LO.JPEG',
            'img/LO.jpeg',
        ];

        function __findLogoByGlob($baseDirs, $relDir, $patterns) {
            if (!is_array($baseDirs) || !is_array($patterns)) return null;
            foreach ($baseDirs as $bd) {
                $bd = rtrim(str_replace('\\', '/', (string)$bd), '/');
                if ($bd === '') continue;
                $dir = $bd . '/' . trim(str_replace('\\', '/', (string)$relDir), '/');
                if (!@is_dir($dir)) continue;
                foreach ($patterns as $p) {
                    try {
                        $matches = @glob($dir . '/' . $p, GLOB_NOSORT | GLOB_BRACE);
                    } catch (\Throwable $e) {
                        $matches = false;
                    }
                    if (is_array($matches) && count($matches) > 0) {
                        foreach ($matches as $m) {
                            if (@is_file($m) && @is_readable($m)) {
                                $baseName = basename($m);
                                return trim(str_replace('\\', '/', (string)$relDir), '/') . '/' . $baseName;
                            }
                        }
                    }
                }
            }
            return null;
        }

        $logoWebRel = null;
        try {
            $baseDirs = [public_path()];
            try {
                $bp = rtrim(str_replace('\\', '/', base_path()), '/');
                if ($bp !== '') {
                    $baseDirs[] = $bp . '/public';
                    $baseDirs[] = $bp . '/public_html';
                    $baseDirs[] = $bp . '/../public_html';
                    $baseDirs[] = $bp . '/../../public_html';
                }
            } catch (\Throwable $e) {}
            $patterns = [
                '[Ll][Oo].[Jj][Pp][Ee][Gg]',
                '[Ll][Oo].[Jj][Pp][Gg]',
                '[Ll][Oo][Gg][Oo].[Jj][Pp][Ee][Gg]',
                '[Ll][Oo][Gg][Oo].[Jj][Pp][Gg]',
                '[Ll][Oo][Gg][Oo].[Pp][Nn][Gg]',
            ];
            $logoWebRel = __findLogoByGlob(array_values(array_unique($baseDirs)), 'img', $patterns);
        } catch (\Throwable $e) {
            $logoWebRel = null;
        }

        if (!$logoWebRel) {
            foreach ($logoWebBaseCandidates as $c) {
                try {
                    $abs = public_path($c);
                    if (@file_exists($abs) && @is_file($abs) && @is_readable($abs)) {
                        $logoWebRel = $c;
                        break;
                    }
                } catch (\Throwable $e) {}
            }
        }

        if (!$logoWebRel) {
            $logoWebRel = $logoWebPrefer;
        }

        $logoWeb = asset($logoWebRel);
        $logoWebFallbacks = [];
        foreach ($logoWebBaseCandidates as $c) {
            $logoWebFallbacks[] = asset($c);
        }
        $logoWebFallbacks = array_values(array_unique($logoWebFallbacks));
        $logoWebFallbackJs = json_encode($logoWebFallbacks, JSON_UNESCAPED_SLASHES);

        $half = (int) ceil(count($items) / 2);
        $left = array_slice($items, 0, $half);
        $right = array_slice($items, $half);
        $maxRows = max(count($left), count($right));
        if ($maxRows < 1) $maxRows = 5;

        $ujianKompre = $ujianKompre ?? [];
        $ujianAda = array_values(array_filter(array_map(fn($v) => trim((string)$v), $ujianKompre), fn($v) => $v !== ''));
        $ujianCount = count($ujianAda);
    @endphp

    <div class="transcript-preview">
        <div class="transcript-paper">
            {{-- ===== KOP SURAT (LOGO DI TENGAH, SESUAI CONTOH WILDAH ASLI) ===== --}}
            <div class="kop-wrap">
                <div class="kop-logo-center">
                    <img id="transkripLogoHeader"
                         src="{{ $logoWeb }}"
                         alt="Logo IAI DDI Sidrap"
                         width="110" height="110">
                    <script>
                        (function () {
                            var img = document.getElementById('transkripLogoHeader');
                            if (!img) return;
                            var fallbacks = {!! $logoWebFallbackJs !!};
                            var idx = 0;
                            var tried = {};
                            var current = img.getAttribute('src') || '';
                            tried[current] = true;
                            img.addEventListener('error', function () {
                                while (idx < fallbacks.length && tried[fallbacks[idx]]) idx++;
                                if (idx < fallbacks.length) {
                                    var next = fallbacks[idx++];
                                    tried[next] = true;
                                    img.setAttribute('src', next);
                                } else {
                                    img.style.display = 'none';
                                }
                            });
                        })();
                    </script>
                </div>
                <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
                <div class="kop-title-a kop-title-a2">DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-title-b">SIDENRENG RAPPANG</div>
                <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
                <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
               
            </div>

            {{-- ===== JUDUL TRANSKRIP ===== --}}
            <div class="judul-box">
                <div class="judul-text">Transkrip Akademik</div>
                <div class="judul-nomor">Nomor : {{ $nomorTranskrip }}</div>
            </div>

            {{-- ===== BIODATA 50/50 URUTAN SESUAI CONTOH WILDAH ===== --}}
            <table class="biodata" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="bio-label">Nama</td>
                    <td class="bio-value"><span class="bio-val">{{ $mahasiswa->nama_lengkap }}</span></td>
                    <td class="bio-label right-label">Program Pendidikan</td>
                    <td class="bio-value right-val"><span class="bio-val">Strata Satu (S1)</span></td>
                </tr>
                <tr>
                    <td class="bio-label">No. Pokok Mahasiswa</td>
                    <td class="bio-value"><span class="bio-val">{{ $mahasiswa->npm ?? '-' }}</span></td>
                    <td class="bio-label right-label">Fakultas</td>
                    <td class="bio-value right-val"><span class="bio-val">{{ $mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan' }}</span></td>
                </tr>
                <tr>
                    <td class="bio-label">No. Ijazah</td>
                    <td class="bio-value"><span class="bio-val">{{ $mahasiswa->nik ?? '-' }}</span></td>
                    <td class="bio-label right-label">Program Studi</td>
                    <td class="bio-value right-val"><span class="bio-val">{{ $mahasiswa->program_studi ?? '-' }}</span></td>
                </tr>
                <tr>
                    <td class="bio-label">Tempat / Tanggal Lahir</td>
                    <td class="bio-value"><span class="bio-val">{{ $tempatTgl }}</span></td>
                    <td class="bio-label right-label">No. SK BAN-PT</td>
                    <td class="bio-value right-val"><span class="bio-val">{{ $skBanpt }}</span></td>
                </tr>
                <tr>
                    <td class="bio-label">Tanggal, Bulan dan Tahun Lulus</td>
                    <td class="bio-value"><span class="bio-val">{{ $tanggalLulus }}</span></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>

            {{-- ===== TABEL NILAI (MK BIASA 2 PANEL → JUMLAH → SUB-PANEL UJIAN KOMPETENSI NOMOR URUT SENDIRI) ===== --}}
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
                    @php
                        $semuaMK = $daftarMataKuliah;
                        $totalMK = count($semuaMK);
                        $barisBawah = 3 + $ujianCount;
                        $sisa = max(0, $totalMK - $barisBawah);
                        $mkAtas = array_slice($semuaMK, 0, $sisa);
                        $mkBawahKiri = array_slice($semuaMK, $sisa);
                        while (count($mkBawahKiri) < $barisBawah) { $mkBawahKiri[] = null; }
                        $halfAtas = (int) ceil(count($mkAtas) / 2);
                        $kiriAtas = array_slice($mkAtas, 0, $halfAtas);
                        $kananAtas = array_slice($mkAtas, $halfAtas);
                        $maxAtas = max(count($kiriAtas), count($kananAtas));
                        $noAwalKanan = count($kiriAtas);
                    @endphp
                    @for($i = 0; $i < $maxAtas; $i++)
                        @php
                            $L = $kiriAtas[$i] ?? null;
                            $R = $kananAtas[$i] ?? null;
                            $noL = $L ? ($i + 1) : '';
                            $noR = $R ? ($noAwalKanan + $i + 1) : '';
                            $namaL = $L ? $L->nama_mata_kuliah : '';
                            $sksL = $L ? ($L->sks == 0 ? '0' : $L->sks) : '';
                            $nhL = $L ? ($L->nilai_huruf !== '' ? $L->nilai_huruf : '') : '';
                            $mutuL = $L ? ($L->nilai_m > 0 ? rtrim(rtrim(number_format($L->nilai_m, 2, '.', ''), '0'), '.') : ($L->nilai_huruf !== '' ? '0' : '')) : '';
                            $namaR = $R ? $R->nama_mata_kuliah : '';
                            $sksR = $R ? ($R->sks == 0 ? '0' : $R->sks) : '';
                            $nhR = $R ? ($R->nilai_huruf !== '' ? $R->nilai_huruf : '') : '';
                            $mutuR = $R ? ($R->nilai_m > 0 ? rtrim(rtrim(number_format($R->nilai_m, 2, '.', ''), '0'), '.') : ($R->nilai_huruf !== '' ? '0' : '')) : '';
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

                    @for($bi = 0; $bi < $barisBawah; $bi++)
                        @php
                            $LL = $mkBawahKiri[$bi] ?? null;
                            $namaLL = $LL ? $LL->nama_mata_kuliah : '';
                            $sksLL = $LL ? ($LL->sks == 0 ? '0' : $LL->sks) : '';
                            $nhLL = $LL ? ($LL->nilai_huruf !== '' ? $LL->nilai_huruf : '') : '';
                            $mutuLL = $LL ? ($LL->nilai_m > 0 ? rtrim(rtrim(number_format($LL->nilai_m, 2, '.', ''), '0'), '.') : ($LL->nilai_huruf !== '' ? '0' : '')) : '';
                            $noLanjutTampil = $LL ? ($noAwalKanan + count($kananAtas) + $bi + 1) : '';
                            if ($bi === 0) {
                                $jenisBaris = 'jumlah';
                            } elseif ($bi === 1) {
                                $jenisBaris = 'spacer';
                            } elseif ($bi === 2) {
                                $jenisBaris = 'ujian-head';
                            } else {
                                $jenisBaris = 'ujian-row';
                                $uIdx = $bi - 3;
                                $uNama = $ujianKompre[$uIdx] ?? '';
                                $uNo = $uIdx + 1;
                            }
                        @endphp
                        @if($jenisBaris === 'jumlah')
                    <tr class="jumlah">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num jumlah-dashed"></td>
                        <td class="mk">Jumlah</td>
                        <td class="sks">{{ $totalSks }}</td>
                        <td class="nilaih"></td>
                        <td class="m">{{ rtrim(rtrim(number_format($totalMutu, 2, '.', ''), '0'), '.') }}</td>
                    </tr>
                        @elseif($jenisBaris === 'spacer')
                    <tr class="spacer-row">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num"></td>
                        <td class="mk"></td>
                        <td class="sks"></td>
                        <td class="nilaih"></td>
                        <td class="m"></td>
                    </tr>
                        @elseif($jenisBaris === 'ujian-head')
                    <tr class="ujian-head">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num ujian-right-spacer"></td>
                        <td class="mk ujian-left-title" colspan="4">Ujian Kompetensi</td>
                    </tr>
                        @else
                    <tr class="ujian-row">
                        <td class="num left-col">{{ $noLanjutTampil }}</td>
                        <td class="mk left-col">{{ $namaLL }}</td>
                        <td class="sks left-col">{{ $sksLL }}</td>
                        <td class="nilaih left-col">{{ $nhLL }}</td>
                        <td class="m left-col">{{ $mutuLL }}</td>
                        <td class="num">{{ $uNo }}</td>
                        <td class="mk">{{ $uNama }}</td>
                        <td class="sks">0</td>
                        <td class="nilaih">A</td>
                        <td class="m">0</td>
                    </tr>
                        @endif
                    @endfor
                </tbody>
            </table>

            {{-- ===== RINGKASAN IPK, PREDIKAT, JUDUL SKRIPSI (SEJAJAR VERTIKAL + JUDUL INDENT) ===== --}}
            <table class="ringkasan" cellpadding="0" cellspacing="0">
                <colgroup>
                    <col style="width:290px;">
                    <col style="width:22px;">
                    <col style="width:auto;">
                </colgroup>
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
                    <td class="label-top">JUDUL SKRIPSI</td>
                    <td class="sep-top">:</td>
                    <td class="val-judul">{{ $judulSkripsi }}</td>
                </tr>
            </table>

            {{-- ===== FOTO MAHASISWA 35mm × 45mm (KIRI BAWAH) + TANDA TANGAN WR AK AKADEMIK (KANAN) ===== --}}
            <div style="page-break-inside: avoid; padding-left:90mm !important; margin-top:10px !important;">
                <table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0" style="padding-left:0 !important; margin:0 !important;">
                    <tr>
                        <td class="ttd-foto-col">
                            <div class="ttd-foto-box">
                                @if($fotoMahasiswa)
                                    <img src="{{ $fotoMahasiswa }}" alt="Foto {{ $mahasiswa->nama_lengkap }}">
                                @else
                                    <div class="ttd-foto-empty">Foto<br>3 × 4</div>
                                @endif
                            </div>
                        </td>
                        <td class="ttd-col-wrapper">
                            <table class="ttd-box" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="ttd-spacer-l"></td>
                                    <td class="ttd-col">
                                        <div>{{ $tanggalTtd }}</div>
                                        <div class="ttd-jabatan">{{ $ttdJabatan }}</div>
                                        <div class="ttd-nama">{{ $ttdNama }}</div>
                                        <div class="ttd-nidk">{{ $ttdNomorLabel }}. {{ $ttdNomor }}</div>
                                    </td>
                                    <td class="ttd-spacer-r"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
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
            height: auto;
            min-height: 330mm;
            max-height: none;
            background: #ffffff;
            color: #000000;
            padding: 10mm 13mm 10mm;
            box-shadow: 0 8px 30px rgba(0,0,0,.15);
            box-sizing: border-box;
            font-family: 'Times New Roman', Times, serif;
            overflow: visible;
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

        .kop-wrap { width: 100%; text-align: center; color: #000000; }
        .kop-logo-center { width: 100%; text-align: center; margin-bottom: 8px; }
        .kop-logo-center img { width: 110px; height: 110px; object-fit: contain; display: inline-block; }
        .kop-title-a {
            font-size: 24px; font-weight: 800; letter-spacing: 0.8px; line-height: 1.2; margin: 3px 0 0; padding: 0; color: #000000;
        }
        .kop-title-a2 { margin-top: 1px; }
        .kop-title-b {
            font-size: 23px; font-weight: 800; letter-spacing: 0.8px; line-height: 1.2; margin: 3px 0 0; padding: 0; color: #000000;
        }
        .kop-terakreditasi {
            font-size: 11px; margin-top: 6px; color: #000000; text-align: center; letter-spacing: 0.1px;
        }
        .kop-alamat-line {
            font-size: 10.5px; margin-top: 4px; line-height: 1.25; color: #000000; text-align: center;
        }
        .kop-email-web { margin-top: 2px; }
        .kop-line-double {
            margin-top: 3px;
            width: 100%;
            display: block;
        }
        .kop-line-double .kop-line-top {
            width: 100%; height: 2px; background: #000000;
        }
        .kop-line-double .kop-line-bottom {
            width: 100%; height: 1.5px; background: #000000; margin-top: 2px;
        }

        .judul-box { text-align: center; margin-top: 10px; }
        .judul-text {
            font-size: 16px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
            text-decoration: none; color: #000000;
        }
        .judul-nomor { font-size: 9.2px; margin-top: 1px; color: #000000; }

        .biodata {
            width: 100%; margin-top: 10px; border-collapse: collapse;
            font-size: 9.5px; color: #000000; table-layout: fixed;
        }
        .biodata td { vertical-align: top; padding: 0; line-height: 1.3; }
        .biodata td.bio-label {
            width: 25%;
            padding: 1.5px 10px 1.5px 0;
            text-align: left;
            font-weight: 400;
            color: #000000;
            position: relative;
        }
        .biodata td.bio-label.right-label {
            width: 20%;
        }
        .biodata td.bio-label:after {
            content: ":";
            position: absolute;
            right: 0px;
            top: 1.5px;
            display: inline-block;
            color: #000000;
        }
        .biodata td.bio-value {
            width: 25%;
            padding: 1.5px 0 1.5px 6px;
            color: #000000;
        }
        .biodata td.bio-value.right-val {
            width: 30%;
        }
        .bio-val { font-weight: 700; color: #000000; display: inline-block; }

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
            background: #ffffff !important; font-weight: 700; padding: 3.5px 6px;
            letter-spacing: 0.2px; line-height: 1.22;
        }
        table.nilai tr.jumlah td.mk { text-align: center; }
        table.nilai tr.jumlah td.jumlah-dashed {
            background: #ffffff !important;
            border-top: 1px dashed #000000 !important;
            border-bottom: none !important;
        }
        table.nilai tr.ujian-head td {
            background: #ffffff !important; font-weight: 700; letter-spacing: 0.2px;
            padding: 3.5px 6px; line-height: 1.22; font-size: 9px;
        }
        table.nilai td.ujian-left-title { text-align: left; padding-left: 8px !important; }
        table.nilai td.ujian-left-spacer,
        table.nilai td.ujian-left-spacer-cell,
        table.nilai td.ujian-right-spacer,
        table.nilai td.ujian-right-title,
        table.nilai td.ujian-right-title-sks,
        table.nilai td.ujian-right-title-nilai,
        table.nilai td.ujian-right-title-m { background: #ffffff !important; }
        table.nilai tr.spacer-row td {
            background: #ffffff !important; border: 1px solid #000000;
            height: 18px; padding: 0;
        }
        table.nilai tr.ujian-row td { font-size: 9px; padding: 3px 5px; line-height: 1.22; }

        table.nilai tr.jumlah td.left-col,
        table.nilai tr.spacer-row td.left-col,
        table.nilai tr.ujian-head td.left-col,
        table.nilai tr.ujian-row td.left-col {
            background: #ffffff !important;
            font-weight: 400 !important;
            padding: 3px 4px !important;
            text-align: center !important;
            letter-spacing: 0 !important;
        }
        table.nilai tr.jumlah td.mk.left-col,
        table.nilai tr.spacer-row td.mk.left-col,
        table.nilai tr.ujian-head td.mk.left-col,
        table.nilai tr.ujian-row td.mk.left-col {
            text-align: left !important;
            padding: 3px 6px !important;
        }

        .ringkasan {
            width: 100%; margin-top: 9px; border-collapse: collapse;
            font-size: 9.5px; color: #000000; table-layout: auto;
        }
        .ringkasan td { vertical-align: top; padding: 1.5px 0; line-height: 1.28; }
        .ringkasan td.label {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 12px;
        }
        .ringkasan td.label-top {
            width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding: 1.5px 12px 0 0;
        }
        .ringkasan td.sep   { width: auto; text-align: left; padding-right: 10px; }
        .ringkasan td.sep-top { width: auto; text-align: left; padding: 1.5px 10px 0 0; }
        .ringkasan td.val   { font-weight: 800; color: #000000; font-size: 9.8px; width: auto; white-space: nowrap; }
        .ringkasan td.val-judul {
            text-align: left; color: #000000; line-height: 1.28; padding: 1.5px 0 1.5px 0;
            vertical-align: top; width: auto;
        }

        .ttd-foto-wrapper {
            width: 100%; margin: 0 !important; border-collapse: collapse;
            padding-left: 0 !important; /* DIV LUAR yang handle geser ke kanan, TABLE INI TIDAK USAH PADDING LAGI, biar tidak konflik */
        }
        .ttd-foto-wrapper td { vertical-align: top; padding: 0; }
        .ttd-foto-col {
            width: 28mm; padding-right: 2mm;
        }
        .ttd-foto-box {
            width: 24mm; height: 32mm; /* foto diperkecil */
            border: 1px solid #333; background: #fdfdfd;
            overflow: hidden; box-sizing: border-box;
            position: relative;
            margin: 0; /* FOTO RATA KIRI FULL DI KOLOM KIRI */
        }
        .ttd-foto-box img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .ttd-foto-empty {
            position: absolute; inset: 0;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            color: #888; font-size: 10.5px; font-weight: 400;
            line-height: 1.25; text-align: center;
            background: #ffffff;
        }
        .ttd-col-wrapper { width: auto; }
        .ttd-box {
            width: 100%; margin-top: 0; border-collapse: collapse;
            font-size: 9.3px; color: #000000;
        }
        .ttd-box td { vertical-align: top; }
        .ttd-spacer-l { width: 0%; }
        .ttd-spacer-r { width: 0%; }
        .ttd-col { width: 100%; text-align: left; line-height: 1.32; color: #000000; padding-left: 0; font-size: 9.5px; }
        .ttd-jabatan { margin-top: 3px; font-weight: 800; letter-spacing: 0.2px; }
        .ttd-nama    { margin-top: 48px; font-weight: 800; text-decoration: underline; font-size: 9.5px; }
        .ttd-nidk    { margin-top: 1px; font-size: 8.5px; letter-spacing: 0.1px; }

        @page {
            size: 210mm 330mm;
            margin: 0 !important;
        }
        @media print {
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 210mm !important;
                height: 330mm !important;
                min-height: 330mm !important;
                max-height: 330mm !important;
                background: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                overflow: hidden !important;
            }
            .transcript-preview {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                width: 100% !important;
                min-height: auto !important;
                height: 330mm !important;
                max-height: 330mm !important;
                overflow: hidden !important;
            }
            .transcript-paper {
                width: 210mm !important;
                height: 330mm !important;
                max-height: 330mm !important;
                margin: 0 !important;
                padding: 8mm 11mm 8mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }
            .no-print, aside, nav, header, [x-data] > aside, nav.sidebar { display: none !important; }
            main {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: none !important;
                width: 100% !important;
                overflow: hidden !important;
            }
        }
    </style>
</x-portal-layout>
