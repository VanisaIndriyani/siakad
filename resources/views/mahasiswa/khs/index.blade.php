<x-portal-layout :title="'KHS - '.config('app.name')" subtitle="KHS Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div>
        <div class="text-xl font-semibold">KHS</div>
        <div class="text-sm text-emerald-100/70">Lihat hasil studi per semester.</div>
    </div>

    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @forelse ($khs as $row)
            <a href="{{ route('mahasiswa.khs.show', $row) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-4 hover:bg-white/10 transition">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm text-emerald-100/70">Semester</div>
                        <div class="text-xl font-semibold">Semester {{ $row->semester }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">MK: <span class="text-emerald-100/90 font-medium">{{ $row->items_count }}</span></div>
                        <div class="mt-2 text-sm text-emerald-100/70">IPS: <span class="text-emerald-100/90 font-medium">{{ $row->ips ?? '-' }}</span> • IPK: <span class="text-emerald-100/90 font-medium">{{ $row->ipk ?? '-' }}</span></div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-400/20 flex items-center justify-center group-hover:bg-emerald-500/15 transition">
                        <i class="fa-solid fa-chevron-right text-emerald-200"></i>
                    </div>
                </div>
                <div class="mt-3 text-xs text-emerald-100/60">Klik untuk lihat detail KHS</div>
            </a>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 text-center text-emerald-100/70 sm:col-span-2 lg:col-span-4">
                Belum ada KHS.
            </div>
        @endforelse
    </div>
</x-portal-layout>
