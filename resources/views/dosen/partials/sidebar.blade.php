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
</a>
<a href="{{ route('dosen.ppl.bimbingan.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.ppl.bimbingan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
    <i class="fa-solid fa-briefcase text-emerald-300"></i>
    <span class="text-sm font-medium">Bimbingan PPL</span>
</a>
@php
    $dosen = auth()->user()?->dosen;
    $isProdiApprover = in_array((string) ($dosen?->status_akademik ?? ''), ['Ketua Prodi', 'Sekretaris Prodi'], true);
@endphp
@if ($isProdiApprover)
    <a href="{{ route('dosen.krs.approval') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.krs.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-circle-check text-emerald-300"></i>
        <span class="text-sm font-medium">Approve KRS</span>
    </a>
    <a href="{{ route('dosen.skripsi-pengajuan.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.skripsi-pengajuan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-file-signature text-emerald-300"></i>
        <span class="text-sm font-medium">Acc Judul Skripsi</span>
    </a>
    <a href="{{ route('dosen.ppl-pengajuan.index') }}"
       class="flex items-center gap-3 px-4 py-3 rounded-xl border transition {{ request()->routeIs('dosen.ppl-pengajuan.*') ? 'bg-white/10 border-white/10' : 'border-transparent hover:bg-white/5 hover:border-white/10' }}">
        <i class="fa-solid fa-building-columns text-emerald-300"></i>
        <span class="text-sm font-medium">Acc PPL</span>
    </a>
@endif
