<x-portal-layout :title="'Tambah Publikasi - '.config('app.name')" subtitle="Tambah Publikasi">
    <x-slot:sidebar>
        @include($routePrefix . '.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold text-white">Tambah Publikasi</div>
            <div class="text-sm text-emerald-100/70">Lengkapi data publikasi baru.</div>
        </div>
        <a href="{{ route($routePrefix . '.publikasi.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route($routePrefix . '.publikasi.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Judul Publikasi</label>
                <input type="text" name="judul" value="{{ old('judul') }}" required autocomplete="off"
                       style="background-color: #06221c !important;"
                       class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white">
                @error('judul') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Kategori</label>
                <select name="kategori" required
                        style="background-color: #06221c !important;"
                        class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white cursor-pointer">
                    <option value="" disabled selected class="bg-[#0d2a23]">Pilih Kategori</option>
                    @foreach(['Penelitian', 'PKM', 'HAKI', 'Buku', 'Sertifikat'] as $kat)
                        <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }} class="bg-[#0d2a23]">{{ $kat }}</option>
                    @endforeach
                </select>
                @error('kategori') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Penulis</label>
                <input type="text" name="penulis" value="{{ old('penulis') }}" required autocomplete="off"
                       style="background-color: #06221c !important;"
                       class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white">
                @error('penulis') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Penerbit</label>
                <input type="text" name="penerbit" value="{{ old('penerbit') }}" required autocomplete="off"
                       style="background-color: #06221c !important;"
                       class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white">
                @error('penerbit') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit', date('Y')) }}" required
                           style="background-color: #06221c !important;"
                           class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white">
                    @error('tahun_terbit') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Reputasi</label>
                    <select name="reputasi" required
                            style="background-color: #06221c !important;"
                            class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white cursor-pointer">
                        <option value="" disabled selected class="bg-[#0d2a23]">Pilih Reputasi</option>
                        <option value="Internasional" {{ old('reputasi') == 'Internasional' ? 'selected' : '' }} class="bg-[#0d2a23]">Internasional</option>
                        <option value="Regional" {{ old('reputasi') == 'Regional' ? 'selected' : '' }} class="bg-[#0d2a23]">Regional</option>
                        <option value="Nasional" {{ old('reputasi') == 'Nasional' ? 'selected' : '' }} class="bg-[#0d2a23]">Nasional</option>
                        <option value="tidakbersinta" {{ old('reputasi') == 'tidakbersinta' ? 'selected' : '' }} class="bg-[#0d2a23]">Tidak Bersinta</option>
                    </select>
                    @error('reputasi') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">URL Link (Opsional)</label>
                <input type="url" name="url_link" value="{{ old('url_link') }}" placeholder="https://example.com"
                       style="background-color: #06221c !important;"
                       class="w-full h-11 px-4 rounded-xl border border-white/10 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 transition outline-none text-white">
                @error('url_link') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-emerald-100/80 mb-1.5">Upload Dokumen (PDF/DOC/Gambar)</label>
                <div class="flex items-center gap-4">
                    <input type="file" name="file"
                           style="background-color: #06221c !important;"
                           class="flex-1 px-4 py-2 rounded-xl border border-white/10 focus:border-emerald-500/50 transition outline-none text-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20">
                    <div class="text-[10px] text-emerald-100/50 leading-tight">
                        Maks. 10MB.<br>Format: PDF, DOC, JPG, PNG.
                    </div>
                </div>
                @error('file') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end">
            <button type="submit" class="h-11 px-8 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-bold text-white shadow-lg shadow-emerald-900/20">
                SIMPAN DATA
            </button>
        </div>
    </form>
</x-portal-layout>