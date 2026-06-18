<x-portal-layout :title="'Mata Kuliah - '.config('app.name')" subtitle="Manajemen Mata Kuliah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col gap-2">
        <div class="text-xl font-semibold">Mata Kuliah</div>
        <div class="text-sm text-emerald-100/70">Pilih jurusan → pilih semester → baru tampil daftar mata kuliah.</div>
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
                <a href="{{ route('admin.mata-kuliah.index') }}" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    Reset
                </a>
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
                    <a href="{{ route('admin.mata-kuliah.create', ['jurusan' => $jurusan, 'semester' => $semester]) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                        <i class="fa-solid fa-plus"></i>
                        <span class="text-sm font-medium">Tambah MK</span>
                    </a>
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
