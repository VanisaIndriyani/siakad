<x-portal-layout :title="'Detail Kegiatan - '.config('app.name')" :subtitle="$kegiatan->judul">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1 px-2.5 h-7 rounded-full text-xs font-medium bg-purple-500/15 text-purple-200 border border-purple-500/20">
                    {{ $kegiatan->jenis_kegiatan }}
                </span>
                @if($kegiatan->is_published)
                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">
                        <i class="fa-solid fa-eye text-[10px]"></i> Published
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-white/5 text-emerald-100/60 border border-white/10">
                        <i class="fa-solid fa-eye-slash text-[10px]"></i> Draft
                    </span>
                @endif
                @if($kegiatan->sertifikat_aktif)
                    <span class="inline-flex items-center gap-1 px-2 h-7 rounded-full text-xs font-medium bg-sky-500/15 text-sky-200 border border-sky-500/20">
                        <i class="fa-solid fa-certificate text-[10px]"></i> Sertifikat Aktif
                    </span>
                @endif
            </div>
            <div class="text-xl font-semibold">{{ $kegiatan->judul }}</div>
            <div class="text-sm text-emerald-100/70 mt-1">
                <i class="fa-solid fa-calendar-day mr-1"></i> {{ $kegiatan->tanggal_waktu }}
                @if($kegiatan->lokasi) • <i class="fa-solid fa-location-dot mr-1"></i> {{ $kegiatan->lokasi }} @endif
                @if($kegiatan->penyelenggara) • <i class="fa-solid fa-building mr-1"></i> {{ $kegiatan->penyelenggara }} @endif
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.kegiatan.daftar-hadir.pdf', $kegiatan) }}" target="_blank" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 transition font-medium text-sm">
                <i class="fa-solid fa-file-lines"></i>
                Preview Daftar Hadir
            </a>
            <a href="{{ route('admin.kegiatan.daftar-hadir.download', $kegiatan) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-500 active:bg-violet-700 transition font-medium text-sm">
                <i class="fa-solid fa-download"></i>
                Download PDF
            </a>
            <a href="{{ route('admin.kegiatan.edit', $kegiatan) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-pen"></i>
                Edit
            </a>
            <a href="{{ route('admin.kegiatan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-5">
        <div class="lg:col-span-3 space-y-5">
            @if(!empty($kegiatan->gambar_url))
                <div class="rounded-2xl overflow-hidden bg-white/5 border border-white/10">
                    <img src="{{ $kegiatan->gambar_url }}" alt="{{ $kegiatan->judul }}" class="w-full max-h-72 object-cover" />
                </div>
            @endif

            @if(!empty($kegiatan->deskripsi) || !empty($kegiatan->narasumber))
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    @if(!empty($kegiatan->narasumber))
                        <div class="mb-4">
                            <div class="text-xs uppercase tracking-wide text-emerald-100/50 mb-1">Narasumber / Pemateri</div>
                            <div class="font-medium"><i class="fa-solid fa-user-tie mr-2 text-emerald-300"></i> {{ $kegiatan->narasumber }}</div>
                        </div>
                    @endif
                    @if(!empty($kegiatan->deskripsi))
                        <div>
                            <div class="text-xs uppercase tracking-wide text-emerald-100/50 mb-1">Deskripsi</div>
                            <div class="text-sm text-emerald-100/90 whitespace-pre-line leading-relaxed">{{ $kegiatan->deskripsi }}</div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <div class="font-semibold">Daftar Peserta</div>
                        <div class="text-xs text-emerald-100/60 mt-0.5">
                            Total: <b class="text-emerald-200">{{ $kegiatan->peserta_count }}</b> orang | 
                            Hadir: <b class="text-emerald-300">{{ $kegiatan->peserta_hadir_count }}</b> orang |
                            Belum: <b class="text-amber-300">{{ $kegiatan->peserta_count - $kegiatan->peserta_hadir_count }}</b> orang
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.kegiatan.tandai-semua-hadir', $kegiatan) }}" onsubmit="return confirm('Tandai SEMUA peserta sebagai hadir?')" class="flex gap-2">
                        @csrf
                        <button class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                            <i class="fa-solid fa-check-double"></i>
                            Tandai Semua Hadir
                        </button>
                    </form>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 p-4 mb-4 space-y-3">
                    <div class="text-sm font-medium text-emerald-100/80 flex items-center gap-2">
                        <i class="fa-solid fa-user-plus"></i>
                        Tambah Peserta dari Data Siakad (Otomatis)
                    </div>
                    <form method="POST" action="{{ route('admin.kegiatan.import-mahasiswa', $kegiatan) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                        @csrf
                        <div>
                            <label class="text-xs text-emerald-100/60">Angkatan</label>
                            <input name="angkatan" placeholder="2022 / 2023..." value="{{ old('angkatan') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">Program Studi</label>
                            <input name="program_studi" placeholder="Teknik Informatika..." value="{{ old('program_studi') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">Fakultas</label>
                            <input name="fakultas" placeholder="Sains & Teknologi..." value="{{ old('fakultas') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div class="flex items-center gap-2 h-9">
                            <label class="flex items-center gap-2 text-xs text-emerald-100/70">
                                <input type="checkbox" name="semua_mahasiswa" value="1" class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                                Semua Mahasiswa
                            </label>
                        </div>
                        <button class="h-9 px-4 rounded-lg bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                            <i class="fa-solid fa-download mr-1"></i> Import
                        </button>
                    </form>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 p-4 mb-4 space-y-3">
                    <div class="text-sm font-medium text-emerald-100/80 flex items-center gap-2">
                        <i class="fa-solid fa-user-pen"></i>
                        Tambah Peserta Manual
                    </div>
                    <form method="POST" action="{{ route('admin.kegiatan.tambah-peserta', $kegiatan) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf
                        <div class="md:col-span-2">
                            <label class="text-xs text-emerald-100/60">Nama Lengkap *</label>
                            <input required name="nama_lengkap" placeholder="Nama lengkap peserta..." value="{{ old('nama_lengkap') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">NIM / NPM</label>
                            <input name="npm" placeholder="NPM / NIK..." value="{{ old('npm') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">Program Studi</label>
                            <input name="program_studi" placeholder="Prodi..." value="{{ old('program_studi') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">Fakultas</label>
                            <input name="fakultas" placeholder="Fakultas..." value="{{ old('fakultas') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">No. Telp</label>
                            <input name="nomor_telp" placeholder="08xx..." value="{{ old('nomor_telp') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div>
                            <label class="text-xs text-emerald-100/60">Email</label>
                            <input type="email" name="email" placeholder="email@example.com" value="{{ old('email') }}" class="mt-1 w-full h-9 rounded-lg bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-sm" />
                        </div>
                        <div class="md:col-span-2 flex items-center justify-end gap-2">
                            <button class="h-9 px-4 rounded-lg bg-sky-600 hover:bg-sky-500 active:bg-sky-700 transition text-sm font-medium">
                                <i class="fa-solid fa-plus mr-1"></i> Tambah Peserta
                            </button>
                        </div>
                    </form>
                </div>

                <div class="rounded-xl border border-white/10 overflow-hidden">
                    <div class="overflow-x-auto max-h-[520px]">
                        <table class="min-w-full text-sm">
                            <thead class="bg-white/10 text-emerald-100/80 sticky top-0">
                                <tr>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">#</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">Nama</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">NPM</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">Prodi</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">Status Hadir</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">Waktu Hadir</th>
                                    <th class="text-left font-medium px-3 py-2.5 whitespace-nowrap">No. Sertifikat</th>
                                    <th class="text-right font-medium px-3 py-2.5 whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @forelse($peserta as $p)
                                    <tr class="hover:bg-white/5">
                                        <td class="px-3 py-2.5 text-emerald-100/70 text-xs whitespace-nowrap">{{ $peserta->firstItem() + $loop->index }}</td>
                                        <td class="px-3 py-2.5">
                                            <div class="font-medium text-xs">{{ $p->nama_lengkap }}</div>
                                            @if($p->nomor_telp || $p->email)
                                                <div class="text-[11px] text-emerald-100/50 mt-0.5">
                                                    @if($p->nomor_telp) <i class="fa-solid fa-phone mr-1"></i>{{ $p->nomor_telp }} @endif
                                                    @if($p->nomor_telp && $p->email) • @endif
                                                    @if($p->email) <i class="fa-solid fa-envelope mr-1"></i>{{ $p->email }} @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-xs text-emerald-100/70 whitespace-nowrap">{{ $p->npm ?? '-' }}</td>
                                        <td class="px-3 py-2.5 text-xs text-emerald-100/70 whitespace-nowrap">
                                            @if($p->program_studi)
                                                {{ $p->program_studi }}
                                                @if($p->fakultas)<div class="text-[11px] text-emerald-100/50">{{ $p->fakultas }}</div>@endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            @if($p->status_hadir)
                                                <span class="inline-flex items-center gap-1 px-2 h-6 rounded-full text-[11px] font-medium bg-emerald-500/15 text-emerald-200 border border-emerald-500/20">
                                                    <i class="fa-solid fa-check text-[9px]"></i> Hadir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 h-6 rounded-full text-[11px] font-medium bg-amber-500/10 text-amber-200 border border-amber-500/20">
                                                    <i class="fa-solid fa-clock text-[9px]"></i> Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 text-[11px] text-emerald-100/60 whitespace-nowrap">{{ $p->waktu_hadir_format ?? '-' }}</td>
                                        <td class="px-3 py-2.5 text-[11px] text-emerald-100/70 whitespace-nowrap font-mono">
                                            @if($p->nomor_sertifikat)
                                                <span class="text-sky-300">{{ $p->nomor_sertifikat }}</span>
                                                @if($p->sertifikat_diunduh_at)
                                                    <div class="text-[10px] text-emerald-100/40">Diunduh: {{ $p->sertifikat_diunduh_at?->format('d/m/y H:i') }}</div>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5 whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1">
                                                <form method="POST" action="{{ route('admin.kegiatan.toggle-hadir', [$kegiatan, $p]) }}" onsubmit="return confirm('Ubah status kehadiran peserta ini?')">
                                                    @csrf
                                                    <button class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-white/5 hover:bg-emerald-500/10 border border-white/10 hover:border-emerald-500/20 transition text-emerald-200" title="Toggle Hadir">
                                                        <i class="fa-solid fa-user-check text-xs"></i>
                                                    </button>
                                                </form>
                                                @if($kegiatan->sertifikat_aktif && $p->status_hadir)
                                                    <a href="{{ route('admin.kegiatan.sertifikat.pdf', [$kegiatan, $p]) }}" target="_blank" class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-white/5 hover:bg-sky-500/10 border border-white/10 hover:border-sky-500/20 transition text-sky-200" title="Preview Sertifikat">
                                                        <i class="fa-solid fa-award text-xs"></i>
                                                    </a>
                                                    <a href="{{ route('admin.kegiatan.sertifikat.download', [$kegiatan, $p]) }}" class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-white/5 hover:bg-indigo-500/10 border border-white/10 hover:border-indigo-500/20 transition text-indigo-200" title="Download Sertifikat">
                                                        <i class="fa-solid fa-download text-xs"></i>
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.kegiatan.hapus-peserta', [$kegiatan, $p]) }}" onsubmit="return confirm('Hapus peserta ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-white/5 hover:bg-rose-500/10 border border-white/10 hover:border-rose-500/20 transition text-rose-200" title="Hapus Peserta">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-emerald-100/60 text-sm">
                                            <i class="fa-solid fa-users text-3xl mb-3 opacity-40 block"></i>
                                            Belum ada peserta.<br />
                                            <span class="text-xs text-emerald-100/50">Gunakan form "Import Mahasiswa" di atas untuk menambahkan peserta otomatis dari data Siakad.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($peserta->hasPages())
                        <div class="px-3 py-3 border-t border-white/10">
                            {{ $peserta->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-chart-simple text-emerald-300"></i>
                    Statistik Kegiatan
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-emerald-100/70">Total Peserta</span>
                        <span class="font-semibold text-lg">{{ $kegiatan->peserta_count }}</span>
                    </div>
                    <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500" style="width: {{ $kegiatan->peserta_count > 0 ? ($kegiatan->peserta_hadir_count / $kegiatan->peserta_count * 100) : 0 }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-emerald-100/60">Hadir: <b class="text-emerald-300">{{ $kegiatan->peserta_hadir_count }}</b></span>
                        <span class="text-emerald-100/60">Belum: <b class="text-amber-300">{{ $kegiatan->peserta_count - $kegiatan->peserta_hadir_count }}</b></span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-white/10">
                        <span class="text-sm text-emerald-100/70">Persentase</span>
                        <span class="font-semibold text-lg text-emerald-300">
                            {{ $kegiatan->peserta_count > 0 ? round($kegiatan->peserta_hadir_count / $kegiatan->peserta_count * 100, 1) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="font-semibold mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-emerald-300"></i>
                    Detail Kegiatan
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
                    <div>
                        <div class="text-xs text-emerald-100/50">Penyelenggara</div>
                        <div class="font-medium">{{ $kegiatan->penyelenggara ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-emerald-100/50">Narasumber</div>
                        <div class="font-medium">{{ $kegiatan->narasumber ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-emerald-100/50">Dibuat Oleh</div>
                        <div class="font-medium">{{ $kegiatan->createdBy?->name ?? '-' }}</div>
                        <div class="text-[11px] text-emerald-100/50">{{ $kegiatan->created_at?->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
