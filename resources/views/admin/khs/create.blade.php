<x-portal-layout :title="'Buat KHS - '.config('app.name')" subtitle="Buat KHS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Buat KHS</div>
            <div class="text-sm text-emerald-100/70">Buat data KHS berdasarkan mahasiswa dan semester.</div>
        </div>
        <a href="{{ route('admin.khs.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.khs.store') }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <label class="text-sm text-emerald-100/80">Mahasiswa</label>
                <select name="mahasiswa_id" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
                    <option value="" class="text-black">Pilih Mahasiswa</option>
                    @foreach ($mahasiswa as $m)
                        <option value="{{ $m->id }}" @selected(old('mahasiswa_id') == $m->id) class="text-black">{{ $m->nama_lengkap }} ({{ $m->npm }})</option>
                    @endforeach
                </select>
                @error('mahasiswa_id') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Semester</label>
                <input type="number" name="semester" value="{{ old('semester', 1) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('semester') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div class="lg:col-span-3">
                <label class="text-sm text-emerald-100/80">Tahun Ajaran</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Contoh: 2026/2027" />
                @error('tahun_ajaran') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Buat & Lanjut Input Nilai
            </button>
        </div>
    </form>
</x-portal-layout>
