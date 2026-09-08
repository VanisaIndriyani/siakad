<x-portal-layout :title="($isEdit ? 'Edit' : 'Tambah').' SK Mengajar - '.config('app.name')" subtitle="Pilih dosen lalu upload file PDF SK Mengajar">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.sk-mengajar.update', $row) : route('admin.sk-mengajar.store') }}" class="max-w-4xl">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <div class="text-xl font-semibold">{{ $isEdit ? 'Edit SK Mengajar' : 'Tambah SK Mengajar' }}</div>
                <div class="text-sm text-emerald-100/70">Cukup pilih dosen lalu upload PDF — otomatis muncul di halaman Input Nilai Dosen.</div>
            </div>
            <a href="{{ route('admin.sk-mengajar.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Kembali</a>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-emerald-500/12 via-white/5 to-white/5 border border-emerald-400/30 p-5 mb-4">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-11 h-11 rounded-xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-300"><i class="fa-solid fa-user-tie text-lg"></i></div>
                <div class="flex-1">
                    <div class="font-semibold mb-1">Pilih Dosen Pengampu <span class="text-red-400">*</span></div>
                    <div class="text-sm text-emerald-100/70 mb-3">Data mata kuliah, semester, dan beban SKS otomatis diambil dari mata kuliah pertama yang diampu dosen ini.</div>
                    <select name="dosen_id" required class="w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                        <option value="">Pilih dosen...</option>
                        @foreach ($dosens as $d)
                            <option value="{{ $d->id }}" {{ old('dosen_id', $row->dosen_id) == $d->id ? 'selected' : '' }}>
                                {{ $d->nama }} @if($d->nidn) (NIDN. {{ $d->nidn }}) @elseif($d->nuptk) (NUPTK. {{ $d->nuptk }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('dosen_id') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror

                    @if (!empty($row->dosen_id) && !empty($row->mata_kuliah_id) && !empty($mataKuliahs))
                        @php
                            $mk = $mataKuliahs->firstWhere('id', $row->mata_kuliah_id);
                        @endphp
                        @if ($mk)
                            <div class="mt-4 p-3 rounded-xl bg-white/5 border border-white/10 text-sm space-y-1">
                                <div class="flex items-center gap-2"><i class="fa-solid fa-book text-emerald-400 w-5"></i><span class="text-emerald-100/80">Mata Kuliah:</span><span class="font-medium">{{ $mk->kode }} — {{ $mk->nama }}</span></div>
                                <div class="flex items-center gap-2"><i class="fa-solid fa-calendar-days text-emerald-400 w-5"></i><span class="text-emerald-100/80">Semester:</span><span class="font-medium">{{ $mk->semester }} • {{ $mk->sks }} SKS</span></div>
                                @if (!empty($mk->jurusan))<div class="flex items-center gap-2"><i class="fa-solid fa-building-columns text-emerald-400 w-5"></i><span class="text-emerald-100/80">Prodi:</span><span class="font-medium">{{ $mk->jurusan }}</span></div>@endif
                            </div>
                        @endif
                    @endif
                </div>
            </div>
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

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="h-11 px-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-floppy-disk"></i> Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
