<x-portal-layout :title="'Edit KRS - '.config('app.name')" subtitle="Edit KRS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    @php
        $selectedIds = old('mata_kuliah_id', $selected ?? []);
        $selectedIds = array_map('intval', (array) $selectedIds);
    @endphp

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Edit KRS</div>
            <div class="text-sm text-emerald-100/70">Semester {{ $krs->semester }} • Status: {{ strtoupper($krs->status_approval) }}</div>
        </div>
        <a href="{{ route('mahasiswa.krs.show', $krs) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('mahasiswa.krs.update', $krs) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Tahun Akademik</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $krs->tahun_ajaran) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tahun_ajaran') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6">
            <div class="text-lg font-semibold">Pilih Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Perubahan akan membuat status kembali menjadi pending.</div>
            @error('mata_kuliah_id') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Pilih</th>
                            <th class="text-left font-medium px-4 py-3">Kode</th>
                            <th class="text-left font-medium px-4 py-3">Nama</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($mataKuliah as $mk)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="mata_kuliah_id[]" value="{{ $mk->id }}" @checked(in_array($mk->id, $selectedIds, true)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                                </td>
                                <td class="px-4 py-3 font-medium">{{ $mk->kode }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $mk->nama }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $mk->sks }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Mata kuliah belum tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-portal-layout>
