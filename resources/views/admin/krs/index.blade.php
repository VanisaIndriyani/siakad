<x-portal-layout :title="'KRS - '.config('app.name')" subtitle="Kelola KRS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">KRS</div>
            <div class="text-sm text-emerald-100/70">Pantau dan kelola status approval KRS.</div>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM..." />
        <select name="status" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            <option value="" class="text-black">Semua Status</option>
            @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
                <option value="{{ $k }}" @selected($status === $k) class="text-black">{{ $v }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 h-11 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Filter</button>
            <a href="{{ route('admin.krs.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
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
        <form x-ref="bulkForm" method="POST" action="{{ route('admin.krs.bulk-delete') }}" @change="onBulkChange($event)"
              data-confirm="Apakah kamu yakin ingin menghapus KRS yang dipilih?">
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
                                <th class="text-left font-medium px-4 py-3">Status</th>
                                <th class="text-right font-medium px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($krs as $row)
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
                                    <td class="px-4 py-3">
                                        @php
                                            $badge = match ($row->status_approval) {
                                                'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                                'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                                default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                                            {{ strtoupper($row->status_approval) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.krs.pdf', $row) }}" class="h-9 px-3 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-emerald-100" title="Print PDF">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            @if ($row->status_approval === 'pending')
                                                <button type="button" 
                                                        onclick="if(confirm('Approve KRS ini?')) { document.getElementById('approve-form-{{ $row->id }}').submit(); }"
                                                        class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-400/25 transition">
                                                    <i class="fa-solid fa-check"></i>
                                                    Approve
                                                </button>
                                                <button type="button" 
                                                        onclick="if(confirm('Tolak KRS ini?')) { document.getElementById('reject-form-{{ $row->id }}').submit(); }"
                                                        class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    Tolak
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.krs.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                                <i class="fa-solid fa-eye"></i>
                                                Detail
                                            </a>
                                            <button type="button"
                                                    onclick="if(confirm('Hapus KRS ini?')) { document.getElementById('delete-form-{{ $row->id }}').submit(); }"
                                                    class="h-9 px-3 inline-flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition text-red-400">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Data tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        @foreach ($krs as $row)
            @if ($row->status_approval === 'pending')
                <form id="approve-form-{{ $row->id }}" method="POST" action="{{ route('admin.krs.status', $row) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_approval" value="approved" />
                    <input type="hidden" name="redirect" value="{{ request()->fullUrl() }}" />
                </form>
                <form id="reject-form-{{ $row->id }}" method="POST" action="{{ route('admin.krs.status', $row) }}" class="hidden">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status_approval" value="rejected" />
                    <input type="hidden" name="redirect" value="{{ request()->fullUrl() }}" />
                </form>
            @endif
            <form id="delete-form-{{ $row->id }}" method="POST" action="{{ route('admin.krs.destroy', $row) }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $krs->links() }}
    </div>
</x-portal-layout>
