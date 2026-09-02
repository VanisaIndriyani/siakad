<x-portal-layout :title="'Sertifikat Saya - '.config('app.name')" subtitle="Kumpulan Sertifikat Kegiatan">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold flex items-center gap-2">
                <i class="fa-solid fa-certificate text-sky-300"></i>
                Sertifikat Saya
            </div>
            <div class="text-sm text-emerald-100/70">Kumpulan sertifikat dari seluruh kegiatan yang telah Anda ikuti dan hadiri.</div>
        </div>
        <a href="{{ route('mahasiswa.kegiatan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
            <i class="fa-solid fa-calendar"></i>
            Lihat Kegiatan
        </a>
    </div>

    @if($pesertas->isEmpty())
        <div class="rounded-2xl bg-white/5 border border-white/10 p-12 text-center">
            <i class="fa-solid fa-certificate text-5xl mb-4 opacity-30 text-sky-300"></i>
            <div class="text-lg font-medium mb-2">Anda Belum Memiliki Sertifikat</div>
            <div class="text-sm text-emerald-100/60 mb-4">Ikuti kegiatan kampus dan hadir untuk mendapatkan sertifikat.</div>
            <a href="{{ route('mahasiswa.kegiatan.index') }}" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                <i class="fa-solid fa-list"></i>
                Lihat Daftar Kegiatan
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($pesertas as $p)
                <div class="rounded-2xl overflow-hidden bg-gradient-to-br from-white/[0.07] to-white/[0.03] border border-white/10 hover:border-sky-500/30 transition group">
                    <div class="relative h-40 bg-gradient-to-br from-emerald-700/50 via-sky-700/50 to-purple-700/50 flex items-center justify-center overflow-hidden">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>
                        <div class="absolute top-3 left-3 w-8 h-8 border-t-4 border-l-4 border-amber-300/70"></div>
                        <div class="absolute top-3 right-3 w-8 h-8 border-t-4 border-r-4 border-amber-300/70"></div>
                        <div class="absolute bottom-3 left-3 w-8 h-8 border-b-4 border-l-4 border-amber-300/70"></div>
                        <div class="absolute bottom-3 right-3 w-8 h-8 border-b-4 border-r-4 border-amber-300/70"></div>
                        <div class="relative text-center z-10">
                            <i class="fa-solid fa-file-certificate text-5xl text-white/80 mb-2 block"></i>
                            <div class="text-[10px] uppercase tracking-widest text-white/70 font-bold">Sertifikat</div>
                        </div>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <div class="text-[10px] uppercase tracking-wide text-emerald-100/50 mb-0.5">{{ $p->kegiatan?->jenis_kegiatan ?? 'Kegiatan' }}</div>
                            <div class="font-semibold text-sm leading-tight line-clamp-2">{{ $p->kegiatan?->judul ?? '-' }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px] text-emerald-100/70">
                            <div>
                                <div class="text-emerald-100/40 mb-0.5">Tanggal</div>
                                <div class="font-medium">{{ $p->kegiatan?->tanggal_kegiatan?->format('d M Y') ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-emerald-100/40 mb-0.5">Diunduh</div>
                                <div class="font-medium">
                                    @if($p->sertifikat_diunduh_at)
                                        {{ $p->sertifikat_diunduh_at?->format('d/m/y') }}
                                    @else
                                        <span class="text-amber-300">Belum</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-white/10 space-y-1.5">
                            <div class="text-[10px] uppercase tracking-wide text-sky-300/80">Nomor Sertifikat</div>
                            <div class="font-mono text-xs text-sky-200 bg-sky-500/10 border border-sky-500/20 rounded-lg px-3 py-1.5">
                                {{ $p->nomor_sertifikat ?? '-' }}
                            </div>
                        </div>
                        <a href="{{ route('mahasiswa.kegiatan.download-sertifikat', $p->kegiatan) }}" class="w-full h-10 inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-500 active:bg-sky-700 transition text-sm font-medium">
                            <i class="fa-solid fa-download"></i>
                            {{ $p->sertifikat_diunduh_at ? 'Download Ulang' : 'Download Sertifikat' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($pesertas->hasPages())
            <div class="mt-5 px-4 py-4 rounded-2xl bg-white/5 border border-white/10">
                {{ $pesertas->links() }}
            </div>
        @endif
    @endif
</x-portal-layout>
