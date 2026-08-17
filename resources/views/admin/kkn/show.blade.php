<x-portal-layout :title="'Detail Pendaftaran KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $isAdminView = ($routePrefix ?? 'admin') === 'admin';
        $jurnalUrl = $isAdminView
            ? route('admin.kkn.jurnal.index', $kkn)
            : route('dosen.kkn.jurnal.index', $kkn);
        $absensiUrl = $isAdminView
            ? route('admin.kkn.absensi.index', $kkn)
            : route('dosen.kkn.absensi.index', $kkn);
        $badge = match ($kkn->status) {
            'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
            'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
            default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
        };
    @endphp

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ $isAdminView ? route('admin.kkn.index') : route('dosen.kkn-pengajuan.index') }}" class="text-sm text-emerald-200/70 hover:text-white inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="mt-2 text-xl font-semibold">Detail Pendaftaran KKN</div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $jurnalUrl }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-orange-500/15 hover:bg-orange-500/20 border border-orange-500/20 text-orange-100 transition text-sm font-medium">
                <i class="fa-solid fa-book-open"></i>
                Jurnal
            </a>
            <a href="{{ $absensiUrl }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/20 border border-purple-500/20 text-purple-100 transition text-sm font-medium">
                <i class="fa-solid fa-clipboard-user"></i>
                Daftar Hadir
            </a>
            @if ($canAssign)
                <form method="POST" action="{{ $isAdminView ? route('admin.kkn.destroy', $kkn) : route('dosen.kkn-pengajuan.destroy', $kkn) }}" data-confirm="Hapus data pendaftaran KKN ini?" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition text-red-100 text-sm font-medium">
                        <i class="fa-solid fa-trash"></i>
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm font-semibold mb-3 text-emerald-100">Biodata Mahasiswa</div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">Nama Lengkap</span>
                    <span class="font-medium">{{ $kkn->mahasiswa?->nama_lengkap ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">NPM</span>
                    <span class="font-medium">{{ $kkn->mahasiswa?->npm ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">Program Studi</span>
                    <span class="font-medium">{{ $kkn->mahasiswa?->program_studi ?: '-' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">Status</span>
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                        {{ strtoupper($kkn->status) }}
                    </span>
                </div>
                @if ($kkn->catatan_admin)
                    <div class="flex justify-between py-2">
                        <span class="text-emerald-100/60">Catatan Admin</span>
                        <span class="font-medium max-w-[60%] text-right">{{ $kkn->catatan_admin }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm font-semibold mb-3 text-emerald-100">Posko KKN</div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">Nama Posko</span>
                    <span class="font-medium">{{ $kkn->posko?->nama_posko ?: 'Belum diplot' }}</span>
                </div>
                <div class="flex justify-between border-b border-white/5 py-2">
                    <span class="text-emerald-100/60">Lokasi</span>
                    <span class="font-medium">{{ $kkn->posko?->lokasi ?: '-' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-emerald-100/60">Pembimbing</span>
                    <span class="font-medium text-right">
                        @if ($kkn->posko && $kkn->posko->pembimbingS?->isNotEmpty())
                            @foreach ($kkn->posko->pembimbingS as $p)
                                <div>{{ $p->nama }}</div>
                            @endforeach
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
