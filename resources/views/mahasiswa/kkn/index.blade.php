<x-portal-layout :title="'Kuliah Kerja Nyata (KKN) - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <div class="text-xl font-semibold text-white">Kuliah Kerja Nyata (KKN)</div>
            <div class="text-sm text-emerald-100/60">Daftar KKN dan pantau informasi posko Anda.</div>
        </div>
    </div>

    @if (!$pengajuan)
        <!-- Form Pendaftaran -->
        <div class="max-w-2xl mx-auto mt-10">
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-8 text-center shadow-2xl overflow-hidden relative">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/10 blur-3xl rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-emerald-500/10 blur-3xl rounded-full"></div>
                
                <div class="relative z-10">
                    <div class="h-20 w-20 bg-emerald-500/20 border border-emerald-500/30 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fa-solid fa-map-location-dot text-4xl text-emerald-400"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Pendaftaran KKN Belum Dikirim</h3>
                    <p class="text-emerald-100/60 mb-8 px-4">Silakan kirim pengajuan pendaftaran KKN untuk mengikuti proses plotting posko oleh Admin Akademik.</p>
                    
                    <form method="POST" action="{{ route('mahasiswa.kkn.store') }}">
                        @csrf
                        <button type="submit" class="h-14 px-10 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold tracking-widest uppercase text-sm transition-all transform hover:scale-105 active:scale-95 shadow-lg shadow-emerald-900/40">
                            Kirim Pendaftaran KKN
                        </button>
                    </form>
                    
                    <div class="mt-8 flex items-center justify-center gap-6 text-xs font-bold text-emerald-100/40 uppercase tracking-widest">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-emerald-500"></i>
                            Terverifikasi
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-emerald-500"></i>
                            Aman
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Status Pendaftaran -->
            <div class="lg:col-span-1 space-y-6">
                <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-6 shadow-xl relative overflow-hidden">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-id-badge text-emerald-400"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-emerald-100/40 uppercase tracking-widest">Status Pendaftaran</div>
                            @php
                                $statusColor = match ($pengajuan->status) {
                                    'approved' => 'text-emerald-400',
                                    'rejected' => 'text-red-400',
                                    default => 'text-yellow-400',
                                };
                            @endphp
                            <div class="text-lg font-black {{ $statusColor }} uppercase">{{ $pengajuan->status }}</div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                            <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-widest mb-1">Tanggal Daftar</div>
                            <div class="text-sm font-bold text-white">{{ $pengajuan->created_at->format('d F Y') }}</div>
                        </div>
                        
                        @if ($pengajuan->catatan_admin)
                            <div class="p-4 rounded-2xl bg-red-500/5 border border-red-500/10">
                                <div class="text-[10px] font-black text-red-400/50 uppercase tracking-widest mb-1">Catatan Admin</div>
                                <div class="text-sm text-red-100/80 italic">{{ $pengajuan->catatan_admin }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($pengajuan->status === 'approved' && !$pengajuan->kkn_posko_id)
                    <div class="rounded-3xl bg-emerald-600/10 border border-emerald-500/20 p-6 text-center">
                        <i class="fa-solid fa-hourglass-half text-3xl text-emerald-400 mb-3 animate-pulse"></i>
                        <h4 class="font-bold text-white mb-1">Menunggu Plotting</h4>
                        <p class="text-xs text-emerald-100/60">Pendaftaran Anda sudah disetujui. Mohon menunggu Admin menempatkan Anda di posko KKN.</p>
                    </div>
                @endif
            </div>

            <!-- Informasi Posko -->
            <div class="lg:col-span-2">
                @if ($pengajuan->posko)
                    <div class="rounded-3xl bg-[#0d2a23] border border-white/10 overflow-hidden shadow-xl">
                        <div class="p-8 bg-gradient-to-br from-emerald-600/20 to-transparent border-b border-white/5">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-2">POSKO ANDA</div>
                                    <h2 class="text-3xl font-black text-white leading-none">{{ $pengajuan->posko->nama_posko }}</h2>
                                    <div class="mt-4 flex items-center gap-3 text-emerald-100/60">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span class="text-sm font-medium">{{ $pengajuan->posko->lokasi ?: 'Lokasi segera ditentukan' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('mahasiswa.kkn.posko', $pengajuan->posko) }}" class="h-14 px-8 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold tracking-widest uppercase text-xs flex items-center justify-center gap-3 transition-all shadow-lg shadow-emerald-900/40">
                                    Buka Halaman Posko
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>

                        <div class="p-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                            <div>
                                <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-3">DOSEN PEMBIMBING LAPANGAN</div>
                                <div class="space-y-4">
                                    @foreach ($pengajuan->posko->pembimbingS as $dpl)
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-sm font-black text-emerald-400">
                                                {{ mb_substr($dpl->nama, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-white">{{ $dpl->nama }}</div>
                                                <div class="text-[10px] text-emerald-100/50">NUPTK: {{ $dpl->nuptk ?: ($dpl->nidn ?: ($dpl->nip ?: '-')) }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($pengajuan->posko->pembimbingS->isEmpty())
                                        <div class="text-sm font-medium text-emerald-100/40 italic">Belum ditentukan</div>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-3">SURAT KEPUTUSAN (SK)</div>
                                @if ($pengajuan->posko->sk_pembimbing_path)
                                    <a href="{{ asset('storage/'.$pengajuan->posko->sk_pembimbing_path) }}" target="_blank" class="flex items-center gap-4 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition group">
                                        <div class="h-11 w-11 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 group-hover:scale-110 transition">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-white truncate">{{ $pengajuan->posko->sk_pembimbing_name }}</div>
                                            <div class="text-[10px] text-emerald-100/40 mt-0.5">Ketuk untuk mengunduh</div>
                                        </div>
                                    </a>
                                @else
                                    <div class="text-sm font-medium text-emerald-100/40 italic py-3">SK Pembimbing belum diunggah.</div>
                                @endif
                            </div>
                        </div>

                        <div class="px-8 pb-8">
                            <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-4">REKAN SATU POSKO</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($pengajuan->posko->pengajuans as $p)
                                    <div class="px-4 py-2 rounded-xl bg-white/5 border border-white/5 flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                        <div class="text-xs font-bold text-white">{{ $p->mahasiswa?->nama_lengkap }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="rounded-3xl bg-white/5 border border-white/10 p-12 text-center">
                        <div class="h-20 w-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fa-solid fa-campground text-4xl text-white/20"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white/50 uppercase tracking-widest">Informasi Posko Belum Tersedia</h3>
                        <p class="text-sm text-emerald-100/30 mt-2">Data posko akan muncul di sini setelah Admin melakukan plotting.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-portal-layout>
