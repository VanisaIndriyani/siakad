<a href="{{ route('mahasiswa.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-house text-emerald-300"></i>
    <span class="text-sm font-medium">Dashboard</span>
</a>
<a href="{{ route('mahasiswa.profil') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.profil*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-id-card text-emerald-300"></i>
    <span class="text-sm font-medium">Profil</span>
</a>
<a href="{{ route('mahasiswa.kalender.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.kalender.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-calendar-days text-emerald-300"></i>
    <span class="text-sm font-medium">Kalender Akademik</span>
</a>
<a href="{{ route('mahasiswa.skripsi.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.skripsi.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-graduation-cap text-emerald-300"></i>
    <span class="text-sm font-medium">Skripsi</span>
    @php
        $unreadSkripsiCount = auth()->user()->unreadSkripsiCount();
    @endphp
    @if ($unreadSkripsiCount > 0)
        <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
            {{ $unreadSkripsiCount }}
        </span>
    @endif
</a>
<a href="{{ route('mahasiswa.skripsi-files.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.skripsi-files.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-upload text-emerald-300"></i>
    <span class="text-sm font-medium">Upload File Skripsi</span>
</a>
<a href="{{ route('mahasiswa.ppl.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.ppl.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-briefcase text-emerald-300"></i>
    <span class="text-sm font-medium">PPL</span>
    @php
        $unreadPplCount = auth()->user()->unreadPplCount();
    @endphp
    @if ($unreadPplCount > 0)
        <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
            {{ $unreadPplCount }}
        </span>
    @endif
</a>
<a href="{{ route('mahasiswa.ppl-files.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.ppl-files.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-upload text-emerald-300"></i>
    <span class="text-sm font-medium">Upload Laporan PPL</span>
</a>
<a href="{{ route('mahasiswa.kkn.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.kkn.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-tent text-emerald-300"></i>
    <span class="text-sm font-medium">KKN</span>
</a>
<a href="{{ route('mahasiswa.laporan.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.laporan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-comments text-emerald-300"></i>
    <span class="text-sm font-medium">Lapor Pengajuan</span>
    @php
        $unreadLaporanCount = auth()->user()->unreadLaporanCount();
    @endphp
    @if ($unreadLaporanCount > 0)
        <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-rose-500/15 border border-rose-500/25 text-rose-100">
            {{ $unreadLaporanCount }}
        </span>
    @endif
</a>
<a href="{{ route('mahasiswa.krs.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.krs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-clipboard-list text-emerald-300"></i>
    <span class="text-sm font-medium">KRS</span>
</a>
<a href="{{ route('mahasiswa.khs.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.khs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-award text-emerald-300"></i>
    <span class="text-sm font-medium">KHS</span>
</a>
<a href="{{ route('mahasiswa.absensi.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.absensi.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-calendar-check text-emerald-300"></i>
    <span class="text-sm font-medium">Absensi</span>
</a>
<a href="{{ route('mahasiswa.pembayaran.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.pembayaran.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-money-bill-wave text-emerald-300"></i>
    <span class="text-sm font-medium">Pembayaran</span>
</a>
<a href="{{ route('mahasiswa.cuti.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.cuti.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-user-clock text-emerald-300"></i>
    <span class="text-sm font-medium">Pengajuan Cuti</span>
</a>
<a href="{{ route('mahasiswa.biodata.pdf') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition border-transparent hover:bg-white/5 hover:border-white/10">
    <i class="fa-solid fa-file-pdf text-emerald-300"></i>
    <span class="text-sm font-medium">Download Biodata</span>
</a>
