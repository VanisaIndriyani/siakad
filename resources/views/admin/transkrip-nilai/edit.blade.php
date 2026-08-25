<x-portal-layout :title="'Edit Data Transkrip - '.config('app.name')" subtitle="Edit Transkrip Akademik">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5 flex-wrap">
        <div>
            <div class="flex items-center gap-2 text-sm text-emerald-100/60 mb-1">
                <a href="{{ route('admin.transkrip-nilai.index') }}" class="hover:text-emerald-300 transition">Transkrip Nilai</a>
                <span>/</span>
                <a href="{{ route('admin.transkrip-nilai.show', $mahasiswa) }}" class="hover:text-emerald-300 transition">{{ $mahasiswa->npm }}</a>
                <span>/</span>
                <span class="text-emerald-100/90">Edit Data</span>
            </div>
            <div class="text-xl font-semibold">Edit Data Transkrip Akademik</div>
            <div class="text-sm text-emerald-100/70">{{ $mahasiswa->nama_lengkap }} @if($mahasiswa->npm)• <span class="font-mono">{{ $mahasiswa->npm }}</span>@endif</div>
        </div>
        <a href="{{ route('admin.transkrip-nilai.show', $mahasiswa) }}"
           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-300"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100 flex items-start gap-2">
            <i class="fa-solid fa-circle-exclamation text-red-300 mt-0.5"></i>
            <div class="space-y-1">
                <div class="font-semibold">Ada kesalahan ketika menyimpan data. Periksa kembali isian berikut:</div>
                <ul class="list-disc list-inside space-y-0.5 text-red-100/90">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.transkrip-nilai.update', $mahasiswa) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <section class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
            <div class="flex items-center gap-3 mb-1">
                <span class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-300">
                    <i class="fa-solid fa-id-card"></i>
                </span>
                <div>
                    <div class="font-semibold">Identitas Transkrip</div>
                    <div class="text-sm text-emerald-100/60">Data identitas mahasiswa yang akan tampil di bagian atas transkrip.</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-sm text-emerald-100/80">Nomor Transkrip</label>
                    <input type="text" name="nomor_transkrip" value="{{ old('nomor_transkrip', $mahasiswa->nomor_transkrip) }}"
                           placeholder="Contoh: TR/20260005/08/2025"
                           class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 px-4 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                    @error('nomor_transkrip') <div class="mt-1.5 text-xs text-red-300">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal, Bulan dan Tahun Lulus</label>
                    <input type="date" name="tanggal_lulus"
                           value="{{ old('tanggal_lulus', $mahasiswa->tanggal_lulus ? $mahasiswa->tanggal_lulus->format('Y-m-d') : '') }}"
                           class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 px-4 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                    @error('tanggal_lulus') <div class="mt-1.5 text-xs text-red-300">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm text-emerald-100/80">No. SK BAN-PT</label>
                    <input type="text" name="nomor_sk_banpt" value="{{ old('nomor_sk_banpt', $mahasiswa->nomor_sk_banpt) }}"
                           placeholder="Contoh: 1981/SK/BAN-PT/Ak/S/V/2023"
                           class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 px-4 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                    @error('nomor_sk_banpt') <div class="mt-1.5 text-xs text-red-300">{{ $message }}</div> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
            <div class="flex items-center gap-3 mb-1">
                <span class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-300">
                    <i class="fa-solid fa-book"></i>
                </span>
                <div>
                    <div class="font-semibold">Judul Skripsi</div>
                    <div class="text-sm text-emerald-100/60">Akan ditampilkan di bawah ringkasan IPK dan Predikat kelulusan.</div>
                </div>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Judul Skripsi</label>
                <textarea name="judul_skripsi" rows="4"
                          placeholder="Contoh: Produktivitas Ekonomi Masyarakat Pasca Banjir..."
                          class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm focus:border-emerald-400 focus:ring-emerald-400 leading-relaxed">{{ old('judul_skripsi', $mahasiswa->judul_skripsi) }}</textarea>
                @error('judul_skripsi') <div class="mt-1.5 text-xs text-red-300">{{ $message }}</div> @enderror
            </div>
        </section>

        <section class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
            <div class="flex items-center gap-3 mb-1">
                <span class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-emerald-500/15 border border-emerald-500/20 text-emerald-300">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <div>
                    <div class="font-semibold">Daftar Ujian Komprehensif (Ujian Kompetensi)</div>
                    <div class="text-sm text-emerald-100/60">Isi nama mata ujian sesuai contoh dokumen. SKS=0 dan Nilai=A otomatis diisi. Biarkan kosong untuk tidak menampilkan baris tersebut.</div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($ujian as $i => $nilai)
                    <div class="flex items-start gap-3">
                        <span class="shrink-0 mt-2 h-8 w-8 inline-flex items-center justify-center rounded-lg bg-white/5 border border-white/10 text-emerald-200 text-sm font-semibold">
                            {{ $i + 1 }}
                        </span>
                        <div class="flex-1">
                            <input type="text" name="ujian[]"
                                   value="{{ old('ujian.'.$i, $nilai) }}"
                                   placeholder="Contoh: {{ $i === 0 ? 'Ujian Komprehensif' : 'Al-Quran, Agama dan Bahasa' }}"
                                   class="w-full h-11 rounded-xl bg-white/5 border border-white/10 px-4 text-sm focus:border-emerald-400 focus:ring-emerald-400">
                            @error('ujian.'.$i) <div class="mt-1.5 text-xs text-red-300">{{ $message }}</div> @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <div class="flex items-center justify-between gap-3 pb-10">
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.transkrip-nilai.show', $mahasiswa) }}"
                   class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm">
                    Batal
                </a>
                <button type="submit"
                        class="h-11 px-6 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium shadow-lg shadow-emerald-900/20">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Data Transkrip
                </button>
            </div>
        </div>
    </form>
</x-portal-layout>
