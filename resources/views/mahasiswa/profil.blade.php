<x-portal-layout :title="'Profil Mahasiswa - '.config('app.name')" subtitle="Profil Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-wrap: wrap; gap: 30px; padding-bottom: 50px;">
        <!-- Form Utama -->
        <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 30px;">
            <form method="POST" action="{{ route('mahasiswa.profil.update') }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 30px;">
                @csrf
                
                <!-- Section: Biodata -->
                <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="height: 45px; width: 45px; border-radius: 12px; background-color: rgba(16,185,129,0.2); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-user-graduate" style="color: #34d399; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 style="color: white; font-size: 1.1rem; font-weight: 800; margin: 0; letter-spacing: 0.5px;">BIODATA MAHASISWA</h2>
                                <p style="color: rgba(52,211,153,0.6); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 2px 0 0 0;">SESUAI STANDAR PD-DIKTI</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 25px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nama Lengkap (Sesuai Ijazah)</label>
                            <div style="position: relative;">
                                <input name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa?->nama_lengkap ?? auth()->user()->name) }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 45px 0 15px; font-weight: 600; outline: none;" readonly />
                                <i class="fa-solid fa-lock" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2); font-size: 12px;"></i>
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tempat Lahir*</label>
                                <input name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa?->tempat_lahir ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tanggal Lahir*</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa?->tanggal_lahir?->format('Y-m-d')) }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Jenis Kelamin*</label>
                                <select name="jenis_kelamin" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required>
                                    <option value="" disabled @selected(!old('jenis_kelamin', $mahasiswa?->jenis_kelamin))>Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Laki-laki') style="background-color: #0d2a23;">Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Perempuan') style="background-color: #0d2a23;">Perempuan</option>
                                </select>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nama Ibu Kandung*</label>
                                <input name="nama_ibu" value="{{ old('nama_ibu', $mahasiswa?->nama_ibu ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Agama*</label>
                                <select name="agama" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required>
                                    <option value="" disabled @selected(!old('agama', $mahasiswa?->agama))>Pilih Agama</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama', $mahasiswa?->agama) === $agama) style="background-color: #0d2a23;">{{ $agama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kewarganegaraan*</label>
                                <input name="kewarganegaraan" value="{{ old('kewarganegaraan', $mahasiswa?->kewarganegaraan ?? 'Indonesia') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">NIK*</label>
                                <input name="nik" value="{{ old('nik', $mahasiswa?->nik ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                            
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">NISN*</label>
                                <input name="nisn" value="{{ old('nisn', $mahasiswa?->nisn ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">NPWP</label>
                                <input name="npwp" value="{{ old('npwp', $mahasiswa?->npwp ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Email Pribadi*</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            @error('email') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Pendidikan Ayah</label>
                                <input name="ayah_pendidikan" value="{{ old('ayah_pendidikan', $mahasiswa?->ayah_pendidikan ?? '') }}"
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                                @error('ayah_pendidikan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Penghasilan Ayah</label>
                                <input name="ayah_penghasilan" value="{{ old('ayah_penghasilan', $mahasiswa?->ayah_penghasilan ?? '') }}"
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                                @error('ayah_penghasilan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Pendidikan Ibu</label>
                                <input name="ibu_pendidikan" value="{{ old('ibu_pendidikan', $mahasiswa?->ibu_pendidikan ?? '') }}"
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                                @error('ibu_pendidikan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Penghasilan Ibu</label>
                                <input name="ibu_penghasilan" value="{{ old('ibu_penghasilan', $mahasiswa?->ibu_penghasilan ?? '') }}"
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                                @error('ibu_penghasilan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Upload Foto -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Foto Profil (Format: JPG, PNG, Max: 2MB)</label>
                            <div style="position: relative; width: 100%; height: 50px; background-color: #0a1f1a; border: 1px dashed rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; padding: 0 15px; overflow: hidden;">
                                <input type="file" name="foto" accept="image/*" 
                                    style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;" 
                                    onchange="this.nextElementSibling.innerText = this.files[0].name" />
                                <div style="color: rgba(255,255,255,0.4); font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px;">
                                    <i class="fa-solid fa-image" style="color: #10b981;"></i>
                                    <span>Pilih file foto atau seret ke sini...</span>
                                </div>
                            </div>
                            @error('foto') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section: Alamat -->
                <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="height: 45px; width: 45px; border-radius: 12px; background-color: rgba(14,165,233,0.2); border: 1px solid rgba(14,165,233,0.3); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-map-location-dot" style="color: #38bdf8; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 style="color: white; font-size: 1.1rem; font-weight: 800; margin: 0; letter-spacing: 0.5px;">ALAMAT DOMISILI</h2>
                                <p style="color: rgba(56,189,248,0.6); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 2px 0 0 0;">TEMPAT TINGGAL SAAT INI</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 25px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Jalan / Alamat Lengkap</label>
                            <input name="jalan" value="{{ old('jalan', $mahasiswa?->jalan ?? '') }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Dusun</label>
                                <input name="dusun" value="{{ old('dusun', $mahasiswa?->dusun ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">RT</label>
                                <input name="rt" value="{{ old('rt', $mahasiswa?->rt ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">RW</label>
                                <input name="rw" value="{{ old('rw', $mahasiswa?->rw ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kelurahan*</label>
                                <input name="kelurahan" value="{{ old('kelurahan', $mahasiswa?->kelurahan ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kode Pos</label>
                                <input name="kode_pos" value="{{ old('kode_pos', $mahasiswa?->kode_pos ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Kecamatan*</label>
                                <input name="kecamatan" value="{{ old('kecamatan', $mahasiswa?->kecamatan ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                            </div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Jenis Tinggal</label>
                                <input name="jenis_tinggal" value="{{ old('jenis_tinggal', $mahasiswa?->jenis_tinggal ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Alat Transportasi</label>
                                <input name="alat_transportasi" value="{{ old('alat_transportasi', $mahasiswa?->alat_transportasi ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nomor HP / WhatsApp*</label>
                            <input name="nomor_telp" value="{{ old('nomor_telp', $mahasiswa?->nomor_telp ?? '') }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required />
                        </div>
                    </div>
                </div>

                <!-- Section: KPS -->
                <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(245,158,11,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="height: 45px; width: 45px; border-radius: 12px; background-color: rgba(245,158,11,0.2); border: 1px solid rgba(245,158,11,0.3); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-id-card-clip" style="color: #f59e0b; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 style="color: white; font-size: 1.1rem; font-weight: 800; margin: 0; letter-spacing: 0.5px;">STATUS SOSIAL (KPS)</h2>
                                <p style="color: rgba(245,158,11,0.6); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 2px 0 0 0;">KARTU PERLINDUNGAN SOSIAL</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Penerima KPS*</label>
                            <select name="penerima_kps" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" required>
                                <option value="Tidak" @selected(old('penerima_kps', $mahasiswa?->penerima_kps) === 'Tidak') style="background-color: #0d2a23;">Tidak</option>
                                <option value="Ya" @selected(old('penerima_kps', $mahasiswa?->penerima_kps) === 'Ya') style="background-color: #0d2a23;">Ya</option>
                            </select>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nomor KPS</label>
                            <input name="no_kps" value="{{ old('no_kps', $mahasiswa?->no_kps ?? '') }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                        </div>
                    </div>
                </div>

                <!-- Section: Data Ayah -->
                <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(59,130,246,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="height: 45px; width: 45px; border-radius: 12px; background-color: rgba(59,130,246,0.2); border: 1px solid rgba(59,130,246,0.3); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-user-tie" style="color: #3b82f6; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 style="color: white; font-size: 1.1rem; font-weight: 800; margin: 0; letter-spacing: 0.5px;">DATA ORANG TUA - AYAH</h2>
                                <p style="color: rgba(59,130,246,0.6); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 2px 0 0 0;">INFORMASI AYAH KANDUNG</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 25px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">NIK Ayah</label>
                                <input name="ayah_nik" value="{{ old('ayah_nik', $mahasiswa?->ayah_nik ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nama Lengkap Ayah</label>
                                <input name="ayah_nama" value="{{ old('ayah_nama', $mahasiswa?->ayah_nama ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tanggal Lahir Ayah</label>
                                <input type="date" name="ayah_tanggal_lahir" value="{{ old('ayah_tanggal_lahir', $mahasiswa?->ayah_tanggal_lahir?->format('Y-m-d')) }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Pekerjaan Ayah</label>
                                <input name="ayah_pekerjaan" value="{{ old('ayah_pekerjaan', $mahasiswa?->ayah_pekerjaan ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Data Ibu -->
                <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(236,72,153,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="height: 45px; width: 45px; border-radius: 12px; background-color: rgba(236,72,153,0.2); border: 1px solid rgba(236,72,153,0.3); display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-person-breastfeeding" style="color: #ec4899; font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <h2 style="color: white; font-size: 1.1rem; font-weight: 800; margin: 0; letter-spacing: 0.5px;">DATA ORANG TUA - IBU</h2>
                                <p style="color: rgba(236,72,153,0.6); font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin: 2px 0 0 0;">INFORMASI IBU KANDUNG</p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 25px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">NIK Ibu</label>
                                <input name="ibu_nik" value="{{ old('ibu_nik', $mahasiswa?->ibu_nik ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Nama Lengkap Ibu</label>
                                <input name="ibu_nama" value="{{ old('ibu_nama', $mahasiswa?->ibu_nama ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tanggal Lahir Ibu</label>
                                <input type="date" name="ibu_tanggal_lahir" value="{{ old('ibu_tanggal_lahir', $mahasiswa?->ibu_tanggal_lahir?->format('Y-m-d')) }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Pekerjaan Ibu</label>
                                <input name="ibu_pekerjaan" value="{{ old('ibu_pekerjaan', $mahasiswa?->ibu_pekerjaan ?? '') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; justify-content: flex-end; padding: 20px 0;">
                    <button type="submit" style="background: linear-gradient(to right, #059669, #10b981); color: white; border: none; padding: 15px 40px; border-radius: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(16,185,129,0.3);">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div style="width: 320px; display: flex; flex-direction: column; gap: 30px;">
            <div style="background-color: #0d2a23 !important; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: sticky; top: 30px;">
                <div style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <div style="position: relative; margin-bottom: 25px;">
                        <div style="position: absolute; inset: -10px; background-color: rgba(16,185,129,0.2); filter: blur(20px); border-radius: 50%;"></div>
                        @if ($mahasiswa?->foto_path)
                            <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" style="position: relative; height: 130px; width: 130px; border-radius: 30px; object-fit: cover; border: 4px solid rgba(16,185,129,0.2);" alt="Foto" />
                        @else
                            <div style="position: relative; height: 130px; width: 130px; border-radius: 30px; background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: 900; color: #10b981;">
                                {{ mb_substr($mahasiswa?->nama_lengkap ?? auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <h3 style="color: white; font-size: 1.2rem; font-weight: 800; margin: 0; line-height: 1.2;">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</h3>
                    <div style="margin-top: 10px; display: inline-block; padding: 4px 15px; background-color: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); border-radius: 50px;">
                        <span style="color: #10b981; font-family: monospace; font-size: 12px; font-weight: 700; letter-spacing: 1px;">{{ $mahasiswa?->npm ?? '-' }}</span>
                    </div>
                </div>

                <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 15px;">
                    <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px;">
                        <span style="color: rgba(255,255,255,0.4); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Program Studi</span>
                        <div style="color: white; font-size: 13px; font-weight: 700; margin-top: 4px;">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                    </div>
                    <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px;">
                        <span style="color: rgba(255,255,255,0.4); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Angkatan</span>
                        <div style="color: white; font-size: 13px; font-weight: 700; margin-top: 4px;">{{ $mahasiswa?->angkatan ?? '-' }}</div>
                    </div>
                    <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 15px;">
                        <span style="color: rgba(255,255,255,0.4); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Status</span>
                        <div style="margin-top: 8px;">
                            <span style="padding: 4px 12px; background-color: rgba(16,185,129,0.2); color: #34d399; font-size: 10px; font-weight: 800; text-transform: uppercase; border-radius: 8px; border: 1px solid rgba(16,185,129,0.3);">{{ $mahasiswa?->status_mahasiswa ?? 'Aktif' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
