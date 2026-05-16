<a href="{{ auth()->user()->role === 'keuangan' ? route('keuangan.dashboard') : route('admin.dashboard') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('*.dashboard') || request()->fullUrl() == route('keuangan.dashboard') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-chart-line text-emerald-300"></i>
    <span class="text-sm font-medium">Dashboard</span>
</a>

@if (auth()->user()->role === 'admin')
    <a href="{{ route('admin.mahasiswa.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.mahasiswa.*') && request()->get('status') !== 'Alumni' ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-user-graduate text-emerald-300"></i>
        <span class="text-sm font-medium">Mahasiswa</span>
    </a>
    <a href="{{ route('admin.mahasiswa.index', ['status' => 'Alumni']) }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.mahasiswa.*') && request()->get('status') === 'Alumni' ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-user-check text-emerald-300"></i>
        <span class="text-sm font-medium">Alumni</span>
    </a>
    <a href="{{ route('admin.dosen.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.dosen.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-chalkboard-user text-emerald-300"></i>
        <span class="text-sm font-medium">Dosen</span>
    </a>
    <a href="{{ route('admin.mata-kuliah.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.mata-kuliah.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-book-open text-emerald-300"></i>
        <span class="text-sm font-medium">Mata Kuliah</span>
    </a>
    @php
        $pendingKrsCount = cache()->remember('admin_pending_krs_count', 10, function () {
            return \App\Models\Krs::query()->where('status_approval', 'pending')->count();
        });
    @endphp
    <a href="{{ route('admin.krs.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.krs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-clipboard-list text-emerald-300"></i>
        <span class="text-sm font-medium">KRS</span>
        @if ($pendingKrsCount > 0)
            <span class="ml-auto inline-flex items-center justify-center min-w-7 h-7 px-2 rounded-full text-xs font-semibold bg-yellow-500/15 border border-yellow-500/25 text-yellow-100">
                {{ $pendingKrsCount }}
            </span>
        @endif
    </a>
    <a href="{{ route('admin.khs.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.khs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-award text-emerald-300"></i>
        <span class="text-sm font-medium">KHS</span>
    </a>
    <a href="{{ route('admin.absensi.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.absensi.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-calendar-check text-emerald-300"></i>
        <span class="text-sm font-medium">Absensi</span>
    </a>
    <a href="{{ route('admin.kalender-akademik.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.kalender-akademik.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-calendar-days text-emerald-300"></i>
        <span class="text-sm font-medium">Kalender Akademik</span>
    </a>
    <a href="{{ route('admin.skripsi.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.skripsi.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-graduation-cap text-emerald-300"></i>
        <span class="text-sm font-medium">Skripsi</span>
    </a>
    <a href="{{ route('admin.ppl.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('admin.ppl.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-briefcase text-emerald-300"></i>
        <span class="text-sm font-medium">PPL</span>
    </a>
    <a href="{{ route('keuangan.pembayaran.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('keuangan.pembayaran.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-money-bill-wave text-emerald-300"></i>
        <span class="text-sm font-medium">Pembayaran</span>
    </a>
@endif

@if (auth()->user()->role === 'keuangan')
    <a href="{{ route('keuangan.pembayaran.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('keuangan.pembayaran.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-money-bill-wave text-emerald-300"></i>
        <span class="text-sm font-medium">Pembayaran</span>
    </a>
@endif

<a href="{{ route('profile.edit') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('profile.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-user-gear text-emerald-300"></i>
    <span class="text-sm font-medium">Profil</span>
</a>
