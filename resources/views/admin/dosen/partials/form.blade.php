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
        <label class="text-sm text-emerald-100/80">NIDN (Sebagai ID Login)</label>
        <input name="nidn" value="{{ old('nidn', $dosen->nidn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
        @error('nidn') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">NIK (Opsional)</label>
        <input name="nik" value="{{ old('nik', $dosen->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nik') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Nomor SK (Opsional)</label>
        <input name="nomor_sk" value="{{ old('nomor_sk', $dosen->nomor_sk ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nomor_sk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Foto (Opsional)</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
        @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        <div class="mt-2 text-xs text-emerald-100/60 italic">Admin hanya menginput data dasar. Dosen dapat melengkapi biodata lainnya melalui profil mereka sendiri.</div>
    </div>
</div>
