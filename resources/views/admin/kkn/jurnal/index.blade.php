<x-portal-layout :title="'Jurnal Kegiatan KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $canReview = (bool) ($canReview ?? false);
        $indexUrl = $prefix === 'admin' ? route('admin.kkn.index') : route('dosen.kkn-pengajuan.index');
        $jurnalStatusUrl = $prefix === 'admin' ? 'admin.kkn.jurnal.status' : 'dosen.kkn.jurnal.status';
        $jurnalPdfUrl = $prefix === 'admin' ? 'admin.kkn.jurnal.pdf' : 'dosen.kkn.jurnal.pdf';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Jurnal Kegiatan KKN</div>
            <div class="text-sm text-emerald-100/70">
                {{ $kkn->posko?->nama_posko ?: 'KKN' }} • {{ $kkn->mahasiswa?->nama_lengkap }} ({{ $kkn->mahasiswa?->npm }})
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $indexUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <a href="{{ route($jurnalPdfUrl, $kkn) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-blue-500/15 hover:bg-blue-500/20 border border-blue-500/20 text-blue-100 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">Cetak PDF</span>
            </a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 gap-3">
        @forelse ($jurnals as $jurnal)
            @php
                $badge = match ($jurnal->status) {
                    'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                };
            @endphp
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <div class="text-base font-semibold">{{ $jurnal->kegiatan }}</div>
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                {{ strtoupper($jurnal->status) }}
                            </span>
                        </div>
                        <div class="mt-1 text-sm text-emerald-100/70">
                            <i class="fa-regular fa-calendar"></i> {{ $jurnal->tanggal?->format('d F Y') }}
                            @if($jurnal->lokasi)
                                • <i class="fa-solid fa-location-dot"></i> {{ $jurnal->lokasi }}
                            @endif
                            @if($jurnal->pihak_terkait)
                                • <i class="fa-solid fa-users"></i> {{ $jurnal->pihak_terkait }}
                            @endif
                        </div>
                        @if($jurnal->deskripsi)
                            <div class="mt-2 text-sm text-emerald-100/80 whitespace-pre-wrap">{{ $jurnal->deskripsi }}</div>
                        @endif
                        @if($jurnal->catatan_pembimbing)
                            <div class="mt-3 rounded-xl bg-blue-500/10 border border-blue-500/20 p-3">
                                <div class="text-xs font-semibold text-blue-200 mb-1"><i class="fa-solid fa-comment"></i> Catatan Pembimbing</div>
                                <div class="text-sm text-blue-100/90 whitespace-pre-wrap">{{ $jurnal->catatan_pembimbing }}</div>
                            </div>
                        @endif

                        @if($canReview)
                            <form method="POST" action="{{ route($jurnalStatusUrl, [$kkn, $jurnal]) }}" class="mt-4 rounded-xl bg-white/5 border border-white/10 p-4">
                                @csrf
                                @method('PATCH')
                                <div class="text-xs font-semibold text-emerald-100/80 mb-2"><i class="fa-solid fa-clipboard-check"></i> Review Jurnal</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <x-input-label for="status_{{ $jurnal->id }}" value="Status" />
                                        <select id="status_{{ $jurnal->id }}" name="status" required
                                            class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition">
                                            <option value="pending" {{ $jurnal->status === 'pending' ? 'selected' : '' }}>PENDING</option>
                                            <option value="approved" {{ $jurnal->status === 'approved' ? 'selected' : '' }}>APPROVED</option>
                                            <option value="rejected" {{ $jurnal->status === 'rejected' ? 'selected' : '' }}>REJECTED</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <x-input-label for="catatan_{{ $jurnal->id }}" value="Catatan Pembimbing" />
                                        <textarea id="catatan_{{ $jurnal->id }}" name="catatan_pembimbing" rows="2"
                                            class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                                            placeholder="Berikan catatan untuk mahasiswa...">{{ old('catatan_pembimbing', $jurnal->catatan_pembimbing) }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <button type="submit" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm">
                                        <i class="fa-solid fa-save"></i>
                                        <span class="font-medium">Simpan Review</span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70">
                Belum ada jurnal kegiatan dari mahasiswa.
            </div>
        @endforelse
    </div>
</x-portal-layout>
