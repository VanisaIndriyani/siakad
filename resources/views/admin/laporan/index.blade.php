<x-portal-layout :title="'Laporan - '.config('app.name')" subtitle="Laporan">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Laporan Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Chat laporan untuk pengajuan yang belum di-approve.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5" x-data="{
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
        },
    }">
        <div class="flex flex-col lg:flex-row gap-3 items-end lg:items-center">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex-1 flex flex-col lg:flex-row gap-3">
                <input name="q" value="{{ $q }}" placeholder="Cari judul / nama / NPM..."
                       class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                <select name="status" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                    @foreach (['open' => 'Open', 'closed' => 'Closed', '' => 'Semua'] as $k => $v)
                        <option value="{{ $k }}" @selected($status === $k) style="background-color: #0d2a23; color: #fff;">{{ $v }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2">
                    <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium text-white">Cari</button>
                    <a href="{{ route('admin.laporan.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center text-white">Reset</a>
                </div>
            </form>

            <form x-ref="bulkForm" method="POST" action="{{ route('admin.laporan.bulk-delete') }}" @change="onBulkChange($event)"
                  @submit.prevent="if(confirm('Apakah kamu yakin ingin menghapus laporan yang dipilih?')) $el.submit()">
                @csrf
                @method('DELETE')
                <input type="hidden" name="ids" :value="selectedIds.join(',')">
                <button type="submit"
                        :disabled="selectedIds.length === 0"
                        class="h-11 px-4 inline-flex items-center gap-2 rounded-xl border transition"
                        :class="selectedIds.length === 0
                            ? 'bg-white/5 border-white/10 text-white/40 cursor-not-allowed'
                            : 'bg-red-500/15 hover:bg-red-500/25 border-red-500/20 text-red-100'">
                    <i class="fa-solid fa-trash"></i>
                    <span class="text-sm font-medium" x-text="`Hapus Terpilih (${selectedIds.length})`"></span>
                </button>
            </form>
        </div>

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" data-bulk="select-all"
                                       class="rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500/30">
                            </th>
                            <th class="text-left font-medium px-4 py-3 w-14">No</th>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-left font-medium px-4 py-3">Judul</th>
                            <th class="text-left font-medium px-4 py-3">Jenis</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-left font-medium px-4 py-3 w-48">Terakhir</th>
                            <th class="text-right font-medium px-4 py-3 w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($items as $i => $row)
                            @php
                                $badge = $row->status === 'open'
                                    ? 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100'
                                    : 'bg-zinc-500/15 border-zinc-500/20 text-zinc-100';
                                $jenisLabel = match($row->jenis) {
                                    'skripsi' => 'Skripsi',
                                    'ppl' => 'PPL',
                                    'krs' => 'KRS',
                                    default => strtoupper($row->jenis)
                                };
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <input type="checkbox" data-bulk="row" value="{{ $row->id }}"
                                           class="rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500/30">
                                </td>
                                <td class="px-4 py-3">{{ $items->firstItem() + $i }}</td>
                                <td class="px-4 py-3 text-white">
                                    <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                    <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-white">
                                    <div class="font-medium">{{ $row->judul }}</div>
                                    @if ($row->latestMessage?->pesan)
                                        <div class="text-xs text-emerald-100/60 mt-1 line-clamp-1">{{ $row->latestMessage->pesan }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $jenisLabel }} #{{ $row->pengajuan_id }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge }}">
                                        {{ $row->status === 'open' ? 'Open' : 'Closed' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-emerald-100/70">{{ $row->last_message_at?->format('d/m/Y H:i') ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end">
                                        <a href="{{ route('admin.laporan.show', $row) }}"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white">
                                            <i class="fa-solid fa-comments"></i>
                                            Buka
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-emerald-100/70">Belum ada laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-5">
        {{ $items->links() }}
    </div>
</x-portal-layout>

