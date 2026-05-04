<x-portal-layout :title="'Tambah Mata Kuliah - '.config('app.name')" subtitle="Tambah Mata Kuliah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Masukkan data mata kuliah.</div>
        </div>
        <a href="{{ route('admin.mata-kuliah.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.mata-kuliah.store') }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-emerald-100/80">Kode</label>
                <input name="kode" value="{{ old('kode') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('kode') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Nama</label>
                <input name="nama" value="{{ old('nama') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('nama') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Jurusan</label>
                <select name="jurusan" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
                    <option value="" class="text-black">Pilih Jurusan</option>
                    @foreach ($jurusanList as $j)
                        <option value="{{ $j }}" @selected(old('jurusan', $defaultJurusan) === $j) class="text-black">{{ $j }}</option>
                    @endforeach
                </select>
                @error('jurusan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">SKS</label>
                <input type="number" name="sks" value="{{ old('sks', 3) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('sks') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Semester</label>
                <input type="number" name="semester" value="{{ old('semester', $defaultSemester ?? 1) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('semester') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div class="lg:col-span-2">
                <label class="text-sm text-emerald-100/80">Dosen Pengampu (opsional)</label>
                <select name="dosen_id" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="" class="text-black">-</option>
                    @foreach ($dosen as $d)
                        <option value="{{ $d->id }}" @selected(old('dosen_id') == $d->id) class="text-black">{{ $d->nama }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
                @error('dosen_id') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
