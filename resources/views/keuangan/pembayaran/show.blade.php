<x-portal-layout :title="'Detail Pembayaran - '.config('app.name')" subtitle="Detail & Riwayat Cicilan">
    <x-slot:sidebar>
        @include('keuangan.partials.sidebar')
    </x-slot:sidebar>

    <div class="max-w-5xl mx-auto space-y-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Detail Pembayaran</h1>
                <p class="text-sm text-emerald-100/70">{{ $pembayaran->mahasiswa->nama_lengkap }} ({{ $pembayaran->mahasiswa->npm }})</p>
            </div>
            <a href="{{ route('keuangan.pembayaran.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Info Ringkasan -->
            <div class="md:col-span-1 space-y-6">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <h2 class="text-sm font-semibold text-emerald-100/50 uppercase tracking-wider mb-4">Informasi Tagihan</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="text-xs text-emerald-100/40">Semester</div>
                            <div class="text-lg font-bold">{{ $pembayaran->semester }} ({{ $pembayaran->tahun_ajaran }})</div>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-100/40">Total Biaya</div>
                            <div class="text-xl font-bold text-emerald-400">Rp {{ number_format($pembayaran->total_biaya, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-100/40">Total Dibayar</div>
                            <div class="text-xl font-bold text-sky-400">Rp {{ number_format($pembayaran->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-emerald-100/40">Sisa Tagihan</div>
                            <div class="text-xl font-bold text-red-400">Rp {{ number_format($pembayaran->total_biaya - $pembayaran->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                        <div class="pt-4 border-t border-white/5">
                            @php
                                $badge = match($pembayaran->status_pembayaran) {
                                    'Lunas' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20',
                                    'Cicil' => 'bg-amber-500/20 text-amber-400 border-amber-500/20',
                                    default => 'bg-red-500/20 text-red-400 border-red-500/20'
                                };
                            @endphp
                            <span class="inline-flex w-full justify-center items-center rounded-xl border px-3 py-2 text-sm font-bold {{ $badge }}">
                                {{ strtoupper($pembayaran->status_pembayaran) }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($pembayaran->status_pembayaran !== 'Lunas')
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <h2 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-emerald-400"></i> Tambah Cicilan
                    </h2>
                    <form action="{{ route('keuangan.pembayaran.cicilan', $pembayaran) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-xs text-emerald-100/60 font-medium">Jumlah Bayar (Rp)</label>
                            <input type="number" name="jumlah_bayar" class="mt-1.5 w-full h-10 rounded-xl bg-white/5 border border-white/10 text-white focus:ring-emerald-400" required />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60 font-medium">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" class="mt-1.5 w-full h-10 rounded-xl bg-white/5 border border-white/10 text-white focus:ring-emerald-400" required />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60 font-medium">Bukti Bayar (Opsional)</label>
                            <input type="file" name="bukti_pembayaran" accept="image/*" class="mt-1.5 w-full text-xs" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60 font-medium">Keterangan</label>
                            <input type="text" name="keterangan" class="mt-1.5 w-full h-10 rounded-xl bg-white/5 border border-white/10 text-white focus:ring-emerald-400" placeholder="Contoh: Cicilan ke-2" />
                        </div>
                        <button type="submit" class="w-full h-11 rounded-xl bg-emerald-600 hover:bg-emerald-500 transition font-bold shadow-lg shadow-emerald-600/20">
                            SIMPAN CICILAN
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Riwayat Cicilan -->
            <div class="md:col-span-2 space-y-6">
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <h2 class="text-sm font-semibold text-emerald-100/50 uppercase tracking-wider mb-5">Riwayat Pembayaran</h2>
                    <div class="space-y-4">
                        @foreach($pembayaran->details as $detail)
                        <div class="p-4 rounded-xl bg-white/5 border border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                                    <i class="fa-solid fa-money-bill-transfer text-xl"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-lg">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</div>
                                    <div class="text-xs text-emerald-100/40">{{ $detail->tanggal_bayar->format('d F Y') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="text-sm font-medium text-emerald-100/80">{{ $detail->keterangan ?? 'Tanpa keterangan' }}</div>
                                    @if($detail->bukti_pembayaran)
                                    <a href="{{ asset('storage/'.$detail->bukti_pembayaran) }}" target="_blank" class="text-xs text-emerald-400 hover:underline">
                                        <i class="fa-solid fa-image"></i> Lihat Bukti
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @if($pembayaran->catatan)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <h2 class="text-sm font-semibold text-emerald-100/50 uppercase tracking-wider mb-2">Catatan Admin Keuangan</h2>
                    <p class="text-emerald-100/80 italic">{{ $pembayaran->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
