<x-portal-layout :title="'Detail Hasil Kuesioner - '.config('app.name')" subtitle="Detail Hasil Kuesioner">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $formatScore = function ($value) {
            return $value !== null ? number_format((float) $value, 2) : '-';
        };
    @endphp

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail Hasil Kuesioner</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('dosen.kuesioner.export.excel', $mataKuliah) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100">
                <i class="fa-solid fa-file-excel"></i>
                Excel
            </a>
            <a href="{{ route('dosen.kuesioner.export.pdf', $mataKuliah) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100">
                <i class="fa-solid fa-file-pdf"></i>
                PDF
            </a>
            <a href="{{ route('dosen.kuesioner.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Dosen Utama</div>
            <div class="mt-2 text-lg font-semibold">{{ $mataKuliah->dosen?->nama ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Dosen Pendamping</div>
            <div class="mt-2 text-lg font-semibold">{{ $mataKuliah->dosen2?->nama ?? '-' }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Total Respon</div>
            <div class="mt-2 text-lg font-semibold">{{ $responses->total() }}</div>
        </div>
    </div>

    <div class="mt-8">
        <div class="text-lg font-semibold">Statistik per Pertanyaan</div>
        <div class="mt-1 text-sm text-emerald-100/70">Ringkasan jawaban mahasiswa untuk setiap pertanyaan.</div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Pertanyaan</th>
                        <th class="text-left font-medium px-4 py-3">Jawaban</th>
                        <th class="text-left font-medium px-4 py-3">Rata-rata</th>
                        @foreach ($scoreLabels as $score => $label)
                            <th class="text-left font-medium px-4 py-3">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($questionStats as $stat)
                        @php
                            $totalAnswers = (int) $stat->answers_count;
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/90">{{ $stat->question }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $totalAnswers }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $formatScore($stat->average_score) }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $totalAnswers > 0 ? number_format(($stat->score_1_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $totalAnswers > 0 ? number_format(($stat->score_2_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $totalAnswers > 0 ? number_format(($stat->score_3_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $totalAnswers > 0 ? number_format(($stat->score_4_total / $totalAnswers) * 100, 2).'%' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-emerald-100/70">Belum ada statistik kuesioner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        <div class="text-lg font-semibold">Komentar Mahasiswa</div>
        <div class="mt-1 text-sm text-emerald-100/70">Komentar ditampilkan apa adanya sesuai pengisian mahasiswa.</div>
    </div>

    <div class="mt-4 space-y-4">
        @forelse ($responses as $response)
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="font-semibold">{{ $response->mahasiswa?->nama_lengkap ?? '-' }}</div>
                        <div class="text-sm text-emerald-100/70">{{ $response->mahasiswa?->npm ?? '-' }} • {{ $response->created_at?->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="text-sm text-emerald-100/70">Rata-rata: <span class="font-medium text-white">{{ $formatScore($response->answers->avg('score')) }}</span></div>
                </div>
                <div class="mt-3 text-sm text-emerald-100/85">
                    {{ $response->komentar ?: 'Tidak ada komentar.' }}
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 text-center text-emerald-100/70">
                Belum ada hasil kuesioner untuk mata kuliah ini.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $responses->links() }}
    </div>
</x-portal-layout>
