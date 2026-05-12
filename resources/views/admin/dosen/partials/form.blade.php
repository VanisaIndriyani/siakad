@php
    $isEdit = isset($dosen);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="text-sm text-emerald-100/80">Nama</label>
        <input name="nama" value="{{ old('nama', $dosen->nama ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
        @error('nama') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">NIDN</label>
        <input name="nidn" value="{{ old('nidn', $dosen->nidn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
        @error('nidn') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">NUPTK</label>
        <input name="nuptk" value="{{ old('nuptk', $dosen->nuptk ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nuptk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Nomor HP</label>
        <input name="nomor_hp" value="{{ old('nomor_hp', $dosen->nomor_hp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nomor_hp') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Mata Kuliah</label>
        <input name="mata_kuliah" value="{{ old('mata_kuliah', $dosen->mata_kuliah ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('mata_kuliah') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Alamat</label>
        <textarea name="alamat" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('alamat', $dosen->alamat ?? '') }}</textarea>
        @error('alamat') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Foto</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
        @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        @if ($isEdit && $dosen->foto_path)
            <div class="mt-3">
                <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-20 w-20 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
            </div>
        @endif
        <div class="mt-2 text-xs text-emerald-100/60">Akun login otomatis dibuat: email berbasis NIDN dan password default: password</div>
    </div>
</div>
