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
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Input Nilai Dosen</div>
            <div class="text-sm text-emerald-100/70">Monitoring status penginputan nilai per mata kuliah dan semester.</div>
        </div>
        <a href="{{ route('admin.nilai-monitoring.pdf', array_filter(['q' => $q ?: null, 'semester' => $semester ?: null, 'status' => $status ?: null, 'all' => $showAll ? 1 : null, 'page' => ! $showAll ? request()->get('page') : null])) }}"
           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-emerald-100">
            <i class="fa-solid fa-file-pdf"></i>
            <span class="text-sm font-medium">PDF</span>
        </a>
    </div>

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
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($rows as $i => $row)
                        @php
                            $peserta = (int) ($row->peserta_approved ?? 0);
                            $terisi = (int) ($row->nilai_terisi ?? 0);
                            $progress = $peserta > 0 ? $terisi.' / '.$peserta : '-';
                            $no = $showAll ? ($i + 1) : ($rows->firstItem() + $i);
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Data tidak ditemukan.</td>
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

