<x-portal-layout :title="'Detail Kegiatan - '.config('app.name')" :subtitle="$kegiatan->judul">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1 px-2.5 h-7 rounded-full text-xs font-medium bg-purple-500/15 text-purple-200 border border-purple-500/20">
                    {{ $kegiatan->jenis_kegiatan }}
                </span>
                @if($kegiatan->sertifikat_aktif)
                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-sky-500/15 text-sky-200 border border-sky-500/20">
                        <i class="fa-solid fa-certificate text-[10px]"></i> Sertifikat Tersedia
                    </span>
                @endif
            </div>
            <div class="text-xl font-semibold">{{ $kegiatan->judul }}</div>
            <div class="text-sm text-emerald-100/70 mt-1">
                <i class="fa-solid fa-calendar-day mr-1"></i> {{ $kegiatan->tanggal_waktu }}
                @if($kegiatan->lokasi) • <i class="fa-solid fa-location-dot mr-1"></i> {{ $kegiatan->lokasi }} @endif
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            @if($peserta && $peserta->status_hadir && $kegiatan->sertifikat_aktif)
                <a href="{{ route('mahasiswa.kegiatan.download-sertifikat', $kegiatan) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-500 active:bg-sky-700 transition font-medium text-sm">
                    <i class="fa-solid fa-download"></i>
                    Download Sertifikat
                </a>
            @endif
            @if(!$peserta)
                <form method="POST" action="{{ route('mahasiswa.kegiatan.daftar', $kegiatan) }}" onsubmit="return confirm('Daftar sebagai peserta kegiatan ini?')">
                    @csrf
                    <button class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium text-sm">
                        <i class="fa-solid fa-user-plus"></i>
                        Daftar Kegiatan
                    </button>
                </form>
            @endif
            <a href="{{ route('mahasiswa.kegiatan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            @if(!empty($kegiatan->gambar_url))
                <div class="rounded-2xl overflow-hidden bg-white/5 border border-white/10">
                    <img src="{{ $kegiatan->gambar_url }}" alt="{{ $kegiatan->judul }}" class="w-full max-h-80 object-cover" />
                </div>
            @endif

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-white/10">
                    <i class="fa-solid fa-circle-info text-emerald-300"></i>
                    <span class="font-semibold">Informasi Lengkap Kegiatan</span>
                </div>
                <div class="space-y-4 text-sm">
                    @if(!empty($kegiatan->penyelenggara))
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-100/50 mb-1">Penyelenggara</div>
                            <div class="font-medium"><i class="fa-solid fa-building mr-2 text-emerald-300"></i> {{ $kegiatan->penyelenggara }}</div>
                        </div>
                    @endif
                    @if(!empty($kegiatan->narasumber))
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-100/50 mb-1">Narasumber / Pemateri</div>
                            <div class="font-medium"><i class="fa-solid fa-user-tie mr-2 text-emerald-300"></i> {{ $kegiatan->narasumber }}</div>
                        </div>
                    @endif
                    @if(!empty($kegiatan->deskripsi))
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-100/50 mb-1">Deskripsi Kegiatan</div>
                            <div class="text-emerald-100/90 whitespace-pre-line leading-relaxed">{{ $kegiatan->deskripsi }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-white/10">
                    <i class="fa-solid fa-id-card text-emerald-300"></i>
                    <span class="font-semibold">Status Keanggotaan</span>
                </div>
                @if($peserta)
                    <div class="space-y-3">
                        <div>
                            <div class="text-xs text-emerald-100/50 mb-1">Status Pendaftaran</div>
                            @if($peserta->status_hadir)
                                <div class="inline-flex items-center gap-2 px-3 h-9 rounded-xl bg-emerald-500/15 text-emerald-200 border border-emerald-500/20 text-sm font-medium">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Terverifikasi Hadir
                                </div>
                                <div class="text-xs text-emerald-100/50 mt-2">
                                    Waktu: {{ $peserta->waktu_hadir_format }}
                                </div>
                            @else
                                <div class="inline-flex items-center gap-2 px-3 h-9 rounded-xl bg-amber-500/10 text-amber-200 border border-amber-500/20 text-sm font-medium">
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Menunggu Verifikasi Kehadiran
                                </div>
                                <div class="text-xs text-emerald-100/50 mt-2">
                                    Silakan hadir pada kegiatan agar status berubah menjadi "Hadir".
                                </div>
                            @endif
                        </div>
                        <div class="pt-2 border-t border-white/10 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-emerald-100/60">Nama</span>
                                <span class="font-medium">{{ $peserta->nama_lengkap }}</span>
                            </div>
                            @if($peserta->npm)
                                <div class="flex justify-between">
                                    <span class="text-emerald-100/60">NPM</span>
                                    <span class="font-medium">{{ $peserta->npm }}</span>
                                </div>
                            @endif
                            @if($peserta->program_studi)
                                <div class="flex justify-between">
                                    <span class="text-emerald-100/60">Program Studi</span>
                                    <span class="font-medium">{{ $peserta->program_studi }}</span>
                                </div>
                            @endif
                            @if($peserta->nomor_sertifikat)
                                <div class="flex justify-between pt-2 border-t border-white/10">
                                    <span class="text-emerald-100/60">No. Sertifikat</span>
                                    <span class="font-mono text-sky-300">{{ $peserta->nomor_sertifikat }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-white/5 flex items-center justify-center">
                            <i class="fa-solid fa-user-plus text-2xl text-emerald-300/70"></i>
                        </div>
                        <div class="font-medium mb-1">Anda Belum Terdaftar</div>
                        <div class="text-xs text-emerald-100/60 mb-4">Daftarkan diri Anda sebagai peserta kegiatan.</div>
                        <form method="POST" action="{{ route('mahasiswa.kegiatan.daftar', $kegiatan) }}" onsubmit="return confirm('Daftar sebagai peserta kegiatan ini?')">
                            @csrf
                            <button class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                                <i class="fa-solid fa-user-plus"></i>
                                Daftar Sekarang
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-white/10">
                    <i class="fa-solid fa-info-circle text-emerald-300"></i>
                    <span class="font-semibold">Detail Waktu & Lokasi</span>
                </div>
                <div class="space-y-2.5 text-sm">
                    <div>
                        <div class="text-xs text-emerald-100/50">Tanggal Pelaksanaan</div>
                        <div class="font-medium">{{ $kegiatan->tanggal_kegiatan?->format('l, d F Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-emerald-100/50">Waktu</div>
                        <div class="font-medium">
                            @if($kegiatan->waktu_mulai)
                                {{ substr($kegiatan->waktu_mulai,0,5) }} - {{ $kegiatan->waktu_selesai ? substr($kegiatan->waktu_selesai,0,5).' WIB' : '-' }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-emerald-100/50">Lokasi</div>
                        <div class="font-medium">{{ $kegiatan->lokasi ?? '-' }}</div>
                    </div>
                </div>
            </div>

            @if($kegiatan->sertifikat_aktif)
                <div class="rounded-2xl bg-sky-500/10 border border-sky-500/20 p-5">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-500/20 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-award text-sky-300"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sky-100 mb-1">Sertifikat Tersedia</div>
                            <div class="text-xs text-sky-200/80 leading-relaxed">
                                Sertifikat akan tersedia untuk di-download setelah Anda tercatat <b>HADIR</b> pada kegiatan ini oleh panitia.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
