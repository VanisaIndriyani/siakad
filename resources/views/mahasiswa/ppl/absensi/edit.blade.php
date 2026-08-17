<x-portal-layout :title="'Edit Daftar Hadir PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Edit Daftar Hadir</div>
            <div class="text-sm text-emerald-100/70">{{ $ppl->instansi_nama }} • {{ $absensi->tanggal?->format('d F Y') }}</div>
        </div>
        <a href="{{ route('mahasiswa.ppl.absensi.index', $ppl) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('mahasiswa.ppl.absensi.update', [$ppl, $absensi]) }}" class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="tanggal" value="Tanggal" />
                <x-text-input id="tanggal" type="date" name="tanggal" :value="old('tanggal', $absensi->tanggal?->format('Y-m-d'))" required autofocus />
                <x-input-error :messages="$errors->get('tanggal')" />
            </div>
            <div>
                <x-input-label for="status_kehadiran" value="Status Kehadiran" />
                <select id="status_kehadiran" name="status_kehadiran" required
                    class="mt-1 w-full h-11 rounded-xl border border-white/10 bg-white/5 text-white px-4 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition">
                    @foreach(['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha / Tanpa Keterangan'] as $v => $l)
                        <option value="{{ $v }}" class="bg-neutral-900" @selected((old('status_kehadiran', $absensi->status_kehadiran) ?? 'hadir') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status_kehadiran')" />
            </div>
            <div>
                <x-input-label for="jam_masuk" value="Jam Masuk" />
                <x-text-input id="jam_masuk" type="time" name="jam_masuk" :value="old('jam_masuk', $absensi->jam_masuk)" />
                <x-input-error :messages="$errors->get('jam_masuk')" />
            </div>
            <div>
                <x-input-label for="jam_pulang" value="Jam Pulang" />
                <x-text-input id="jam_pulang" type="time" name="jam_pulang" :value="old('jam_pulang', $absensi->jam_pulang)" />
                <x-input-error :messages="$errors->get('jam_pulang')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="keterangan" value="Keterangan (jika Izin/Sakit/Alpha)" />
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                    >{{ old('keterangan', $absensi->keterangan) }}</textarea>
                <x-input-error :messages="$errors->get('keterangan')" />
            </div>
        </div>
        @if($absensi->catatan_pembimbing)
            <div class="mt-4 rounded-xl bg-blue-500/10 border border-blue-500/20 p-3">
                <div class="text-xs font-semibold text-blue-200 mb-1"><i class="fa-solid fa-comment"></i> Catatan Pembimbing (tidak dapat diubah)</div>
                <div class="text-sm text-blue-100/90 whitespace-pre-wrap">{{ $absensi->catatan_pembimbing }}</div>
            </div>
        @endif
        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="{{ route('mahasiswa.ppl.absensi.index', $ppl) }}" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <span class="text-sm font-medium">Batal</span>
            </a>
            <button type="submit" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-save"></i>
                <span class="text-sm font-medium">Simpan Perubahan</span>
            </button>
        </div>
    </form>
</x-portal-layout>
