<x-portal-layout :title="'Mata Kuliah - '.config('app.name')" subtitle="Mata Kuliah">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Daftar mata kuliah yang diampu dosen dan upload RPS.</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Kode</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Jurusan</th>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Contoh RPS (Admin)</th>
                        <th class="text-left font-medium px-4 py-3">RPS Dosen</th>
                        <th class="text-right font-medium px-4 py-3">Upload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->kode }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div class="font-medium text-white">{{ $row->nama }}</div>
                                <div class="text-xs text-emerald-100/60 mt-1">SKS: {{ $row->sks }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->jurusan }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">S{{ $row->semester }}</td>
                            <td class="px-4 py-3">
                                @if ($row->rps_admin_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dosen.mata-kuliah.rps-admin.preview', $row) }}" target="_blank"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-eye"></i>
                                            <span class="text-sm font-medium">Preview</span>
                                        </a>
                                        <a href="{{ route('dosen.mata-kuliah.rps-admin.download', $row) }}"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-download"></i>
                                            <span class="text-sm font-medium">Download</span>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-emerald-100/60">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($row->rps_dosen_path)
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dosen.mata-kuliah.rps-dosen.preview', $row) }}" target="_blank"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-eye"></i>
                                            <span class="text-sm font-medium">Preview</span>
                                        </a>
                                        <a href="{{ route('dosen.mata-kuliah.rps-dosen.download', $row) }}"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/20 border border-emerald-400/25 transition">
                                            <i class="fa-solid fa-download"></i>
                                            <span class="text-sm font-medium">Download</span>
                                        </a>
                                        <form method="POST" action="{{ route('dosen.mata-kuliah.rps.destroy', $row) }}" onsubmit="return confirm('Hapus file RPS Dosen ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-400/25 transition text-red-100">
                                                <i class="fa-solid fa-trash"></i>
                                                <span class="text-sm font-medium">Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-emerald-100/60">Belum upload</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <form method="POST" action="{{ route('dosen.mata-kuliah.rps.upload', $row) }}" enctype="multipart/form-data" class="flex items-center justify-end gap-2">
                                    @csrf
                                    <input type="file" name="rps_dosen" accept=".pdf,.doc,.docx"
                                           class="w-64 max-w-full h-9 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-3 file:h-9 file:border-0 file:bg-white/10 file:text-white file:px-3 file:cursor-pointer" required />
                                    <button class="h-9 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                                        Upload
                                    </button>
                                </form>
                                @error('rps_dosen')
                                    <div class="mt-2 text-xs text-red-200 text-right">{{ $message }}</div>
                                @enderror
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah yang ditetapkan untuk dosen ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-portal-layout>
