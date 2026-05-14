<x-portal-layout :title="'Profil Mahasiswa - '.config('app.name')" subtitle="Profil Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <form method="POST" action="{{ route('mahasiswa.profil.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Biodata Utama -->
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-8 w-1 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-lg font-semibold text-white">BIODATA MAHASISWA (PD-DIKTI)</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-sm text-emerald-100/80">Nama Lengkap*</label>
                            <input name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa?->nama_lengkap ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" readonly />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Tempat Lahir*</label>
                            <input name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa?->tempat_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('tempat_lahir') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Tanggal Lahir*</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa?->tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('tanggal_lahir') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Jenis Kelamin*</label>
                            <select name="jenis_kelamin" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" required>
                                <option value="" disabled @selected(!old('jenis_kelamin', $mahasiswa?->jenis_kelamin))>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Laki-laki') class="text-black">Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Perempuan') class="text-black">Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Nama Ibu Kandung*</label>
                            <input name="nama_ibu" value="{{ old('nama_ibu', $mahasiswa?->nama_ibu ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('nama_ibu') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Agama*</label>
                            <select name="agama" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" required>
                                <option value="" disabled @selected(!old('agama', $mahasiswa?->agama))>Pilih Agama</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" @selected(old('agama', $mahasiswa?->agama) === $agama) class="text-black">{{ $agama }}</option>
                                @endforeach
                            </select>
                            @error('agama') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Kewarganegaraan*</label>
                            <input name="kewarganegaraan" value="{{ old('kewarganegaraan', $mahasiswa?->kewarganegaraan ?? 'Indonesia') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('kewarganegaraan') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">NIK*</label>
                            <input name="nik" value="{{ old('nik', $mahasiswa?->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('nik') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">NISN*</label>
                            <input name="nisn" value="{{ old('nisn', $mahasiswa?->nisn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                            @error('nisn') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">NPWP</label>
                            <input name="npwp" value="{{ old('npwp', $mahasiswa?->npwp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                            @error('npwp') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Email Pribadi*</label>
                            <input type="email" name="email_pribadi" value="{{ old('email_pribadi', $mahasiswa?->user?->email ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" readonly />
                        </div>
                    </div>
                </div>

                <!-- Alamat Detail -->
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-8 w-1 bg-sky-500 rounded-full"></div>
                        <h2 class="text-lg font-semibold text-white">ALAMAT DOMISILI</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-sm text-emerald-100/80">Jalan</label>
                            <input name="jalan" value="{{ old('jalan', $mahasiswa?->jalan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Dusun</label>
                            <input name="dusun" value="{{ old('dusun', $mahasiswa?->dusun ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-sm text-emerald-100/80">RT</label>
                                <input name="rt" value="{{ old('rt', $mahasiswa?->rt ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                            </div>
                            <div>
                                <label class="text-sm text-emerald-100/80">RW</label>
                                <input name="rw" value="{{ old('rw', $mahasiswa?->rw ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                            </div>
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Kelurahan*</label>
                            <input name="kelurahan" value="{{ old('kelurahan', $mahasiswa?->kelurahan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Kecamatan*</label>
                            <input name="kecamatan" value="{{ old('kecamatan', $mahasiswa?->kecamatan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Kode Pos</label>
                            <input name="kode_pos" value="{{ old('kode_pos', $mahasiswa?->kode_pos ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Jenis Tinggal</label>
                            <input name="jenis_tinggal" value="{{ old('jenis_tinggal', $mahasiswa?->jenis_tinggal ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Contoh: Bersama orang tua" />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Alat Transportasi</label>
                            <input name="alat_transportasi" value="{{ old('alat_transportasi', $mahasiswa?->alat_transportasi ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Contoh: Motor" />
                        </div>
                        
                        <div>
                            <label class="text-sm text-emerald-100/80">Telepon/HP*</label>
                            <input name="nomor_telp" value="{{ old('nomor_telp', $mahasiswa?->nomor_telp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-sm text-emerald-100/80">Penerima KPS?*</label>
                                <select name="penerima_kps" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" required>
                                    <option value="Tidak" @selected(old('penerima_kps', $mahasiswa?->penerima_kps) === 'Tidak') class="text-black">Tidak</option>
                                    <option value="Ya" @selected(old('penerima_kps', $mahasiswa?->penerima_kps) === 'Ya') class="text-black">Ya</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-emerald-100/80">No. KPS</label>
                                <input name="no_kps" value="{{ old('no_kps', $mahasiswa?->no_kps ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orang Tua -->
                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-8 w-1 bg-amber-500 rounded-full"></div>
                        <h2 class="text-lg font-semibold text-white">DATA ORANG TUA</h2>
                    </div>
                    
                    <div class="space-y-6">
                        <!-- Ayah -->
                        <div>
                            <h3 class="text-sm font-medium text-emerald-300 mb-3 border-b border-white/5 pb-2">Data Ayah</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-emerald-100/80">NIK Ayah</label>
                                    <input name="ayah_nik" value="{{ old('ayah_nik', $mahasiswa?->ayah_nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Nama Ayah</label>
                                    <input name="ayah_nama" value="{{ old('ayah_nama', $mahasiswa?->ayah_nama ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Tanggal Lahir Ayah</label>
                                    <input type="date" name="ayah_tanggal_lahir" value="{{ old('ayah_tanggal_lahir', $mahasiswa?->ayah_tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Pendidikan Ayah</label>
                                    <input name="ayah_pendidikan" value="{{ old('ayah_pendidikan', $mahasiswa?->ayah_pendidikan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Pekerjaan Ayah</label>
                                    <input name="ayah_pekerjaan" value="{{ old('ayah_pekerjaan', $mahasiswa?->ayah_pekerjaan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Penghasilan Ayah</label>
                                    <input name="ayah_penghasilan" value="{{ old('ayah_penghasilan', $mahasiswa?->ayah_penghasilan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                            </div>
                        </div>

                        <!-- Ibu -->
                        <div>
                            <h3 class="text-sm font-medium text-emerald-300 mb-3 border-b border-white/5 pb-2">Data Ibu</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-emerald-100/80">NIK Ibu</label>
                                    <input name="ibu_nik" value="{{ old('ibu_nik', $mahasiswa?->ibu_nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Nama Ibu</label>
                                    <input name="ibu_nama" value="{{ old('ibu_nama', $mahasiswa?->ibu_nama ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Tanggal Lahir Ibu</label>
                                    <input type="date" name="ibu_tanggal_lahir" value="{{ old('ibu_tanggal_lahir', $mahasiswa?->ibu_tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Pendidikan Ibu</label>
                                    <input name="ibu_pendidikan" value="{{ old('ibu_pendidikan', $mahasiswa?->ibu_pendidikan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Pekerjaan Ibu</label>
                                    <input name="ibu_pekerjaan" value="{{ old('ibu_pekerjaan', $mahasiswa?->ibu_pekerjaan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                                <div>
                                    <label class="text-sm text-emerald-100/80">Penghasilan Ibu</label>
                                    <input name="ibu_penghasilan" value="{{ old('ibu_penghasilan', $mahasiswa?->ibu_penghasilan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="h-8 w-1 bg-purple-500 rounded-full"></div>
                        <h2 class="text-lg font-semibold text-white">FOTO PROFIL</h2>
                    </div>
                    <div>
                        <input type="file" name="foto" accept="image/*" class="w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
                        @error('foto') <div class="mt-1 text-xs text-red-400">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button class="h-12 px-8 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-bold text-white shadow-lg shadow-emerald-600/20">
                        SIMPAN PERUBAHAN
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 sticky top-6">
                <div class="flex flex-col items-center text-center">
                    @if ($mahasiswa?->foto_path)
                        <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="h-32 w-32 rounded-3xl object-cover ring-4 ring-white/5 shadow-2xl mb-4" alt="Foto" />
                    @else
                        <div class="h-32 w-32 rounded-3xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-4xl font-bold mb-4">
                            {{ mb_substr($mahasiswa?->nama_lengkap ?? auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="text-xl font-bold text-white">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</div>
                    <div class="text-emerald-400 font-mono text-sm mt-1">{{ $mahasiswa?->npm ?? '-' }}</div>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-xs text-emerald-100/50 uppercase tracking-wider font-semibold">Program Studi</div>
                        <div class="text-sm text-white mt-1 font-medium">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-xs text-emerald-100/50 uppercase tracking-wider font-semibold">Angkatan</div>
                        <div class="text-sm text-white mt-1 font-medium">{{ $mahasiswa?->angkatan ?? '-' }}</div>
                    </div>
                    <div class="p-3 rounded-xl bg-white/5 border border-white/5">
                        <div class="text-xs text-emerald-100/50 uppercase tracking-wider font-semibold">Status Mahasiswa</div>
                        <div class="text-sm text-emerald-400 mt-1 font-bold">{{ $mahasiswa?->status_mahasiswa ?? '-' }}</div>
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-white/5 text-center">
                    <p class="text-xs text-emerald-100/40 leading-relaxed italic">
                        Harap isi data dengan benar sesuai dengan dokumen asli untuk keperluan sinkronisasi PD-DIKTI.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
