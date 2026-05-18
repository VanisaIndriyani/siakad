<x-portal-layout :title="'Mahasiswa - '.config('app.name')" subtitle="Manajemen Mahasiswa">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div x-data="{
        q: @js($q),
        status: @js($status),
        angkatan: @js($angkatan),
        prodi: @js($prodi),
        semester: @js($semester),
        tableHtml: null,
        loading: false,
        selectedIds: [],
        fetchTable() {
            this.loading = true;
            const params = new URLSearchParams({
                q: this.q || '',
                status: this.status || '',
                angkatan: this.angkatan || '',
                prodi: this.prodi || '',
                semester: this.semester || '',
                partial: '1'
            });
            fetch(`{{ route('admin.mahasiswa.index') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => { this.tableHtml = html; this.$nextTick(() => this.syncSelectedFromDom()); })
            .finally(() => { this.loading = false; });
        },
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
    }" x-init="tableHtml = $refs.initial.innerHTML; $nextTick(() => syncSelectedFromDom())">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">CRUD, pencarian, export, dan detail mahasiswa.</div>
        </div>
        <div class="flex items-center gap-2">
            <a :href="`{{ route('admin.mahasiswa.export.pdf') }}?q=${q || ''}&status=${status || ''}&angkatan=${angkatan || ''}&prodi=${prodi || ''}&semester=${semester || ''}`"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-pdf text-red-300"></i>
                <span class="text-sm font-medium">PDF</span>
            </a>
            <a :href="`{{ route('admin.mahasiswa.export.excel') }}?q=${q || ''}&status=${status || ''}&angkatan=${angkatan || ''}&prodi=${prodi || ''}&semester=${semester || ''}`"
               class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-excel text-emerald-300"></i>
                <span class="text-sm font-medium">Excel</span>
            </a>
            <a href="{{ route('admin.mahasiswa.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Tambah</span>
            </a>
        </div>
    </div>

    <div class="mt-5">
        <div class="flex flex-col xl:flex-row xl:items-center gap-3 mb-4">
            <div class="relative w-full xl:w-80">
                <input type="text"
                       x-model="q"
                       @input.debounce.350ms="fetchTable()"
                       class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-white/40 focus:ring-emerald-400 focus:border-emerald-400 text-sm"
                       placeholder="Cari nama / NPM / NIK..." />
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" x-show="!loading">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" x-show="loading" x-cloak>
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <select x-model="semester" @change="fetchTable()"
                        class="h-11 min-w-[140px] rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:ring-emerald-400 focus:border-emerald-400 px-3">
                    <option value="" style="background-color: #0d2a23; color: #fff;">Semua Semester</option>
                    @foreach(range(1, 8) as $s)
                        <option value="{{ $s }}" style="background-color: #0d2a23; color: #fff;">Semester {{ $s }}</option>
                    @endforeach
                </select>

                <select x-model="angkatan" @change="fetchTable()"
                        class="h-11 min-w-[140px] rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:ring-emerald-400 focus:border-emerald-400 px-3">
                    <option value="" style="background-color: #0d2a23; color: #fff;">Semua Angkatan</option>
                    @foreach($angkatanList as $a)
                        <option value="{{ $a }}" style="background-color: #0d2a23; color: #fff;">{{ $a }}</option>
                    @endforeach
                </select>

                <select x-model="prodi" @change="fetchTable()"
                        class="h-11 min-w-[180px] rounded-xl bg-white/5 border border-white/10 text-white text-sm focus:ring-emerald-400 focus:border-emerald-400 px-3">
                    <option value="" style="background-color: #0d2a23; color: #fff;">Semua Prodi</option>
                    @foreach($prodiList as $p)
                        <option value="{{ $p }}" style="background-color: #0d2a23; color: #fff;">{{ $p }}</option>
                    @endforeach
                </select>

                <a href="{{ route('admin.mahasiswa.index', ['status' => $status]) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                    Reset
                </a>
            </div>
        </div>

        <form x-ref="bulkForm" method="POST" action="{{ route('admin.mahasiswa.bulk-delete') }}" @change="onBulkChange($event)"
              data-confirm="Apakah kamu yakin ingin menghapus mahasiswa yang dipilih?">
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

            <div x-html="tableHtml"></div>
        </form>
        <div x-ref="initial" class="hidden">
            @include('admin.mahasiswa.partials.table', ['mahasiswa' => $mahasiswa])
        </div>
    </div>
    </div>
</x-portal-layout>
