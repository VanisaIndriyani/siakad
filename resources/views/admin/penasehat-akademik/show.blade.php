<x-portal-layout :title="'Detail Penasehat Akademik - '.config('app.name')" subtitle="Penasehat Akademik">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $canAssign = (bool) ($canAssign ?? false);
        $indexUrl = $prefix === 'admin' ? route('admin.penasehat-akademik.index') : route('dosen.penasehat-akademik.index');
        $assignAction = $prefix === 'admin' ? route('admin.penasehat-akademik.assign', $mahasiswa) : route('dosen.penasehat-akademik.assign', $mahasiswa);
        $skDestroyAction = $prefix === 'admin' ? route('admin.penasehat-akademik.destroy-sk', $mahasiswa) : route('dosen.penasehat-akademik.destroy-sk', $mahasiswa);
        $pembimbingResetAction = $prefix === 'admin' ? route('admin.penasehat-akademik.reset', $mahasiswa) : route('dosen.penasehat-akademik.reset', $mahasiswa);
        $skPreviewUrl = $prefix === 'admin'
            ? route('admin.penasehat-akademik.sk.preview', $mahasiswa)
            : route('dosen.penasehat-akademik.sk.preview', $mahasiswa);
        $skDownloadUrl = $prefix === 'admin'
            ? route('admin.penasehat-akademik.sk.download', $mahasiswa)
            : route('dosen.penasehat-akademik.sk.download', $mahasiswa);
        $messageAction = $prefix === 'admin' ? route('admin.penasehat-akademik.message', $mahasiswa) : route('dosen.penasehat-akademik.message', $mahasiswa);
        $printUrl = $prefix === 'admin' ? route('admin.penasehat-akademik.print', $mahasiswa) : route('dosen.penasehat-akademik.print', $mahasiswa);
        $pdfUrl = $prefix === 'admin' ? route('admin.penasehat-akademik.pdf', $mahasiswa) : route('dosen.penasehat-akademik.pdf', $mahasiswa);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Detail Penasehat Akademik</div>
            <div class="mt-2 flex items-center gap-2 text-sm text-emerald-100/70 flex-wrap">
                <span class="font-medium">{{ $mahasiswa->nama_lengkap ?: '-' }}</span>
                <span>({{ $mahasiswa->npm ?: '-' }})</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $printUrl }}" target="_blank" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-print"></i>
                <span class="text-sm font-medium">Print</span>
            </a>
            <a href="{{ $pdfUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">PDF</span>
            </a>
            <a href="{{ $indexUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Mahasiswa</div>
                <div class="mt-1 text-base font-semibold">{{ $mahasiswa->nama_lengkap }}</div>
                <div class="text-sm text-emerald-100/70">NPM: {{ $mahasiswa->npm }}</div>
                <div class="text-sm text-emerald-100/70">Program Studi: {{ $mahasiswa->program_studi }}</div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Riwayat Bimbingan</div>
                <div class="mt-3 space-y-3">
                    @forelse ($mahasiswa->bimbinganAkademikMessages->sortBy('id') as $msg)
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <div class="text-xs font-semibold text-emerald-100/60 mb-1">
                                {{ $msg->sender?->name ?: 'User' }} • {{ $msg->created_at?->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-emerald-100/90 whitespace-pre-line">{{ $msg->pesan }}</div>
                        </div>
                    @empty
                        <div class="text-center text-emerald-100/70 py-8">Belum ada pesan bimbingan.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Kirim Pesan</div>
                <form method="POST" action="{{ $messageAction }}" class="mt-3 space-y-3">
                    @csrf
                    <textarea name="pesan" rows="4" placeholder="Tulis pesan..." required
                              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('pesan') }}</textarea>
                    <button class="h-11 w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            @if ($canAssign)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <div class="text-sm font-semibold">Tetapkan Penasehat Akademik (SK)</div>

                    <form method="POST" action="{{ $assignAction }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Dosen Penasehat</label>
                            <select name="dosen_penasehat_id" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" required>
                                <option value="" disabled @selected(! old('dosen_penasehat_id', $mahasiswa->dosen_penasehat_id)) style="background-color: #0d2a23; color: #fff;">Pilih dosen</option>
                                @foreach ($dosenList as $d)
                                    <option value="{{ $d->id }}" @selected((string) old('dosen_penasehat_id', $mahasiswa->dosen_penasehat_id) === (string) $d->id) style="background-color: #0d2a23; color: #fff;">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Nomor SK (Opsional)</label>
                            <input name="nomor_sk_penasehat" value="{{ old('nomor_sk_penasehat', $mahasiswa->nomor_sk_penasehat) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Tanggal SK (Opsional)</label>
                            <input type="date" name="tanggal_sk_penasehat" value="{{ old('tanggal_sk_penasehat', optional($mahasiswa->tanggal_sk_penasehat)->format('Y-m-d')) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Upload SK Penasehat (PDF, Opsional)</label>
                            <input type="file" name="sk_penasehat_file" accept=".pdf"
                                   class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-3 file:h-11 file:border-0 file:bg-white/10 file:text-white file:px-3 file:cursor-pointer" />
                            @error('sk_penasehat_file') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                        </div>

                        <button class="h-11 w-full rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-500/25 transition text-sm font-medium">
                            Simpan Penasehat
                        </button>
                    </form>
                </div>
            @endif

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Penasehat Akademik Saat Ini</div>
                <div class="mt-1 font-medium">{{ $mahasiswa->dosenPenasehat?->nama ?: '-' }}</div>
                <div class="mt-2 text-sm text-emerald-100/70">
                    SK:
                    <span class="font-medium">{{ $mahasiswa->nomor_sk_penasehat ?: '-' }}</span>
                    @if ($mahasiswa->tanggal_sk_penasehat)
                        <span>•</span>
                        <span class="font-medium">{{ $mahasiswa->tanggal_sk_penasehat->format('d/m/Y') }}</span>
                    @endif
                </div>
                @if ($canAssign && ($mahasiswa->dosen_penasehat_id || $mahasiswa->nomor_sk_penasehat || $mahasiswa->tanggal_sk_penasehat || $mahasiswa->sk_penasehat_path))
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        @if ($mahasiswa->sk_penasehat_path)
                            <form method="POST" action="{{ $skDestroyAction }}" data-confirm="Hapus file SK penasehat?">
                                @csrf
                                @method('DELETE')
                                <button class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition">
                                    <i class="fa-solid fa-trash"></i>
                                    <span class="text-sm font-medium">Hapus SK</span>
                                </button>
                            </form>
                        @endif
                        <form method="POST" action="{{ $pembimbingResetAction }}" data-confirm="Reset penasehat & SK untuk mahasiswa ini?">
                            @csrf
                            <button class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition">
                                <i class="fa-solid fa-user-xmark"></i>
                                <span class="text-sm font-medium">Reset Penasehat</span>
                            </button>
                        </form>
                    </div>
                @endif
                @if ($mahasiswa->sk_penasehat_path)
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
                        <div class="text-sm text-emerald-100/70 truncate">
                            {{ $mahasiswa->sk_penasehat_name ?: basename($mahasiswa->sk_penasehat_path) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
