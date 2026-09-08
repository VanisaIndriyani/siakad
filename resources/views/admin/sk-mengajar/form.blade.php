<x-portal-layout :title="($isEdit ? 'Edit' : 'Tambah').' SK Mengajar - '.config('app.name')" subtitle="Upload file PDF SK Mengajar atau isi data manual">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.sk-mengajar.update', $row) : route('admin.sk-mengajar.store') }}" class="max-w-4xl">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <div class="text-xl font-semibold">{{ $isEdit ? 'Edit SK Mengajar' : 'Tambah SK Mengajar' }}</div>
                <div class="text-sm text-emerald-100/70">Cukup upload file PDF — file langsung muncul di halaman Input Nilai Dosen.</div>
            </div>
            <a href="{{ route('admin.sk-mengajar.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Kembali</a>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-emerald-500/10 via-white/5 to-white/5 border border-emerald-400/30 p-5 mb-4">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-11 h-11 rounded-xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-300"><i class="fa-solid fa-cloud-arrow-up text-lg"></i></div>
                <div class="flex-1">
                    <div class="font-semibold mb-1">Upload File PDF SK Mengajar (Disarankan)</div>
                    <div class="text-sm text-emerald-100/70 mb-3">File PDF yang diupload akan langsung terlihat & ter-download di halaman Input Nilai Dosen. Maks 10MB. Format PDF only.</div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <label class="flex-1 w-full">
                            <input type="file" name="file_pdf_upload" accept="application/pdf" class="file:mr-3 file:py-2 file:px-5 file:rounded-lg file:border-0 file:bg-emerald-500 file:text-white file:font-semibold hover:file:bg-emerald-400 file:cursor-pointer w-full p-1.5 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400" />
                        </label>
                        @if ($isEdit && !empty($row->file_pdf))
                            <label class="inline-flex items-center gap-2 text-sm text-red-300">
                                <input type="checkbox" name="hapus_file_pdf" value="1" class="h-4 w-4 rounded bg-white/5 border-white/20 text-red-500 focus:ring-red-400" />
                                Hapus file lama
                            </label>
                        @endif
                    </div>
                    @if (!empty($row->file_pdf))
                        <div class="mt-3 flex items-center gap-2 text-sm text-emerald-200/80 bg-white/5 rounded-xl border border-white/10 px-3 py-2">
                            <i class="fa-solid fa-file-pdf text-red-400"></i>
                            <span class="truncate flex-1">{{ basename($row->file_pdf) }}</span>
                            @php
                                $url = Storage::disk('public')->url($row->file_pdf);
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="ml-2 h-7 px-3 inline-flex items-center gap-1 rounded-lg bg-emerald-500/15 border border-emerald-400/30 hover:bg-emerald-500/25 transition text-xs font-medium">
                                <i class="fa-solid fa-eye"></i> Preview
                            </a>
                        </div>
                    @endif
                    @error('file_pdf_upload') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <details class="rounded-2xl bg-white/5 border border-white/10 mb-3" @if(empty($row->file_pdf) && old('_token') === null) open @endif>
            <summary class="cursor-pointer select-none px-5 py-3.5 text-sm font-semibold flex items-center gap-2 hover:bg-white/5 rounded-t-2xl">
                <i class="fa-solid fa-caret-down"></i> Isi data manual (opsional — jika tidak ada file PDF, halaman dosen akan generate SK otomatis dari data ini)
            </summary>
            <div class="p-5 pt-0 space-y-5">
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
            </div>
        </details>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="h-11 px-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
