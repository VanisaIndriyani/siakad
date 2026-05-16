<x-portal-layout :title="'Ajukan PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Ajukan PPL</div>
            <div class="text-sm text-emerald-100/70">Ajukan instansi/sekolah untuk PPL.</div>
        </div>
        <a href="{{ route('mahasiswa.ppl.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('mahasiswa.ppl.store') }}" class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-4">
        @csrf

        <div>
            <label class="text-sm text-emerald-100/80">Nama Instansi / Sekolah</label>
            <input name="instansi_nama" value="{{ old('instansi_nama') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
            @error('instansi_nama') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-emerald-100/80">Alamat Instansi (Opsional)</label>
            <textarea name="instansi_alamat" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('instansi_alamat') }}</textarea>
            @error('instansi_alamat') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="text-sm text-emerald-100/80">Keterangan (Opsional)</label>
            <textarea name="keterangan" rows="4" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('keterangan') }}</textarea>
            @error('keterangan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Kirim Pengajuan
            </button>
        </div>
    </form>
</x-portal-layout>

