<x-portal-layout :title="'Input Absensi - '.config('app.name')" subtitle="Input Absensi">
    <x-slot:sidebar>
        @include($sidebarView ?? 'admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Input Absensi</div>
            <div class="text-sm text-emerald-100/70">
                {{ $absensi->jurusan }} • Semester {{ $absensi->semester }} • {{ $absensi->mataKuliah?->kode }} - {{ $absensi->mataKuliah?->nama }} • Pertemuan {{ $absensi->pertemuan }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.export.pdf', $absensi) }}"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-pdf text-red-300"></i>
                PDF
            </a>
            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.export.excel', $absensi) }}"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-excel text-emerald-300"></i>
                Excel
            </a>
            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.index', ['jurusan' => $absensi->jurusan, 'semester' => $absensi->semester, 'mata_kuliah_id' => $absensi->mata_kuliah_id]) }}"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route(($routePrefix ?? 'admin').'.absensi.update', $absensi) }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div>
                <label class="text-sm text-emerald-100/80">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', $absensi->tanggal ? $absensi->tanggal->format('Y-m-d') : '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div class="lg:col-span-2">
                <label class="text-sm text-emerald-100/80">Materi Pembelajaran</label>
                <textarea name="materi" rows="1" class="mt-2 w-full min-h-[44px] rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-3" placeholder="Masukkan materi yang diajarkan...">{{ old('materi', $absensi->materi) }}</textarea>
                @error('materi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror

                <div class="mt-3">
                    <label class="text-sm text-emerald-100/80">Upload Materi (PDF/Word/PPT)</label>
                    <input type="file" name="materi_file" accept=".pdf,.doc,.docx,.ppt,.pptx" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-2" />
                    @error('materi_file') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror

                    @if ($absensi->materi_file_path)
                        <div class="mt-2 text-sm text-emerald-100/70 flex flex-wrap items-center gap-2">
                            <span>File saat ini: <span class="font-medium">{{ $absensi->materi_file_name ?? 'materi' }}</span></span>
                            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.materi', $absensi) }}"
                               class="h-8 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                <i class="fa-solid fa-download"></i>
                                Download
                            </a>
                            <a href="{{ route(($routePrefix ?? 'admin').'.absensi.materi', ['absensi' => $absensi, 'inline' => 1]) }}"
                               target="_blank"
                               rel="noopener"
                               class="h-8 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                <i class="fa-solid fa-eye"></i>
                                Lihat
                            </a>
                            <form action="{{ route(($routePrefix ?? 'admin').'.absensi.materi.destroy', $absensi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-8 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-300 transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-3 flex flex-col gap-3 lg:items-end">
                <div class="w-full max-w-lg">
                    <label class="text-sm text-emerald-100/80">Cari (nama / NPM)</label>
                    <input id="absensiSearch" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Ketik untuk filter..." />
                </div>
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <button type="button" data-set-status="hadir" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-400/25 transition">
                        Set Semua Hadir
                    </button>
                    <button type="button" data-set-status="alpha" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-400/25 transition">
                        Set Semua Alpha
                    </button>
                    <button type="button" data-set-status="" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        Kosongkan
                    </button>
                </div>
                <div class="text-sm text-emerald-100/70">
                    Daftar mahasiswa diambil otomatis dari KRS approved untuk jurusan, semester, dan mata kuliah ini.
                </div>
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-left font-medium px-4 py-3">NPM</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-left font-medium px-4 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="absensiTbody" class="divide-y divide-white/10">
                        @forelse ($absensi->items->sortBy(fn($i) => $i->mahasiswa?->npm) as $item)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 font-medium">{{ $item->mahasiswa?->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mahasiswa?->npm ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $current = old('status.'.$item->id, $item->status);
                                    @endphp
                                    <select name="status[{{ $item->id }}]" class="h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                                        <option value="" @selected($current === null || $current === '') class="text-black">-</option>
                                        <option value="hadir" @selected($current === 'hadir') class="text-black">Hadir</option>
                                        <option value="izin" @selected($current === 'izin') class="text-black">Izin</option>
                                        <option value="sakit" @selected($current === 'sakit') class="text-black">Sakit</option>
                                        <option value="alpha" @selected($current === 'alpha') class="text-black">Alpha</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input name="keterangan[{{ $item->id }}]" value="{{ old('keterangan.'.$item->id, $item->keterangan) }}" class="w-full h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Opsional" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mahasiswa untuk daftar hadir ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Absensi
            </button>
        </div>
    </form>

    <script>
        (function () {
            const tbody = document.getElementById('absensiTbody');
            const search = document.getElementById('absensiSearch');
            const setButtons = document.querySelectorAll('[data-set-status]');

            function setAllStatus(value) {
                const selects = tbody ? tbody.querySelectorAll('select[name^="status["]') : [];
                selects.forEach((el) => {
                    el.value = value;
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                });
            }

            setButtons.forEach((btn) => {
                btn.addEventListener('click', () => setAllStatus(btn.getAttribute('data-set-status') ?? ''));
            });

            function applyFilter() {
                const q = (search?.value ?? '').toLowerCase().trim();
                const rows = tbody ? tbody.querySelectorAll('tr') : [];
                rows.forEach((tr) => {
                    const text = (tr.textContent ?? '').toLowerCase();
                    tr.style.display = q === '' || text.includes(q) ? '' : 'none';
                });
            }

            if (search) {
                search.addEventListener('input', applyFilter);
            }
        })();
    </script>
</x-portal-layout>
