<x-portal-layout :title="'Edit Kegiatan - '.config('app.name')" subtitle="Edit Data Kegiatan">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Edit Kegiatan</div>
            <div class="text-sm text-emerald-100/70">Perbarui informasi kegiatan.</div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kegiatan.show', $kegiatan) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-eye"></i>
                Detail
            </a>
            <a href="{{ route('admin.kegiatan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.kegiatan.update', $kegiatan) }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Judul Kegiatan <span class="text-rose-400">*</span></label>
                <input required name="judul" value="{{ old('judul', $kegiatan->judul) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('judul') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Jenis Kegiatan <span class="text-rose-400">*</span></label>
                <select required name="jenis_kegiatan" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Seminar" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Seminar')>Seminar</option>
                    <option value="Workshop" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Workshop')>Workshop</option>
                    <option value="Pelatihan" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Pelatihan')>Pelatihan</option>
                    <option value="Konferensi" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Konferensi')>Konferensi</option>
                    <option value="Sosialisasi" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Sosialisasi')>Sosialisasi</option>
                    <option value="Rapat" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Rapat')>Rapat</option>
                    <option value="Lainnya" @selected(old('jenis_kegiatan', $kegiatan->jenis_kegiatan) === 'Lainnya')>Lainnya</option>
                </select>
                @error('jenis_kegiatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Penyelenggara</label>
                <input name="penyelenggara" value="{{ old('penyelenggara', $kegiatan->penyelenggara) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Kegiatan <span class="text-rose-400">*</span></label>
                <input required type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan', $kegiatan->tanggal_kegiatan?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Lokasi Kegiatan</label>
                <input name="lokasi" value="{{ old('lokasi', $kegiatan->lokasi) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai', $kegiatan->waktu_mulai) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai', $kegiatan->waktu_selesai) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Narasumber / Pemateri</label>
                <input name="narasumber" value="{{ old('narasumber', $kegiatan->narasumber) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Narasumber <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="narasumber_nip" value="{{ old('narasumber_nip', $kegiatan->narasumber_nip) }}" placeholder="Contoh: 196501011990031001" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Nama Lengkap Ketua Panitia</label>
                <input name="ketua_panitia_nama" value="{{ old('ketua_panitia_nama', $kegiatan->ketua_panitia_nama) }}" placeholder="Contoh: Dra. Hj. Siti Nurhaliza, M.Pd" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Ketua Panitia <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="ketua_panitia_nip" value="{{ old('ketua_panitia_nip', $kegiatan->ketua_panitia_nip) }}" placeholder="Contoh: 197801012005012002" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Nama Rektor <span class="text-emerald-200/40">(Default: Rektor IAI DDI)</span></label>
                <input name="rektor_nama" value="{{ old('rektor_nama', $kegiatan->rektor_nama ?? 'Dr. H. Muh. Anshar, M.Ag.') }}" placeholder="Contoh: Prof. Dr. H. Muh. Anshar, M.Ag." class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Rektor <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="rektor_nip" value="{{ old('rektor_nip', $kegiatan->rektor_nip) }}" placeholder="Contoh: 196001011988121001" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" rows="4" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-3">{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Poster / Gambar Kegiatan</label>
                @if(!empty($kegiatan->gambar_url))
                    <div class="mt-2 mb-2">
                        <a href="{{ $kegiatan->gambar_url }}" target="_blank" class="inline-block">
                            <img src="{{ $kegiatan->gambar_url }}" alt="Poster" class="h-24 rounded-xl border border-white/10 object-cover" />
                        </a>
                        <label class="flex items-center gap-2 mt-2 text-xs text-emerald-100/70">
                            <input type="checkbox" name="hapus_gambar" value="1" @checked((bool) old('hapus_gambar')) class="rounded border-white/20 bg-white/10 text-rose-500 focus:ring-rose-400" />
                            Hapus gambar saat ini
                        </label>
                    </div>
                @endif
                <input type="file" accept="image/*" name="gambar" class="w-full file:mr-3 file:h-11 file:px-4 file:rounded-xl file:bg-white/10 file:border-0 file:text-emerald-100 hover:file:bg-white/20 h-14 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-1">
                <div class="text-xs text-emerald-100/50 mt-1">Maksimal 4 MB.</div>
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Prefix Nomor Sertifikat</label>
                <input name="nomor_sertifikat_prefix" value="{{ old('nomor_sertifikat_prefix', $kegiatan->nomor_sertifikat_prefix) }}" placeholder="Contoh: SERT/SEMINAR/IAI" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                <div class="text-xs text-emerald-100/50 mt-1">Format: PREFIX/0001/MM/YYYY</div>
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-white/10">
                <label class="flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', $kegiatan->is_published)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">Publish & Tampilkan ke Mahasiswa</span>
                </label>
                <label class="flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="sertifikat_aktif" value="1" @checked((bool) old('sertifikat_aktif', $kegiatan->sertifikat_aktif)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">Aktifkan Sertifikat untuk Peserta Hadir</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
            <button type="reset" class="h-11 px-5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                Reset
            </button>
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-portal-layout>
