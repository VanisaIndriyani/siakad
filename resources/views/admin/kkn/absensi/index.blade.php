<x-portal-layout :title="'Daftar Hadir KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $canReview = (bool) ($canReview ?? false);
        $indexUrl = $prefix === 'admin' ? route('admin.kkn.index') : route('dosen.kkn-pengajuan.index');
        $absensiStatusUrl = $prefix === 'admin' ? 'admin.kkn.absensi.status' : 'dosen.kkn.absensi.status';
        $absensiPdfUrl = $prefix === 'admin' ? 'admin.kkn.absensi.pdf' : 'dosen.kkn.absensi.pdf';
        $absensiEditUrl = $prefix === 'admin' ? 'admin.kkn.absensi.edit' : 'dosen.kkn.absensi.edit';
        $absensiDestroyUrl = $prefix === 'admin' ? 'admin.kkn.absensi.destroy' : 'dosen.kkn.absensi.destroy';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Daftar Hadir KKN</div>
            <div class="text-sm text-emerald-100/70">
                {{ $kkn->posko?->nama_posko ?: 'KKN' }} • {{ $kkn->mahasiswa?->nama_lengkap }} ({{ $kkn->mahasiswa?->npm }})
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $indexUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <a href="{{ route($absensiPdfUrl, $kkn) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-blue-500/15 hover:bg-blue-500/20 border border-blue-500/20 text-blue-100 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">Cetak PDF</span>
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
                            <a href="{{ route($absensiEditUrl, [$kkn, $absensi]) }}" class="ml-2 h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs text-white/90">
                                <i class="fa-solid fa-pen"></i>
                                <span class="font-medium">Edit</span>
                            </a>
                            <form method="POST" action="{{ route($absensiDestroyUrl, [$kkn, $absensi]) }}" onsubmit="return confirm('Hapus daftar hadir ini?');" class="m-0 inline-flex">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-red-500/15 hover:bg-red-500/20 border border-red-500/20 text-red-100 transition text-xs">
                                    <i class="fa-solid fa-trash"></i>
                                    <span class="font-medium">Hapus</span>
                                </button>
                            </form>
                        </div>
                        <div class="mt-1 text-sm text-emerald-100/70">
                            <i class="fa-regular fa-clock"></i> Masuk: {{ $absensi->jam_masuk ?? '-' }} • Pulang: {{ $absensi->jam_pulang ?? '-' }}
                        </div>
                        @if($absensi->keterangan)
                            <div class="mt-2 text-sm text-emerald-100/80 whitespace-pre-wrap"><span class="font-semibold">Keterangan:</span> {{ $absensi->keterangan }}</div>
                        @endif
                        @if($absensi->catatan_pembimbing && !$canReview)
                            <div class="mt-3 rounded-xl bg-blue-500/10 border border-blue-500/20 p-3">
                                <div class="text-xs font-semibold text-blue-200 mb-1"><i class="fa-solid fa-comment"></i> Catatan Pembimbing</div>
                                <div class="text-sm text-blue-100/90 whitespace-pre-wrap">{{ $absensi->catatan_pembimbing }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($canReview)
                    <form method="POST" action="{{ route($absensiStatusUrl, [$kkn, $absensi]) }}" class="mt-4 pt-4 border-t border-white/10">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:items-end">
                            <div class="md:col-span-1">
                                <x-input-label value="Verifikasi Status" />
                                <select name="status" required
                                    class="mt-1 w-full h-11 rounded-xl border border-white/10 bg-white/5 text-white px-4 text-sm outline-none transition">
                                    @foreach(['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $v => $l)
                                        <option value="{{ $v }}" class="bg-neutral-900" @selected($absensi->status === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="catatan_{{ $absensi->id }}" value="Catatan Pembimbing (opsional)" />
                                <textarea id="catatan_{{ $absensi->id }}" name="catatan_pembimbing" rows="2"
                                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-2 text-sm outline-none transition resize-y"
                                    placeholder="Tulis catatan untuk mahasiswa...">{{ $absensi->catatan_pembimbing }}</textarea>
                            </div>
                            <div class="md:col-span-1 flex md:justify-end">
                                <button type="submit" class="h-11 w-full md:w-auto px-5 inline-flex items-center gap-2 justify-center rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                                    <i class="fa-solid fa-save"></i>
                                    <span class="text-sm font-medium">Simpan Review</span>
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Mahasiswa belum mengisi daftar hadir.
            </div>
        @endforelse
    </div>
</x-portal-layout>
