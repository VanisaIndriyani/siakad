<a href="{{ route('dosen.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.dashboard') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-house text-emerald-300"></i>
    <span class="text-sm font-medium">Dashboard</span>
</a>
<a href="{{ route('dosen.mahasiswa.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.mahasiswa.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-users text-emerald-300"></i>
    <span class="text-sm font-medium">Daftar Mahasiswa</span>
</a>
<a href="{{ route('dosen.nilai.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.nilai.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-pen-to-square text-emerald-300"></i>
    <span class="text-sm font-medium">Input Nilai</span>
</a>
<a href="{{ route('dosen.mata-kuliah.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.mata-kuliah.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-book-open text-emerald-300"></i>
    <span class="text-sm font-medium">Mata Kuliah</span>
</a>
<a href="{{ route('dosen.absensi.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.absensi.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-calendar-check text-emerald-300"></i>
    <span class="text-sm font-medium">Absensi</span>
</a>
<a href="{{ route('dosen.profil') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.profil*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-id-card text-emerald-300"></i>
    <span class="text-sm font-medium">Profil</span>
</a>
<a href="{{ route('dosen.kalender.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.kalender.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-calendar-days text-emerald-300"></i>
    <span class="text-sm font-medium">Kalender Akademik</span>
</a>
<a href="{{ route('dosen.skripsi.bimbingan.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.skripsi.bimbingan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-graduation-cap text-emerald-300"></i>
    <span class="text-sm font-medium">Bimbingan Skripsi</span>
    @php
        $unreadSkripsiCount = auth()->user()->unreadSkripsiCount();
    @endphp
    @if ($unreadSkripsiCount > 0)
        <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
            {{ $unreadSkripsiCount }}
        </span>
    @endif
</a>
<a href="{{ route('dosen.ppl.bimbingan.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.ppl.bimbingan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-briefcase text-emerald-300"></i>
    <span class="text-sm font-medium">Bimbingan PPL</span>
    @php
        $unreadPplCount = auth()->user()->unreadPplCount();
    @endphp
    @if ($unreadPplCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
                {{ $unreadPplCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('dosen.kkn.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.kkn.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-tent text-emerald-300"></i>
        <span class="text-sm font-medium">Bimbingan KKN</span>
    </a>
    <a href="{{ route('dosen.publikasi-kk.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.publikasi-kk.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-newspaper text-emerald-300"></i>
        <span class="text-sm font-medium">Publikasi KK</span>
    </a>
@php
    $dosen = auth()->user()?->dosen;
    $hasProdi = !empty($dosen?->program_studi);
    $statusAkademik = (string) ($dosen?->status_akademik ?? '');
    $isProdiManager = in_array($statusAkademik, ['Ketua Prodi', 'Sekretaris Prodi'], true);
@endphp
@if ($isProdiManager)
    <a href="{{ route('dosen.krs.approval') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.krs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-circle-check text-emerald-300"></i>
        <span class="text-sm font-medium">Approve KRS</span>
    </a>
    <a href="{{ route('dosen.skripsi-pengajuan.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.skripsi-pengajuan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-graduation-cap text-emerald-300"></i>
        <span class="text-sm font-medium">Skripsi</span>
        @php
            $unreadSkripsiProdiCount = auth()->user()->unreadSkripsiProdiCount();
        @endphp
        @if ($unreadSkripsiProdiCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
                {{ $unreadSkripsiProdiCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('dosen.ppl-pengajuan.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.ppl-pengajuan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-briefcase text-emerald-300"></i>
        <span class="text-sm font-medium">PPL</span>
        @php
            $unreadPplProdiCount = auth()->user()->unreadPplProdiCount();
        @endphp
        @if ($unreadPplProdiCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
                {{ $unreadPplProdiCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('dosen.laporan.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.laporan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-comments text-emerald-300"></i>
        <span class="text-sm font-medium">Laporan</span>
        @php
            $unreadLaporanProdiCount = auth()->user()->unreadLaporanProdiCount();
        @endphp
        @if ($unreadLaporanProdiCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
                {{ $unreadLaporanProdiCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('dosen.cuti.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.cuti.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-user-clock text-emerald-300"></i>
        <span class="text-sm font-medium">Pengajuan Cuti</span>
        @php
            $pendingCutiProdiCount = auth()->user()->pendingCutiCount();
        @endphp
        @if ($pendingCutiProdiCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
                {{ $pendingCutiProdiCount }}
            </span>
        @endif
    </a>
@endif
