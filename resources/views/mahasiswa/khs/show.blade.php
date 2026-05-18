<x-portal-layout :title="'Detail KHS - '.config('app.name')" subtitle="Detail KHS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <style>
        .print-only { display: none; }
        @media print {
            aside, header, .no-print { display: none !important; }
            .print-only { display: block !important; }
            .lg\:pl-72 { padding-left: 0 !important; }
            main { padding: 0 !important; }
            body { background: #fff !important; color: #000 !important; }
            @page { margin: 16mm 12mm; }
            .print-content, .print-content * { color: #000 !important; }
            a { color: #000 !important; text-decoration: none !important; }
            .print-content [class*="bg-"],
            .print-content [class*="border-"],
            .print-content .rounded-2xl { background: transparent !important; box-shadow: none !important; }
            .print-content .rounded-2xl { border-radius: 0 !important; }
            .print-content .overflow-hidden,
            .print-content .overflow-x-auto { overflow: visible !important; }
            .print-content table { border-collapse: collapse !important; }
            .print-content th, .print-content td { border: 1px solid #111827 !important; }
            .print-content thead { background: transparent !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>

    @php
        $mahasiswa = auth()->user()->mahasiswa;
        $items = $khs->items->sortBy(fn ($item) => (string) ($item->mataKuliah?->kode ?? ''));
        $bobotMap = [
            'A' => 4,
            'A-' => 3.66,
            'B+' => 3.33,
            'B' => 3,
            'B-' => 2.66,
            'C+' => 2.33,
            'C' => 2,
            'D' => 1,
            'E' => 0,
        ];
        $formatAngka = function ($value) {
            if ($value === null || $value === '') {
                return '-';
            }
            if (!is_numeric($value)) {
                return (string) $value;
            }
            $formatted = number_format((float) $value, 2, '.', '');
            return rtrim(rtrim($formatted, '0'), '.');
        };
        $totalSks = 0;
        $totalSksBobot = 0.0;
        foreach ($items as $item) {
            $sks = (int) ($item->mataKuliah?->sks ?? 0);
            $huruf = strtoupper(trim((string) ($item->nilai_huruf ?? '')));
            $bobot = $bobotMap[$huruf] ?? null;
            if ($sks > 0) {
                $totalSks += $sks;
                if ($bobot !== null) {
                    $totalSksBobot += $sks * $bobot;
                }
            }
        }

        $logoPath = public_path('img/lo.jpeg');
        $logoBase64 = null;
        if (is_string($logoPath) && file_exists($logoPath)) {
            $data = file_get_contents($logoPath);
            $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
        }
        $kopLine1 = 'INSTITUT AGAMA ISLAM';
        $kopLine2 = "DARUD DA'WAH WAL IRSYAD";
        $kopLine3 = 'SIDENRENG RAPPANG';
        $kopLine4 = 'TERAKREDITASI INSTITUSI • SK: 576/SK/BAN-PT/Akred/PT/IV/2021';
        $kopLine5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
        $kopLine6 = 'E-mail : iaiddisidrap@gmail.com  Website : www.yppddisrapp.ac.id';

        $kaprodiNama = $kaprodiNama ?? null;
        $kotaTtd = env('KAMPUS_KOTA') ?: 'Majelling Watang';
        $tanggalTtd = now()->format('d-m-Y');
    @endphp

    <div class="print-only" style="margin-bottom: 14px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 120px; vertical-align: middle; padding-top: 2px;">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" style="display: block; width: 95px; height: auto;" />
                    @endif
                </td>
                <td style="text-align: center;">
                    <div style="color: #111827; font-size: 18px; font-weight: 800; letter-spacing: 0.2px; line-height: 1.15;">{{ $kopLine1 }}</div>
                    <div style="color: #111827; font-size: 26px; font-weight: 900; letter-spacing: 0.6px; margin-top: 2px; line-height: 1.1;">{{ $kopLine2 }}</div>
                    <div style="color: #111827; font-size: 18px; font-weight: 900; letter-spacing: 0.2px; margin-top: 1px; line-height: 1.15;">{{ $kopLine3 }}</div>
                    <div style="color: #111827; font-size: 11px; margin-top: 6px; font-weight: 800; line-height: 1.2;">{{ $kopLine4 }}</div>
                    <div style="color: #111827; font-size: 11px; margin-top: 2px; line-height: 1.2;">{{ $kopLine5 }}</div>
                    <div style="color: #111827; font-size: 11px; margin-top: 2px; line-height: 1.2;">{{ $kopLine6 }}</div>
                </td>
                <td style="width: 120px;"></td>
            </tr>
        </table>
        <div style="border-top: 3px solid #6b7280; margin-top: 10px;"></div>
        <div style="border-top: 1px solid #6b7280; margin-top: 4px;"></div>
        @php
            $ps = strtoupper((string) ($mahasiswa?->program_studi ?? ''));
            $jenjang = str_contains($ps, 'S2') ? 'S2' : (str_contains($ps, 'S3') ? 'S3' : ($ps !== '' ? 'S1' : '-'));
        @endphp

        <div style="text-align: center; margin-top: 14px; font-size: 12px; font-weight: 800;">KARTU HASIL STUDI (KHS)</div>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 11px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="width: 140px;">Jenjang/Program</td><td style="width: 10px; text-align: center;">:</td><td>{{ $jenjang }}</td></tr>
                        <tr><td>Fakultas</td><td style="text-align: center;">:</td><td>{{ $mahasiswa?->fakultas ?? '-' }}</td></tr>
                        <tr><td>Program Studi</td><td style="text-align: center;">:</td><td>{{ $mahasiswa?->program_studi ?? '-' }}</td></tr>
                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr><td style="width: 140px;">Nama</td><td style="width: 10px; text-align: center;">:</td><td style="font-weight: 700;">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</td></tr>
                        <tr><td>NIM</td><td style="text-align: center;">:</td><td style="font-weight: 700;">{{ $mahasiswa?->npm ?? '-' }}</td></tr>
                        <tr><td>Tahun Akademik</td><td style="text-align: center;">:</td><td style="font-weight: 700;">{{ $khs->tahun_ajaran ?? '-' }}</td></tr>
                        <tr><td>Semester</td><td style="text-align: center;">:</td><td style="font-weight: 700;">{{ $khs->semester }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Kartu Hasil Studi (KHS)</div>
            <div class="text-sm text-emerald-100/70">
                {{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}
                @if ($mahasiswa?->npm)
                    • {{ $mahasiswa->npm }}
                @endif
                • {{ $khs->tahun_ajaran ?? '-' }} • Semester {{ $khs->semester }}
            </div>
        </div>
        <div class="flex items-center gap-2">
          
          
            <a href="{{ route('mahasiswa.khs.pdf', $khs) }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                <i class="fa-solid fa-file-pdf"></i>
                PDF
            </a>
            <a href="{{ route('mahasiswa.khs.index') }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 print-content">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Nilai</div>
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-4 py-3 w-16">No</th>
                                <th class="text-left font-medium px-4 py-3">Kode Mata Kuliah</th>
                                <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                                <th class="text-left font-medium px-4 py-3">SKS</th>
                                <th class="text-left font-medium px-4 py-3">Semester</th>
                                <th class="text-left font-medium px-4 py-3">Nilai</th>
                                <th class="text-left font-medium px-4 py-3">Bobot</th>
                                <th class="text-left font-medium px-4 py-3">SKS * Bobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($items as $item)
                                @php
                                    $sks = (int) ($item->mataKuliah?->sks ?? 0);
                                    $huruf = strtoupper(trim((string) ($item->nilai_huruf ?? '')));
                                    $bobot = $bobotMap[$huruf] ?? null;
                                    $sksBobot = $bobot !== null ? $sks * $bobot : null;
                                @endphp
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">
                                        <div>{{ $item->mataKuliah?->nama }}</div>
                                        @if ($item->mataKuliah?->dosen)
                                            <div class="text-xs mt-0.5 text-emerald-100/50">Dosen: {{ $item->mataKuliah->dosen->nama }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $khs->semester }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->nilai_huruf ?? '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $formatAngka($bobot) }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $formatAngka($sksBobot) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-emerald-100/70">Belum ada nilai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($items->count() > 0)
                            <tfoot class="bg-white/5">
                                <tr>
                                    <td class="px-4 py-3 font-medium" colspan="3">Total</td>
                                    <td class="px-4 py-3 font-medium">{{ $totalSks }}</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 font-medium">{{ $formatAngka($totalSksBobot) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="no-print rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Ringkasan</div>
            <div class="text-sm text-emerald-100/70 space-y-3">
                <div class="flex items-center justify-between">
                    <span>Total SKS</span>
                    <span class="font-medium text-white">{{ $totalSks }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>IPS</span>
                    <span class="font-medium text-white">{{ $khs->ips ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>IPK</span>
                    <span class="font-medium text-white">{{ $khs->ipk ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="print-only print-content" style="margin-top: 18mm; font-size: 11px;">
        <div>{{ $kotaTtd }}, {{ $tanggalTtd }}</div>
        <div style="margin-top: 2px; font-weight: 700;">Ketua Prodi {{ $mahasiswa?->program_studi ?? '-' }}</div>
        <div style="height: 70px;"></div>
        <div style="font-weight: 800;">{{ $kaprodiNama ?: '-' }}</div>
    </div>
</x-portal-layout>
