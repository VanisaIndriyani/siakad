<x-portal-layout :title="($isEdit ? 'Edit' : 'Tambah').' Roster - '.config('app.name')" subtitle="Input detail jadwal pertemuan">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <form method="POST" action="{{ $isEdit ? route('admin.roster.update', $row) : route('admin.roster.store') }}" class="max-w-4xl">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <div class="text-xl font-semibold">{{ $isEdit ? 'Edit Roster Pertemuan' : 'Tambah Roster Pertemuan' }}</div>
                <div class="text-sm text-emerald-100/70">Isi jadwal pertemuan sesuai kontrak perkuliahan.</div>
            </div>
            <a href="{{ route('admin.roster.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Kembali</a>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Mata Kuliah <span class="text-red-400">*</span></label>
                    <select name="mata_kuliah_id" required class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih mata kuliah...</option>
                        @foreach ($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id', $row->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->jurusan }} • Smt {{ $mk->semester }})
                            </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Dosen Pengampu</label>
                    <select name="dosen_id" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih dosen...</option>
                        @foreach ($dosens as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_id', $row->dosen_id) == $d->id ? 'selected' : '' }}>{{ $d->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Semester <span class="text-red-400">*</span></label>
                    <select name="semester" required class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        @foreach (range(1, 8) as $s)
                            <option value="{{ $s }}" {{ old('semester', $row->semester) == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tahun Ajaran</label>
                    <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $row->tahun_ajaran) }}" placeholder="Contoh: 2026/2027" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Pertemuan Ke- <span class="text-red-400">*</span></label>
                    <input name="pertemuan_ke" required type="number" min="1" max="30" value="{{ old('pertemuan_ke', $row->pertemuan_ke) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Hari</label>
                    <select name="hari" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih hari...</option>
                        @foreach ($listHari as $h)
                            <option value="{{ $h }}" {{ old('hari', $row->hari) === $h ? 'selected' : '' }}>{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Jam Mulai</label>
                    <input type="time" name="jam_mulai" value="{{ old('jam_mulai', optional($row->jam_mulai)?->format('H:i')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Jam Selesai</label>
                    <input type="time" name="jam_selesai" value="{{ old('jam_selesai', optional($row->jam_selesai)?->format('H:i')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Ruang</label>
                    <input name="ruang" value="{{ old('ruang', $row->ruang) }}" placeholder="Contoh: R-201, Daring via Zoom" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal Pertemuan</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', optional($row->tanggal)?->format('Y-m-d')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-emerald-100/80">Metode Pembelajaran</label>
                    <select name="metode_pembelajaran" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih metode...</option>
                        @foreach ($listMetode as $m)
                            <option value="{{ $m }}" {{ old('metode_pembelajaran', $row->metode_pembelajaran) === $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-emerald-100/80">Materi Pembelajaran</label>
                    <input name="materi" value="{{ old('materi', $row->materi) }}" placeholder="Contoh: Pengantar Audit Syariah" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-emerald-100/80">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('keterangan', $row->keterangan) }}</textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="h-11 px-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</x-portal-layout>
