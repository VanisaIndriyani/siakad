<x-portal-layout :title="'Edit KHS - '.config('app.name')" subtitle="Edit KHS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    @php
        $selectedIds = old('mata_kuliah_id', $khs->items->pluck('mata_kuliah_id')->all());
        $selectedIds = array_map('strval', (array) $selectedIds);
        $existingMap = $khs->items->keyBy('mata_kuliah_id');
    @endphp

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Edit KHS</div>
            <div class="text-sm text-emerald-100/70">{{ $khs->mahasiswa?->nama_lengkap }} • Semester {{ $khs->semester }}</div>
        </div>
        <a href="{{ route('admin.khs.show', $khs) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.khs.update', $khs) }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <label class="text-sm text-emerald-100/80">Tahun Ajaran</label>
                <input name="tahun_ajaran" value="{{ old('tahun_ajaran', $khs->tahun_ajaran) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tahun_ajaran') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">IPS</label>
                <input type="number" step="0.01" name="ips" value="{{ old('ips', $khs->ips) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('ips') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="text-sm text-emerald-100/80">IPK</label>
                <input type="number" step="0.01" name="ipk" value="{{ old('ipk', $khs->ipk) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('ipk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mt-6">
            <div class="text-lg font-semibold">Mata Kuliah & Nilai</div>
            <div class="text-sm text-emerald-100/70">Centang mata kuliah yang ingin dimasukkan ke KHS.</div>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Pilih</th>
                            <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Angka</th>
                            <th class="text-left font-medium px-4 py-3">Nilai Huruf</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($mataKuliah as $mk)
                            @php
                                $checked = in_array((string) $mk->id, $selectedIds, true);
                                $item = $existingMap->get($mk->id);
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="mata_kuliah_id[]" value="{{ $mk->id }}" @checked($checked) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $mk->kode }} - {{ $mk->nama }}</div>
                                    <div class="text-xs text-emerald-100/60">Semester {{ $mk->semester }}</div>
                                </td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $mk->sks }}</td>
                                <td class="px-4 py-3">
                                    <input type="number" step="0.01" name="nilai_angka[{{ $mk->id }}]" value="{{ old('nilai_angka.'.$mk->id, $item?->nilai_angka) }}" class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </td>
                                <td class="px-4 py-3">
                                    <input name="nilai_huruf[{{ $mk->id }}]" value="{{ old('nilai_huruf.'.$mk->id, $item?->nilai_huruf) }}" class="w-28 h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="A/B+/B" />
                                </td>
                            </tr>
                        @endforeach
                        @if ($mataKuliah->count() === 0)
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Mata kuliah belum tersedia.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
