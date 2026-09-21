<x-portal-layout :title="'Input Nilai Dosen - '.config('app.name')" subtitle="Monitoring Nilai">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    @php
        $statusLabel = function (string $status) use ($statusOptions) {
            return $statusOptions[$status] ?? $status;
        };
        $statusBadge = function (string $status) {
            return match ($status) {
                'SUDAH_LENGKAP' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                'BELUM_LENGKAP' => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                'BELUM_SIAP' => 'bg-rose-500/15 border-rose-500/20 text-rose-100',
                default => 'bg-zinc-500/15 border-zinc-500/20 text-zinc-100',
            };
        };
        $confirmText = 'RESET-SEMESTER-'.$semester;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Input Nilai Dosen</div>
            <div class="text-sm text-emerald-100/70">Monitoring status penginputan nilai per mata kuliah dan semester.</div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.nilai-monitoring.pdf', array_filter(['q' => $q ?: null, 'semester' => $semester ?: null, 'status' => $status ?: null, 'all' => $showAll ? 1 : null, 'page' => ! $showAll ? request()->get('page') : null])) }}"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">PDF</span>
            </a>
            <button type="button" @click="$dispatch('open-modal', { id: 'reset-nilai-modal' })"
                    class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 border border-rose-500/25 transition text-rose-100">
                <i class="fa-solid fa-arrow-rotate-left"></i>
                <span class="text-sm font-medium">Reset Nilai Semester</span>
            </button>
        </div>
    </div>

    <div x-data="{
            showModal: false,
            confirmInput: '',
            expected: @js($confirmText),
            get isValid() { return this.confirmInput.trim().toUpperCase() === this.expected; }
        }"
         x-on:open-modal.window="if ($event.detail.id === 'reset-nilai-modal') showModal = true"
         x-show="showModal"
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative w-full max-w-lg rounded-2xl border border-rose-500/25 bg-[#0a1f1a] shadow-[0_0_80px_-15px_rgba(244,63,94,0.45)] overflow-hidden"
             x-show="showModal" x-transition:enter="scale-95 opacity-0" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
            <div class="px-6 py-5 border-b border-white/10">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 w-12 h-12 rounded-2xl bg-rose-500/20 border border-rose-500/30 inline-flex items-center justify-center shadow-[0_0_25px_-5px_rgba(244,63,94,0.6)]">
                        <i class="fa-solid fa-triangle-exclamation text-rose-300 text-lg"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-lg font-bold text-rose-100 tracking-tight">Reset Nilai Semester {{ $semester }}</div>
                        <div class="mt-1 text-xs text-emerald-100/70 leading-relaxed">
                            Semua nilai (TM, Quis, Mid, Final, Angka, Huruf, IPS) untuk semester ini akan dikosongkan.
                            <span class="font-semibold text-emerald-200">Nilai lama TIDAK dihapus</span> — otomatis di-archive dengan kode batch unik dan bisa di-restore.
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.nilai-monitoring.reset-semester') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="semester" value="{{ $semester }}" />
                @error('reset')
                    <div class="text-xs px-3 py-2 rounded-lg bg-rose-500/10 border border-rose-500/25 text-rose-200 font-medium">{{ $message }}</div>
                @enderror
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-emerald-100/80">Konfirmasi Keamanan</label>
                    <div class="text-xs text-emerald-100/70">Ketik teks berikut di kolom bawah untuk konfirmasi:</div>
                    <div class="px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs font-mono font-bold text-amber-300 tracking-wider select-all">
                        {{ $confirmText }}
                    </div>
                    <input type="text" x-model="confirmInput" name="confirm_reset"
                           placeholder="Ketik konfirmasi disini..."
                           class="w-full h-11 px-3 rounded-xl bg-white/5 border border-white/10 focus:border-rose-400 focus:ring-rose-400 text-sm tracking-wide"
                           autocomplete="off" />
                </div>
                <div class="flex items-center justify-between gap-3 pt-2">
                    <button type="button" @click="showModal = false"
                            class="h-11 px-5 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" :disabled="!isValid"
                            :class="isValid
                                ? 'bg-rose-500 hover:bg-rose-400 active:bg-rose-600 shadow-[0_0_25px_-5px_rgba(244,63,94,0.7)] hover:shadow-[0_0_32px_-2px_rgba(244,63,94,0.85)] border-rose-400/30'
                                : 'bg-white/5 border-white/10 cursor-not-allowed text-white/40'"
                            class="h-11 px-6 inline-flex items-center justify-center gap-2 rounded-xl border transition text-sm font-bold text-white">
                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                        <span>Reset Semester {{ $semester }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($archiveBatches->count() > 0)
        <div class="mt-5 rounded-2xl border border-amber-400/20 bg-amber-500/5 p-5 shadow-[inset_0_0_30px_-18px_rgba(245,158,11,0.45)]">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-400/25 inline-flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-amber-300"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-amber-100">Riwayat Reset Nilai Terakhir</div>
                        <div class="mt-0.5 text-xs text-emerald-100/70">Setiap reset otomatis membuat backup nilai agar bisa diakses kapan saja.</div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-xl border border-white/10">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-3 py-2">Waktu Reset</th>
                                <th class="text-left font-medium px-3 py-2">Batch Code</th>
                                <th class="text-left font-medium px-3 py-2">Semester</th>
                                <th class="text-left font-medium px-3 py-2">Reset Oleh</th>
                                <th class="text-right font-medium px-3 py-2">Jumlah Data</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($archiveBatches as $b)
                                <tr class="hover:bg-white/5">
                                    <td class="px-3 py-2 text-emerald-100/80 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($b->reset_at)->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="px-3 py-2 font-mono text-[11px] text-amber-300 whitespace-nowrap">{{ $b->batch_code }}</td>
                                    <td class="px-3 py-2 font-semibold whitespace-nowrap">Semester {{ $b->semester }}</td>
                                    <td class="px-3 py-2 text-emerald-100/80 whitespace-nowrap">{{ $b->resetBy?->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">{{ (int) $b->total_records }} nilai</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Total (Halaman)</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['total'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Sudah Lengkap</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['sudah_lengkap'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Belum Lengkap</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['belum_lengkap'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Belum Disiapkan</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['belum_siap'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Tidak Ada Peserta</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['tidak_ada_peserta'] }}</div>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode / mata kuliah / dosen..." />
        <input type="number" name="semester" min="1" max="8" value="{{ $semester }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Semester" />
        <select name="status" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="" @selected($status === '') style="background-color: #0d2a23; color: #fff;">Semua Status</option>
            @foreach ($statusOptions as $k => $v)
                <option value="{{ $k }}" @selected($status === $k) style="background-color: #0d2a23; color: #fff;">{{ $v }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 h-11 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Filter</button>
            <a href="{{ route('admin.nilai-monitoring.index', array_filter(['semester' => $semester ?: null])) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
            @if (! $showAll)
                <a href="{{ route('admin.nilai-monitoring.index', array_filter(['q' => $q ?: null, 'semester' => $semester ?: null, 'status' => $status ?: null, 'all' => 1])) }}"
                   class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100">Buka Semua</a>
            @else
                <a href="{{ route('admin.nilai-monitoring.index', array_filter(['q' => $q ?: null, 'semester' => $semester ?: null, 'status' => $status ?: null])) }}"
                   class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Tutup Semua</a>
            @endif
        </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Dosen</th>
                        <th class="text-left font-medium px-4 py-3 w-32">Peserta</th>
                        <th class="text-left font-medium px-4 py-3 w-40">Nilai Terisi</th>
                        <th class="text-left font-medium px-4 py-3 w-40">Status</th>
                        <th class="text-left font-medium px-4 py-3 w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($rows as $i => $row)
                        @php
                            $peserta = (int) ($row->peserta_approved ?? 0);
                            $terisi = (int) ($row->nilai_terisi ?? 0);
                            $progress = $peserta > 0 ? $terisi.' / '.$peserta : '-';
                            $no = $showAll ? ($i + 1) : ($rows->firstItem() + $i);
                            $detailPdfUrl = route('admin.nilai-monitoring.detail.pdf', [$row->id, $semester]);
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $no }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->kode }} - {{ $row->nama }}</div>
                                <div class="text-xs text-emerald-100/60">Semester {{ $semester }}{{ $row->jurusan ? ' • '.$row->jurusan : '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div>{{ $row->dosen_1 ?: '-' }}</div>
                                @if ($row->dosen_2)
                                    <div class="text-xs text-emerald-100/60 mt-1">{{ $row->dosen_2 }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $peserta }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $progress }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusBadge($row->status_input) }}">
                                    {{ $statusLabel($row->status_input) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ $detailPdfUrl }}"
                                   class="h-9 px-3 inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100"
                                   title="Download PDF Nilai">
                                    <i class="fa-solid fa-file-pdf text-xs"></i>
                                    <span class="text-xs font-medium">PDF Nilai</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-emerald-100/70">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (! $showAll)
        <div class="mt-4">
            {{ $rows->links() }}
        </div>
    @else
        <div class="mt-4 text-sm text-emerald-100/70">
            Menampilkan semua {{ $rows->count() }} data mata kuliah.
        </div>
    @endif
</x-portal-layout>

