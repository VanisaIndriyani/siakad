<x-portal-layout :title="'Detail Arsip Batch - '.config('app.name')" subtitle="Backup Nilai Per Batch Reset">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    @php
        $restoreConfirm = 'RESTORE-BATCH-'.$batchCode;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.nilai-monitoring.arsip') }}" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 inline-flex items-center justify-center transition" title="Kembali ke Arsip Nilai">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="text-xl font-semibold">Detail Arsip Batch</div>
                    <div class="text-sm text-emerald-100/70">Semua nilai yang di-backup pada batch reset berikut.</div>
                </div>
            </div>
        </div>
        <button type="button" @click="$dispatch('open-modal', { id: 'restore-batch-modal' })"
                class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-400/30 transition text-emerald-100 shadow-[0_0_20px_-12px_rgba(16,185,129,0.5)]">
            <i class="fa-solid fa-rotate-left"></i>
            <span class="text-sm font-semibold">Restore Seluruh Batch</span>
        </button>
    </div>

    <div x-data="{
            showModal: false,
            confirmInput: '',
            expected: @js($restoreConfirm),
            get isValid() { return this.confirmInput.trim().toUpperCase() === this.expected; }
        }"
         x-on:open-modal.window="if ($event.detail.id === 'restore-batch-modal') showModal = true"
         x-show="showModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl border border-emerald-500/25 bg-[#0a1f1a] shadow-[0_0_80px_-15px_rgba(16,185,129,0.45)] overflow-hidden"
             x-show="showModal" x-transition:enter="scale-95 opacity-0" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            <div class="px-6 py-5 border-b border-white/10">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 inline-flex items-center justify-center shadow-[0_0_25px_-5px_rgba(16,185,129,0.6)]">
                        <i class="fa-solid fa-rotate-left text-emerald-300 text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-lg font-bold text-emerald-100 tracking-tight">Restore Semua Nilai Batch Ini</div>
                        <div class="mt-1 text-xs text-emerald-100/70 leading-relaxed">
                            Semua nilai pada batch <span class="font-mono font-bold text-amber-300">{{ $batchCode }}</span> akan dikembalikan ke tabel KHS Items.
                            <span class="font-semibold text-emerald-200">Nilai yang sudah ada di KHS akan di-overwrite sesuai data arsip ini.</span>
                            IPS/IPK mahasiswa otomatis dihitung ulang setelah restore.
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.nilai-monitoring.arsip-restore-batch', ['batch' => $batchCode]) }}" class="px-6 py-5 space-y-4">
                @csrf
                @error('restore')
                    <div class="text-xs px-3 py-2 rounded-lg bg-rose-500/10 border border-rose-500/25 text-rose-200 font-medium">{{ $message }}</div>
                @enderror
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-emerald-100/80">Konfirmasi Keamanan</label>
                    <div class="text-xs text-emerald-100/70">Ketik teks berikut di kolom bawah untuk konfirmasi restore (case-insensitive):</div>
                    <div class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-mono font-bold text-emerald-300 tracking-wider select-all">
                        {{ $restoreConfirm }}
                    </div>
                    <input type="text" x-model="confirmInput" name="confirm_restore"
                           placeholder="Ketik konfirmasi restore..."
                           class="w-full h-11 px-3 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm tracking-wide"
                           autocomplete="off" />
                </div>
                <div class="flex items-center justify-between gap-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="h-11 px-5 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" :disabled="!isValid"
                            :class="isValid
                                ? 'bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 shadow-[0_0_25px_-5px_rgba(16,185,129,0.7)] hover:shadow-[0_0_32px_-2px_rgba(16,185,129,0.85)] border-emerald-400/30'
                                : 'bg-white/5 border-white/10 cursor-not-allowed text-white/40'"
                            class="h-11 px-6 inline-flex items-center justify-center gap-2 rounded-xl border transition text-sm font-bold text-white">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Restore Batch</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Batch Code</div>
            <div class="mt-1 font-mono text-[11px] font-bold text-amber-300 break-all">{{ $batchInfo->batch_code }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Semester</div>
            <div class="mt-1 text-xl font-bold text-emerald-100">{{ (int) $batchInfo->semester }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Waktu Reset</div>
            <div class="mt-1 text-sm font-semibold text-emerald-100">{{ \Carbon\Carbon::parse($batchInfo->reset_at)->format('d M Y H:i:s') }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Total Mahasiswa</div>
            <div class="mt-1 text-xl font-bold text-blue-200">{{ (int) $batchInfo->total_mahasiswa }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Total Mata Kuliah</div>
            <div class="mt-1 text-xl font-bold text-blue-200">{{ (int) $batchInfo->total_mk }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Total Record Nilai</div>
            <div class="mt-1 text-xl font-bold text-amber-200">{{ (int) $batchInfo->total_records }}</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-amber-400/20 bg-amber-500/5 p-4 flex items-start gap-3 shadow-[inset_0_0_30px_-18px_rgba(245,158,11,0.45)]">
        <div class="shrink-0 w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-400/30 inline-flex items-center justify-center">
            <i class="fa-solid fa-circle-info text-amber-300"></i>
        </div>
        <div class="text-xs text-amber-100/90 leading-relaxed">
            <div class="font-bold text-amber-200 mb-0.5">Info Operator Reset</div>
            Reset dilakukan oleh <span class="font-semibold">{{ $batchInfo->resetBy?->name ?? 'Tidak diketahui' }}</span>, Tahun Ajaran saat reset: <span class="font-mono font-semibold">{{ $batchInfo->tahun_ajaran }}</span>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4">
        <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div class="space-y-1">
                <label class="block text-[11px] font-semibold uppercase tracking-wider text-emerald-100/60">Cari Cepat Mahasiswa</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-xs text-emerald-100/40"></i>
                    <input type="text" name="mhs_q" value="{{ request('mhs_q') }}" placeholder="Cari nama / NPM (contoh: 0229250002 / Herliana)"
                           class="w-full h-11 pl-9 pr-3 rounded-xl bg-[#0a1f1a] border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-400/30 transition text-sm font-semibold text-emerald-100">
                    <i class="fa-solid fa-filter"></i> Cari
                </button>
                @if(request('mhs_q'))
                    <a href="{{ url()->current() }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">Reset</a>
                @endif
            </div>
            <div class="text-xs text-emerald-100/60 md:text-right">
                Total mahasiswa ditampilkan: <span class="font-bold text-emerald-200">{{ $recordsGrouped->count() }}</span>
            </div>
        </form>
    </div>

    <div class="mt-5 space-y-5">
        @forelse ($recordsGrouped as $mhsId => $items)
            @php
                $firstItem = $items->first();
                $mhs = $firstItem?->mahasiswa;
                $ipsSnapshot = $firstItem?->ips_saat_reset;
                $ipkSnapshot = $firstItem?->ipk_saat_reset;
                $mhsRestConfirm = $mhs ? 'RESTORE-MHS-'.$mhs->npm.'-BATCH-'.$batchCode : 'RESTORE-MHS-'.$mhsId.'-BATCH-'.$batchCode;
                $mhsModalId = 'restore-mhs-modal-'.$mhsId;
            @endphp
            <div x-data="{
                    showModal{{ $mhsId }}: false,
                    confirmInput{{ $mhsId }}: '',
                    expected{{ $mhsId }}: @js($mhsRestConfirm),
                    get isValid() { return this['confirmInput{{ $mhsId }}'].trim().toUpperCase() === this['expected{{ $mhsId }}']; }
                }"
                 x-on:open-modal.window="if ($event.detail.id === 'restore-mhs-modal-{{ $mhsId }}') this['showModal{{ $mhsId }}'] = true"
                 x-show="showModal{{ $mhsId }}" x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal{{ $mhsId }} = false"></div>
                <div class="relative w-full max-w-lg rounded-2xl border border-emerald-500/25 bg-[#0a1f1a] shadow-2xl overflow-hidden"
                     x-show="showModal{{ $mhsId }}" x-transition:enter="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                    <div class="px-6 py-5 border-b border-white/10">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 inline-flex items-center justify-center">
                                <i class="fa-solid fa-user-rotate text-emerald-300 text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-lg font-bold text-emerald-100">Restore Hanya Mahasiswa Ini</div>
                                <div class="mt-1 text-xs text-emerald-100/70 leading-relaxed">
                                    Hanya merestor nilai milik <span class="font-bold text-emerald-200">{{ $mhs?->nama_lengkap ?? 'Mahasiswa #'.$mhsId }}</span>
                                    @if($mhs) <span class="font-mono text-amber-300">(NPM {{ $mhs->npm }})</span> @endif
                                    pada batch <span class="font-mono text-amber-300">{{ $batchCode }}</span>.
                                    Mahasiswa LAINNYA TIDAK TERDAMPAK sama sekali. IPS/IPK mahasiswa ini otomatis dihitung ulang.
                                </div>
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.nilai-monitoring.arsip-restore-mahasiswa', ['batch' => $batchCode, 'mahasiswa' => $mhsId]) }}" class="px-6 py-5 space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-emerald-100/80">Konfirmasi Keamanan</label>
                            <div class="text-xs text-emerald-100/70">Ketik teks berikut (case-insensitive):</div>
                            <div class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-mono font-bold text-emerald-300 break-all select-all">
                                {{ $mhsRestConfirm }}
                            </div>
                            <input type="text" x-model="confirmInput{{ $mhsId }}" name="confirm"
                                   placeholder="Ketik konfirmasi restore mahasiswa..."
                                   class="w-full h-11 px-3 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm"
                                   autocomplete="off" />
                        </div>
                        <div class="flex items-center justify-between gap-3 pt-2">
                            <button type="button" @click="showModal{{ $mhsId }} = false"
                                    class="h-11 px-5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-sm font-medium">
                                Batal
                            </button>
                            <button type="submit" :disabled="!isValid"
                                    :class="isValid
                                        ? 'bg-emerald-500 hover:bg-emerald-400 shadow-[0_0_25px_-5px_rgba(16,185,129,0.7)] border-emerald-400/30'
                                        : 'bg-white/5 border-white/10 cursor-not-allowed text-white/40'"
                                    class="h-11 px-6 inline-flex items-center gap-2 rounded-xl border transition text-sm font-bold text-white">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                                <span>Restore Mahasiswa Ini</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-[#0b231c] shadow-2xl overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500/10 via-transparent to-transparent px-5 py-4 border-b border-white/10">
                    <div class="flex flex-wrap items-center gap-4 justify-between">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="shrink-0 w-11 h-11 rounded-2xl bg-emerald-500/15 border border-emerald-400/30 inline-flex items-center justify-center text-emerald-300 font-bold">
                                @if($mhs)
                                    {{ strtoupper(substr($mhs->nama_lengkap ?? 'M', 0, 1)) }}
                                @else
                                    ?
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-emerald-100 text-base whitespace-nowrap">
                                    {{ $mhs?->nama_lengkap ?? 'Mahasiswa #'.$mhsId }}
                                </div>
                                @if($mhs)
                                    <div class="text-xs text-emerald-100/70 font-mono mt-0.5">
                                        NPM {{ $mhs->npm }} · {{ $mhs->program_studi }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="$dispatch('open-modal', { id: 'restore-mhs-modal-{{ $mhsId }}' })"
                                    class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-400/30 transition text-emerald-100 text-xs font-semibold shadow-[0_0_18px_-10px_rgba(16,185,129,0.55)]">
                                <i class="fa-solid fa-user-rotate"></i>
                                Restore Hanya Mahasiswa Ini
                            </button>
                            <div class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-400/25">
                                <div class="text-[10px] uppercase tracking-wider font-semibold text-blue-200/80">Total MK</div>
                                <div class="text-sm font-bold text-blue-100">{{ $items->count() }} matkul</div>
                            </div>
                            <div class="px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-400/25">
                                <div class="text-[10px] uppercase tracking-wider font-semibold text-amber-200/80">IPS Saat Reset</div>
                                <div class="text-sm font-bold text-amber-100 font-mono">{{ $ipsSnapshot !== null ? number_format($ipsSnapshot, 2) : '-' }}</div>
                            </div>
                            <div class="px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-400/25">
                                <div class="text-[10px] uppercase tracking-wider font-semibold text-emerald-200/80">IPK Saat Reset</div>
                                <div class="text-sm font-bold text-emerald-100 font-mono">{{ $ipkSnapshot !== null ? number_format($ipkSnapshot, 2) : '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-4 py-2.5 whitespace-nowrap min-w-[250px]">Mata Kuliah</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">SKS</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">TM</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Quis</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Mid</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Final</th>
                                <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Nilai Angka</th>
                                <th class="text-center font-medium px-4 py-2.5 whitespace-nowrap">Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($items as $row)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        @if ($row->mataKuliah)
                                            <div class="font-semibold text-emerald-100 whitespace-nowrap">{{ $row->mataKuliah->nama }}</div>
                                            <div class="text-[11px] text-emerald-100/60 font-mono">{{ $row->mataKuliah->kode }}</div>
                                        @else
                                            <span class="text-rose-300">MK #{{ (int) $row->mata_kuliah_id }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-3 text-center font-semibold">{{ $row->mataKuliah?->sks ?? '-' }}</td>
                                    <td class="px-2 py-3 text-center font-mono text-blue-100">{{ $row->nilai_tm !== null ? number_format($row->nilai_tm, 2) : '-' }}</td>
                                    <td class="px-2 py-3 text-center font-mono text-blue-100">{{ $row->nilai_quis !== null ? number_format($row->nilai_quis, 2) : '-' }}</td>
                                    <td class="px-2 py-3 text-center font-mono text-amber-100">{{ $row->nilai_mid !== null ? number_format($row->nilai_mid, 2) : '-' }}</td>
                                    <td class="px-2 py-3 text-center font-mono text-amber-100">{{ $row->nilai_final !== null ? number_format($row->nilai_final, 2) : '-' }}</td>
                                    <td class="px-2 py-3 text-center font-mono font-bold text-emerald-200">{{ $row->nilai_angka !== null ? number_format($row->nilai_angka, 2) : '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($row->nilai_huruf)
                                            <span class="inline-flex items-center justify-center w-10 h-7 rounded-md bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 font-bold">
                                                {{ $row->nilai_huruf }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-white/10 bg-[#0b231c] px-6 py-12 text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/5 border border-white/10 mb-4">
                    <i class="fa-solid fa-box-archive text-2xl text-emerald-100/30"></i>
                </div>
                <div class="font-medium mb-1 text-emerald-100/80">Tidak ada data di batch ini</div>
                <div class="text-xs text-emerald-100/50">Batch {{ $batchCode }} tidak memiliki record nilai.</div>
            </div>
        @endforelse
    </div>
</x-portal-layout>
