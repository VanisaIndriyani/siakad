<x-portal-layout :title="'Tambah Daftar Hadir KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Tambah Daftar Hadir</div>
            <div class="text-sm text-emerald-100/70">{{ $kkn->posko?->nama_posko ?: 'KKN' }}</div>
        </div>
        <a href="{{ route('mahasiswa.kkn.absensi.index', $kkn) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('mahasiswa.kkn.absensi.store', $kkn) }}" class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="tanggal" value="Tanggal" />
                <x-text-input id="tanggal" type="date" name="tanggal" :value="old('tanggal', date('Y-m-d'))" required autofocus />
                <x-input-error :messages="$errors->get('tanggal')" />
            </div>
            <div>
                <x-input-label for="status_kehadiran" value="Status Kehadiran" />
                <select id="status_kehadiran" name="status_kehadiran" required
                    class="mt-1 w-full h-11 rounded-xl border border-white/10 bg-white/5 text-white px-4 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition">
                    @php($sel = old('status_kehadiran', 'hadir'))
                    @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha / Tanpa Keterangan'] as $v => $l)
                        <option value="{{ $v }}" class="bg-neutral-900" @selected($sel === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status_kehadiran')" />
            </div>
            <div>
                <x-input-label for="jam_masuk" value="Jam Masuk" />
                <x-text-input id="jam_masuk" type="time" name="jam_masuk" :value="old('jam_masuk', date('H:i'))" />
                <x-input-error :messages="$errors->get('jam_masuk')" />
            </div>
            <div>
                <x-input-label for="jam_pulang" value="Jam Pulang" />
                <x-text-input id="jam_pulang" type="time" name="jam_pulang" :value="old('jam_pulang')" />
                <x-input-error :messages="$errors->get('jam_pulang')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="keterangan" value="Keterangan (jika Izin/Sakit/Alpha)" />
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                    placeholder="Contoh: Mengikuti kegiatan kerja bakti desa...">{{ old('keterangan') }}</textarea>
                <x-input-error :messages="$errors->get('keterangan')" />
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="{{ route('mahasiswa.kkn.absensi.index', $kkn) }}" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <span class="text-sm font-medium">Batal</span>
            </a>
            <button type="submit" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-save"></i>
                <span class="text-sm font-medium">Simpan</span>
            </button>
        </div>
    </form>
</x-portal-layout>
