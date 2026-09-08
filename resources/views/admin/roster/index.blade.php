<x-portal-layout :title="'Roster Perkuliahan - '.config('app.name')" subtitle="Manajemen Jadwal Roster Pertemuan">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">Roster Perkuliahan</div>
            <div class="text-sm text-emerald-100/70">Input daftar jadwal pertemuan, materi, dan ruangan per mata kuliah. Ditampilkan di halaman Input Nilai Dosen sebagai PDF Roster.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roster.create', array_filter(['mata_kuliah_id' => request('mata_kuliah_id'), 'dosen_id' => request('dosen_id'), 'semester' => $semester, 'tahun_ajaran' => request('tahun_ajaran')])) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Tambah Roster</span>
            </a>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari materi / ruang / kode MK..." />
        <select name="semester" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Semester</option>
            @foreach (range(1, 8) as $s)
                <option value="{{ $s }}" {{ (int) $semester === $s ? 'selected' : '' }}>Semester {{ $s }}</option>
            @endforeach
        </select>
        <select name="mata_kuliah_id" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Mata Kuliah</option>
            @foreach ($mataKuliahs as $m)
                <option value="{{ $m->id }}" {{ (int) $mataKuliahId === (int) $m->id ? 'selected' : '' }}>{{ $m->kode }} - {{ $m->nama }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-2">
            <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Filter</button>
            <a href="{{ route('admin.roster.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3 w-16">Prt.</th>
                        <th class="text-left font-medium px-4 py-3 w-28">Hari / Tanggal</th>
                        <th class="text-left font-medium px-4 py-3 w-24">Jam</th>
                        <th class="text-left font-medium px-4 py-3 w-28">Ruang</th>
                        <th class="text-left font-medium px-4 py-3">Materi</th>
                        <th class="text-left font-medium px-4 py-3 w-24">Semester</th>
                        <th class="text-left font-medium px-4 py-3 w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($rows as $i => $row)
                        @php $no = $rows->firstItem() + $i; @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $no }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-xs">{{ $row->mataKuliah?->kode }} - {{ $row->mataKuliah?->nama }}</div>
                                @if($row->dosen) <div class="text-xs text-emerald-100/60">Dosen: {{ $row->dosen->nama }}</div> @endif
                            </td>
                            <td class="px-4 py-3"><span class="px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-500/20 font-semibold text-xs">{{ $row->pertemuan_ke }}</span></td>
                            <td class="px-4 py-3 text-xs">
                                <div>{{ $row->hari ?: '-' }}</div>
                                <div class="text-emerald-100/60">{{ $row->tanggal?->format('d/m/Y') ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono">
                                @if($row->jam_mulai) {{ $row->jam_mulai->format('H:i') }} - {{ $row->jam_selesai?->format('H:i') ?: '--:--' }} @else - @endif
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $row->ruang ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs">
                                <div>{{ $row->materi ?: '-' }}</div>
                                @if($row->metode_pembelajaran) <div class="text-emerald-100/60 mt-1">[{{ $row->metode_pembelajaran }}]</div> @endif
                                @if($row->keterangan) <div class="text-emerald-100/60 mt-1 italic">{{ $row->keterangan }}</div> @endif
                            </td>
                            <td class="px-4 py-3">{{ $row->semester }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.roster.edit', $row) }}" class="h-8 px-3 inline-flex items-center gap-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs" title="Edit">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.roster.destroy', $row) }}" data-confirm-item-delete="1" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-8 px-3 inline-flex items-center gap-1 rounded-lg bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 text-red-100 transition text-xs">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-emerald-100/70">Belum ada data Roster. Klik "Tambah Roster" untuk memulai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($rows->hasPages())
            <div class="px-5 py-4 border-t border-white/10">{{ $rows->links() }}</div>
        @endif
    </div>
</x-portal-layout>
