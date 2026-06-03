<x-portal-layout :title="'Tambah Posko KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Posko KKN</div>
            <div class="text-sm text-emerald-100/70">Buat posko baru dan tentukan pembimbing.</div>
        </div>
        <a href="{{ route('admin.kkn.posko.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.kkn.posko.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-6">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-2">Nama Posko</label>
                <input name="nama_posko" value="{{ old('nama_posko') }}" required
                       placeholder="Contoh: Posko 01 - Desa Makmur"
                       class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-2">Lokasi / Alamat Posko</label>
                <input name="lokasi" value="{{ old('lokasi') }}"
                       placeholder="Contoh: Desa Makmur, Kec. Sidrap"
                       class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-2">Dosen Pembimbing Lapangan (DPL)</label>
                <select name="dosen_pembimbing_id" class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition">
                    <option value="" style="background-color: #0d2a23;">Pilih DPL</option>
                    @foreach ($dosenList as $d)
                        <option value="{{ $d->id }}" @selected(old('dosen_pembimbing_id') == $d->id) style="background-color: #0d2a23;">{{ $d->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-emerald-100/80 mb-2">Nomor SK Pembimbing</label>
                <input name="nomor_sk" value="{{ old('nomor_sk') }}"
                       placeholder="Masukkan nomor SK jika ada"
                       class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
            </div>

            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-emerald-100/80 mb-2">Upload File SK (PDF/Gambar)</label>
                <input type="file" name="sk_pembimbing_file" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full h-12 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-4 file:h-12 file:border-0 file:bg-white/10 file:text-white file:px-6 file:cursor-pointer hover:file:bg-white/20 transition" />
                <div class="mt-2 text-xs text-emerald-100/50 italic">Maksimal 10MB. Format yang didukung: PDF, JPG, PNG.</div>
            </div>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="h-12 px-8 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-bold text-sm tracking-widest uppercase">
                Simpan Posko
            </button>
        </div>
    </form>
</x-portal-layout>
