@php
    $isEdit = isset($mahasiswa);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="text-sm text-emerald-100/80">Nama Lengkap</label>
        <input name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
        @error('nama_lengkap') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">NPM</label>
        <input name="npm" value="{{ old('npm', $mahasiswa->npm ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
        @error('npm') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Program Studi</label>
        <select name="program_studi" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
            @php
                $selectedProdi = old('program_studi', $mahasiswa->program_studi ?? '');
            @endphp
            <option value="" @selected($selectedProdi === '') class="text-black">Pilih Program Studi</option>
            @foreach (($jurusan ?? []) as $opt)
                <option value="{{ $opt }}" @selected($selectedProdi === $opt) class="text-black">{{ $opt }}</option>
            @endforeach
        </select>
        @error('program_studi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Fakultas</label>
        <select name="fakultas" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
            @php
                $selectedFak = old('fakultas', $mahasiswa->fakultas ?? '');
            @endphp
            <option value="" @selected($selectedFak === '') class="text-black">-</option>
            @foreach (($fakultasList ?? []) as $opt)
                <option value="{{ $opt }}" @selected($selectedFak === $opt) class="text-black">{{ $opt }}</option>
            @endforeach
        </select>
        @error('fakultas') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Angkatan (Tahun Masuk)</label>
        <input type="number" name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan ?? date('Y')) }}" min="1900" max="2100" placeholder="Contoh: 2026" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('angkatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Status Mahasiswa</label>
        <select name="status_mahasiswa" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
            @php
                $selected = old('status_mahasiswa', $mahasiswa->status_mahasiswa ?? 'Aktif');
            @endphp
            @foreach (['Aktif', 'Cuti', 'Alumni', 'Nonaktif'] as $opt)
                <option value="{{ $opt }}" @selected($selected === $opt) class="text-black">{{ $opt }}</option>
            @endforeach
        </select>
        @error('status_mahasiswa') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Foto (Opsional)</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
        @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        <div class="mt-2 text-xs text-emerald-100/60 italic">Admin hanya menginput data dasar. Mahasiswa dapat melengkapi biodata PD-DIKTI melalui profil mereka sendiri.</div>
    </div>
</div>
