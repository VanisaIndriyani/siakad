<x-portal-layout :title="'Detail Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $canAssign = (bool) ($canAssign ?? false);
        $indexUrl = $prefix === 'admin' ? route('admin.skripsi.index') : route('dosen.skripsi-pengajuan.index');
        $statusAction = $prefix === 'admin' ? route('admin.skripsi.status', $skripsi) : route('dosen.skripsi-pengajuan.status', $skripsi);
        $skPreviewUrl = $prefix === 'admin'
            ? route('admin.skripsi.sk.preview', $skripsi)
            : route('dosen.skripsi-pengajuan.sk.preview', $skripsi);
        $skDownloadUrl = $prefix === 'admin'
            ? route('admin.skripsi.sk.download', $skripsi)
            : route('dosen.skripsi-pengajuan.sk.download', $skripsi);

        $badge = match ($skripsi->status) {
            'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
            'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
            'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
            default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Detail Pengajuan Skripsi</div>
            <div class="mt-2 flex items-center gap-2 text-sm text-emerald-100/70 flex-wrap">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                    {{ strtoupper($skripsi->status) }}
                </span>
                <span>•</span>
                <span class="font-medium">{{ $skripsi->mahasiswa?->nama_lengkap ?: '-' }}</span>
                <span>({{ $skripsi->mahasiswa?->npm ?: '-' }})</span>
            </div>
        </div>
        <a href="{{ $indexUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Judul</div>
                <div class="mt-1 text-base font-semibold">{{ $skripsi->judul }}</div>
                @if ($skripsi->deskripsi)
                    <div class="mt-3 text-sm text-emerald-100/85 whitespace-pre-line">{{ $skripsi->deskripsi }}</div>
                @endif
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Riwayat Bimbingan</div>
                <div class="mt-3 space-y-3">
                    @forelse ($skripsi->messages->sortBy('id') as $msg)
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
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Status Pengajuan</div>

                <form method="POST" action="{{ $statusAction }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-3">
                        <select name="status" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <option value="approved" @selected(old('status', $skripsi->status) === 'approved') style="background-color: #0d2a23; color: #fff;">Setujui</option>
                            <option value="rejected" @selected(old('status', $skripsi->status) === 'rejected') style="background-color: #0d2a23; color: #fff;">Tolak</option>
                        </select>
                        <textarea name="catatan_admin" rows="4" placeholder="Catatan (opsional)"
                                  class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('catatan_admin', $skripsi->catatan_admin) }}</textarea>
                    </div>

                    <button class="h-11 w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                        Simpan Status
                    </button>
                </form>
            </div>

            @if ($canAssign)
                <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                    <div class="text-sm font-semibold">Tetapkan Pembimbing (SK)</div>

                    <form method="POST" action="{{ route('admin.skripsi.assign', $skripsi) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Dosen Pembimbing</label>
                            <select name="dosen_pembimbing_id" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" required>
                                <option value="" disabled @selected(! old('dosen_pembimbing_id', $skripsi->dosen_pembimbing_id)) style="background-color: #0d2a23; color: #fff;">Pilih dosen</option>
                                @foreach ($dosenList as $d)
                                    <option value="{{ $d->id }}" @selected((string) old('dosen_pembimbing_id', $skripsi->dosen_pembimbing_id) === (string) $d->id) style="background-color: #0d2a23; color: #fff;">{{ $d->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Nomor SK (Opsional)</label>
                            <input name="nomor_sk" value="{{ old('nomor_sk', $skripsi->nomor_sk) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Tanggal SK (Opsional)</label>
                            <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', optional($skripsi->tanggal_sk)->format('Y-m-d')) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Upload SK Pembimbing (PDF, Opsional)</label>
                            <input type="file" name="sk_pembimbing_file" accept=".pdf"
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
                <div class="text-sm text-emerald-100/70">Pembimbing Saat Ini</div>
                <div class="mt-1 font-medium">{{ $skripsi->dosenPembimbing?->nama ?: '-' }}</div>
                <div class="mt-2 text-sm text-emerald-100/70">
                    SK:
                    <span class="font-medium">{{ $skripsi->nomor_sk ?: '-' }}</span>
                    @if ($skripsi->tanggal_sk)
                        <span>•</span>
                        <span class="font-medium">{{ $skripsi->tanggal_sk->format('d/m/Y') }}</span>
                    @endif
                </div>
                @if ($skripsi->sk_pembimbing_path)
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
                            {{ $skripsi->sk_pembimbing_name ?: basename($skripsi->sk_pembimbing_path) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
