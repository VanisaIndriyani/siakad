<x-portal-layout :title="'KHS - '.config('app.name')" subtitle="KHS Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    @php
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
    @endphp

    <div>
        <div class="text-xl font-semibold">KHS</div>
        <div class="text-sm text-emerald-100/70">KHS akan tampil jika nilai sudah diinput.</div>
    </div>

    <div class="mt-5 space-y-3">
        @forelse ($khs as $row)
            @php
                $items = $row->items
                    ->filter(fn ($item) => $item->nilai_huruf !== null || $item->nilai_angka !== null)
                    ->sortBy(fn ($item) => (string) ($item->mataKuliah?->kode ?? ''));
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

            <details class="group rounded-2xl bg-white/5 border border-white/10 p-4 hover:bg-white/10 transition">
                <summary class="cursor-pointer list-none">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm text-emerald-100/70">Semester</div>
                            <div class="text-xl font-semibold">Semester {{ $row->semester }}</div>
                            <div class="mt-1 text-sm text-emerald-100/70">
                                Tahun Akademik: <span class="text-emerald-100/90 font-medium">{{ $row->tahun_ajaran ?? '-' }}</span>
                                • Nilai: <span class="text-emerald-100/90 font-medium">{{ $row->nilai_count }}</span>
                            </div>
                            <div class="mt-2 text-sm text-emerald-100/70">IPS: <span class="text-emerald-100/90 font-medium">{{ $row->ips ?? '-' }}</span> • IPK: <span class="text-emerald-100/90 font-medium">{{ $row->ipk ?? '-' }}</span></div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('mahasiswa.khs.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                <i class="fa-solid fa-eye"></i>
                                Detail
                            </a>
                            <div class="h-9 w-9 rounded-xl bg-emerald-500/10 border border-emerald-400/20 flex items-center justify-center group-hover:bg-emerald-500/15 transition">
                                <i class="fa-solid fa-chevron-down text-emerald-200 group-open:rotate-180 transition-transform"></i>
                            </div>
                        </div>
                    </div>
                </summary>

                <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/5 text-emerald-100/80">
                                <tr>
                                    <th class="text-left font-medium px-4 py-3 w-16">No</th>
                                    <th class="text-left font-medium px-4 py-3">Kode</th>
                                    <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                                    <th class="text-left font-medium px-4 py-3">SKS</th>
                                    <th class="text-left font-medium px-4 py-3">Nilai</th>
                                    <th class="text-left font-medium px-4 py-3">Bobot</th>
                                    <th class="text-left font-medium px-4 py-3">SKS * Bobot</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach ($items as $item)
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
                                        <td class="px-4 py-3 text-emerald-100/80">{{ $item->nilai_huruf ?? '-' }}</td>
                                        <td class="px-4 py-3 text-emerald-100/80">{{ $formatAngka($bobot) }}</td>
                                        <td class="px-4 py-3 text-emerald-100/80">{{ $formatAngka($sksBobot) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-white/5">
                                <tr>
                                    <td class="px-4 py-3 font-medium" colspan="3">Total</td>
                                    <td class="px-4 py-3 font-medium">{{ $totalSks }}</td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3"></td>
                                    <td class="px-4 py-3 font-medium">{{ $formatAngka($totalSksBobot) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </details>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 text-center text-emerald-100/70">
                Nilai belum tersedia.
            </div>
        @endforelse
    </div>
</x-portal-layout>
