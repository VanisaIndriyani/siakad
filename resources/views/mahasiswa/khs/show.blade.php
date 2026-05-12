<x-portal-layout :title="'Detail KHS - '.config('app.name')" subtitle="Detail KHS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            .lg\:pl-72 { padding-left: 0 !important; }
            main { padding: 0 !important; }
            body { background: #fff !important; color: #000 !important; }
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
    @endphp

    <div class="flex items-center justify-between gap-3 mb-5">
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
            <button type="button" onclick="window.print()" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-print"></i>
                Cetak
            </button>
            <a href="{{ route('mahasiswa.khs.index') }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
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

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
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
</x-portal-layout>
