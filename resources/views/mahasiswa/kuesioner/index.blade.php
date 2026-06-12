<x-portal-layout :title="'Kuesioner - '.config('app.name')" subtitle="Kuesioner Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col gap-2">
        <div class="text-xl font-semibold">Kuesioner</div>
        <div class="text-sm text-emerald-100/70">Isi semua kuesioner yang tersedia sebelum membuka KHS.</div>
    </div>

    @if ($pendingItems->isNotEmpty())
        <div class="mt-5 rounded-2xl border border-yellow-500/20 bg-yellow-500/10 px-4 py-3 text-sm text-yellow-100">
            Masih ada <span class="font-semibold">{{ $pendingItems->count() }}</span> kuesioner yang wajib diisi.
        </div>
    @endif

    <div class="mt-5">
        <div class="text-lg font-semibold">Perlu Diisi</div>
        <div class="mt-1 text-sm text-emerald-100/70">Daftar mata kuliah yang sudah memiliki nilai tetapi belum diisi kuesionernya.</div>
    </div>

    <div class="mt-4 grid grid-cols-1 xl:grid-cols-2 gap-4">
        @forelse ($pendingItems as $item)
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-emerald-100/60">Semester {{ $item->khs?->semester }} • {{ $item->khs?->tahun_ajaran ?? '-' }}</div>
                        <div class="mt-1 text-lg font-semibold">{{ $item->mataKuliah?->nama }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">{{ $item->mataKuliah?->kode }} • {{ $item->mataKuliah?->sks }} SKS</div>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-yellow-500/20 bg-yellow-500/10 px-3 py-1 text-xs font-semibold text-yellow-100">
                        Belum Diisi
                    </span>
                </div>

                <div class="mt-4 text-sm text-emerald-100/80 space-y-1">
                    <div>Dosen 1: {{ $item->mataKuliah?->dosen?->nama ?? '-' }}</div>
                    @if ($item->mataKuliah?->dosen2?->nama)
                        <div>Dosen 2: {{ $item->mataKuliah->dosen2->nama }}</div>
                    @endif
                </div>

                <div class="mt-5">
                    <a href="{{ route('mahasiswa.kuesioner.show', [$item->khs_id, $item->mata_kuliah_id]) }}"
                       class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                        <i class="fa-solid fa-pen"></i>
                        Isi Kuesioner
                    </a>
                </div>
            </div>
        @empty
            <div class="xl:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-6 text-center text-emerald-100/70">
                Tidak ada kuesioner yang menunggu pengisian.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        <div class="text-lg font-semibold">Riwayat Pengisian</div>
        <div class="mt-1 text-sm text-emerald-100/70">Kuesioner yang sudah pernah Anda kirim.</div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Rata-rata</th>
                        <th class="text-left font-medium px-4 py-3">Komentar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($completedResponses as $response)
                        @php
                            $averageScore = $response->answers->avg('score');
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/80">{{ $response->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $response->mataKuliah?->nama ?? '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $response->mataKuliah?->kode ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">Semester {{ $response->semester }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $averageScore !== null ? number_format($averageScore, 2) : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $response->komentar ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada riwayat pengisian kuesioner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-portal-layout>
