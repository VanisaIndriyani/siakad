<x-portal-layout :title="'Detail Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    @php
        $badge = match ($skripsi->status) {
            'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
            'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
            'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
            default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Detail Skripsi</div>
            <div class="mt-1 text-sm text-emerald-100/70">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                    {{ strtoupper($skripsi->status) }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if ($skripsi->dosen_pembimbing_id)
                <a href="{{ route('mahasiswa.skripsi.bimbingan', $skripsi) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                    <i class="fa-solid fa-comments"></i>
                    <span class="text-sm font-medium">Bimbingan</span>
                </a>
            @endif
            <a href="{{ route('mahasiswa.skripsi.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3 max-w-3xl">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Judul</div>
            <div class="mt-1 text-base font-semibold">{{ $skripsi->judul }}</div>
            @if ($skripsi->deskripsi)
                <div class="mt-3 text-sm text-emerald-100/80 whitespace-pre-line">{{ $skripsi->deskripsi }}</div>
            @endif
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-emerald-100/70">Pembimbing</div>
                    <div class="mt-1 font-medium">{{ $skripsi->dosenPembimbing?->nama ?: '-' }}</div>
                </div>
                <div>
                    <div class="text-emerald-100/70">SK Pembimbing</div>
                    <div class="mt-1 font-medium">
                        {{ $skripsi->nomor_sk ?: '-' }}
                        @if ($skripsi->tanggal_sk)
                            • {{ $skripsi->tanggal_sk->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($skripsi->catatan_admin)
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Catatan Admin/Prodi</div>
                <div class="mt-2 text-sm text-emerald-100/85 whitespace-pre-line">{{ $skripsi->catatan_admin }}</div>
            </div>
        @endif
    </div>
</x-portal-layout>
