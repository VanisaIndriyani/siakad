<x-portal-layout :title="'Informasi Dosen - '.config('app.name')" subtitle="Informasi">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Informasi</div>
            <div class="text-sm text-emerald-100/70">Lihat pengumuman dan informasi aktif untuk dosen.</div>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="rounded-2xl border border-white/10 bg-white/5 px-5 py-12 text-center text-emerald-100/60">
            Belum ada informasi aktif.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            @foreach($items as $item)
                <div class="rounded-3xl bg-white/5 border border-white/10 overflow-hidden shadow-sm">
                    @if(!empty($item->gambar_url))
                        <a href="{{ $item->share_url }}" target="_blank" rel="noopener" class="block bg-black/20">
                            <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="w-full h-64 object-contain" />
                        </a>
                    @endif

                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-lg font-semibold text-white break-words">{{ $item->judul }}</div>
                                <div class="mt-1 text-xs text-emerald-100/50">
                                    @if($item->created_at) Diterbitkan: {{ $item->created_at->format('d M Y') }} @endif
                                </div>
                            </div>
                            <span class="shrink-0 inline-flex items-center gap-2 h-8 px-3 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-100 text-xs font-medium">
                                <i class="fa-solid fa-bullhorn"></i>
                                Aktif
                            </span>
                        </div>

                        @if(!empty($item->deskripsi))
                            <div class="mt-4 text-sm leading-relaxed text-emerald-100/85 whitespace-pre-wrap">{{ $item->deskripsi }}</div>
                        @endif

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <a href="{{ $item->share_url }}" target="_blank" rel="noopener" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 transition text-sm font-medium">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Buka
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($item->judul . ' - ' . $item->share_url) }}" target="_blank" rel="noopener" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-green-600/90 hover:bg-green-500 border border-green-400/30 transition text-sm font-medium text-white">
                                <i class="fa-brands fa-whatsapp"></i>
                                Share
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5">
            {{ $items->links() }}
        </div>
    @endif
</x-portal-layout>
