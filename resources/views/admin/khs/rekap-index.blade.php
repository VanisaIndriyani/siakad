<x-portal-layout :title="'Rekapitulasi Nilai - '.config('app.name')" subtitle="Transkip Nilai Acuan Ijazah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Rekapitulasi Nilai Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Rekap KHS semester 1 s/d 8 sebagai acuan Transkip Nilai Ijazah.</div>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM..." />
        <select name="prodi" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Prodi</option>
            @foreach ($prodiList as $p)
                <option value="{{ $p }}" @selected($prodi === (string) $p)>{{ $p }}</option>
            @endforeach
        </select>
        <select name="angkatan" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Angkatan</option>
            @foreach ($angkatanList as $a)
                <option value="{{ $a }}" @selected($angkatan === (string) $a)>{{ $a }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 h-11 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">Cari</button>
            <a href="{{ route('admin.rekap-nilai.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-12">No</th>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">Prodi</th>
                        <th class="text-center font-medium px-4 py-3">Angkatan</th>
                        <th class="text-center font-medium px-4 py-3">Semester Terisi</th>
                        <th class="text-center font-medium px-4 py-3">Total SKS</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($mahasiswaList as $m)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/70">{{ $mahasiswaList->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $m->nama_lengkap }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $m->npm }} • {{ $m->user?->email ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $m->program_studi ?? '-' }}</td>
                            <td class="px-4 py-3 text-center text-emerald-100/80">{{ $m->angkatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold bg-emerald-500/15 border border-emerald-500/20 text-emerald-100">
                                    {{ $m->total_semester_terisi ?? 0 }} / 8
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center text-emerald-100/80">{{ $m->total_sks ?? 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.rekap-nilai.show', $m) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="text-xs">Lihat</span>
                                    </a>
                                    <a href="{{ route('admin.rekap-nilai.pdf', $m) }}" target="_blank" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-white font-medium">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        <span class="text-xs">PDF</span>
                                    </a>
                                </div>
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

    <div class="mt-4">
        {{ $mahasiswaList->links() }}
    </div>
</x-portal-layout>
