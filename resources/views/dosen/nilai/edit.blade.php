<x-portal-layout :title="'Input Nilai - '.config('app.name')" subtitle="Input Nilai">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Input Nilai</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }} • Semester {{ $semester }}</div>
        </div>
        <a href="{{ route('dosen.nilai.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="GET" action="{{ route('dosen.nilai.edit', [$mataKuliah, $semester]) }}" class="mb-4 flex flex-col sm:flex-row gap-3">
        <input name="q" value="{{ $q ?? '' }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM..." />
        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
        <a href="{{ route('dosen.nilai.edit', [$mataKuliah, $semester]) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
    </form>

    <form method="POST" action="{{ route('dosen.nilai.update', [$mataKuliah, $semester]) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')

        <div>
            <div class="text-lg font-semibold">Nilai per Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Nilai diambil dari KRS approved pada semester ini.</div>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Angka</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Huruf</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($krs as $row)
                            @php
                                $mhs = $row->mahasiswa;
                                $existingItem = $existing->get($row->mahasiswa_id);
                                $isReady = (bool) $existingItem;
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $mhs?->nama_lengkap }}</div>
                                    <div class="text-xs text-emerald-100/60">{{ $mhs?->npm }}</div>
                                    @if (! $isReady)
                                        <div class="text-xs text-red-200/90 mt-1">KHS belum disiapkan Admin.</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" min="0" max="100"
                                           id="nilaiAngka{{ $row->mahasiswa_id }}"
                                           name="nilai_angka[{{ $row->mahasiswa_id }}]"
                                           value="{{ old('nilai_angka.'.$row->mahasiswa_id, $existingItem?->nilai_angka) }}"
                                           class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400"
                                           data-huruf-target="nilaiHuruf{{ $row->mahasiswa_id }}"
                                           @disabled(! $isReady) />
                                </td>
                                <td class="px-4 py-3">
                                    <input id="nilaiHuruf{{ $row->mahasiswa_id }}"
                                           name="nilai_huruf[{{ $row->mahasiswa_id }}]"
                                           value="{{ old('nilai_huruf.'.$row->mahasiswa_id, $existingItem?->nilai_huruf) }}"
                                           class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400"
                                           readonly />
                                </td>
                            </tr>
                        @endforeach
                        @if ($krs->count() === 0)
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-emerald-100/70">Tidak ada mahasiswa pada mata kuliah ini (KRS approved).</td>
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

    <div class="mt-4">
        {{ $krs->links() }}
    </div>

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
