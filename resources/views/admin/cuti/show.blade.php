<x-portal-layout :title="'Detail Pengajuan Cuti - '.config('app.name')" subtitle="Detail Pengajuan Cuti">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail Pengajuan Cuti</div>
            <div class="text-sm text-emerald-100/70">{{ $cuti->mahasiswa?->nama_lengkap }} • Semester {{ $cuti->semester }}</div>
        </div>
        <a href="{{ route('admin.cuti.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <div class="text-lg font-semibold mb-4 text-white">Informasi Mahasiswa & Pengajuan</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Nama Mahasiswa</div>
                        <div class="mt-1 text-white font-medium">{{ $cuti->mahasiswa?->nama_lengkap }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">NIM / NPM</div>
                        <div class="mt-1 text-white font-medium">{{ $cuti->mahasiswa?->npm }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Program Studi</div>
                        <div class="mt-1 text-white font-medium">{{ $cuti->mahasiswa?->program_studi }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Semester & Tahun Ajaran</div>
                        <div class="mt-1 text-white font-medium">Semester {{ $cuti->semester }} • {{ $cuti->tahun_ajaran }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Alasan Cuti</div>
                        <div class="mt-1 text-white leading-relaxed">{{ $cuti->alasan }}</div>
                    </div>
                </div>
            </div>

            @if ($cuti->catatan_prodi)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider mb-2">Catatan Prodi (Kaprodi/Sekprodi):</div>
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 text-sm text-emerald-100/90 whitespace-pre-line">
                        {{ $cuti->catatan_prodi }}
                    </div>
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
            <div class="text-lg font-semibold mb-4 text-white">Approval Admin</div>

            <div class="text-sm text-emerald-100/70 space-y-3 mb-6">
                <div class="flex items-center justify-between">
                    <span>Status Saat Ini</span>
                    <span class="font-medium text-white uppercase px-2 py-0.5 rounded-lg bg-white/10 border border-white/10">
                        {{ $cuti->status }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Verifikasi Prodi</span>
                    @if ($cuti->approvedByProdi)
                        <span class="text-emerald-400 font-medium">Sudah Verifikasi</span>
                    @else
                        <span class="text-yellow-400 font-medium">Menunggu</span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.cuti.status', $cuti) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-sm text-emerald-100/80">Ubah Status</label>
                    <select name="status" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" required>
                        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('status', $cuti->status) === $k) class="text-black">{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('status') <div class="mt-1 text-xs text-red-200">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" placeholder="Masukkan catatan atau alasan jika ditolak...">{{ old('catatan_admin', $cuti->catatan_admin) }}</textarea>
                    @error('catatan_admin') <div class="mt-1 text-xs text-red-200">{{ $message }}</div> @enderror
                </div>

                <button class="w-full h-11 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium text-white shadow-lg shadow-emerald-900/20">
                    Update Status Pengajuan
                </button>
            </form>
        </div>
    </div>
</x-portal-layout>
