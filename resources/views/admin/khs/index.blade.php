<x-portal-layout :title="'KHS - '.config('app.name')" subtitle="Kelola KHS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">KHS</div>
            <div class="text-sm text-emerald-100/70">Kelola nilai, IPS, IPK, dan detail KHS.</div>
        </div>
        <a href="{{ route('admin.khs.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Buat KHS</span>
        </a>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM..." />
        <input type="number" name="semester" value="{{ $semester }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Semester" />
        <div class="flex gap-2">
            <button class="flex-1 h-11 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Filter</button>
            <a href="{{ route('admin.khs.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
        </div>
    </form>

    <div class="mt-4" x-data="{
        selectedIds: [],
        onBulkChange(e) {
            const t = e.target;
            if (!(t instanceof HTMLInputElement)) return;

            if (t.dataset.bulk === 'select-all') {
                const rows = this.$refs.bulkForm.querySelectorAll('input[data-bulk=row]');
                rows.forEach((cb) => { cb.checked = t.checked; });
            }

            this.syncSelectedFromDom();
        },
        syncSelectedFromDom() {
            const rows = Array.from(this.$refs.bulkForm.querySelectorAll('input[data-bulk=row]'));
            this.selectedIds = rows.filter((cb) => cb.checked).map((cb) => cb.value);

            const selectAll = this.$refs.bulkForm.querySelector('input[data-bulk=select-all]');
            if (!selectAll) return;

            if (rows.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                return;
            }

            const checkedCount = rows.filter((cb) => cb.checked).length;
            selectAll.checked = checkedCount === rows.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
        }
    }">
        <form x-ref="bulkForm" method="POST" action="{{ route('admin.khs.bulk-delete') }}" @change="onBulkChange($event)"
              data-confirm="Apakah kamu yakin ingin menghapus KHS yang dipilih?">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 mb-3">
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
                                <th class="px-4 py-3 text-left w-10">
                                    <input type="checkbox" data-bulk="select-all" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                </th>
                                <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                                <th class="text-left font-medium px-4 py-3">Semester</th>
                                <th class="text-left font-medium px-4 py-3">MK</th>
                                <th class="text-left font-medium px-4 py-3">IPS</th>
                                <th class="text-left font-medium px-4 py-3">IPK</th>
                                <th class="text-right font-medium px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($khs as $row)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-bulk="row" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap }}</div>
                                        <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $row->semester }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $row->items_count }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $row->ips ?? '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $row->ipk ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.khs.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                                <i class="fa-solid fa-eye"></i>
                                                Detail
                                            </a>
                                            <a href="{{ route('admin.khs.edit', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                                <i class="fa-solid fa-pen"></i>
                                                Edit
                                            </a>
                                            <button type="button"
                                                    onclick="if(confirm('Hapus KHS ini?')) { document.getElementById('delete-form-{{ $row->id }}').submit(); }"
                                                    class="h-9 px-3 inline-flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition text-red-400">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
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
        </form>

        @foreach ($khs as $row)
            <form id="delete-form-{{ $row->id }}" method="POST" action="{{ route('admin.khs.destroy', $row) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $khs->links() }}
    </div>
</x-portal-layout>
