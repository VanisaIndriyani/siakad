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
