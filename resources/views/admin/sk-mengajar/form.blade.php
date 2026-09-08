<x-portal-layout :title="($isEdit ? 'Edit' : 'Upload').' SK Mengajar - '.config('app.name')" subtitle="Upload 1x per Prodi, otomatis muncul untuk SEMUA Dosen di Prodi tersebut">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <form method="POST" enctype="multipart/form-data" action="{{ $isEdit ? route('admin.sk-mengajar.update', $row) : route('admin.sk-mengajar.store') }}" class="max-w-4xl">
        @csrf @if($isEdit) @method('PUT') @endif
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <div class="text-xl font-semibold">{{ $isEdit ? 'Edit SK Mengajar' : 'Upload SK Mengajar' }}</div>
                <div class="text-sm text-emerald-100/70">Pilih Prodi lalu upload PDF. 1x upload → otomatis muncul untuk semua dosen di Prodi tersebut.</div>
            </div>
            <a href="{{ route('admin.sk-mengajar.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Kembali</a>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-emerald-500/15 via-white/5 to-white/5 border border-emerald-400/30 p-6 mb-4">
            <div class="flex items-start gap-4">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/40 flex items-center justify-center text-emerald-300"><i class="fa-solid fa-building-columns text-xl"></i></div>
                <div class="flex-1 space-y-5">
                    <div>
                        <label class="block font-semibold mb-2">Pilih Prodi / Program Studi <span class="text-red-400">*</span></label>
                        <select name="prodi" required class="w-full h-12 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-base">
                            <option value="">Pilih Prodi...</option>
                            @foreach ($prodies as $p)
                                <option value="{{ $p }}" {{ old('prodi', $row->program_studi ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                        @error('prodi') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        @error('dosen_id') <div class="mt-1 text-xs text-red-400">{!! $message !!}</div> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-2">Upload File PDF SK Mengajar <span class="text-red-400">*</span></label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <label class="flex-1 w-full">
                                <input type="file" name="file_pdf_upload" accept="application/pdf" {{ $isEdit ? '' : 'required' }} class="file:mr-3 file:py-2.5 file:px-5 file:rounded-lg file:border-0 file:bg-emerald-500 file:text-white file:font-semibold hover:file:bg-emerald-400 file:cursor-pointer w-full p-2 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400" />
                            </label>
                            @if ($isEdit && !empty($row->file_pdf))
                                <label class="inline-flex items-center gap-2 text-sm text-red-300">
                                    <input type="checkbox" name="hapus_file_pdf" value="1" class="h-4 w-4 rounded bg-white/5 border-white/20 text-red-500 focus:ring-red-400" />
                                    Hapus file lama
                                </label>
                            @endif
                        </div>
                        @if (!empty($row->file_pdf))
                            <div class="mt-3 flex items-center gap-2 text-sm text-emerald-200/90 bg-white/5 rounded-xl border border-white/10 px-4 py-3">
                                <i class="fa-solid fa-file-pdf text-red-400 text-lg"></i>
                                <span class="truncate flex-1">{{ basename($row->file_pdf) }}</span>
                                @php $url = Storage::disk('public')->url($row->file_pdf); @endphp
                                <a href="{{ $url }}" target="_blank" class="ml-2 h-8 px-4 inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/15 border border-emerald-400/30 hover:bg-emerald-500/25 transition text-xs font-medium">
                                    <i class="fa-solid fa-eye"></i> Preview
                                </a>
                            </div>
                        @endif
                        @error('file_pdf_upload') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="h-12 px-7 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-cloud-arrow-up"></i> Simpan & Upload
            </button>
        </div>
    </form>
</x-portal-layout>
