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
</a>
<a href="{{ route('mahasiswa.ppl.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('mahasiswa.ppl.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-briefcase text-emerald-300"></i>
    <span class="text-sm font-medium">PPL</span>
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
<a href="{{ route('mahasiswa.biodata.pdf') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition border-transparent hover:bg-white/5 hover:border-white/10">
    <i class="fa-solid fa-file-pdf text-emerald-300"></i>
    <span class="text-sm font-medium">Download Biodata</span>
</a>
