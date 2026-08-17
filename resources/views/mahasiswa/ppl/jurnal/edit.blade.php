<x-portal-layout :title="'Edit Jurnal PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Edit Jurnal Kegiatan</div>
            <div class="text-sm text-emerald-100/70">{{ $ppl->instansi_nama }} • {{ $jurnal->tanggal?->format('d F Y') }}</div>
        </div>
        <a href="{{ route('mahasiswa.ppl.jurnal.index', $ppl) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <form method="POST" action="{{ route('mahasiswa.ppl.jurnal.update', [$ppl, $jurnal]) }}" class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
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
                <x-text-input id="lokasi" type="text" name="lokasi" :value="old('lokasi', $jurnal->lokasi)" />
                <x-input-error :messages="$errors->get('lokasi')" />
            </div>
            <div>
                <x-input-label for="pihak_terkait" value="Pihak Terkait" />
                <x-text-input id="pihak_terkait" type="text" name="pihak_terkait" :value="old('pihak_terkait', $jurnal->pihak_terkait)" />
                <x-input-error :messages="$errors->get('pihak_terkait')" />
            </div>
            <div class="md:col-span-2">
                <x-input-label for="deskripsi" value="Deskripsi Kegiatan" />
                <textarea id="deskripsi" name="deskripsi" rows="5" required
                    class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 text-white px-4 py-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 outline-none transition resize-y"
                    >{{ old('deskripsi', $jurnal->deskripsi) }}</textarea>
                <x-input-error :messages="$errors->get('deskripsi')" />
            </div>
        </div>
        @if($jurnal->catatan_pembimbing)
            <div class="mt-4 rounded-xl bg-blue-500/10 border border-blue-500/20 p-3">
                <div class="text-xs font-semibold text-blue-200 mb-1"><i class="fa-solid fa-comment"></i> Catatan Pembimbing (tidak dapat diubah)</div>
                <div class="text-sm text-blue-100/90 whitespace-pre-wrap">{{ $jurnal->catatan_pembimbing }}</div>
            </div>
        @endif
        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="{{ route('mahasiswa.ppl.jurnal.index', $ppl) }}" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <span class="text-sm font-medium">Batal</span>
            </a>
            <button type="submit" class="h-10 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-save"></i>
                <span class="text-sm font-medium">Simpan Perubahan</span>
            </button>
        </div>
    </form>
</x-portal-layout>
