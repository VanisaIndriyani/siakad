<x-portal-layout :title="'Publikasi KK - '.config('app.name')" subtitle="Publikasi KK">
    <x-slot:sidebar>
        @include($routePrefix . '.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold text-white">Publikasi KK</div>
            <div class="text-sm text-emerald-100/70">Kelola data publikasi (Penelitian, PKM, HAKI, Buku).</div>
        </div>
        <a href="{{ route($routePrefix . '.publikasi-kk.create') }}" class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium inline-flex items-center gap-2 text-white">
            <i class="fa-solid fa-plus"></i>
            Tambah Publikasi
        </a>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">No</th>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Penulis</th>
                        <th class="text-left font-medium px-4 py-3">Kategori</th>
                        <th class="text-left font-medium px-4 py-3">Tahun</th>
                        <th class="text-left font-medium px-4 py-3">Reputasi</th>
                        <th class="text-left font-medium px-4 py-3">File</th>
                        <th class="text-right font-medium px-4 py-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $row)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-3 text-emerald-100/70">{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $row->judul }}</td>
                            <td class="px-4 py-3 text-emerald-100/70">{{ $row->penulis }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    @if($row->kategori == 'Penelitian') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                    @elseif($row->kategori == 'PKM') bg-purple-500/10 text-purple-400 border border-purple-500/20
                                    @elseif($row->kategori == 'HAKI') bg-orange-500/10 text-orange-400 border border-orange-500/20
                                    @else bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @endif">
                                    {{ $row->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/70">{{ $row->tahun_terbit }}</td>
                            <td class="px-4 py-3 text-emerald-100/70 capitalize">{{ $row->reputasi }}</td>
                            <td class="px-4 py-3">
                                @if($row->file_path)
                                    <a href="{{ route($routePrefix . '.publikasi-kk.download', $row) }}" class="text-emerald-400 hover:underline inline-flex items-center gap-1">
                                        <i class="fa-solid fa-file-arrow-down"></i>
                                        Download
                                    </a>
                                @else
                                    <span class="text-emerald-100/30">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route($routePrefix . '.publikasi-kk.edit', $row) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route($routePrefix . '.publikasi-kk.destroy', $row) }}" onsubmit="return confirm('Apakah kamu yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-emerald-100/50">Belum ada data publikasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</x-portal-layout>