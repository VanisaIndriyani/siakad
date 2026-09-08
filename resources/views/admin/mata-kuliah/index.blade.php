<x-portal-layout :title="'Mata Kuliah - '.config('app.name')" subtitle="Manajemen Mata Kuliah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Pilih jurusan → pilih semester → baru tampil daftar mata kuliah.</div>
        </div>
        <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
            <i class="fa-solid fa-file-pdf"></i>
            <span class="text-sm font-medium">PDF</span>
        </a>
    </div>

    <div class="mt-7 mb-8">
        <h2 class="text-xl font-semibold mb-1">Upload SK Mengajar & Roster Kuliah Per Prodi</h2>
        <div class="text-sm text-emerald-100/70 mb-4">Upload 1x per prodi → otomatis muncul untuk <strong>SEMUA dosen</strong> di prodi tersebut di halaman Input Nilai Dosen. Cukup upload disini, nanti otomatis menyesuaikan prodi masing-masing dosen.</div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($jurusanList as $j)
                @php
                    $sk = $skPerProdi->get($j);
                    $rs = $rosterPerProdi->get($j);
                    $skAda = $sk && !empty($sk->file_pdf) && \Illuminate\Support\Facades\Storage::disk('public')->exists($sk->file_pdf);
                    $rsAda = $rs && !empty($rs->file_pdf) && \Illuminate\Support\Facades\Storage::disk('public')->exists($rs->file_pdf);
                @endphp
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5 shadow-[0_0_40px_-12px_rgba(16,185,129,0.15)] hover:shadow-[0_0_50px_-10px_rgba(16,185,129,0.25)] transition">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-4">
                        <div>
                            <div class="text-lg font-semibold leading-tight">{{ $j }}</div>
                            <div class="text-xs text-emerald-100/60 mt-1">Upload 1x untuk seluruh dosen</div>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs border {{ $skAda ? 'bg-emerald-500/15 border-emerald-400/25 text-emerald-200' : 'bg-red-500/15 border-red-500/25 text-red-200' }}">
                                <i class="fa-solid fa-file-signature text-[10px]"></i>
                                SK {{ $skAda ? '✓' : 'Belum' }}
                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs border {{ $rsAda ? 'bg-blue-500/15 border-blue-400/25 text-blue-200' : 'bg-red-500/15 border-red-500/25 text-red-200' }}">
                                <i class="fa-solid fa-calendar-days text-[10px]"></i>
                                Roster {{ $rsAda ? '✓' : 'Belum' }}
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.mata-kuliah.upload-sk', $j) }}" enctype="multipart/form-data" class="mb-3 rounded-xl border border-emerald-400/20 bg-emerald-500/5 p-3">
                        @csrf
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="inline-flex items-center gap-1.5 text-emerald-200">
                                <i class="fa-solid fa-file-signature text-sm"></i>
                                <span class="text-sm font-medium">SK Mengajar</span>
                            </div>
                            @if($skAda)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($sk->file_pdf) }}" target="_blank" rel="noopener" class="text-[11px] text-emerald-300 hover:text-emerald-200 underline inline-flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i>
                                    Preview
                                </a>
                            @endif
                        </div>
                        @if($skAda)
                            <div class="text-[11px] text-emerald-100/70 mb-2 truncate" title="{{ basename($sk->file_pdf) }}">
                                <i class="fa-solid fa-paperclip mr-1"></i>{{ basename($sk->file_pdf) }}
                            </div>
                        @endif
                        @error('sk_pdf')
                            <div class="text-[11px] text-red-300 mb-2">{{ $message }}</div>
                        @enderror
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="file" name="file_pdf" accept="application/pdf,.pdf" required class="w-full h-9 px-2.5 text-xs rounded-lg bg-emerald-500/10 border border-emerald-400/20 focus:border-emerald-400 focus:ring-emerald-400 file:mr-2 file:mt-1.5 file:h-6 file:px-2.5 file:text-[11px] file:rounded-md file:bg-emerald-500 file:text-white file:border-0 file:cursor-pointer" />
                            <button type="submit" class="h-9 px-3.5 inline-flex items-center justify-center gap-1 rounded-lg bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 transition text-xs font-medium text-white shrink-0">
                                <i class="fa-solid fa-upload"></i>
                                Upload
                            </button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('admin.mata-kuliah.upload-roster', $j) }}" enctype="multipart/form-data" class="rounded-xl border border-blue-400/20 bg-blue-500/5 p-3">
                        @csrf
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="inline-flex items-center gap-1.5 text-blue-200">
                                <i class="fa-solid fa-calendar-days text-sm"></i>
                                <span class="text-sm font-medium">Roster Kuliah</span>
                            </div>
                            @if($rsAda)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($rs->file_pdf) }}" target="_blank" rel="noopener" class="text-[11px] text-blue-300 hover:text-blue-200 underline inline-flex items-center gap-1">
                                    <i class="fa-solid fa-eye"></i>
                                    Preview
                                </a>
                            @endif
                        </div>
                        @if($rsAda)
                            <div class="text-[11px] text-blue-100/70 mb-2 truncate" title="{{ basename($rs->file_pdf) }}">
                                <i class="fa-solid fa-paperclip mr-1"></i>{{ basename($rs->file_pdf) }}
                            </div>
                        @endif
                        @error('roster_pdf')
                            <div class="text-[11px] text-red-300 mb-2">{{ $message }}</div>
                        @enderror
                        <div class="flex flex-col sm:flex-row gap-2">
                            <input type="file" name="file_pdf" accept="application/pdf,.pdf" required class="w-full h-9 px-2.5 text-xs rounded-lg bg-blue-500/10 border border-blue-400/20 focus:border-blue-400 focus:ring-blue-400 file:mr-2 file:mt-1.5 file:h-6 file:px-2.5 file:text-[11px] file:rounded-md file:bg-blue-500 file:text-white file:border-0 file:cursor-pointer" />
                            <button type="submit" class="h-9 px-3.5 inline-flex items-center justify-center gap-1 rounded-lg bg-blue-500 hover:bg-blue-400 active:bg-blue-600 transition text-xs font-medium text-white shrink-0">
                                <i class="fa-solid fa-upload"></i>
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($jurusanList as $j)
            <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $j]) }}"
               class="group rounded-2xl border p-5 transition {{ $jurusan === $j ? 'bg-emerald-500/15 border-emerald-400/25' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                <div class="text-lg font-semibold">{{ $j }}</div>
                <div class="mt-1 text-sm text-emerald-100/70">Klik untuk pilih semester</div>
            </a>
        @endforeach
    </div>

    @if ($jurusan)
        <div class="mt-6 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="text-lg font-semibold">{{ $jurusan }}</div>
                    <div class="text-sm text-emerald-100/70">Pilih semester untuk menampilkan mata kuliah.</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span class="text-sm font-medium">PDF</span>
                    </a>
                    <a href="{{ route('admin.mata-kuliah.index') }}" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        Reset
                    </a>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (range(1, 8) as $s)
                    <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $jurusan, 'semester' => $s]) }}"
                       class="h-10 px-4 inline-flex items-center justify-center rounded-xl border transition {{ (int) $semester === $s ? 'bg-emerald-500/15 border-emerald-400/25 hover:bg-emerald-500/20' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                        Semester {{ $s }}
                    </a>
                @endforeach
            </div>

            @if ($semester)
                <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <form method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="jurusan" value="{{ $jurusan }}" />
                        <input type="hidden" name="semester" value="{{ $semester }}" />
                        <input name="q" value="{{ $q }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode / nama..." />
                        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
                        <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $jurusan, 'semester' => $semester]) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            Reset
                        </a>
                    </form>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span class="text-sm font-medium">PDF</span>
                        </a>
                        <a href="{{ route('admin.mata-kuliah.create', ['jurusan' => $jurusan, 'semester' => $semester]) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                            <i class="fa-solid fa-plus"></i>
                            <span class="text-sm font-medium">Tambah MK</span>
                        </a>
                    </div>
                </div>

                <div x-data="{
                    selectedIds: [],
                    onBulkChange(e) {
                        const t = e.target;
                        if (!(t instanceof HTMLInputElement)) return;

                        if (t.dataset.bulk === 'select-all') {
                            const rows = Array.from(this.$el.querySelectorAll('input[data-bulk=row]'));
                            rows.forEach((cb) => { cb.checked = t.checked; });
                        }

                        this.syncSelectedFromDom();
                    },
                    syncSelectedFromDom() {
                        const rows = Array.from(this.$el.querySelectorAll('input[data-bulk=row]'));
                        this.selectedIds = rows.filter((cb) => cb.checked).map((cb) => cb.value);

                        const selectAll = this.$el.querySelector('input[data-bulk=select-all]');
                        if (!selectAll) return;

                        if (rows.length === 0) {
                            selectAll.checked = false;
                            selectAll.indeterminate = false;
                            return;
                        }

                        const checkedCount = rows.filter((cb) => cb.checked).length;
                        selectAll.checked = checkedCount === rows.length;
                        selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
                    },
                }">
                    <form x-ref="bulkForm" method="POST" action="{{ route('admin.mata-kuliah.bulk-delete') }}" @change="onBulkChange($event)"
                          data-confirm="Apakah kamu yakin ingin menghapus mata kuliah yang dipilih?">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-between gap-3 mb-3 mt-5">
                            <button type="submit"
                                    :disabled="selectedIds.length === 0"
                                    class="h-10 px-4 inline-flex items-center gap-2 rounded-xl border transition"
                                    :class="selectedIds.length === 0
                                        ? 'bg-white/5 border-white/10 text-white/40 cursor-not-allowed'
                                        : 'bg-red-500/15 hover:bg-red-500/25 border-red-500/20 text-red-100'">
                                <i class="fa-solid fa-trash"></i>
                                <span class="text-sm font-medium" x-text="`Hapus Terpilih (${selectedIds.length})`"></span>
                            </button>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-white/5 text-emerald-100/80">
                                        <tr>
                                            <th class="text-left font-medium px-4 py-3 w-10">
                                                <input type="checkbox" data-bulk="select-all"
                                                       class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                                            </th>
                                            <th class="text-left font-medium px-4 py-3">Kode</th>
                                            <th class="text-left font-medium px-4 py-3">Nama</th>
                                            <th class="text-left font-medium px-4 py-3">SKS</th>
                                            <th class="text-left font-medium px-4 py-3">Dosen</th>
                                            <th class="text-right font-medium px-4 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        @forelse ($mataKuliah as $row)
                                            <tr class="hover:bg-white/5">
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-bulk="row"
                                                           class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                                                </td>
                                                <td class="px-4 py-3 font-medium">{{ $row->kode }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">{{ $row->nama }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">{{ $row->sks }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">
                                                    <div class="font-medium text-white">{{ $row->dosen?->nama ?? '-' }}</div>
                                                    @if ($row->dosen2?->nama)
                                                        <div class="text-xs text-emerald-100/70 mt-1">{{ $row->dosen2?->nama }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="{{ route('admin.mata-kuliah.edit', $row) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Edit">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.mata-kuliah.destroy', $row) }}" data-confirm="Apakah kamu yakin ingin menghapus mata kuliah ini?">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition" title="Hapus">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah untuk jurusan dan semester ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mt-4">
                    {{ $mataKuliah->links() }}
                </div>
            @endif
        </div>
    @endif
</x-portal-layout>
