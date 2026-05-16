<x-portal-layout :title="'Kalender Akademik - '.config('app.name')" subtitle="Kalender Akademik">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Kalender Akademik</div>
            <div class="text-sm text-emerald-100/70">Lihat semua kegiatan akademik.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('dosen.kalender.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari judul/kategori..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('dosen.kalender.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3">
        @forelse ($events as $event)
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-base font-semibold">{{ $event->judul }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">
                            @php
                                $mulai = $event->tanggal_mulai?->format('d/m/Y');
                                $selesai = $event->tanggal_selesai?->format('d/m/Y');
                            @endphp
                            {{ $mulai }}@if ($selesai && $selesai !== $mulai) - {{ $selesai }}@endif
                            @if ($event->kategori)
                                • {{ $event->kategori }}
                            @endif
                        </div>
                    </div>
                </div>
                @if ($event->deskripsi)
                    <div class="mt-3 text-sm text-emerald-100/80 whitespace-pre-line">{{ $event->deskripsi }}</div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Belum ada kegiatan kalender akademik.
            </div>
        @endforelse
    </div>
</x-portal-layout>

