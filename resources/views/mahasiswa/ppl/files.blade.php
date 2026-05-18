<x-portal-layout :title="'Upload Laporan PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Upload Laporan PPL</div>
            <div class="mt-1 text-sm text-emerald-100/70">Upload laporan PPL beserta keterangan, lalu bisa preview, download, dan hapus.</div>
        </div>
    </div>

    @if (! $ppl)
        <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-6 text-emerald-100/80">
            Belum ada pengajuan PPL.
        </div>
    @else
        <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-6">
            <div class="text-sm text-emerald-100/70">Instansi/Sekolah</div>
            <div class="mt-1 font-semibold">{{ $ppl->instansi_nama }}</div>
            <div class="mt-3 text-sm text-emerald-100/70">Status: <span class="font-semibold text-white">{{ strtoupper($ppl->status) }}</span></div>
        </div>

        <form method="POST" action="{{ route('mahasiswa.ppl-files.store') }}" enctype="multipart/form-data" class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-6">
            @csrf
            <div class="text-sm font-semibold">Upload Baru</div>
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">File (PDF/DOC/DOCX)</label>
                    <input type="file" name="file" accept=".pdf,.doc,.docx"
                           class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-3 file:h-11 file:border-0 file:bg-white/10 file:text-white file:px-3 file:cursor-pointer" />
                    @error('file') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Keterangan</label>
                    <input name="keterangan" value="{{ old('keterangan') }}"
                           class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                           placeholder="Contoh: Laporan PPL minggu 1" />
                    @error('keterangan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 flex items-center justify-end">
                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Upload
                </button>
            </div>
        </form>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="px-6 py-4 text-sm font-semibold">Daftar File</div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-6 py-3 w-14">No</th>
                            <th class="text-left font-medium px-6 py-3">Nama File</th>
                            <th class="text-left font-medium px-6 py-3">Oleh</th>
                            <th class="text-left font-medium px-6 py-3">Keterangan</th>
                            <th class="text-left font-medium px-6 py-3 w-44">Tanggal</th>
                            <th class="text-right font-medium px-6 py-3 w-56">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($ppl->files as $i => $f)
                            <tr class="hover:bg-white/5">
                                <td class="px-6 py-3">{{ $i + 1 }}</td>
                                <td class="px-6 py-3 text-emerald-100/90">
                                    <div class="font-semibold">{{ $f->file_name }}</div>
                                </td>
                                <td class="px-6 py-3">
                                    @if($f->creator)
                                        <div class="font-semibold text-emerald-100/90">{{ $f->creator->name }}</div>
                                        <div class="text-xs text-emerald-100/60">{{ ucfirst($f->creator->role) }}</div>
                                    @else
                                        <span class="text-emerald-100/50">---</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-emerald-100/80 whitespace-pre-line">{{ $f->keterangan ?: '-' }}</td>
                                <td class="px-6 py-3 text-emerald-100/70">{{ $f->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center justify-end gap-2 flex-wrap">
                                        <a href="{{ route('mahasiswa.ppl-files.preview', $f) }}" target="_blank"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-eye"></i>
                                            Preview
                                        </a>
                                        <a href="{{ route('mahasiswa.ppl-files.download', $f) }}"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-download"></i>
                                            Download
                                        </a>
                                        <form method="POST" action="{{ route('mahasiswa.ppl-files.destroy', $f) }}" onsubmit="return confirm('Hapus file ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition">
                                                <i class="fa-solid fa-trash"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-emerald-100/70">Belum ada file diupload.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-portal-layout>

