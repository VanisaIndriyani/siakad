<x-portal-layout :title="'Edit Mata Kuliah - '.config('app.name')" subtitle="Edit Mata Kuliah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Edit Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }}</div>
        </div>
        <a href="{{ route('admin.mata-kuliah.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.mata-kuliah.update', $mataKuliah) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="text-sm text-emerald-100/80">Kode</label>
                <input name="kode" value="{{ old('kode', $mataKuliah->kode) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('kode') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Nama</label>
                <input name="nama" value="{{ old('nama', $mataKuliah->nama) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('nama') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Jurusan</label>
                <select name="jurusan" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
                    <option value="" class="text-black">Pilih Jurusan</option>
                    @foreach ($jurusanList as $j)
                        <option value="{{ $j }}" @selected(old('jurusan', $mataKuliah->jurusan) === $j) class="text-black">{{ $j }}</option>
                    @endforeach
                </select>
                @error('jurusan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">SKS</label>
                <input type="number" name="sks" min="0" value="{{ old('sks', $mataKuliah->sks) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('sks') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Semester</label>
                <input type="number" name="semester" value="{{ old('semester', $mataKuliah->semester) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                @error('semester') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div class="lg:col-span-2 grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Dosen 1 (opsional)</label>
                    <select name="dosen_id" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="" class="text-black">-</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" @selected(old('dosen_id', $mataKuliah->dosen_id) == $d->id) class="text-black">{{ $d->nama }} ({{ $d->nidn }})</option>
                        @endforeach
                    </select>
                    @error('dosen_id') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Dosen 2 (opsional)</label>
                    <select name="dosen_id_2" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="" class="text-black">-</option>
                        @foreach ($dosen as $d)
                            <option value="{{ $d->id }}" @selected(old('dosen_id_2', $mataKuliah->dosen_id_2) == $d->id) class="text-black">{{ $d->nama }} ({{ $d->nidn }})</option>
                        @endforeach
                    </select>
                    @error('dosen_id_2') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Perubahan
            </button>
        </div>
    </form>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="text-lg font-semibold">File RPS</div>
        <div class="mt-1 text-sm text-emerald-100/70">Kelola contoh RPS dari Admin dan lihat RPS yang diupload dosen.</div>

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                <div class="text-sm font-medium">1) Contoh RPS (Admin)</div>
                <div class="mt-2 flex items-center gap-2">
                    @if ($mataKuliah->rps_admin_path)
                        <a href="{{ route('admin.mata-kuliah.rps-admin.download', $mataKuliah) }}"
                           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-download"></i>
                            Download
                        </a>
                        <div class="text-sm text-emerald-100/70 truncate">
                            {{ $mataKuliah->rps_admin_name ?: basename($mataKuliah->rps_admin_path) }}
                        </div>
                    @else
                        <div class="text-sm text-emerald-100/70">Belum ada file.</div>
                    @endif
                </div>

                <form method="POST" action="{{ route('admin.mata-kuliah.rps-admin.upload', $mataKuliah) }}" enctype="multipart/form-data" class="mt-4 flex flex-col sm:flex-row gap-3">
                    @csrf
                    <input type="file" name="rps_admin" accept=".pdf,.doc,.docx"
                           class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-3 file:h-11 file:border-0 file:bg-white/10 file:text-white file:px-3 file:cursor-pointer" required />
                    <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                        Upload
                    </button>
                </form>
                @error('rps_admin') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                <div class="text-sm font-medium">2) RPS Dosen</div>
                <div class="mt-2 flex items-center gap-2">
                    @if ($mataKuliah->rps_dosen_path)
                        <a href="{{ route('admin.mata-kuliah.rps-dosen.download', $mataKuliah) }}"
                           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-400/25 transition">
                            <i class="fa-solid fa-download"></i>
                            Download
                        </a>
                        <div class="text-sm text-emerald-100/70 truncate">
                            {{ $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path) }}
                        </div>
                    @else
                        <div class="text-sm text-emerald-100/70">Belum ada file dari dosen.</div>
                    @endif
                </div>
                <div class="mt-4 text-sm text-emerald-100/60">
                    Upload RPS dosen dilakukan dari akun dosen di menu Mata Kuliah.
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
