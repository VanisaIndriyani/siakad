<x-portal-layout :title="'Detail Pengajuan Cuti - '.config('app.name')" subtitle="Detail Pengajuan Cuti">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail Pengajuan Cuti</div>
            <div class="text-sm text-emerald-100/70">
                Semester {{ $cuti->semester }} • {{ $cuti->tahun_ajaran }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if ($cuti->status === 'approved')
                <a href="{{ route('mahasiswa.cuti.pdf', $cuti) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-500/20 transition text-emerald-100">
                    <i class="fa-solid fa-file-pdf"></i>
                    Cetak Surat Cuti
                </a>
            @endif
            <a href="{{ route('mahasiswa.cuti.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <div class="text-lg font-semibold mb-4">Informasi Pengajuan</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Tahun Ajaran</div>
                        <div class="mt-1 text-white font-medium">{{ $cuti->tahun_ajaran }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Semester</div>
                        <div class="mt-1 text-white font-medium">Semester {{ $cuti->semester }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Alasan Cuti</div>
                        <div class="mt-1 text-white leading-relaxed">{{ $cuti->alasan }}</div>
                    </div>
                </div>
            </div>

            @if ($cuti->catatan_prodi || $cuti->catatan_admin)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-6">
                    <div class="text-lg font-semibold">Tanggapan</div>
                    @if ($cuti->catatan_prodi)
                        <div>
                            <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Catatan Kaprodi / Sekprodi:</div>
                            <div class="mt-2 p-4 rounded-xl bg-white/5 border border-white/10 text-sm text-emerald-100/90 whitespace-pre-line">
                                {{ $cuti->catatan_prodi }}
                            </div>
                        </div>
                    @endif
                    @if ($cuti->catatan_admin)
                        <div>
                            <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Catatan Admin:</div>
                            <div class="mt-2 p-4 rounded-xl bg-white/5 border border-white/10 text-sm text-emerald-100/90 whitespace-pre-line">
                                {{ $cuti->catatan_admin }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <div class="text-lg font-semibold mb-4">Status Approval</div>
                <div class="space-y-4">
                    @php
                        $statusBadge = match ($cuti->status) {
                            'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                            'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                            default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                        };
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-emerald-100/70">Status Akhir:</span>
                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $statusBadge }}">
                            {{ strtoupper($cuti->status) }}
                        </span>
                    </div>

                    <div class="pt-4 border-t border-white/10 space-y-4">
                        <div>
                            <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider mb-2">Prodi (Kaprodi/Sekprodi)</div>
                            @if ($cuti->approvedByProdi)
                                <div class="flex items-center gap-2 text-sm text-emerald-400">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Sudah diverifikasi</span>
                                </div>
                                <div class="mt-1 text-xs text-emerald-100/50">Oleh: {{ $cuti->approvedByProdi->name }}</div>
                            @else
                                <div class="flex items-center gap-2 text-sm text-yellow-400">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Menunggu verifikasi</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider mb-2">Administrasi (Admin)</div>
                            @if ($cuti->approvedByAdmin)
                                <div class="flex items-center gap-2 text-sm text-emerald-400">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Sudah diverifikasi</span>
                                </div>
                                <div class="mt-1 text-xs text-emerald-100/50">Oleh: {{ $cuti->approvedByAdmin->name }}</div>
                            @else
                                <div class="flex items-center gap-2 text-sm text-yellow-400">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Menunggu verifikasi</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if ($cuti->status === 'pending')
                <div class="rounded-2xl bg-red-500/10 border border-red-500/20 p-6">
                    <div class="text-sm text-red-200/80 mb-4">Ingin membatalkan pengajuan ini?</div>
                    <form method="POST" action="{{ route('mahasiswa.cuti.destroy', $cuti) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan cuti ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full h-10 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-100 transition text-sm font-medium">
                            Batalkan Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
