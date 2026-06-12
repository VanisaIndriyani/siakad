<x-portal-layout :title="'Isi Kuesioner - '.config('app.name')" subtitle="Isi Kuesioner">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Isi Kuesioner</div>
            <div class="text-sm text-emerald-100/70">Nilai setiap pertanyaan dari 1 sampai 4 sesuai pengalaman perkuliahan.</div>
        </div>
        <a href="{{ route('mahasiswa.kuesioner.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-sm text-emerald-100/80">
            <div>
                <div class="text-emerald-100/60">Tahun Ajaran</div>
                <div class="mt-1 font-medium text-white">{{ $item->khs?->tahun_ajaran ?? '-' }}</div>
            </div>
            <div>
                <div class="text-emerald-100/60">Semester</div>
                <div class="mt-1 font-medium text-white">Semester {{ $item->khs?->semester }}</div>
            </div>
            <div>
                <div class="text-emerald-100/60">Mata Kuliah</div>
                <div class="mt-1 font-medium text-white">{{ $item->mataKuliah?->kode }} - {{ $item->mataKuliah?->nama }}</div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('mahasiswa.kuesioner.store', [$item->khs_id, $item->mata_kuliah_id]) }}" class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3 w-16">No</th>
                            <th class="text-left font-medium px-4 py-3">Pernyataan</th>
                            @foreach ($scoreLabels as $score => $label)
                                <th class="text-center font-medium px-4 py-3">{{ $label }} ({{ $score }})</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($questions as $question)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-4 text-emerald-100/80">{{ $loop->iteration }}</td>
                                <td class="px-4 py-4 text-emerald-100/90">{{ $question->question }}</td>
                                @foreach ($scoreLabels as $score => $label)
                                    <td class="px-4 py-4 text-center">
                                        <input type="radio"
                                               name="answers[{{ $question->id }}]"
                                               value="{{ $score }}"
                                               @checked((string) old('answers.'.$question->id) === (string) $score)
                                               class="h-4 w-4 border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0"
                                               required />
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-5">
            <label class="text-sm text-emerald-100/80">Saran atau masukan (opsional)</label>
            <textarea name="komentar" rows="5" maxlength="2000" class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Tulis saran atau masukan untuk pembelajaran mata kuliah ini...">{{ old('komentar') }}</textarea>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Kirim Kuesioner
            </button>
        </div>
    </form>
</x-portal-layout>
