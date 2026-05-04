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
        <label class="text-sm text-emerald-100/80">NIK</label>
        <input name="nik" value="{{ old('nik', $mahasiswa->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nik') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="text-sm text-emerald-100/80">Tempat Lahir</label>
            <input name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa->tempat_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            @error('tempat_lahir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>
        <div>
            <label class="text-sm text-emerald-100/80">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', isset($mahasiswa->tanggal_lahir) ? $mahasiswa->tanggal_lahir->format('Y-m-d') : '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            @error('tanggal_lahir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Nomor Telp</label>
        <input name="nomor_telp" value="{{ old('nomor_telp', $mahasiswa->nomor_telp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('nomor_telp') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Angkatan (Tahun Masuk)</label>
        <input type="number" name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan ?? '') }}" min="1900" max="2100" placeholder="Contoh: 2026" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        <div class="mt-2 text-xs text-emerald-100/60">Isi tahun masuk kuliah (4 digit), bukan semester.</div>
        @error('angkatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
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
        <label class="text-sm text-emerald-100/80">Asal Sekolah</label>
        <input name="asal_sekolah" value="{{ old('asal_sekolah', $mahasiswa->asal_sekolah ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
        @error('asal_sekolah') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="text-sm text-emerald-100/80">Status Mahasiswa</label>
        <select name="status_mahasiswa" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
            @php
                $selected = old('status_mahasiswa', $mahasiswa->status_mahasiswa ?? 'Aktif');
            @endphp
            @foreach (['Aktif', 'Cuti', 'Lulus', 'Nonaktif'] as $opt)
                <option value="{{ $opt }}" @selected($selected === $opt) class="text-black">{{ $opt }}</option>
            @endforeach
        </select>
        @error('status_mahasiswa') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Alamat</label>
        <textarea name="alamat" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('alamat', $mahasiswa->alamat ?? '') }}</textarea>
        @error('alamat') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
    </div>

    <div class="lg:col-span-2">
        <label class="text-sm text-emerald-100/80">Foto</label>
        <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
        @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        @if ($isEdit && $mahasiswa->foto_path)
            <div class="mt-3">
                <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="h-20 w-20 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
            </div>
        @endif
        <div class="mt-2 text-xs text-emerald-100/60">Akun login otomatis dibuat: email berbasis NPM dan password default: password</div>
    </div>
</div>
