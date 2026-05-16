<x-portal-layout :title="'Tambah Kalender Akademik - '.config('app.name')" subtitle="Kalender Akademik">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Tambah Kegiatan</div>
            <div class="text-sm text-emerald-100/70">Input kegiatan kalender akademik.</div>
        </div>
        <a href="{{ route('admin.kalender-akademik.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5 max-w-2xl">
        <form method="POST" action="{{ route('admin.kalender-akademik.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Judul</label>
                <input name="judul" value="{{ old('judul') }}" required
                       class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                @error('judul')
                    <div class="mt-1 text-sm text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required
                           class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                    @error('tanggal_mulai')
                        <div class="mt-1 text-sm text-red-200">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Selesai (Opsional)</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                           class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                    @error('tanggal_selesai')
                        <div class="mt-1 text-sm text-red-200">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategori (Opsional)</label>
                <input name="kategori" value="{{ old('kategori') }}"
                       class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
                @error('kategori')
                    <div class="mt-1 text-sm text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="4"
                          class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="mt-1 text-sm text-red-200">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-portal-layout>

