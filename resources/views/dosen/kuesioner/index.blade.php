<x-portal-layout :title="'Hasil Kuesioner - '.config('app.name')" subtitle="Hasil Kuesioner">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $formatScore = function ($value) {
            return $value !== null ? number_format((float) $value, 2) : '-';
        };
    @endphp

    <div>
        <div class="text-xl font-semibold">Hasil Kuesioner</div>
        <div class="text-sm text-emerald-100/70">Lihat hasil evaluasi mahasiswa pada mata kuliah yang Anda ampu.</div>
    </div>

    <div class="mt-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ $q }}" class="w-full sm:max-w-lg h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode atau mata kuliah..." />
            <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
            <a href="{{ route('dosen.kuesioner.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </form>

        <div class="flex items-center gap-2">
            <a href="{{ route('dosen.kuesioner.summary.excel', array_filter(['q' => $q])) }}" class="h-11 px-4 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100">
                <i class="fa-solid fa-file-excel"></i>
                Excel
            </a>
            <a href="{{ route('dosen.kuesioner.summary.pdf', array_filter(['q' => $q])) }}" class="h-11 px-4 inline-flex items-center justify-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100">
                <i class="fa-solid fa-file-pdf"></i>
                PDF
            </a>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Respon</th>
                        <th class="text-left font-medium px-4 py-3">Rata-rata</th>
                        @foreach ($scoreLabels as $score => $label)
                            <th class="text-left font-medium px-4 py-3">{{ $label }}</th>
                        @endforeach
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $course->kode }} - {{ $course->nama }}</div>
                                <div class="text-xs text-emerald-100/60">Semester {{ $course->semester }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $course->responses_count }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $formatScore($course->average_score) }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_1_pct !== null ? number_format((float) $course->score_1_pct, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_2_pct !== null ? number_format((float) $course->score_2_pct, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_3_pct !== null ? number_format((float) $course->score_3_pct, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_4_pct !== null ? number_format((float) $course->score_4_pct, 2).'%' : '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('dosen.kuesioner.show', $course->id) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-emerald-100/70">Belum ada hasil kuesioner pada mata kuliah Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>
</x-portal-layout>
