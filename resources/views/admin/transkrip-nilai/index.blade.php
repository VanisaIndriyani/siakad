<x-portal-layout :title="'Transkrip Nilai - '.config('app.name')" subtitle="Transkrip Akademik Mahasiswa">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Transkrip Nilai Akademik</div>
            <div class="text-sm text-emerald-100/70">Cari mahasiswa dan cetak transkrip nilai.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.transkrip-nilai.index') }}" class="flex flex-col xl:flex-row xl:items-center gap-3">
            <div class="relative flex-1">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-white/40 focus:ring-emerald-400 focus:border-emerald-400 text-sm pl-10 pr-4"
                       placeholder="Cari nama / NPM / NIK mahasiswa..." />
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-white/40">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>

            <select name="angkatan"
                    class="h-11 min-w-[150px] rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:ring-emerald-400 focus:border-emerald-400 px-3">
                <option value="" style="background-color: #0d2a23; color: #fff;">Semua Angkatan</option>
                @foreach($angkatanList as $a)
                    <option value="{{ $a }}" @selected($angkatan === (string) $a) style="background-color: #0d2a23; color: #fff;">Angkatan {{ $a }}</option>
                @endforeach
            </select>

            <select name="prodi"
                    class="h-11 min-w-[220px] rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:ring-emerald-400 focus:border-emerald-400 px-3">
                <option value="" style="background-color: #0d2a23; color: #fff;">Semua Program Studi</option>
                @foreach($prodiList as $p)
                    <option value="{{ $p }}" @selected($prodi === $p) style="background-color: #0d2a23; color: #fff;">{{ $p }}</option>
                @endforeach
            </select>

            <button type="submit" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                <i class="fa-solid fa-filter"></i>
                Filter
            </button>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-[60px]">No</th>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">NPM</th>
                        <th class="text-left font-medium px-4 py-3">Prodi</th>
                        <th class="text-left font-medium px-4 py-3">Angkatan</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($mahasiswa as $idx => $m)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3 text-emerald-100/50">{{ $mahasiswa->firstItem() + $idx }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($m->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div class="leading-tight">
                                        <div class="font-semibold">{{ $m->nama_lengkap }}</div>
                                        <div class="text-xs text-emerald-100/50">{{ $m->nik ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono">{{ $m->npm ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $m->program_studi ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $m->angkatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                                    <a href="{{ route('admin.transkrip-nilai.show', $m) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs">
                                        <i class="fa-regular fa-file-lines text-emerald-300"></i>
                                        Lihat
                                    </a>
                                    <a href="{{ route('admin.transkrip-nilai.pdf', $m) }}" target="_blank" rel="noopener" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs">
                                        <i class="fa-solid fa-print text-emerald-300"></i>
                                        Cetak
                                    </a>
                                    @php
                                        $namaFilePdf = 'Transkrip-' . ($m->npm ?: $m->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string)$m->nama_lengkap) . '.pdf';
                                    @endphp
                                    <a href="{{ route('admin.transkrip-nilai.pdf', [$m, 'download' => 1, 'fd' => 1]) }}"
                                       download="{{ $namaFilePdf }}"
                                       type="application/octet-stream"
                                       class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-xs font-medium">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                        Download PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if($mahasiswa->isEmpty())
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-emerald-100/60">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-emerald-100/30"></i>
                                Mahasiswa tidak ditemukan.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if($mahasiswa->hasPages())
        <div class="mt-5">
            {{ $mahasiswa->links() }}
        </div>
    @endif
</x-portal-layout>
