<x-portal-layout :title="'Mahasiswa - '.config('app.name')" subtitle="Manajemen Mahasiswa">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">CRUD, pencarian, export, dan detail mahasiswa.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mahasiswa.export.pdf', ['q' => $q]) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-pdf text-red-300"></i>
                <span class="text-sm font-medium">PDF</span>
            </a>
            <a href="{{ route('admin.mahasiswa.export.excel', ['q' => $q]) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-excel text-emerald-300"></i>
                <span class="text-sm font-medium">Excel</span>
            </a>
            <a href="{{ route('admin.mahasiswa.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Tambah</span>
            </a>
        </div>
    </div>

    <div class="mt-5" x-data="{
        q: @js($q),
        tableHtml: null,
        loading: false,
        timer: null,
        fetchTable() {
            this.loading = true;
            fetch(`{{ route('admin.mahasiswa.index') }}?q=${encodeURIComponent(this.q || '')}&partial=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => { this.tableHtml = html; })
            .finally(() => { this.loading = false; });
        }
    }" x-init="tableHtml = $refs.initial.innerHTML">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
            <div class="relative w-full sm:max-w-md">
                <input type="text"
                       x-model="q"
                       @input.debounce.350ms="fetchTable()"
                       class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-white/40 focus:ring-emerald-400 focus:border-emerald-400"
                       placeholder="Cari nama / NPM / NIK / angkatan / prodi..." />
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" x-show="!loading">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40" x-show="loading" x-cloak>
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
            </div>
            <a href="{{ route('admin.mahasiswa.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                Reset
            </a>
        </div>

        <div x-html="tableHtml"></div>
        <div x-ref="initial" class="hidden">
            @include('admin.mahasiswa.partials.table', ['mahasiswa' => $mahasiswa])
        </div>
    </div>
</x-portal-layout>
