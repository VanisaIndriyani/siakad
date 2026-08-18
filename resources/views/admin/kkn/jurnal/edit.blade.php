<x-portal-layout :title="'Edit Jurnal KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $prefix = $routePrefix ?? 'admin';
        $indexUrl = $prefix === 'admin' ? route('admin.kkn.jurnal.index', $kkn) : route('dosen.kkn.jurnal.index', $kkn);
        $updateUrl = $prefix === 'admin' ? route('admin.kkn.jurnal.update', [$kkn, $jurnal]) : route('dosen.kkn.jurnal.update', [$kkn, $jurnal]);
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Edit Jurnal Kegiatan KKN</div>
            <div class="text-sm text-emerald-100/70">{{ $kkn->mahasiswa?->nama_lengkap }} ({{ $kkn->mahasiswa?->npm }}) • {{ $jurnal->tanggal?->format('d F Y') }}</div>
        </div>
        <a href="{{ $indexUrl }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ $updateUrl }}" class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="tanggal" value="Tanggal Kegiatan" />
                <x-text-input id="tanggal" type="date" name="tanggal" :value="old('tanggal', $jurnal->tanggal?->format('Y-m-d'))" required autofocus />
                <x-input-error :messages="$errors->get('tanggal')" />
            </div>
            <div>
                <x-input-label for="kegiatan" value="Nama Kegiatan" />
                <x-text-input id="kegiatan" type="text" name="kegiatan" :value="old('kegiatan', $jurnal->kegiatan)" required />
                <x-input-error :messages="$errors->get('kegiatan')" />
            </div>
            <div>
                <x-input-label for="lokasi" value="Lokasi" />
                <x-text-input id="lokasi" type="text" name="lokasi" :value="old('lokasi', $jurnal->lokasi)" required />
                <x-input-error :messages="$errors->get('lokasi')" />
            </div>
            <div>
                <x-input-label for="pihak_terkait" value="Pihak Terkait" />
                <x-text-input id="pihak_terkait" type="text" name="pihak_terkait" :value="old('pihak_terkait', $jurnal->pihak_terkait)" required />
                <x-input-error :messages="$errors->get('pihak_terkait')" />
            </div>
            <div>
                <x-input-label for="status" value="Status Verifikasi" />
                <select id="status" name="status" required
                    class="mt-1 w-full h-11 rounded-xl border border-white/10 bg-white/5 text-white px-4 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition">
                    @foreach(['pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $v => $l)
                        <option value="{{ $v }}" class="bg-neutral-900" @selected(old('status', $jurnal->status) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" />
            </div>
            <div>
                <x-input-label for="catatan_pembimbing" value="Catatan Pembimbing" />
                <textarea id="catatan_pembimbing" name="catatan_pembimbing" rows="3"
                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-2 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                    placeholder="Tulis catatan untuk mahasiswa...">{{ old('catatan_pembimbing', $jurnal->catatan_pembimbing) }}</textarea>
                <x-input-error :messages="$errors->get('catatan_pembimbing')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="deskripsi" value="Deskripsi Kegiatan" />
                <textarea id="deskripsi" name="deskripsi" rows="5" required
                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                    >{{ old('deskripsi', $jurnal->deskripsi) }}</textarea>
                <x-input-error :messages="$errors->get('deskripsi')" />
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="{{ $indexUrl }}" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <span class="text-sm font-medium">Batal</span>
            </a>
            <button type="submit" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-save"></i>
                <span class="text-sm font-medium">Simpan Perubahan</span>
            </button>
        </div>
    </form>
</x-portal-layout>
