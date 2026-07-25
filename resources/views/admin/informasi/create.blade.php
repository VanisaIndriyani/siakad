<x-portal-layout :title="'Tambah Informasi - '.config('app.name')" subtitle="Tambah Informasi">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Informasi</div>
            <div class="text-sm text-emerald-100/70">Buat informasi / pengumuman baru untuk ditampilkan ke mahasiswa.</div>
        </div>
        <a href="{{ route('admin.informasi.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.informasi.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Judul Informasi</label>
                <input required name="judul" value="{{ old('judul') }}" placeholder="Contoh: Libur Hari Raya..." class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('judul') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Deskripsi (opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Keterangan singkat informasi..." class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Gambar Informasi</label>
                <input type="file" accept="image/*" name="gambar" class="mt-2 w-full file:mr-3 file:h-11 file:px-4 file:rounded-xl file:bg-white/10 file:border-0 file:text-emerald-100 hover:file:bg-white/20 h-14 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-1">
                <div class="text-xs text-emerald-100/50 mt-1">Maksimal 4 MB, format JPG/PNG/WebP.</div>
                @error('gambar') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Status</label>
                <label class="mt-2 flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="is_aktif" value="1" @checked((bool) old('is_aktif', true)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">Aktif dan tampilkan ke mahasiswa</span>
                </label>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Aktif (opsional)</label>
                <input type="date" name="tanggal_aktif" value="{{ old('tanggal_aktif') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal_aktif') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Kadaluarsa (opsional)</label>
                <input type="date" name="tanggal_kadaluarsa" value="{{ old('tanggal_kadaluarsa') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal_kadaluarsa') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Informasi
            </button>
        </div>
    </form>
</x-portal-layout>
