<x-portal-layout :title="'Roster Kuliah - '.config('app.name')" subtitle="Upload file PDF Roster (Jadwal &amp; Absensi)">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">Roster Kuliah</div>
            <div class="text-sm text-emerald-100/70">Upload file PDF Roster per Mata Kuliah &amp; Semester. File langsung muncul di halaman Input Nilai Dosen.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.roster.create', array_filter(['mata_kuliah_id' => $mataKuliahId, 'dosen_id' => $dosenId, 'semester' => $semester, 'tahun_ajaran' => request('tahun_ajaran')])) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-500 active:bg-blue-700 transition shadow-lg shadow-blue-900/30">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                <span class="text-sm font-medium">Upload Roster PDF</span>
            </a>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari MK / dosen / kelas..." />
        <select name="mata_kuliah_id" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Mata Kuliah</option>
            @foreach ($mataKuliahs as $mk)
                <option value="{{ $mk->id }}" {{ (int)$mataKuliahId === (int)$mk->id ? 'selected' : '' }}>{{ $mk->kode }} - {{ $mk->nama }}</option>
            @endforeach
        </select>
        <select name="dosen_id" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Dosen</option>
            @foreach ($dosens as $d)
                <option value="{{ $d->id }}" {{ (int)$dosenId === (int)$d->id ? 'selected' : '' }}>{{ $d->nama }}</option>
            @endforeach
        </select>
        <select name="semester" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="">Semua Semester</option>
            @foreach (range(1, 8) as $s)
                <option value="{{ $s }}" {{ (int) $semester === $s ? 'selected' : '' }}>Semester {{ $s }}</option>
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
                        <th class="text-left font-medium px-4 py-3 w-24">Semester</th>
                        <th class="text-left font-medium px-4 py-3 w-24">Kelas</th>
                        <th class="text-left font-medium px-4 py-3 w-36">File</th>
                        <th class="text-left font-medium px-4 py-3 w-48">Aksi</th>
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
                                @if($row->tahun_ajaran) <div class="text-xs text-emerald-100/50">TA {{ $row->tahun_ajaran }}</div> @endif
                            </td>
                            <td class="px-4 py-3">{{ $row->semester }}</td>
                            <td class="px-4 py-3">{{ $row->kelas ?: '-' }}</td>
                            <td class="px-4 py-3">
                                @if (!empty($row->file_pdf) && Storage::disk('public')->exists($row->file_pdf))
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 border border-emerald-400/30 text-xs text-emerald-300 font-medium">
                                        <i class="fa-solid fa-file-pdf text-red-400"></i> PDF
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-500/10 border border-red-400/30 text-xs text-red-300 font-medium">
                                        <i class="fa-solid fa-circle-xmark"></i> Tidak Ada
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if (!empty($row->file_pdf) && Storage::disk('public')->exists($row->file_pdf))
                                        <a href="{{ Storage::disk('public')->url($row->file_pdf) }}" target="_blank" class="h-8 px-3 inline-flex items-center gap-1 rounded-lg bg-blue-500/15 hover:bg-blue-500/25 border border-blue-400/30 text-blue-100 transition text-xs">
                                            <i class="fa-solid fa-eye"></i> Lihat
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.roster.edit', $row) }}" class="h-8 px-3 inline-flex items-center gap-1 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs">
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
                            <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Belum ada roster. Klik "Upload Roster PDF" untuk mulai upload.</td>
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
