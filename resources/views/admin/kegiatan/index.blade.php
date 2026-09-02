<x-portal-layout :title="'Kegiatan - '.config('app.name')" subtitle="Kelola Pemberitahuan Kegiatan (Seminar, Workshop, dll)">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Kegiatan</div>
            <div class="text-sm text-emerald-100/70">Kelola pemberitahuan kegiatan seperti seminar, workshop, pelatihan, dan lainnya. Kelola peserta, daftar hadir, dan sertifikat.</div>
        </div>
        <a href="{{ route('admin.kegiatan.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
            <i class="fa-solid fa-plus"></i>
            Tambah Kegiatan
        </a>
    </div>

    <form method="GET" action="{{ route('admin.kegiatan.index') }}" class="rounded-2xl bg-white/5 border border-white/10 p-4 mb-5">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div>
                <label class="text-sm text-emerald-100/80">Cari</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="Judul / lokasi / penyelenggara..." class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Jenis Kegiatan</label>
                <select name="jenis" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="">Semua</option>
                    @foreach($jenisList as $j)
                        <option value="{{ $j }}" @selected($jenis === $j)>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Status</label>
                <select name="status" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="">Semua</option>
                    <option value="published" @selected($status === 'published')>Published</option>
                    <option value="draft" @selected($status === 'draft')>Draft</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 transition">
                    Cari
                </button>
                <a href="{{ route('admin.kegiatan.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
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
                        <th class="text-left font-medium px-4 py-3">Kegiatan</th>
                        <th class="text-left font-medium px-4 py-3">Jenis</th>
                        <th class="text-left font-medium px-4 py-3">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3">Lokasi</th>
                        <th class="text-left font-medium px-4 py-3">Peserta</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $item)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 text-emerald-100/80">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $item->judul }}</div>
                                @if(!empty($item->penyelenggara))
                                    <div class="text-xs text-emerald-100/60 mt-0.5">{{ $item->penyelenggara }}</div>
                                @endif
                                @if($item->sertifikat_aktif)
                                    <span class="inline-flex items-center gap-1 px-2 h-6 mt-1 rounded-full text-[11px] font-medium bg-sky-500/15 text-sky-200 border border-sky-500/20">
                                        <i class="fa-solid fa-certificate text-[10px]"></i>
                                        Ada Sertifikat
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 h-7 rounded-full text-xs font-medium bg-purple-500/15 text-purple-200 border border-purple-500/20">
                                    {{ $item->jenis_kegiatan }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80 text-xs">
                                <div>{{ $item->tanggal_kegiatan?->format('d M Y') }}</div>
                                @if($item->waktu_mulai)
                                    <div class="text-emerald-100/60 mt-0.5">{{ substr($item->waktu_mulai,0,5) }} - {{ $item->waktu_selesai ? substr($item->waktu_selesai,0,5).' WIB' : '' }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80 text-xs">
                                {{ $item->lokasi ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80 text-xs">
                                <div><i class="fa-solid fa-users mr-1"></i> {{ $item->peserta_count }} total</div>
                                <div class="text-emerald-100/60 mt-0.5"><i class="fa-solid fa-check mr-1"></i> {{ $item->peserta_hadir_count }} hadir</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->is_published)
                                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-white/5 text-emerald-100/60 border border-white/10">
                                        <i class="fa-solid fa-eye-slash text-[10px]"></i>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <a href="{{ route('admin.kegiatan.show', $item) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-emerald-200" title="Detail & Peserta">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kegiatan.toggle-publish', $item) }}" onsubmit="return confirm('Ubah status publish kegiatan ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-yellow-200" title="Toggle Publish">
                                            <i class="fa-solid fa-bullhorn"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.kegiatan.edit', $item) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-emerald-200" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kegiatan.destroy', $item) }}" onsubmit="return confirm('Hapus kegiatan ini beserta seluruh data pesertanya?')">
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
                            <td colspan="8" class="px-4 py-12 text-center text-emerald-100/60">
                                <i class="fa-solid fa-calendar-xmark text-3xl mb-3 opacity-40"></i>
                                <div>Belum ada kegiatan.</div>
                                <a href="{{ route('admin.kegiatan.create') }}" class="inline-flex items-center gap-2 mt-4 text-emerald-300 hover:underline">
                                    <i class="fa-solid fa-plus"></i> Buat kegiatan pertama
                                </a>
                            </td>
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
