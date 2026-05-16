<x-portal-layout :title="'Bimbingan Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Bimbingan Skripsi</div>
            <div class="text-sm text-emerald-100/70">Daftar mahasiswa bimbingan sesuai SK.</div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3">
        @forelse ($items as $row)
            @php
                $latest = $row->latestMessage;
                $lastRead = $row->dosen_last_read_at;
                $hasUnread = $latest
                    && (int) $latest->sender_user_id !== (int) auth()->id()
                    && (! $lastRead || $latest->created_at?->gt($lastRead));
            @endphp
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-base font-semibold">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                        <div class="mt-2 text-sm text-emerald-100/85">{{ $row->judul }}</div>
                    </div>
                    <a href="{{ route('dosen.skripsi.bimbingan.show', $row) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                        <span style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px;">
                            <i class="fa-solid fa-comments"></i>
                            @if ($hasUnread)
                                <span style="position: absolute; top: -4px; right: -6px; width: 10px; height: 10px; border-radius: 999px; background: #ef4444; border: 2px solid rgba(3, 105, 70, 0.85);"></span>
                            @endif
                        </span>
                        <span class="text-sm font-medium">Buka</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Belum ada skripsi yang ditetapkan untuk dosen ini.
            </div>
        @endforelse
    </div>
</x-portal-layout>
