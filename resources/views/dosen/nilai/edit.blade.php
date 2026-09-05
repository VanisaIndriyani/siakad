<x-portal-layout :title="'Input Nilai - '.config('app.name')" subtitle="Input Nilai">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Input Nilai</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }} • Semester {{ $semester }}</div>
            <div class="text-xs text-emerald-100/60 mt-1">
                Bobot Nilai: Tatap Muka (50%) • Tugas (20%) • MID (10%) • Final (20%)
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
                            <th class="text-center font-medium px-3 py-3 w-12">
                                <label for="selectAllReset" class="cursor-pointer inline-flex items-center justify-center w-full h-full">
                                    <input id="selectAllReset"
                                           type="checkbox"
                                           title="Pilih semua mahasiswa untuk hapus nilai"
                                           class="w-4 h-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-400 pointer-events-auto relative z-20" />
                                </label>
                            </th>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-center font-medium px-3 py-3">TM (50%)</th>
                            <th class="text-center font-medium px-3 py-3">Tugas (20%)</th>
                            <th class="text-center font-medium px-3 py-3">MID (10%)</th>
                            <th class="text-center font-medium px-3 py-3">Final (20%)</th>
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
                                $hasAnyNilai = $isReady && (
                                    $existingItem->nilai_tm !== null || $existingItem->nilai_quis !== null
                                    || $existingItem->nilai_mid !== null || $existingItem->nilai_final !== null
                                    || $existingItem->nilai_angka !== null || $existingItem->nilai_huruf !== null
                                );
                                $tm = old('nilai_tm.'.$row->mahasiswa_id, $existingItem?->nilai_tm);
                                $quis = old('nilai_quis.'.$row->mahasiswa_id, $existingItem?->nilai_quis);
                                $mid = old('nilai_mid.'.$row->mahasiswa_id, $existingItem?->nilai_mid);
                                $final = old('nilai_final.'.$row->mahasiswa_id, $existingItem?->nilai_final);
                                $angka = old('nilai_angka.'.$row->mahasiswa_id, $existingItem?->nilai_angka);
                                $huruf = old('nilai_huruf.'.$row->mahasiswa_id, $existingItem?->nilai_huruf);
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-3 py-3 text-center align-middle">
                                    <input type="checkbox"
                                           id="resetCheck{{ (int) $row->mahasiswa_id }}"
                                           form="bulkResetForm"
                                           name="reset_ids[]"
                                           value="{{ (int) $row->mahasiswa_id }}"
                                           class="row-reset-check w-4 h-4 rounded border-white/20 bg-white/5 text-red-500 focus:ring-red-400 align-middle pointer-events-auto relative z-20 cursor-pointer"
                                           title="{{ $hasAnyNilai ? 'Pilih untuk hapus / reset nilai mahasiswa ini' : 'Belum ada nilai untuk dihapus. Anda tetap bisa memilih; sistem akan otomatis melewati jika nilainya kosong.' }}" />
                                </td>
                                <td class="px-4 py-3">
                                    <label for="resetCheck{{ (int) $row->mahasiswa_id }}" class="cursor-pointer block">
                                        <div class="font-medium">{{ $mhs?->nama_lengkap }}</div>
                                        <div class="text-xs text-emerald-100/60">{{ $mhs?->npm }}</div>
                                    </label>
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
                                <td colspan="8" class="px-4 py-10 text-center text-emerald-100/70">Tidak ada mahasiswa pada mata kuliah ini (KRS approved).</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="text-xs text-emerald-100/60">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Centang kolom kiri untuk reset/hapus nilai mahasiswa (TM, Tugas, MID, Final, Total, Mutu menjadi kosong), kemudian klik tombol merah di bawah.
            </div>
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Nilai
            </button>
        </div>
    </form>

    {{-- ===== FORM BULK RESET / HAPUS NILAI (terpisah dari form simpan update) ===== --}}
    <form id="bulkResetForm"
          method="POST"
          action="{{ route('dosen.nilai.bulk-reset', [$mataKuliah, $semester]) }}"
          onsubmit="return confirmResetSelected(this);"
          class="mt-6 rounded-2xl border border-red-500/30 bg-red-500/5 p-5">
        @csrf
        @method('DELETE')

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="font-semibold text-red-100 flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    Hapus / Reset Nilai Terpilih
                </div>
                <div class="text-xs text-red-200/70 mt-1">
                    Total mahasiswa tercentang: <span id="resetSelectedCount" class="font-semibold text-red-100">0</span> orang.
                    Nilai yang dihapus: TM, Tugas, MID, Final, Total Angka, dan Nilai Mutu.
                </div>
            </div>
            <button id="btnResetSubmit"
                    type="submit"
                    disabled
                    class="h-11 px-5 rounded-xl bg-red-600/90 hover:bg-red-500 active:bg-red-700 disabled:bg-red-900/40 disabled:text-red-200/50 disabled:cursor-not-allowed transition font-medium text-white">
                <i class="fa-solid fa-trash-can mr-1"></i>
                Hapus Nilai Terpilih
            </button>
        </div>
    </form>

    <div class="mt-4">
        {{ $krs->links() }}
    </div>

    <script>
        (function () {
            const BOBOT = { tm: 0.50, quis: 0.20, mid: 0.10, final: 0.20 };

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

            /* ===== BULK RESET / HAPUS NILAI CHECKBOX LOGIC ===== */
            const selectAll = document.getElementById('selectAllReset');
            const rowChecks = Array.prototype.slice.call(document.querySelectorAll('input.row-reset-check'));
            const countEl = document.getElementById('resetSelectedCount');
            const btnReset = document.getElementById('btnResetSubmit');

            function updateResetUi() {
                const enabled = rowChecks.filter((c) => !c.disabled);
                const checkedN = rowChecks.filter((c) => c.checked).length;
                if (countEl) countEl.textContent = String(checkedN);
                if (btnReset) btnReset.disabled = checkedN === 0;
                if (selectAll) {
                    if (enabled.length === 0) {
                        selectAll.checked = false;
                        selectAll.indeterminate = false;
                    } else if (checkedN === 0) {
                        selectAll.checked = false;
                        selectAll.indeterminate = false;
                    } else if (checkedN === enabled.length) {
                        selectAll.checked = true;
                        selectAll.indeterminate = false;
                    } else {
                        selectAll.checked = false;
                        selectAll.indeterminate = true;
                    }
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    const want = !!this.checked;
                    rowChecks.forEach((c) => {
                        if (!c.disabled) c.checked = want;
                    });
                    updateResetUi();
                });
            }
            rowChecks.forEach((c) => c.addEventListener('change', updateResetUi));
            updateResetUi();

            window.confirmResetSelected = function (formEl) {
                const checkedN = rowChecks.filter((c) => c.checked).length;
                if (checkedN === 0) {
                    alert('Belum ada mahasiswa yang dipilih. Silakan centang kolom kiri terlebih dahulu.');
                    return false;
                }
                const msg = 'Anda yakin akan MENGHAPUS / MERESSET NILAI untuk ' + checkedN + ' mahasiswa yang dipilih?\n\nSemua nilai (TM, Tugas, MID, Final, Total Angka, Nilai Mutu) akan menjadi KOSONG, dan IPS/IPK akan dihitung ulang.\n\nTindakan ini tidak dapat dibatalkan.';
                return confirm(msg);
            };
        })();
    </script>
</x-portal-layout>
