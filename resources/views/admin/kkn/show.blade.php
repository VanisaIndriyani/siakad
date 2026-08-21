<x-portal-layout :title="'Detail Pendaftaran KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $canAssign = (bool) ($canAssign ?? false);
        $isAdminView = $prefix === 'admin';
        $indexUrl = $isAdminView ? route('admin.kkn.index') : route('dosen.kkn-pengajuan.index');
        $statusAction = $isAdminView ? route('admin.kkn.status', $kkn) : route('dosen.kkn-pengajuan.status', $kkn);
        $assignAction = $isAdminView ? route('admin.kkn.assign', $kkn) : route('dosen.kkn-pengajuan.assign', $kkn);
        $skPreviewUrl = $isAdminView
            ? route('admin.kkn.sk.preview', $kkn)
            : route('dosen.kkn-pengajuan.sk.preview', $kkn);
        $skDownloadUrl = $isAdminView
            ? route('admin.kkn.sk.download', $kkn)
            : route('dosen.kkn-pengajuan.sk.download', $kkn);
        $skDestroyUrl = $isAdminView
            ? route('admin.kkn.sk.destroy', $kkn)
            : route('dosen.kkn-pengajuan.sk.destroy', $kkn);
        $jurnalUrl = $isAdminView
            ? route('admin.kkn.jurnal.index', $kkn)
            : route('dosen.kkn.jurnal.index', $kkn);
        $absensiUrl = $isAdminView
            ? route('admin.kkn.absensi.index', $kkn)
            : route('dosen.kkn.absensi.index', $kkn);

        $badge = match ($kkn->status) {
            'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
            'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
            'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
            default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <a href="{{ $indexUrl }}" class="text-sm text-emerald-200/70 hover:text-white inline-flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div class="mt-2 text-xl font-semibold">Detail Pendaftaran KKN</div>
            <div class="mt-2 flex items-center gap-2 text-sm text-emerald-100/70 flex-wrap">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                    {{ strtoupper($kkn->status) }}
                </span>
                <span>•</span>
                <span class="font-medium">{{ $kkn->mahasiswa?->nama_lengkap ?: '-' }}</span>
                <span>({{ $kkn->mahasiswa?->npm ?: '-' }})</span>
            </div>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $jurnalUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-orange-500/15 hover:bg-orange-500/20 border border-orange-500/20 text-orange-100 transition">
                <i class="fa-solid fa-book-open"></i>
                <span class="text-sm font-medium">Jurnal</span>
            </a>
            <a href="{{ $absensiUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/20 border border-purple-500/20 text-purple-100 transition">
                <i class="fa-solid fa-clipboard-user"></i>
                <span class="text-sm font-medium">Daftar Hadir</span>
            </a>
            @if ($canAssign)
                <form method="POST" action="{{ $isAdminView ? route('admin.kkn.destroy', $kkn) : route('dosen.kkn-pengajuan.destroy', $kkn) }}" data-confirm="Hapus data pendaftaran KKN ini?" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition text-red-100 text-sm font-medium">
                        <i class="fa-solid fa-trash"></i>
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold mb-3 text-emerald-100">Biodata Mahasiswa</div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Nama Lengkap</span>
                        <span class="font-medium">{{ $kkn->mahasiswa?->nama_lengkap ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">NPM</span>
                        <span class="font-medium">{{ $kkn->mahasiswa?->npm ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Program Studi</span>
                        <span class="font-medium">{{ $kkn->mahasiswa?->program_studi ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Semester</span>
                        <span class="font-medium">{{ $kkn->semester ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Tahun Akademik</span>
                        <span class="font-medium">{{ $kkn->tahun_akademik ?: '-' }}</span>
                    </div>
                    @if ($kkn->keterangan)
                        <div class="flex justify-between py-2">
                            <span class="text-emerald-100/60">Keterangan</span>
                            <span class="font-medium max-w-[60%] text-right whitespace-pre-line">{{ $kkn->keterangan }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold mb-3 text-emerald-100">Posko KKN</div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Nama Posko</span>
                        <span class="font-medium">{{ $kkn->posko?->nama_posko ?: 'Belum diplot' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 py-2">
                        <span class="text-emerald-100/60">Lokasi</span>
                        <span class="font-medium">{{ $kkn->posko?->lokasi ?: '-' }}</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-emerald-100/60">Pembimbing Posko</span>
                        <span class="font-medium text-right">
                            @if ($kkn->posko && $kkn->posko->pembimbingS?->isNotEmpty())
                                @foreach ($kkn->posko->pembimbingS as $p)
                                    <div>{{ $p->nama }}</div>
                                @endforeach
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @if ($canAssign)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <div class="text-sm font-semibold">Status Pengajuan</div>

                    <form method="POST" action="{{ $statusAction }}" class="mt-4 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-3">
                            <select name="status" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                <option value="approved" @selected(old('status', $kkn->status) === 'approved') style="background-color: #0d2a23; color: #fff;">Setujui</option>
                                <option value="rejected" @selected(old('status', $kkn->status) === 'rejected') style="background-color: #0d2a23; color: #fff;">Tolak</option>
                            </select>
                            <textarea name="catatan_admin" rows="3" placeholder="Catatan (opsional)"
                                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('catatan_admin', $kkn->catatan_admin) }}</textarea>
                        </div>

                        <button class="h-11 w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                            Simpan Status
                        </button>
                    </form>
                </div>
            @endif

            @if ($canAssign)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <div class="text-sm font-semibold">Tetapkan Pembimbing (SK)</div>

                    <form method="POST" action="{{ $assignAction }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Pembimbing 1</label>
                            <select name="dosen_pembimbing_id" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" required>
                                <option value="" disabled @selected(! old('dosen_pembimbing_id', $kkn->dosen_pembimbing_id)) style="background-color: #0d2a23; color: #fff;">Pilih dosen</option>
                                @foreach ($dosenList as $d)
                                    <option value="{{ $d->id }}" @selected((string) old('dosen_pembimbing_id', $kkn->dosen_pembimbing_id) === (string) $d->id) style="background-color: #0d2a23; color: #fff;">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Pembimbing 2 (Opsional)</label>
                            <select name="dosen_pembimbing_id_2" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                <option value="" @selected(! old('dosen_pembimbing_id_2', $kkn->dosen_pembimbing_id_2)) style="background-color: #0d2a23; color: #fff;">-</option>
                                @foreach ($dosenList as $d)
                                    <option value="{{ $d->id }}" @selected((string) old('dosen_pembimbing_id_2', $kkn->dosen_pembimbing_id_2) === (string) $d->id) style="background-color: #0d2a23; color: #fff;">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                            @error('dosen_pembimbing_id_2') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Nomor SK (Opsional)</label>
                            <input name="nomor_sk" value="{{ old('nomor_sk', $kkn->nomor_sk) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Tanggal SK (Opsional)</label>
                            <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', optional($kkn->tanggal_sk)->format('Y-m-d')) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Upload SK Pembimbing (PDF/JPG/PNG, Opsional)</label>
                            <input type="file" name="sk_pembimbing_file" accept=".pdf,.jpg,.jpeg,.png"
                                   class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-3 file:h-11 file:border-0 file:bg-white/10 file:text-white file:px-3 file:cursor-pointer" />
                            @error('sk_pembimbing_file') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                        </div>

                        <button class="h-11 w-full rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 transition text-sm font-medium">
                            Simpan Pembimbing
                        </button>
                    </form>
                </div>
            @endif

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Pembimbing Pengajuan</div>
                <div class="mt-1 font-medium">{{ $kkn->dosenPembimbing?->nama ?: '-' }}</div>
                @if ($kkn->dosenPembimbing2?->nama)
                    <div class="mt-1 font-medium">{{ $kkn->dosenPembimbing2?->nama }}</div>
                @endif
                <div class="mt-2 text-sm text-emerald-100/70">
                    SK:
                    <span class="font-medium">{{ $kkn->nomor_sk ?: '-' }}</span>
                    @if ($kkn->tanggal_sk)
                        <span>•</span>
                        <span class="font-medium">{{ $kkn->tanggal_sk->format('d/m/Y') }}</span>
                    @endif
                </div>
                @if ($kkn->sk_pembimbing_path)
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        <a href="{{ $skPreviewUrl }}" target="_blank"
                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-eye"></i>
                            <span class="text-sm font-medium">Preview SK</span>
                        </a>
                        <a href="{{ $skDownloadUrl }}"
                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-download"></i>
                            <span class="text-sm font-medium">Download</span>
                        </a>
                        @if ($canAssign)
                            <form method="POST" action="{{ $skDestroyUrl }}" data-confirm="Hapus file SK ini?" class="inline-flex">
                                @csrf
                                @method('DELETE')
                                <button class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition">
                                    <i class="fa-solid fa-trash"></i>
                                    <span class="text-sm font-medium">Hapus</span>
                                </button>
                            </form>
                        @endif
                        <div class="text-sm text-emerald-100/70 truncate">
                            {{ $kkn->sk_pembimbing_name ?: basename($kkn->sk_pembimbing_path) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
