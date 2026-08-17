<x-portal-layout :title="'Daftar Hadir KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Daftar Hadir KKN</div>
            <div class="text-sm text-emerald-100/70">
                {{ $kkn->posko?->nama_posko ?: 'KKN' }} • {{ $kkn->mahasiswa?->nama_lengkap }} ({{ $kkn->mahasiswa?->npm }})
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('mahasiswa.kkn.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <a href="{{ route('mahasiswa.kkn.absensi.pdf', $kkn) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-blue-500/15 hover:bg-blue-500/20 border border-blue-500/20 text-blue-100 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">Cetak PDF</span>
            </a>
            <a href="{{ route('mahasiswa.kkn.absensi.create', $kkn) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Tambah Absensi</span>
            </a>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="rounded-2xl bg-emerald-500/10 border border-emerald-500/20 p-4">
            <div class="text-xs font-semibold text-emerald-200/80 uppercase tracking-wider">HADIR</div>
            <div class="mt-1 text-2xl font-bold text-emerald-100">{{ $totals['hadir'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-blue-500/10 border border-blue-500/20 p-4">
            <div class="text-xs font-semibold text-blue-200/80 uppercase tracking-wider">IZIN</div>
            <div class="mt-1 text-2xl font-bold text-blue-100">{{ $totals['izin'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-yellow-500/10 border border-yellow-500/20 p-4">
            <div class="text-xs font-semibold text-yellow-200/80 uppercase tracking-wider">SAKIT</div>
            <div class="mt-1 text-2xl font-bold text-yellow-100">{{ $totals['sakit'] ?? 0 }}</div>
        </div>
        <div class="rounded-2xl bg-red-500/10 border border-red-500/20 p-4">
            <div class="text-xs font-semibold text-red-200/80 uppercase tracking-wider">ALPHA</div>
            <div class="mt-1 text-2xl font-bold text-red-100">{{ $totals['alpha'] ?? 0 }}</div>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3">
        @forelse ($absensis as $absensi)
            @php
                $badge = match ($absensi->status) {
                    'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                };
                $khBadge = match ($absensi->status_kehadiran) {
                    'hadir' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'izin' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
                    'sakit' => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                    default => 'bg-red-500/15 border-red-500/20 text-red-100',
                };
            @endphp
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="text-base font-semibold"><i class="fa-regular fa-calendar"></i> {{ $absensi->tanggal?->format('d F Y') }}</div>
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $khBadge }}">
                                {{ strtoupper($absensi->status_kehadiran) }}
                            </span>
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                STATUS: {{ strtoupper($absensi->status) }}
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-emerald-100/70">
                            <i class="fa-regular fa-clock"></i> Masuk: {{ $absensi->jam_masuk ?? '-' }} • Pulang: {{ $absensi->jam_pulang ?? '-' }}
                        </div>
                        @if($absensi->keterangan)
                            <div class="mt-2 text-sm text-emerald-100/80 whitespace-pre-wrap"><span class="font-semibold">Keterangan:</span> {{ $absensi->keterangan }}</div>
                        @endif
                        @if($absensi->catatan_pembimbing)
                            <div class="mt-3 rounded-xl bg-blue-500/10 border border-blue-500/20 p-3">
                                <div class="text-xs font-semibold text-blue-200 mb-1"><i class="fa-solid fa-comment"></i> Catatan Pembimbing</div>
                                <div class="text-sm text-blue-100/90 whitespace-pre-wrap">{{ $absensi->catatan_pembimbing }}</div>
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('mahasiswa.kkn.absensi.edit', [$kkn, $absensi]) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                            <i class="fa-solid fa-pen"></i>
                            <span class="font-medium">Edit</span>
                        </a>
                        <form method="POST" action="{{ route('mahasiswa.kkn.absensi.destroy', [$kkn, $absensi]) }}" onsubmit="return confirm('Hapus absensi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/20 text-red-100 transition text-sm">
                                <i class="fa-solid fa-trash"></i>
                                <span class="font-medium">Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Belum ada daftar hadir.
            </div>
        @endforelse
    </div>
</x-portal-layout>
