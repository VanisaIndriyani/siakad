<x-portal-layout :title="($isEdit ? 'Edit' : 'Tambah').' SK Mengajar - '.config('app.name')" subtitle="Input data SK Mengajar">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <form method="POST" action="{{ $isEdit ? route('admin.sk-mengajar.update', $row) : route('admin.sk-mengajar.store') }}" class="max-w-4xl">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <div class="text-xl font-semibold">{{ $isEdit ? 'Edit SK Mengajar' : 'Tambah SK Mengajar' }}</div>
                <div class="text-sm text-emerald-100/70">Lengkapi data berikut untuk menyimpan SK Mengajar.</div>
            </div>
            <a href="{{ route('admin.sk-mengajar.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Kembali</a>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Mata Kuliah <span class="text-red-400">*</span></label>
                    <select name="mata_kuliah_id" required class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih mata kuliah...</option>
                        @foreach ($mataKuliahs as $mk)
                            <option value="{{ $mk->id }}" {{ old('mata_kuliah_id', $row->mata_kuliah_id) == $mk->id ? 'selected' : '' }}>
                                {{ $mk->kode }} - {{ $mk->nama }} ({{ $mk->jurusan }} • Smt {{ $mk->semester }} • {{ $mk->sks }} SKS)
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
                            <option value="{{ $d->id }}" {{ old('dosen_id', $row->dosen_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }} @if($d->nidn) (NIDN. {{ $d->nidn }}) @endif
                            </option>
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
                    <label class="text-sm text-emerald-100/80">Nomor SK</label>
                    <input name="nomor_sk" value="{{ old('nomor_sk', $row->nomor_sk) }}" placeholder="Contoh: 001/IADD-SK/2026" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('nomor_sk') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal SK</label>
                    <input type="date" name="tanggal_sk" value="{{ old('tanggal_sk', optional($row->tanggal_sk)->format('Y-m-d')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal Berlaku (Mulai)</label>
                    <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', optional($row->tanggal_mulai)->format('Y-m-d')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal Berlaku (Selesai)</label>
                    <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', optional($row->tanggal_selesai)->format('Y-m-d')) }}" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Beban SKS</label>
                    <input name="beban_sks" step="0.1" min="0" value="{{ old('beban_sks', $row->beban_sks) }}" type="number" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Kelas</label>
                    <input name="kelas" value="{{ old('kelas', $row->kelas) }}" placeholder="Contoh: A, B, Pagi-A" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Program Studi</label>
                    <input name="program_studi" value="{{ old('program_studi', $row->program_studi) }}" placeholder="Contoh: Pendidikan Agama Islam" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Jabatan Dosen</label>
                    <input name="jabatan_dosen" value="{{ old('jabatan_dosen', $row->jabatan_dosen) }}" placeholder="Contoh: Lektor / Asisten Ahli" class="mt-1 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Tugas Tambahan</label>
                <textarea name="tugas_tambahan" rows="2" class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('tugas_tambahan', $row->tugas_tambahan) }}</textarea>
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">Catatan</label>
                <textarea name="catatan" rows="2" class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('catatan', $row->catatan) }}</textarea>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="h-11 px-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</x-portal-layout>
