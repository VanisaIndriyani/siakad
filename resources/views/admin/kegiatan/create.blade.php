<x-portal-layout :title="'Tambah Kegiatan - '.config('app.name')" subtitle="Tambah Kegiatan Baru">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Kegiatan</div>
            <div class="text-sm text-emerald-100/70">Buat kegiatan baru seperti seminar, workshop, pelatihan, atau acara lainnya.</div>
        </div>
        <a href="{{ route('admin.kegiatan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.kegiatan.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-5">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Judul Kegiatan <span class="text-rose-400">*</span></label>
                <input required name="judul" value="{{ old('judul') }}" placeholder="Contoh: Seminar Nasional Teknologi Informasi 2026" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('judul') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Jenis Kegiatan <span class="text-rose-400">*</span></label>
                <select required name="jenis_kegiatan" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Kuliah Perdana" @selected(old('jenis_kegiatan') === 'Kuliah Perdana')>Kuliah Perdana</option>
                    <option value="Kuliah Umum" @selected(old('jenis_kegiatan') === 'Kuliah Umum')>Kuliah Umum</option>
                    <option value="Kuliah Tamu" @selected(old('jenis_kegiatan') === 'Kuliah Tamu')>Kuliah Tamu</option>
                    <option value="Seminar" @selected(old('jenis_kegiatan') === 'Seminar')>Seminar</option>
                    <option value="Workshop" @selected(old('jenis_kegiatan') === 'Workshop')>Workshop</option>
                    <option value="Pelatihan" @selected(old('jenis_kegiatan') === 'Pelatihan')>Pelatihan</option>
                    <option value="Konferensi" @selected(old('jenis_kegiatan') === 'Konferensi')>Konferensi</option>
                    <option value="Sosialisasi" @selected(old('jenis_kegiatan') === 'Sosialisasi')>Sosialisasi</option>
                    <option value="Rapat" @selected(old('jenis_kegiatan') === 'Rapat')>Rapat</option>
                    <option value="Lainnya" @selected(old('jenis_kegiatan') === 'Lainnya')>Lainnya</option>
                </select>
                @error('jenis_kegiatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Penyelenggara</label>
                <input name="penyelenggara" value="{{ old('penyelenggara') }}" placeholder="Contoh: Prodi Teknik Informatika" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('penyelenggara') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Tanggal Kegiatan <span class="text-rose-400">*</span></label>
                <input required type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('tanggal_kegiatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Lokasi Kegiatan</label>
                <input name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Aula Utama Lt.3 / Zoom Meeting" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('lokasi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('waktu_mulai') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                @error('waktu_selesai') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Narasumber / Pemateri</label>
                <input name="narasumber" value="{{ old('narasumber') }}" placeholder="Contoh: Dr. Ir. H. Ahmad Subarjo, M.Kom" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Narasumber <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="narasumber_nip" value="{{ old('narasumber_nip') }}" placeholder="Contoh: 196501011990031001" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Nama Lengkap Ketua Panitia</label>
                <input name="ketua_panitia_nama" value="{{ old('ketua_panitia_nama') }}" placeholder="Contoh: Dra. Hj. Siti Nurhaliza, M.Pd" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Ketua Panitia <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="ketua_panitia_nip" value="{{ old('ketua_panitia_nip') }}" placeholder="Contoh: 197801012005012002" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Nama Rektor <span class="text-emerald-200/40">(Default: Rektor IAI DDI)</span></label>
                <input name="rektor_nama" value="{{ old('rektor_nama', 'Dr. H. Muh. Anshar, M.Ag.') }}" placeholder="Contoh: Prof. Dr. H. Muh. Anshar, M.Ag." class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">NIP Rektor <span class="text-emerald-200/40">(Opsional)</span></label>
                <input name="rektor_nip" value="{{ old('rektor_nip') }}" placeholder="Contoh: 196001011988121001" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
            </div>

            <div class="md:col-span-2">
                <label class="text-sm text-emerald-100/80">Deskripsi Kegiatan</label>
                <textarea name="deskripsi" rows="4" placeholder="Keterangan detail mengenai kegiatan..." class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Poster / Gambar Kegiatan</label>
                <input type="file" accept="image/*" name="gambar" class="mt-2 w-full file:mr-3 file:h-11 file:px-4 file:rounded-xl file:bg-white/10 file:border-0 file:text-emerald-100 hover:file:bg-white/20 h-14 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 p-1">
                <div class="text-xs text-emerald-100/50 mt-1">Maksimal 4 MB, format JPG/PNG/WebP.</div>
                @error('gambar') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="text-sm text-emerald-100/80">Upload File Sertifikat Peserta <span class="text-emerald-200/50">(PDF / Gambar PNG/JPG/WEBP)</span></label>
                <input name="sertifikat_file_upload" type="file" accept="application/pdf,.pdf,image/png,.png,image/jpeg,.jpg,.jpeg,image/webp,.webp" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white/90 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-800 file:text-emerald-50 hover:file:bg-emerald-700" />
                <div class="text-xs text-emerald-100/50 mt-1">Upload file sertifikat master (PDF atau Gambar JPG/PNG/WEBP). Satu file ini akan berlaku untuk SEMUA peserta yang hadir. (Bisa juga upload nanti di halaman detail kegiatan)</div>
                @error('sertifikat_file_upload') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
            </div>

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4 pt-2 border-t border-white/10">
                <label class="flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="is_published" value="1" @checked((bool) old('is_published', true)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">🎓 Publikasikan ke Mahasiswa</span>
                </label>
                <label class="flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="tampilkan_ke_dosen" value="1" @checked((bool) old('tampilkan_ke_dosen', true)) class="rounded border-white/20 bg-white/10 text-sky-500 focus:ring-sky-400" />
                    <span class="text-sm text-emerald-100/90">👨‍🏫 Tampilkan ke Dosen</span>
                </label>
                <label class="flex items-center gap-3 px-4 h-11 rounded-xl bg-white/5 border border-white/10 cursor-pointer">
                    <input type="checkbox" name="sertifikat_aktif" value="1" @checked((bool) old('sertifikat_aktif', true)) class="rounded border-white/20 bg-white/10 text-emerald-500 focus:ring-emerald-400" />
                    <span class="text-sm text-emerald-100/90">🏆 Aktifkan Sertifikat Peserta Hadir</span>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
            <a href="{{ route('admin.kegiatan.index') }}" class="h-11 px-5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                Batal
            </a>
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan Kegiatan
            </button>
        </div>
    </form>
</x-portal-layout>
