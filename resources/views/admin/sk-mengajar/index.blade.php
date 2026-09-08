<x-portal-layout :title="'SK Mengajar - '.config('app.name')" subtitle="Manajemen SK Mengajar Dosen">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">SK Mengajar Dosen</div>
            <div class="text-sm text-emerald-100/70">Input data SK Mengajar per mata kuliah dan semester. Data akan tampil di halaman Input Nilai Dosen sebagai PDF.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sk-mengajar.create', array_filter(['mata_kuliah_id' => request('mata_kuliah_id'), 'dosen_id' => $dosenId, 'semester' => $semester, 'tahun_ajaran' => request('tahun_ajaran')])) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Tambah SK Mengajar</span>
            </a>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nomor SK / MK / dosen..." />
        <select name="semester" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Semester</option>
            @foreach (range(1, 8) as $s)
                <option value="{{ $s }}" {{ (int) $semester === $s ? 'selected' : '' }}>Semester {{ $s }}</option>
            @endforeach
        </select>
        <select name="dosen_id" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Dosen</option>
            @foreach ($dosens as $d)
                <option value="{{ $d->id }}" {{ (int) $dosenId === (int) $d->id ? 'selected' : '' }}>{{ $d->nama_lengkap }}</option>
            @endforeach
        </select>
        <div class="flex items-center gap-2">
            <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Filter</button>
            <a href="{{ route('admin.sk-mengajar.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </div>
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Dosen Pengampu</th>
                        <th class="text-left font-medium px-4 py-3 w-44">Nomor SK</th>
                        <th class="text-left font-medium px-4 py-3 w-28">Tgl SK</th>
                        <th class="text-left font-medium px-4 py-3 w-24">Semester</th>
                        <th class="text-left font-medium px-4 py-3 w-20">SKS</th>
                        <th class="text-left font-medium px-4 py-3 w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($rows as $i => $row)
                        @php $no = $rows->firstItem() + $i; @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $no }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->mataKuliah?->kode }} - {{ $row->mataKuliah?->nama }}</div>
                                @if ($row->kelas) <div class="text-xs text-emerald-100/60">Kelas {{ $row->kelas }}{{ $row->program_studi ? ' • '.$row->program_studi : '' }}</div> @endif
                            </td>
                            <td class="px-4 py-3">{{ $row->dosen?->nama_lengkap ?? '-' }}</td>
                            <td class="px-4 py-3 font-mono text-xs">{{ $row->nomor_sk ?: '-' }}</td>
                            <td class="px-4 py-3 text-xs">{{ $row->tanggal_sk?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $row->semester }} @if($row->tahun_ajaran)<span class="text-xs text-emerald-100/60"> • {{ $row->tahun_ajaran }}</span>@endif</td>
                            <td class="px-4 py-3">{{ $row->beban_sks ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.sk-mengajar.edit', $row) }}" class="h-8 px-3 inline-flex items-center gap-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs" title="Edit">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.sk-mengajar.destroy', $row) }}" data-confirm-item-delete="1" class="inline">
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
                            <td colspan="8" class="px-4 py-10 text-center text-emerald-100/70">Belum ada data SK Mengajar. Klik "Tambah SK Mengajar" untuk menambahkan.</td>
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
