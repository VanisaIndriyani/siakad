<x-portal-layout :title="'Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Skripsi</div>
            <div class="text-sm text-emerald-100/70">Pengajuan judul ke Admin/Prodi dan bimbingan online.</div>
        </div>
        <a href="{{ route('mahasiswa.skripsi.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Ajukan Judul</span>
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3">
        @forelse ($items as $row)
            @php
                $badge = match ($row->status) {
                    'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                };
            @endphp
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0">
                        <div class="text-base font-semibold">{{ $row->judul }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">
                            Status:
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                {{ strtoupper($row->status) }}
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-emerald-100/80">
                            Pembimbing:
                            <span class="font-medium">{{ $row->dosenPembimbing?->nama_lengkap ?: '-' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('mahasiswa.skripsi.show', $row) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-eye"></i>
                            <span class="text-sm font-medium">Detail</span>
                        </a>
                        @if ($row->dosen_pembimbing_id)
                            <a href="{{ route('mahasiswa.skripsi.bimbingan', $row) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition">
                                <i class="fa-solid fa-comments"></i>
                                <span class="text-sm font-medium">Bimbingan</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Belum ada pengajuan skripsi.
            </div>
        @endforelse
    </div>
</x-portal-layout>

