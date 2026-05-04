<x-portal-layout :title="'Input Nilai - '.config('app.name')" subtitle="Input Nilai">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Input Nilai</div>
            <div class="text-sm text-emerald-100/70">{{ $krs->mahasiswa?->nama_lengkap }} • Semester {{ $krs->semester }}</div>
        </div>
        <a href="{{ route('dosen.nilai.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('dosen.nilai.update', $krs) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')

        <div>
            <div class="text-lg font-semibold">Nilai per Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Dosen hanya menginput nilai untuk tiap mata kuliah pada KRS ini.</div>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Angka</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Huruf</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($krs->items as $item)
                            @php
                                $mk = $item->mataKuliah;
                                $existingItem = $existing->get($item->mata_kuliah_id);
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $mk?->kode }} - {{ $mk?->nama }}</div>
                                </td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $mk?->sks }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" min="0" max="100"
                                           id="nilaiAngka{{ $item->mata_kuliah_id }}"
                                           name="nilai_angka[{{ $item->mata_kuliah_id }}]"
                                           value="{{ old('nilai_angka.'.$item->mata_kuliah_id, $existingItem?->nilai_angka) }}"
                                           class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400"
                                           data-huruf-target="nilaiHuruf{{ $item->mata_kuliah_id }}" />
                                </td>
                                <td class="px-4 py-3">
                                    <input id="nilaiHuruf{{ $item->mata_kuliah_id }}"
                                           name="nilai_huruf[{{ $item->mata_kuliah_id }}]"
                                           value="{{ old('nilai_huruf.'.$item->mata_kuliah_id, $existingItem?->nilai_huruf) }}"
                                           class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400"
                                           readonly />
                                </td>
                            </tr>
                        @endforeach
                        @if ($krs->items->count() === 0)
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Tidak ada mata kuliah pada KRS ini.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Nilai
            </button>
        </div>
    </form>

    <script>
        (function () {
            function hurufFromAngka(angka) {
                if (angka === null || Number.isNaN(angka)) return '';
                if (angka >= 85) return 'A';
                if (angka >= 80) return 'A-';
                if (angka >= 75) return 'B+';
                if (angka >= 70) return 'B';
                if (angka >= 65) return 'B-';
                if (angka >= 60) return 'C+';
                if (angka >= 55) return 'C';
                if (angka >= 40) return 'D';
                return 'E';
            }

            const angkaInputs = document.querySelectorAll('input[data-huruf-target]');
            angkaInputs.forEach((inp) => {
                const targetId = inp.getAttribute('data-huruf-target');
                const target = targetId ? document.getElementById(targetId) : null;
                if (!target) return;

                const sync = () => {
                    const raw = inp.value;
                    if (raw === '') {
                        target.value = '';
                        return;
                    }
                    const angka = parseFloat(raw);
                    target.value = hurufFromAngka(angka);
                };

                inp.addEventListener('input', sync);
                sync();
            });
        })();
    </script>
</x-portal-layout>
