<x-portal-layout :title="'Buat KRS - '.config('app.name')" subtitle="Buat KRS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Buat KRS</div>
            <div class="text-sm text-emerald-100/70">Pilih mata kuliah untuk semester {{ $semester }}.</div>
        </div>
        <a href="{{ route('mahasiswa.krs.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('mahasiswa.krs.create') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Semester</label>
                <select name="semester" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" onchange="this.form.submit()">
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" @selected((int) $semester === $i) class="text-black">Semester {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </form>

        <form method="POST" action="{{ route('mahasiswa.krs.store') }}" class="mt-5">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}" />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-2">
                    <label class="text-sm text-emerald-100/80">Tahun Akademik</label>
                    <input name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Contoh: 2026/2027" />
                    @error('tahun_ajaran') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-6">
                <div class="text-lg font-semibold">Pilih Mata Kuliah</div>
                <div class="text-sm text-emerald-100/70">Minimal pilih 1 mata kuliah.</div>
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
                                <th class="text-left font-medium px-4 py-3">Dosen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($mataKuliah as $mk)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="mata_kuliah_id[]" value="{{ $mk->id }}" @checked(is_array(old('mata_kuliah_id')) && in_array($mk->id, old('mata_kuliah_id'))) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ $mk->kode }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $mk->nama }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $mk->sks }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $mk->dosen?->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Mata kuliah untuk semester ini belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end">
                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Simpan KRS
                </button>
            </div>
        </form>
    </div>
</x-portal-layout>
