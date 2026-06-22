<x-portal-layout :title="'Penasehat Akademik - '.config('app.name')" subtitle="Penasehat Akademik">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $isAdminView = ($routePrefix ?? 'admin') === 'admin';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Penasehat Akademik</div>
            <div class="text-sm text-emerald-100/70">Kelola penasehat akademik mahasiswa.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ ($routePrefix ?? 'admin') === 'admin' ? route('admin.penasehat-akademik.index') : route('dosen.penasehat-akademik.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari nama / NPM..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ ($routePrefix ?? 'admin') === 'admin' ? route('admin.penasehat-akademik.index') : route('dosen.penasehat-akademik.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">NPM</th>
                        <th class="text-left font-medium px-4 py-3">Program Studi</th>
                        <th class="text-left font-medium px-4 py-3">Penasehat Akademik</th>
                        <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->nama_lengkap ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->npm ?: '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->program_studi ?: '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div>{{ $row->dosenPenasehat?->nama ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route(($routePrefix ?? 'admin').'.penasehat-akademik.show', $row) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="text-sm font-medium">Detail</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada data mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $items->links() }}
    </div>
</x-portal-layout>