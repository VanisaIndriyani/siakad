<x-portal-layout :title="'Bimbingan KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <div class="text-xl font-semibold text-white uppercase tracking-tight">Bimbingan KKN</div>
            <div class="text-sm text-emerald-100/60 font-medium">Daftar posko KKN yang Anda bimbing.</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($poskos as $posko)
            <div class="group relative rounded-3xl bg-[#0d2a23] border border-white/10 overflow-hidden shadow-xl hover:border-emerald-500/30 transition-all duration-500">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-600/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-tent text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-1">Anggota</div>
                            <div class="text-lg font-black text-white leading-none">{{ $posko->pengajuans->count() }}</div>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-white leading-tight mb-2 group-hover:text-emerald-400 transition-colors duration-500">{{ $posko->nama_posko }}</h3>
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-100/40 uppercase tracking-widest mb-6">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $posko->lokasi ?: 'Lokasi segera ditentukan' }}
                    </div>

                    <div class="space-y-4 pt-6 border-t border-white/5">
                        <div class="flex items-center justify-between text-[10px] font-black text-emerald-100/30 uppercase tracking-widest">
                            <span>Nomor SK</span>
                            <span class="text-emerald-100/60">{{ $posko->nomor_sk ?: '-' }}</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            @foreach ($posko->pengajuans->take(3) as $p)
                                <div class="h-8 w-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-black text-white/40" title="{{ $p->mahasiswa?->nama_lengkap }}">
                                    {{ mb_substr($p->mahasiswa?->nama_lengkap, 0, 1) }}
                                </div>
                            @endforeach
                            @if ($posko->pengajuans->count() > 3)
                                <div class="h-8 px-2 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-[9px] font-black text-white/40">
                                    +{{ $posko->pengajuans->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('dosen.kkn.posko', $posko) }}" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black tracking-[0.2em] uppercase text-xs flex items-center justify-center gap-3 transition-all shadow-lg shadow-emerald-900/40 group-hover:shadow-emerald-900/60">
                            Masuk Bimbingan
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 py-20 text-center">
                <div class="h-24 w-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-campground text-5xl text-white/10"></i>
                </div>
                <h3 class="text-2xl font-black uppercase tracking-widest text-white/30 leading-none">Belum Ada Posko</h3>
                <p class="text-sm text-emerald-100/20 mt-3 font-medium">Anda belum ditugaskan sebagai DPL pada posko KKN manapun.</p>
            </div>
        @endforelse
    </div>
</x-portal-layout>
