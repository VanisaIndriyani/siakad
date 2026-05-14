<x-portal-layout :title="'Profil Mahasiswa - '.config('app.name')" subtitle="Profil Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Form Utama -->
        <div class="lg:col-span-8 space-y-6">
            <form method="POST" action="{{ route('mahasiswa.profil.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- Biodata Utama -->
                <div class="rounded-2xl bg-[#0d2a23] border border-white/5 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-6 w-1 bg-emerald-500 rounded-full"></div>
                        <h2 class="text-base font-bold text-white tracking-wide uppercase">Biodata Mahasiswa (PD-DIKTI)</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Nama Lengkap*</label>
                            <input name="nama_lengkap" value="{{ old('nama_lengkap', $mahasiswa?->nama_lengkap ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.03] border border-white/10 px-4 text-white/40 cursor-not-allowed focus:outline-none" readonly />
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Tempat Lahir*</label>
                            <input name="tempat_lahir" value="{{ old('tempat_lahir', $mahasiswa?->tempat_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('tempat_lahir') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Tanggal Lahir*</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $mahasiswa?->tanggal_lahir?->format('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('tanggal_lahir') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Jenis Kelamin*</label>
                            <select name="jenis_kelamin" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none appearance-none" required>
                                <option value="" disabled @selected(!old('jenis_kelamin', $mahasiswa?->jenis_kelamin))>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Laki-laki') class="bg-[#0d2a23]">Laki-laki</option>
                                <option value="Perempuan" @selected(old('jenis_kelamin', $mahasiswa?->jenis_kelamin) === 'Perempuan') class="bg-[#0d2a23]">Perempuan</option>
                            </select>
                            @error('jenis_kelamin') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Nama Ibu Kandung*</label>
                            <input name="nama_ibu" value="{{ old('nama_ibu', $mahasiswa?->nama_ibu ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('nama_ibu') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Agama*</label>
                            <select name="agama" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none appearance-none" required>
                                <option value="" disabled @selected(!old('agama', $mahasiswa?->agama))>Pilih Agama</option>
                                @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu'] as $agama)
                                    <option value="{{ $agama }}" @selected(old('agama', $mahasiswa?->agama) === $agama) class="bg-[#0d2a23]">{{ $agama }}</option>
                                @endforeach
                            </select>
                            @error('agama') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Kewarganegaraan*</label>
                            <input name="kewarganegaraan" value="{{ old('kewarganegaraan', $mahasiswa?->kewarganegaraan ?? 'Indonesia') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('kewarganegaraan') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">NIK*</label>
                            <input name="nik" value="{{ old('nik', $mahasiswa?->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('nik') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">NISN*</label>
                            <input name="nisn" value="{{ old('nisn', $mahasiswa?->nisn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                            @error('nisn') <div class="mt-1 text-[11px] text-red-400">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Alamat Detail -->
                <div class="rounded-2xl bg-[#0d2a23] border border-white/5 p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-6 w-1 bg-sky-500 rounded-full"></div>
                        <h2 class="text-base font-bold text-white tracking-wide uppercase">Alamat Domisili</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Jalan / Alamat Lengkap</label>
                            <input name="jalan" value="{{ old('jalan', $mahasiswa?->jalan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" />
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Kelurahan*</label>
                            <input name="kelurahan" value="{{ old('kelurahan', $mahasiswa?->kelurahan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Kecamatan*</label>
                            <input name="kecamatan" value="{{ old('kecamatan', $mahasiswa?->kecamatan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                        </div>
                        
                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Nomor HP*</label>
                            <input name="nomor_telp" value="{{ old('nomor_telp', $mahasiswa?->nomor_telp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" required />
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-emerald-100/50 uppercase tracking-wider">Kode Pos</label>
                            <input name="kode_pos" value="{{ old('kode_pos', $mahasiswa?->kode_pos ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/[0.05] border border-white/10 px-4 text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all outline-none" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end pb-10">
                    <button class="h-12 px-10 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition-all font-bold text-white shadow-lg shadow-emerald-900/20 uppercase tracking-widest text-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-4 space-y-6">
            <div class="rounded-2xl bg-[#0d2a23] border border-white/5 p-6 sticky top-6">
                <div class="flex flex-col items-center text-center">
                    <div class="relative group">
                        @if ($mahasiswa?->foto_path)
                            <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="h-28 w-28 rounded-2xl object-cover ring-2 ring-white/10 shadow-xl transition-transform group-hover:scale-105" alt="Foto" />
                        @else
                            <div class="h-28 w-28 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-4xl font-bold text-emerald-500 shadow-inner">
                                {{ mb_substr($mahasiswa?->nama_lengkap ?? auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-5">
                        <h3 class="text-lg font-bold text-white leading-tight">{{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}</h3>
                        <p class="text-emerald-500 font-mono text-sm mt-1 font-semibold tracking-wider">{{ $mahasiswa?->npm ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-8 space-y-3">
                    <div class="p-4 rounded-xl bg-white/[0.03] border border-white/5 group hover:bg-white/[0.05] transition-colors">
                        <div class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Program Studi</div>
                        <div class="text-sm text-white mt-1.5 font-semibold">{{ $mahasiswa?->program_studi ?? '-' }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/[0.03] border border-white/5 group hover:bg-white/[0.05] transition-colors">
                        <div class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Angkatan</div>
                        <div class="text-sm text-white mt-1.5 font-semibold">{{ $mahasiswa?->angkatan ?? '-' }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white/[0.03] border border-white/5 group hover:bg-white/[0.05] transition-colors">
                        <div class="text-[10px] text-emerald-100/40 uppercase tracking-[0.2em] font-bold">Status Mahasiswa</div>
                        <div class="mt-2">
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                {{ $mahasiswa?->status_mahasiswa ?? 'Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t border-white/5">
                    <div class="flex items-start gap-3 p-3 rounded-xl bg-amber-500/5 border border-amber-500/10">
                        <i class="fa-solid fa-circle-info text-amber-500 mt-0.5 text-sm"></i>
                        <p class="text-[11px] text-emerald-100/40 leading-relaxed italic">
                            Pastikan data sesuai dokumen asli untuk keperluan sinkronisasi <span class="text-amber-500/80 font-bold">PD-DIKTI</span>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
