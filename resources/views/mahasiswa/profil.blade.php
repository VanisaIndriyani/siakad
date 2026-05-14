<x-portal-layout :title="'Profil Mahasiswa - '.config('app.name')" subtitle="Profil Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" style="padding-bottom: 50px;">
        <!-- Form Utama -->
        <div class="lg:col-span-8 space-y-8">
            <form method="POST" action="{{ route('mahasiswa.profil.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- Section: Biodata -->
                <div class="rounded-3xl border border-white/5 shadow-2xl overflow-hidden" style="background-color: #0d2a23 !important; border: 1px solid rgba(255,255,255,0.05) !important;">
                    <div class="p-6 md:p-8" style="background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                                <i class="fa-solid fa-user-graduate text-emerald-400"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white tracking-wide">BIODATA MAHASISWA</h2>
                                <p class="text-[11px] text-emerald-400/60 font-medium uppercase tracking-widest">Sesuai Standar PD-DIKTI</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Nama Lengkap (Sesuai Ijazah)</label>
                                <div class="relative">
                                    <input name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa?->nama_lengkap ?? auth()->user()->name) }}" 
                                        class="w-full h-12 rounded-xl border border-white/10 px-4 text-white font-semibold outline-none transition-all" 
                                        style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" readonly />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-white/20">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Tempat Lahir*</label>
                                <input name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa?->tempat_lahir ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('tempat_lahir') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Tanggal Lahir*</label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa?->tanggal_lahir?->format('Y-m-d')) }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('tanggal_lahir') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Jenis Kelamin*</label>
                                <select name="jenis_kelamin" class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all appearance-none" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required>
                                    <option value="" disabled @selected(!old('jenis_kelamin', $mahasiswa?->jenis_kelamin))>Pilih Jenis Kelamin</option>
                                    <option value="Laki-laki" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Laki-laki') style="background-color: #0d2a23;">Laki-laki</option>
                                    <option value="Perempuan" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Perempuan') style="background-color: #0d2a23;">Perempuan</option>
                                </select>
                                @error('jenis_kelamin') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Nama Ibu Kandung*</label>
                                <input name="nama_ibu" value="{{ old('nama_ibu', $mahasiswa?->nama_ibu ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('nama_ibu') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Agama*</label>
                                <select name="agama" class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all appearance-none" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required>
                                    <option value="" disabled @selected(!old('agama', $mahasiswa?->agama))>Pilih Agama</option>
                                    @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $agama)
                                        <option value="{{ $agama }}" @selected(old('agama', $mahasiswa?->agama) === $agama) style="background-color: #0d2a23;">{{ $agama }}</option>
                                    @endforeach
                                </select>
                                @error('agama') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Kewarganegaraan*</label>
                                <input name="kewarganegaraan" value="{{ old('kewarganegaraan', $mahasiswa?->kewarganegaraan ?? 'Indonesia') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('kewarganegaraan') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">NIK (Nomor Induk Kependudukan)*</label>
                                <input name="nik" value="{{ old('nik', $mahasiswa?->nik ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('nik') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">NISN (Nasional)*</label>
                                <input name="nisn" value="{{ old('nisn', $mahasiswa?->nisn ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                                @error('nisn') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Alamat -->
                <div class="rounded-3xl border border-white/5 shadow-2xl overflow-hidden" style="background-color: #0d2a23 !important; border: 1px solid rgba(255,255,255,0.05) !important;">
                    <div class="p-6 md:p-8" style="background: linear-gradient(135deg, rgba(14,165,233,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-xl bg-sky-500/20 border border-sky-500/30 flex items-center justify-center">
                                <i class="fa-solid fa-map-location-dot text-sky-400"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white tracking-wide">ALAMAT DOMISILI</h2>
                                <p class="text-[11px] text-sky-400/60 font-medium uppercase tracking-widest">Tempat Tinggal Saat Ini</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Jalan / Alamat Lengkap</label>
                                <input name="jalan" value="{{ old('jalan', $mahasiswa?->jalan ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" />
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Kelurahan*</label>
                                <input name="kelurahan" value="{{ old('kelurahan', $mahasiswa?->kelurahan ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Kecamatan*</label>
                                <input name="kecamatan" value="{{ old('kecamatan', $mahasiswa?->kecamatan ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Nomor HP / WhatsApp*</label>
                                <input name="nomor_telp" value="{{ old('nomor_telp', $mahasiswa?->nomor_telp ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" required />
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-emerald-400/80 uppercase tracking-widest mb-2 ml-1">Kode Pos</label>
                                <input name="kode_pos" value="{{ old('kode_pos', $mahasiswa?->kode_pos ?? '') }}" 
                                    class="w-full h-12 rounded-xl border border-white/10 px-4 text-white focus:border-emerald-500 outline-none transition-all" 
                                    style="background-color: #0a1f1a !important; color: #ffffff !important; border: 1px solid rgba(255,255,255,0.1) !important;" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button Area -->
                <div class="flex items-center justify-center md:justify-end py-4">
                    <button type="submit" 
                        class="group relative overflow-hidden h-14 px-12 rounded-2xl font-black text-white transition-all shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] active:scale-95" 
                        style="background: linear-gradient(to right, #059669, #10b981) !important; border: 1px solid rgba(255,255,255,0.2) !important;">
                        <div class="flex items-center gap-3 relative z-10">
                            <i class="fa-solid fa-cloud-arrow-up text-lg group-hover:animate-bounce"></i>
                            <span class="uppercase tracking-[0.2em] text-sm">Simpan Perubahan</span>
                        </div>
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-4">
            <div class="rounded-3xl border border-white/5 p-8 sticky top-8 shadow-2xl" style="background-color: #0d2a23 !important;">
                <div class="flex flex-col items-center text-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full"></div>
                        @if ($mahasiswa?->foto_path)
                            <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="relative h-36 w-36 rounded-3xl object-cover ring-4 ring-emerald-500/20 shadow-2xl" alt="Foto" />
                        @else
                            <div class="relative h-36 w-36 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-5xl font-black text-emerald-500 shadow-inner">
                                {{ mb_substr($mahasiswa?->nama_lengkap ?? auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-8">
                        <h3 class="text-xl font-black text-white tracking-tight leading-tight">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</h3>
                        <div class="mt-2 inline-flex items-center px-4 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                            <span class="text-emerald-400 font-mono text-sm font-bold tracking-widest">{{ $mahasiswa?->npm ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-10 space-y-4">
                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/5 group hover:border-emerald-500/30 transition-all" style="background-color: rgba(255,255,255,0.02) !important;">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fa-solid fa-graduation-cap text-emerald-500/50 text-xs"></i>
                            <span class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Program Studi</span>
                        </div>
                        <div class="text-sm text-white font-bold">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                    </div>
                    
                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/5 group hover:border-emerald-500/30 transition-all" style="background-color: rgba(255,255,255,0.02) !important;">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fa-solid fa-calendar-days text-emerald-500/50 text-xs"></i>
                            <span class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Angkatan</span>
                        </div>
                        <div class="text-sm text-white font-bold">{{ $mahasiswa?->angkatan ?? '-' }}</div>
                    </div>

                    <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/5 group hover:border-emerald-500/30 transition-all" style="background-color: rgba(255,255,255,0.02) !important;">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="fa-solid fa-shield-check text-emerald-500/50 text-xs"></i>
                            <span class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Status Akun</span>
                        </div>
                        <div class="mt-1">
                            <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                {{ $mahasiswa?->status_mahasiswa ?? 'Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-10 pt-8 border-t border-white/5">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-amber-500/5 border border-amber-500/10">
                        <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-1 text-sm"></i>
                        <p class="text-[11px] text-emerald-100/40 leading-relaxed font-medium">
                            <strong class="text-amber-500">PENTING:</strong> Data ini disinkronkan langsung ke sistem <span class="text-white">PD-DIKTI</span>. Pastikan semua kolom bertanda (*) diisi sesuai dokumen asli.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
