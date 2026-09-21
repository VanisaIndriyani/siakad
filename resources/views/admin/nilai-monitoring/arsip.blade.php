<x-portal-layout :title="'Arsip Nilai - '.config('app.name')" subtitle="Backup Nilai Setelah Reset">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.nilai-monitoring.index') }}" class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 inline-flex items-center justify-center transition" title="Kembali ke Input Nilai Dosen">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <div class="text-xl font-semibold">Arsip Nilai (Backup)</div>
                    <div class="text-sm text-emerald-100/70">Semua nilai yang pernah di-reset — tersimpan AMAN dan bisa direstore kapan saja.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-amber-400/20 bg-gradient-to-br from-amber-500/10 to-transparent p-5 shadow-[0_0_30px_-18px_rgba(245,158,11,0.6)]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-500/15 border border-amber-400/30 inline-flex items-center justify-center">
                    <i class="fa-solid fa-database text-amber-300"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wider text-amber-200/80">Total Arsip</div>
                    <div class="mt-1 text-2xl font-bold text-amber-100">{{ number_format($totalArsip, 0, ',', '.') }} <span class="text-xs font-medium text-amber-200/70">record</span></div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-blue-400/20 bg-gradient-to-br from-blue-500/10 to-transparent p-5 shadow-[0_0_30px_-18px_rgba(59,130,246,0.6)]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-500/15 border border-blue-400/30 inline-flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-blue-300"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wider text-blue-200/80">Total Batch Reset</div>
                    <div class="mt-1 text-2xl font-bold text-blue-100">{{ number_format($totalBatch, 0, ',', '.') }} <span class="text-xs font-medium text-blue-200/70">aksi</span></div>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-emerald-400/20 bg-gradient-to-br from-emerald-500/10 to-transparent p-5 shadow-[0_0_30px_-18px_rgba(16,185,129,0.6)]">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/15 border border-emerald-400/30 inline-flex items-center justify-center">
                    <i class="fa-solid fa-rotate-left text-emerald-300"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-semibold uppercase tracking-wider text-emerald-200/80">Bisa Direstore</div>
                    <div class="mt-1 text-2xl font-bold text-emerald-100">100% <span class="text-xs font-medium text-emerald-200/70">aman</span></div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-white/10 bg-[#0b231c] p-4 shadow-2xl">
        <form method="GET" action="{{ route('admin.nilai-monitoring.arsip') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-4 space-y-1.5">
                <label class="block text-xs font-semibold text-emerald-100/80">Cari (Mahasiswa / MK / Batch Code)</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-emerald-100/40 text-sm"></i>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Nama / NPM / Kode MK / Batch..." class="w-full h-10 pl-10 pr-3 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm"/>
                </div>
            </div>
            <div class="md:col-span-2 space-y-1.5">
                <label class="block text-xs font-semibold text-emerald-100/80">Semester</label>
                <select name="semester" class="w-full h-10 px-3 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm">
                    <option value="">Semua</option>
                    @foreach($semesterOptions as $s)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 space-y-1.5">
                <label class="block text-xs font-semibold text-emerald-100/80">Batch Code</label>
                <input type="text" name="batch" value="{{ $batchCode }}" placeholder="Contoh: RESET-SMT8-..." class="w-full h-10 px-3 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm font-mono"/>
            </div>
            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="h-10 flex-1 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-semibold inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('admin.nilai-monitoring.arsip') }}" class="h-10 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm inline-flex items-center justify-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if ($batchGroups->count() > 0)
        <div class="mt-5 rounded-2xl border border-amber-400/20 bg-amber-500/5 p-5 shadow-[inset_0_0_30px_-18px_rgba(245,158,11,0.45)]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-400/25 inline-flex items-center justify-center">
                        <i class="fa-solid fa-clock-rotate-left text-amber-300"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-amber-100">Batch Reset Terakhir (10 aksi)</div>
                        <div class="mt-0.5 text-xs text-emerald-100/70">Klik "Lihat Detail" untuk melihat semua nilai pada batch tersebut, atau lakukan restore.</div>
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
                                <th class="text-right font-medium px-3 py-2">Jumlah Mahasiswa</th>
                                <th class="text-right font-medium px-3 py-2">Jumlah Nilai</th>
                                <th class="text-center font-medium px-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($batchGroups as $b)
                                <tr class="hover:bg-white/5">
                                    <td class="px-3 py-2 text-emerald-100/80 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($b->reset_at)->format('d M Y H:i:s') }}
                                    </td>
                                    <td class="px-3 py-2 font-mono text-[11px] text-amber-300 whitespace-nowrap">{{ $b->batch_code }}</td>
                                    <td class="px-3 py-2 font-semibold whitespace-nowrap">Semester {{ $b->semester }}</td>
                                    <td class="px-3 py-2 text-emerald-100/80 whitespace-nowrap">{{ $b->resetBy?->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">{{ (int) $b->total_mahasiswa }} orang</td>
                                    <td class="px-3 py-2 text-right font-semibold whitespace-nowrap">{{ (int) $b->total_records }} nilai</td>
                                    <td class="px-3 py-2 text-center whitespace-nowrap">
                                        <a href="{{ route('admin.nilai-monitoring.arsip-detail', ['batch' => $b->batch_code]) }}" class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-400/30 transition text-emerald-100 text-[11px] font-semibold">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                            <span>Lihat Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-5 rounded-2xl border border-white/10 bg-[#0b231c] shadow-2xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
            <div class="text-sm font-semibold">Data Arsip Nilai ({{ $records->total() }} total)</div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-2.5 whitespace-nowrap">Waktu</th>
                        <th class="text-left font-medium px-4 py-2.5 whitespace-nowrap">Batch Code</th>
                        <th class="text-left font-medium px-4 py-2.5 whitespace-nowrap">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-2.5 whitespace-nowrap">Mata Kuliah</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">SMT</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">TM</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Quis</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Mid</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Final</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Angka</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">Huruf</th>
                        <th class="text-center font-medium px-2 py-2.5 whitespace-nowrap">IPS Saat Reset</th>
                        <th class="text-center font-medium px-4 py-2.5 whitespace-nowrap">Reset Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($records as $r)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/75 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->reset_at)->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.nilai-monitoring.arsip-detail', ['batch' => $r->batch_code]) }}" class="inline-flex items-center gap-1 font-mono text-[10px] text-amber-300 hover:text-amber-200 hover:underline decoration-amber-400/40 whitespace-nowrap">
                                    <i class="fa-solid fa-up-right-from-square text-[9px]"></i>
                                    {{ $r->batch_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3 min-w-[180px]">
                                @if ($r->mahasiswa)
                                    <div class="font-semibold text-emerald-100 whitespace-nowrap">{{ $r->mahasiswa->nama_lengkap }}</div>
                                    <div class="text-[11px] text-emerald-100/60 font-mono">NPM {{ $r->mahasiswa->npm }} · {{ $r->mahasiswa->program_studi }}</div>
                                @else
                                    <span class="text-rose-300">Mahasiswa tidak ditemukan</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 min-w-[220px]">
                                @if ($r->mataKuliah)
                                    <div class="font-semibold text-emerald-100 whitespace-nowrap">{{ $r->mataKuliah->nama_mata_kuliah }}</div>
                                    <div class="text-[11px] text-emerald-100/60 font-mono">{{ $r->mataKuliah->kode }} · {{ $r->mataKuliah->sks }} SKS</div>
                                @else
                                    <span class="text-rose-300">MK tidak ditemukan</span>
                                @endif
                            </td>
                            <td class="px-2 py-3 text-center font-semibold">{{ (int) $r->semester }}</td>
                            <td class="px-2 py-3 text-center font-mono text-blue-100">{{ $r->nilai_tm !== null ? number_format($r->nilai_tm, 2) : '-' }}</td>
                            <td class="px-2 py-3 text-center font-mono text-blue-100">{{ $r->nilai_quis !== null ? number_format($r->nilai_quis, 2) : '-' }}</td>
                            <td class="px-2 py-3 text-center font-mono text-amber-100">{{ $r->nilai_mid !== null ? number_format($r->nilai_mid, 2) : '-' }}</td>
                            <td class="px-2 py-3 text-center font-mono text-amber-100">{{ $r->nilai_final !== null ? number_format($r->nilai_final, 2) : '-' }}</td>
                            <td class="px-2 py-3 text-center font-mono font-bold text-emerald-200">{{ $r->nilai_angka !== null ? number_format($r->nilai_angka, 2) : '-' }}</td>
                            <td class="px-2 py-3 text-center">
                                @if ($r->nilai_huruf)
                                    <span class="inline-flex items-center justify-center w-9 h-6 rounded-md bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 font-bold">{{ $r->nilai_huruf }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-2 py-3 text-center">
                                <div class="font-mono font-semibold text-blue-200">{{ $r->ips_saat_reset !== null ? number_format($r->ips_saat_reset, 2) : '-' }}</div>
                                <div class="text-[10px] text-emerald-100/50">IPK: {{ $r->ipk_saat_reset !== null ? number_format($r->ipk_saat_reset, 2) : '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-emerald-100/75 whitespace-nowrap">{{ $r->resetBy?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="px-6 py-12 text-center text-sm text-emerald-100/50">
                                <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/5 border border-white/10 mb-4">
                                    <i class="fa-solid fa-box-archive text-2xl text-emerald-100/30"></i>
                                </div>
                                <div class="font-medium mb-1">Belum ada data arsip nilai</div>
                                <div class="text-xs">Data backup akan muncul disini setelah kamu lakukan reset nilai semester.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($records->hasPages())
            <div class="border-t border-white/10 px-5 py-4">
                {{ $records->links() }}
            </div>
        @endif
    </div>
</x-portal-layout>
