<x-portal-layout :title="'Profil Dosen - '.config('app.name')" subtitle="Profil Dosen">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-xl font-semibold">Profil</div>
            <div class="text-sm text-emerald-100/70 mt-1">Perbarui data profil dosen.</div>

            <form method="POST" action="{{ route('dosen.profil.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label class="text-sm text-emerald-100/80">Alamat</label>
                    <textarea name="alamat" rows="4" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('alamat', $dosen?->alamat ?? '') }}</textarea>
                    @error('alamat') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">NIK</label>
                        <input name="nik" value="{{ old('nik', $dosen?->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nik') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Nomor SK</label>
                        <input name="nomor_sk" value="{{ old('nomor_sk', $dosen?->nomor_sk ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nomor_sk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Nomor HP</label>
                        <input name="nomor_hp" value="{{ old('nomor_hp', $dosen?->nomor_hp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nomor_hp') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Mata Kuliah</label>
                        <input name="mata_kuliah" value="{{ old('mata_kuliah', $dosen?->mata_kuliah ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('mata_kuliah') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
                    @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center gap-4">
                @if ($dosen?->foto_path)
                    <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
                @else
                    <div class="h-16 w-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-2xl font-semibold">
                        {{ mb_substr($dosen?->nama ?? auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-semibold">{{ $dosen?->nama ?? auth()->user()->name }}</div>
                    <div class="text-sm text-emerald-100/70">{{ $dosen?->nidn ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm text-emerald-100/75">
                <div class="flex items-center justify-between">
                    <span>Email</span>
                    <span class="font-medium text-white">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>NIK</span>
                    <span class="font-medium text-white">{{ $dosen?->nik ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Nomor SK</span>
                    <span class="font-medium text-white">{{ $dosen?->nomor_sk ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
