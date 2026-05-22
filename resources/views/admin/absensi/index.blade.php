<x-portal-layout :title="'Absensi - '.config('app.name')" subtitle="Kelola Absensi">
    <x-slot:sidebar>
        @include($sidebarView ?? 'admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Absensi</div>
            <div class="text-sm text-emerald-100/70">Daftar hadir otomatis tersusun dari KRS yang sudah approved.</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route(($routePrefix ?? 'admin').'.absensi.index') }}" class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-sm text-emerald-100/80">Jurusan</label>
                <select name="jurusan" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" onchange="this.form.submit()">
                    <option value="" class="text-black">Pilih Jurusan</option>
                    @foreach ($jurusanList as $opt)
                        <option value="{{ $opt }}" @selected($jurusan === $opt) class="text-black">{{ $opt }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Semester</label>
                <select name="semester" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" onchange="this.form.submit()">
                    <option value="" class="text-black">Pilih Semester</option>
                    @foreach (range(1, 8) as $s)
                        <option value="{{ $s }}" @selected((int) $semester === $s) class="text-black">Semester {{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="text-sm text-emerald-100/80">Mata Kuliah</label>
                <select name="mata_kuliah_id" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" onchange="this.form.submit()">
                    <option value="" class="text-black">{{ $semester ? 'Pilih Mata Kuliah' : 'Pilih semester dulu' }}</option>
                    @foreach ($mataKuliah as $mk)
                        <option value="{{ $mk->id }}" @selected((int) $mataKuliahId === (int) $mk->id) class="text-black">
                            S{{ $mk->semester }} • {{ $mk->kode }} - {{ $mk->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-4 flex items-center justify-end gap-3">
                <a href="{{ route(($routePrefix ?? 'admin').'.absensi.index') }}" class="h-11 px-5 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    Reset
                </a>
                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    @if ($jurusan && $semester && $mataKuliahId)
        <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-lg font-semibold">Pilih Pertemuan</div>
                    <div class="text-sm text-emerald-100/70">Buka pertemuan untuk mengisi daftar hadir.</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route(($routePrefix ?? 'admin').'.absensi.manual', ['jurusan' => $jurusan, 'semester' => $semester, 'mata_kuliah_id' => $mataKuliahId]) }}"
                       class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        <i class="fa-solid fa-file-pdf text-emerald-300"></i>
                        Manual (PDF)
                    </a>
                    <a href="{{ route(($routePrefix ?? 'admin').'.absensi.manual', ['jurusan' => $jurusan, 'semester' => $semester, 'mata_kuliah_id' => $mataKuliahId, 'inline' => 1]) }}"
                       target="_blank"
                       rel="noopener"
                       class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        <i class="fa-solid fa-print text-emerald-200"></i>
                        Manual (Print)
                    </a>
                    @if (($routePrefix ?? 'admin') === 'dosen')
                        <a href="{{ route('dosen.absensi.rekap', ['jurusan' => $jurusan, 'semester' => $semester, 'mata_kuliah_id' => $mataKuliahId]) }}"
                           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-file-pdf text-red-300"></i>
                            Rekap PDF
                        </a>
                    @endif
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (range(1, 16) as $p)
                    <a href="{{ route(($routePrefix ?? 'admin').'.absensi.entry', ['jurusan' => $jurusan, 'semester' => $semester, 'mata_kuliah_id' => $mataKuliahId, 'pertemuan' => $p]) }}"
                       class="h-10 px-4 inline-flex items-center justify-center rounded-xl border transition {{ $sessions->firstWhere('pertemuan', $p) ? 'bg-emerald-500/15 border-emerald-400/25 hover:bg-emerald-500/20' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                        Pertemuan {{ $p }}
                    </a>
                @endforeach
            </div>

            <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-4 py-3">Pertemuan</th>
                                <th class="text-left font-medium px-4 py-3">Tanggal</th>
                                <th class="text-left font-medium px-4 py-3">Materi</th>
                                <th class="text-left font-medium px-4 py-3">Terisi</th>
                                <th class="text-right font-medium px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($sessions as $s)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3 font-medium">Pertemuan {{ $s->pertemuan }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $s->tanggal?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80 max-w-xs truncate">{{ $s->materi ?? '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ (int) $s->terisi_count }}/{{ (int) $s->items_count }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end">
                                            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.export.pdf', ['absensi' => $s, 'inline' => 1]) }}"
                                               target="_blank"
                                               rel="noopener"
                                               class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition mr-2">
                                                <i class="fa-solid fa-print"></i>
                                                Print
                                            </a>
                                            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.export.pdf', ['absensi' => $s]) }}"
                                               class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition mr-2">
                                                <i class="fa-solid fa-file-pdf"></i>
                                                PDF
                                            </a>
                                            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.entry', ['jurusan' => $jurusan, 'semester' => $semester, 'mata_kuliah_id' => $mataKuliahId, 'pertemuan' => $s->pertemuan]) }}"
                                               class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                                Isi
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada pertemuan yang dibuat. Klik pertemuan di atas untuk mulai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-portal-layout>
