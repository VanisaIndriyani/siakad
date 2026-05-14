<x-portal-layout :title="'Riwayat Pembayaran - '.config('app.name')" subtitle="Riwayat Pembayaran">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="space-y-6 text-white">
        <div>
            <h1 class="text-2xl font-bold">Riwayat Pembayaran</h1>
            <p class="text-sm text-emerald-100/70">Daftar pembayaran semester dan status tagihan Anda.</p>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($pembayarans as $p)
            <div class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
                <div class="p-6 border-b border-white/10 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white/5">
                    <div>
                        <div class="text-xs text-emerald-100/40 uppercase tracking-wider font-semibold">Semester {{ $p->semester }}</div>
                        <div class="text-xl font-bold">{{ $p->tahun_ajaran }}</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-6">
                        <div>
                            <div class="text-xs text-emerald-100/40 font-medium">Total Tagihan</div>
                            <div class="text-lg font-bold text-emerald-400">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-100/40 font-medium">Telah Dibayar</div>
                            <div class="text-lg font-bold text-sky-400">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            @php
                                $badge = match($p->status_pembayaran) {
                                    'Lunas' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20',
                                    'Cicil' => 'bg-amber-500/20 text-amber-400 border-amber-500/20',
                                    default => 'bg-red-500/20 text-red-400 border-red-500/20'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $badge }}">
                                {{ strtoupper($p->status_pembayaran) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-sm font-semibold text-emerald-100/60 mb-4">Riwayat Transaksi</h3>
                    <div class="space-y-3">
                        @foreach($p->details as $detail)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 border border-white/5">
                            <div class="flex items-center gap-4">
                                <div class="text-sm font-bold">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</div>
                                <div class="text-xs text-emerald-100/40">{{ $detail->tanggal_bayar->format('d/m/Y') }}</div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="text-xs text-emerald-100/60 italic">{{ $detail->keterangan }}</span>
                                @if($detail->bukti_pembayaran)
                                <a href="{{ asset('storage/'.$detail->bukti_pembayaran) }}" target="_blank" class="text-xs text-emerald-400 hover:underline">
                                    <i class="fa-solid fa-image"></i> Bukti
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($p->catatan)
                    <div class="mt-4 p-3 rounded-xl bg-amber-500/5 border border-amber-500/10">
                        <div class="text-[10px] text-amber-500/60 uppercase font-bold">Catatan Keuangan:</div>
                        <div class="text-xs text-emerald-100/80">{{ $p->catatan }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-10 text-center">
                <div class="text-emerald-100/30 text-5xl mb-4">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </div>
                <div class="text-emerald-100/50 font-medium">Belum ada data pembayaran yang tercatat.</div>
            </div>
            @endforelse
        </div>
    </div>
</x-portal-layout>
