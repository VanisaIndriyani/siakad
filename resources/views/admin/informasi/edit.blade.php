<x-portal-layout :title="'Edit Informasi - '.config('app.name')" subtitle="Edit Informasi">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Edit Informasi</div>
            <div class="text-sm text-emerald-100/70">Perbarui detail informasi / pengumuman.</div>
        </div>
        <a href="{{ route('admin.informasi.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if(!empty($informasi->gambar_url))
        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 mb-5">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="text-sm font-semibold mb-2">Pratinjau & Bagikan</div>
                    <div class="text-xs text-emerald-100/60">Gambar saat ini dan tombol share / preview halaman publik.</div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="copyLinkBtn" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 border border-sky-500/20 text-sky-200 text-xs font-medium transition">
                        <i class="fa-solid fa-link"></i>
                        Salin Link Share
                    </button>
                    <a href="{{ $informasi->share_url }}" target="_blank" rel="noopener" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-emerald-100 text-xs font-medium transition">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        Buka Halaman
                    </a>
                </div>
            </div>
            <a href="{{ $informasi->gambar_url }}" target="_blank" rel="noopener" class="mt-4 inline-block">
                <img src="{{ $informasi->gambar_url }}" alt="{{ $informasi->judul }}" class="w-full max-w-xl mx-auto rounded-2xl border border-white/10 object-contain bg-white/5" />
            </a>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.informasi.update', $informasi) }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Judul Informasi</label>
                <input required name="judul" value="{{ old('judul', $informasi->judul) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('judul') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Deskripsi (opsional)</label>
                <textarea name="deskripsi" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-3">{{ old('deskripsi', $informasi->deskripsi) }}</textarea>
                @error('deskripsi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Ganti Gambar (opsional)</label>
                <input type="file" accept="image/*" name="gambar" class="mt-2 w-full file:mr-3 file:h-11 file:px-4 file:rounded-xl file:bg-white/10 file:border-0 file:text-emerald-100 hover:file:bg-white/20 h-14 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-1">
                <div class="text-xs text-emerald-100/50 mt-1">Maksimal 4 MB, format JPG/PNG/WebP. Kosongkan jika tidak ingin diganti.</div>
                @error('gambar') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                @if(!empty($informasi->gambar_path))
                    <label class="mt-3 flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                        <input type="checkbox" name="hapus_gambar" value="1" @checked((bool) old('hapus_gambar')) class="rounded border-white/20 bg-white/10 text-rose-500 focus:ring-rose-400" />
                        <span class="text-sm text-rose-200/90">Hapus gambar yang sekarang</span>
                    </label>
                @endif
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Status</label>
                <label class="mt-2 flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="is_aktif" value="1" @checked((bool) old('is_aktif', $informasi->is_aktif)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">Aktif dan tampilkan ke mahasiswa</span>
                </label>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Aktif (opsional)</label>
                <input type="date" name="tanggal_aktif" value="{{ old('tanggal_aktif', $informasi->tanggal_aktif?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal_aktif') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Kadaluarsa (opsional)</label>
                <input type="date" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa', $informasi->tanggal_kadaluarsa?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal_kadaluarsa') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Perubahan
            </button>
        </div>
    </form>

    @if(!empty($informasi->share_url))
        @push('scripts')
        <script>
            document.getElementById('copyLinkBtn')?.addEventListener('click', function () {
                const url = @json($informasi->share_url);
                const done = () => {
                    const btn = document.getElementById('copyLinkBtn');
                    if (!btn) return;
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
                    setTimeout(() => { btn.innerHTML = original; }, 1800);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(done).catch(() => {
                        const ta = document.createElement('textarea');
                        ta.value = url;
                        document.body.appendChild(ta);
                        ta.select();
                        try { document.execCommand('copy'); done(); } catch (e) {}
                        document.body.removeChild(ta);
                    });
                }
            });
        </script>
        @endpush
    @endif
</x-portal-layout>
