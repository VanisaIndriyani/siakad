<x-portal-layout :title="'Informasi - '.config('app.name')" subtitle="Kelola Informasi / Pengumuman">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Informasi</div>
            <div class="text-sm text-emerald-100/70">Kelola pengumuman dan informasi untuk mahasiswa.</div>
        </div>
        <a href="{{ route('admin.informasi.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
            <i class="fa-solid fa-plus"></i>
            Tambah Informasi
        </a>
    </div>

    <form method="GET" action="{{ route('admin.informasi.index') }}" class="rounded-2xl bg-white/5 border border-white/10 p-4 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="text-sm text-emerald-100/80">Cari</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Judul / deskripsi..." class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Status</label>
                <select name="status" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="">Semua</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Non Aktif</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 transition">
                    Cari
                </button>
                <a href="{{ route('admin.informasi.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">#</th>
                        <th class="text-left font-medium px-4 py-3">Gambar</th>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-left font-medium px-4 py-3">Periode</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $item)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/80">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">
                                @if(!empty($item->gambar_url))
                                    <a href="{{ $item->gambar_url }}" target="_blank" rel="noopener" class="inline-block">
                                        <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="h-14 w-24 object-cover rounded-xl border border-white/10" />
                                    </a>
                                @else
                                    <div class="h-14 w-24 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-emerald-100/40 text-xs">
                                        Tidak ada
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $item->judul }}</div>
                                @if(!empty($item->deskripsi))
                                    <div class="text-xs text-emerald-100/60 mt-0.5 line-clamp-2">{{ Str::limit($item->deskripsi, 80) }}</div>
                                @endif
                                <div class="text-xs text-emerald-100/40 mt-1">
                                    Dibuat {{ $item->created_at?->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->is_aktif)
                                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-white/5 text-emerald-100/60 border border-white/10">
                                        <i class="fa-solid fa-circle-pause text-[10px]"></i>
                                        Non Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80 text-xs">
                                @if($item->tanggal_aktif || $item->tanggal_kadaluarsa)
                                    <div>
                                        @if($item->tanggal_aktif) Mulai: {{ $item->tanggal_aktif->format('d M Y') }} @endif
                                    </div>
                                    <div class="text-emerald-100/60">
                                        @if($item->tanggal_kadaluarsa) Selesai: {{ $item->tanggal_kadaluarsa->format('d M Y') }} @endif
                                    </div>
                                @else
                                    <span class="text-emerald-100/50">Selamanya</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!empty($item->gambar_url))
                                        <a href="{{ $item->share_url }}" target="_blank" rel="noopener" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sky-200" title="Lihat halaman share">
                                            <i class="fa-solid fa-share-nodes"></i>
                                        </a>
                                    @endif
                                    <form method="POST" action="{{ route('admin.informasi.toggle', $item) }}" onsubmit="return confirm('Ubah status informasi ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-yellow-200" title="Toggle aktif">
                                            <i class="fa-solid fa-power-off"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.informasi.edit', $item) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-emerald-200" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.informasi.destroy', $item) }}" onsubmit="return confirm('Hapus informasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-rose-500/10 border border-white/10 hover:border-rose-500/20 transition text-rose-200" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-emerald-100/60">Belum ada informasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-4 py-4 border-t border-white/10">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-portal-layout>
