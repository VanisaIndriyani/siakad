<x-portal-layout :title="'Kalender Akademik - '.config('app.name')" subtitle="Kalender Akademik">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Kalender Akademik</div>
            <div class="text-sm text-emerald-100/70">Kelola kegiatan akademik (UJIAN, INPUT NILAI, KRS, dsb).</div>
        </div>
        <a href="{{ route('admin.kalender-akademik.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Tambah</span>
        </a>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('admin.kalender-akademik.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari judul/kategori..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('admin.kalender-akademik.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3">Kategori</th>
                        <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($events as $event)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $event->judul }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                @php
                                    $mulai = $event->tanggal_mulai?->format('d/m/Y');
                                    $selesai = $event->tanggal_selesai?->format('d/m/Y');
                                @endphp
                                {{ $mulai }}@if ($selesai && $selesai !== $mulai) - {{ $selesai }}@endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $event->kategori ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kalender-akademik.edit', $event) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-pen"></i>
                                        <span class="text-sm font-medium">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kalender-akademik.destroy', $event) }}" data-confirm="Hapus kegiatan ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition text-red-100">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span class="text-sm font-medium">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $events->links() }}
    </div>
</x-portal-layout>

