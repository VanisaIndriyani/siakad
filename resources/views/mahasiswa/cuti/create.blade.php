<x-portal-layout :title="'Buat Pengajuan Cuti - '.config('app.name')" subtitle="Buat Pengajuan Cuti">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Buat Pengajuan Cuti</div>
            <div class="text-sm text-emerald-100/70">Silakan isi formulir di bawah untuk mengajukan cuti akademik.</div>
        </div>
        <a href="{{ route('mahasiswa.cuti.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 max-w-2xl">
        <form method="POST" action="{{ route('mahasiswa.cuti.store') }}" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm text-emerald-100/80">Semester (untuk pengajuan cuti)</label>
                    <select name="semester" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white">
                        @for ($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" @selected(old('semester') == $i) class="text-black">Semester {{ $i }}</option>
                        @endfor
                    </select>
                    @error('semester') <div class="mt-1 text-xs text-red-200">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tahun Ajaran</label>
                    <input name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" placeholder="Contoh: 2026/2027" />
                    @error('tahun_ajaran') <div class="mt-1 text-xs text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Alasan Cuti</label>
                <textarea name="alasan" rows="4" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" placeholder="Sebutkan alasan Anda mengajukan cuti akademik...">{{ old('alasan') }}</textarea>
                @error('alasan') <div class="mt-1 text-xs text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="pt-2 flex items-center justify-end">
                <button type="submit" class="h-11 px-8 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</x-portal-layout>
