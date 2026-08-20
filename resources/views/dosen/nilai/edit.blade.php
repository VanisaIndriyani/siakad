<x-portal-layout :title="'Input Nilai - '.config('app.name')" subtitle="Input Nilai">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Input Nilai</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }} • Semester {{ $semester }}</div>
            <div class="text-xs text-emerald-100/60 mt-1">
                Bobot Nilai: Tatap Muka (50%) • Tugas/Quis (20%) • MID (15%) • Final (15%)
            </div>
        </div>
        <a href="{{ route('dosen.nilai.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="mb-4 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
        <form method="GET" action="{{ route('dosen.nilai.edit', [$mataKuliah, $semester]) }}" class="flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ $q ?? '' }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM..." />
            <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
            <a href="{{ route('dosen.nilai.edit', [$mataKuliah, $semester]) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </form>

        <a href="{{ route('dosen.nilai.pdf', [$mataKuliah, $semester] + array_filter(['q' => $q])) }}" class="h-11 px-4 inline-flex items-center justify-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100">
            <i class="fa-solid fa-file-pdf"></i>
            PDF
        </a>
    </div>

    <form method="POST" action="{{ route('dosen.nilai.update', [$mataKuliah, $semester]) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')

        <div>
            <div class="text-lg font-semibold">Nilai per Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Input nilai komponen, Total Angka dan Nilai Mutu dihitung otomatis.</div>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-center font-medium px-3 py-3">TM (50%)</th>
                            <th class="text-center font-medium px-3 py-3">Quis (20%)</th>
                            <th class="text-center font-medium px-3 py-3">MID (15%)</th>
                            <th class="text-center font-medium px-3 py-3">Final (15%)</th>
                            <th class="text-center font-medium px-4 py-3">Total Angka</th>
                            <th class="text-center font-medium px-4 py-3">Nilai Mutu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($krs as $row)
                            @php
                                $mhs = $row->mahasiswa;
                                $existingItem = $existing->get($row->mahasiswa_id);
                                $isReady = (bool) $existingItem;
                                $tm = old('nilai_tm.'.$row->mahasiswa_id, $existingItem?->nilai_tm);
                                $quis = old('nilai_quis.'.$row->mahasiswa_id, $existingItem?->nilai_quis);
                                $mid = old('nilai_mid.'.$row->mahasiswa_id, $existingItem?->nilai_mid);
                                $final = old('nilai_final.'.$row->mahasiswa_id, $existingItem?->nilai_final);
                                $angka = old('nilai_angka.'.$row->mahasiswa_id, $existingItem?->nilai_angka);
                                $huruf = old('nilai_huruf.'.$row->mahasiswa_id, $existingItem?->nilai_huruf);
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $mhs?->nama_lengkap }}</div>
                                    <div class="text-xs text-emerald-100/60">{{ $mhs?->npm }}</div>
                                    @if (! $isReady)
                                        <div class="text-xs text-red-200/90 mt-1">KHS belum disiapkan Admin.</div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-komponen="tm"
                                           name="nilai_tm[{{ $row->mahasiswa_id }}]"
                                           value="{{ $tm }}"
                                           class="w-24 h-10 mx-auto rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-center"
                                           @disabled(! $isReady) />
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-komponen="quis"
                                           name="nilai_quis[{{ $row->mahasiswa_id }}]"
                                           value="{{ $quis }}"
                                           class="w-24 h-10 mx-auto rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-center"
                                           @disabled(! $isReady) />
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-komponen="mid"
                                           name="nilai_mid[{{ $row->mahasiswa_id }}]"
                                           value="{{ $mid }}"
                                           class="w-24 h-10 mx-auto rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-center"
                                           @disabled(! $isReady) />
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <input type="number" step="0.01" min="0" max="100"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-komponen="final"
                                           name="nilai_final[{{ $row->mahasiswa_id }}]"
                                           value="{{ $final }}"
                                           class="w-24 h-10 mx-auto rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-center"
                                           @disabled(! $isReady) />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input id="nilaiAngka{{ $row->mahasiswa_id }}"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-hasil="angka"
                                           value="{{ $angka !== null ? number_format((float) $angka, 2) : '' }}"
                                           class="w-28 h-10 mx-auto rounded-xl bg-white/5 border border-white/10 text-center font-semibold text-emerald-100"
                                           readonly />
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input id="nilaiHuruf{{ $row->mahasiswa_id }}"
                                           data-row-id="{{ $row->mahasiswa_id }}"
                                           data-hasil="huruf"
                                           value="{{ $huruf }}"
                                           class="w-20 h-10 mx-auto rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-center font-bold text-emerald-100"
                                           readonly />
                                </td>
                            </tr>
                        @endforeach
                        @if ($krs->count() === 0)
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-emerald-100/70">Tidak ada mahasiswa pada mata kuliah ini (KRS approved).</td>
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
            const BOBOT = { tm: 0.50, quis: 0.20, mid: 0.15, final: 0.15 };

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

            function formatAngka(nilai) {
                if (nilai === null || nilai === undefined || Number.isNaN(nilai)) return '';
                return Number(nilai).toFixed(2);
            }

            function hitungRow(rowId) {
                const inputs = document.querySelectorAll(`input[data-row-id="${rowId}"][data-komponen]`);
                let adaIsi = false;
                const values = { tm: 0, quis: 0, mid: 0, final: 0 };

                inputs.forEach((inp) => {
                    const key = inp.getAttribute('data-komponen');
                    const raw = inp.value;
                    if (raw === '' || raw === null || raw === undefined) return;
                    const num = parseFloat(raw);
                    if (Number.isNaN(num)) return;
                    values[key] = num;
                    adaIsi = true;
                });

                if (!adaIsi) {
                    const angkaEl = document.querySelector(`input[data-row-id="${rowId}"][data-hasil="angka"]`);
                    const hurufEl = document.querySelector(`input[data-row-id="${rowId}"][data-hasil="huruf"]`);
                    if (angkaEl) angkaEl.value = '';
                    if (hurufEl) hurufEl.value = '';
                    return;
                }

                const total = (values.tm * BOBOT.tm) + (values.quis * BOBOT.quis) + (values.mid * BOBOT.mid) + (values.final * BOBOT.final);
                const totalBulat = Math.round(total * 100) / 100;

                const angkaEl = document.querySelector(`input[data-row-id="${rowId}"][data-hasil="angka"]`);
                const hurufEl = document.querySelector(`input[data-row-id="${rowId}"][data-hasil="huruf"]`);
                if (angkaEl) angkaEl.value = formatAngka(totalBulat);
                if (hurufEl) hurufEl.value = hurufFromAngka(totalBulat);
            }

            const komponenInputs = document.querySelectorAll('input[data-komponen]');
            const rowIds = new Set();
            komponenInputs.forEach((inp) => {
                const rowId = inp.getAttribute('data-row-id');
                if (rowId) rowIds.add(rowId);
                inp.addEventListener('input', () => {
                    const r = inp.getAttribute('data-row-id');
                    if (r) hitungRow(r);
                });
            });

            rowIds.forEach((id) => hitungRow(id));
        })();
    </script>
</x-portal-layout>
