<x-portal-layout :title="'Detail KRS - '.config('app.name')" subtitle="Detail KRS">
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
            .print-content .rounded-2xl,
            .print-content .bg-white\/5,
            .print-content .bg-white\/10,
            .print-content .border,
            .print-content [class*="bg-"],
            .print-content [class*="border-"] { background: transparent !important; box-shadow: none !important; }
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
        $items = $krs->items->sortBy(fn ($item) => (string) ($item->mataKuliah?->kode ?? ''));
        $totalSks = $items->sum(fn ($item) => (int) ($item->mataKuliah?->sks ?? 0));
        $kaprodiNama = $kaprodiNama ?? null;
        $sekprodiNama = $sekprodiNama ?? null;

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
        $kopLine6 = 'E-mail : iaiddisrapp@gmail.com  Website : www.yppddisrapp.ac.id';
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
            $semesterLabel = ((int) $krs->semester % 2 === 0) ? 'GENAP' : 'GANJIL';
            $semesterHeader = $semesterLabel.($krs->tahun_ajaran ? '-'.$krs->tahun_ajaran : '');
        @endphp

        <div style="text-align: center; margin-top: 14px; font-size: 12px; font-weight: 800;">KARTU RENCANA STUDI</div>
        <div style="text-align: center; margin-top: 2px; font-size: 12px; font-weight: 800;">SEMESTER : {{ $semesterHeader }}</div>

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
                        <tr><td>Semester</td><td style="text-align: center;">:</td><td style="font-weight: 700;">{{ $semesterHeader }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="no-print flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Kartu Rencana Studi (KRS)</div>
            <div class="text-sm text-emerald-100/70">
                {{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}
                @if ($mahasiswa?->npm)
                    • {{ $mahasiswa->npm }}
                @endif
                • {{ $krs->tahun_ajaran ?? '-' }} • Semester {{ $krs->semester }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if ($krs->status_approval !== 'approved')
                <a href="{{ route('mahasiswa.krs.edit', $krs) }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-pen"></i>
                    Edit
                </a>
            @endif
           
       
            <a href="{{ route('mahasiswa.krs.pdf', $krs) }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                <i class="fa-solid fa-file-pdf"></i>
                PDF
            </a>
            <a href="{{ route('mahasiswa.krs.index') }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 print-content">
        <div class="no-print flex flex-wrap items-center gap-3 text-sm">
            @php
                $badge = match ($krs->status_approval) {
                    'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                {{ strtoupper($krs->status_approval) }}
            </span>
            @if ($krs->status_approval === 'approved')
                <span class="text-emerald-100/70">Disetujui oleh:</span>
                <span class="font-medium">Admin</span>
            @elseif ($krs->status_approval === 'rejected')
                <span class="text-emerald-100/70">Ditolak oleh:</span>
                <span class="font-medium">Admin</span>
            @else
                <span class="text-emerald-100/70">Menunggu verifikasi Admin</span>
            @endif
        </div>

        <div class="no-print">
            @if ($krs->catatan_approval)
                <div class="mt-4 p-4 rounded-xl bg-white/5 border border-white/10">
                    <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Keterangan Admin:</div>
                    <div class="mt-1 text-sm text-emerald-100/90 whitespace-pre-line">{{ $krs->catatan_approval }}</div>
                </div>
            @endif
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3 w-16">No</th>
                            <th class="text-left font-medium px-4 py-3">Kode Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                            <th class="text-left font-medium px-4 py-3">Semester</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($items as $item)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 text-emerald-100/80">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $krs->semester }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($items->count() > 0)
                        <tfoot class="bg-white/5">
                            <tr>
                                <td class="px-4 py-3 font-medium" colspan="3">Total SKS</td>
                                <td class="px-4 py-3 font-medium">{{ $totalSks }}</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="print-only" style="margin-top: 22mm;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%; text-align: center; vertical-align: top;">
                        <div style="font-size: 11px; font-weight: 700;">Ketua Prodi {{ $mahasiswa?->program_studi ?? '-' }}</div>
                        <div style="height: 64px;"></div>
                        <div style="font-size: 11px; font-weight: 800;">{{ $kaprodiNama ?: '-' }}</div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top;">
                        <div style="font-size: 11px; font-weight: 700;">Sekretaris Prodi {{ $mahasiswa?->program_studi ?? '-' }}</div>
                        <div style="height: 64px;"></div>
                        <div style="font-size: 11px; font-weight: 800;">{{ $sekprodiNama ?: '-' }}</div>
                    </td>
                    <td style="width: 33.33%; text-align: center; vertical-align: top;">
                        <div style="font-size: 11px; font-weight: 700;">Mahasiswa</div>
                        <div style="height: 64px;"></div>
                        <div style="font-size: 11px; font-weight: 800;">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</x-portal-layout>
