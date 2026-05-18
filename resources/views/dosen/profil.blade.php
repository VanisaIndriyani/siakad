<x-portal-layout :title="'Profil Dosen - '.config('app.name')" subtitle="Profil Dosen">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <style>
        .print-only { display: none; }
        @media print {
            aside, header, #confirmModal { display: none !important; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: #fff !important; color: #000 !important; }
            main { padding: 0 !important; }
        }
    </style>

    <div class="print-only">
        <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 2px;">
            <tr>
                <td style="width: 110px; vertical-align: middle;">
                    @php
                        $logoPath = public_path('img/lo.jpeg');
                        $logoBase64 = '';
                        if (file_exists($logoPath)) {
                            $logoData = file_get_contents($logoPath);
                            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                        }
                    @endphp
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" style="width: 100px; height: auto;">
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    <div style="font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1;">INSTITUT AGAMA ISLAM</div>
                    <div style="font-size: 24px; font-weight: 900; margin: 2px 0; line-height: 1;">DARUD DA'WAH WAL IRSYAD</div>
                    <div style="font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1;">SIDENRENG RAPPANG</div>
                    <div style="font-size: 10px; font-weight: 700; margin-top: 3px;">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                    <div style="font-size: 10px; margin: 2px 0;">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                    <div style="font-size: 11px; margin: 2px 0;">E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id</div>
                </td>
                <td style="width: 90px;"></td>
            </tr>
        </table>
        <div style="border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px;"></div>
        
        <div style="text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase;">Profil Dosen</div>

        <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; border: none; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold; width: 30%;">Nama Lengkap</td>
                            <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nama ?? auth()->user()->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">NIDN</td>
                            <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nidn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">NIK</td>
                            <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Program Studi</td>
                            <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->program_studi ?? '-' }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 120px; text-align: right; vertical-align: top; border: none; padding-left: 10px;">
                    <div style="width: 100px; height: 125px; border: 1px solid #000; padding: 2px; background: white; display: inline-block;">
                        @if ($dosen?->foto_path)
                            @php
                                $path = storage_path('app/public/'.$dosen->foto_path);
                                $type = pathinfo($path, PATHINFO_EXTENSION);
                                $data = @file_get_contents($path);
                                $base64 = $data ? 'data:image/' . $type . ';base64,' . base64_encode($data) : asset('storage/'.$dosen->foto_path);
                            @endphp
                            <img src="{{ $base64 }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Foto" />
                        @else
                            <div style="width: 100%; height: 100%; background: #f3f4f6; color: #9ca3af; display: flex; align-items: center; justify-content: center; text-align: center; padding-top: 40px; font-size: 8px;">No Photo</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        <div style="background-color: #f3f4f6; font-weight: bold; padding: 5px 10px; border: 1px solid #000; border-bottom: none; margin-top: 15px; font-size: 12px; text-transform: uppercase;">Data Personal & Akademik</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 30%; padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Tempat / Tanggal Lahir</td>
                    <td style="padding: 8px; border: 1px solid #000;">
                        {{ $dosen?->tempat_lahir ?: '-' }} /
                        {{ $dosen?->tanggal_lahir ? \Illuminate\Support\Carbon::parse($dosen->tanggal_lahir)->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Email</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->email ?? auth()->user()->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Nomor Telp</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nomor_hp ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Alamat</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->alamat ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Status Dosen</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ ($dosen?->status_dosen ?? 'aktif') === 'tidak aktif' ? 'Tidak Aktif' : 'Aktif' }}</td>
                </tr>
            </tbody>
        </table>

        <div style="background-color: #f3f4f6; font-weight: bold; padding: 5px 10px; border: 1px solid #000; border-bottom: none; margin-top: 15px; font-size: 12px; text-transform: uppercase;">Data Kepegawaian</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
            <tbody>
                <tr>
                    <td style="width: 30%; padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">NUPTK</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nuptk ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">NIP</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nip ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Jabatan Fungsional</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->jabatan_fungsional ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Kepangkatan</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->kepangkatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Pendidikan Terakhir</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->pendidikan_terakhir ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Rumpun Ilmu</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->rumpun_ilmu ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Status Pegawai</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->status_pegawai ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Ikatan Kerja</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->ikatan_kerja ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Pengangkatan</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->tanggal_pengangkatan ? \Illuminate\Support\Carbon::parse($dosen->tanggal_pengangkatan)->format('d/m/Y') : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #000; background: #f3f4f6; font-weight: bold;">Nomor SK</td>
                    <td style="padding: 8px; border: 1px solid #000;">{{ $dosen?->nomor_sk ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #6b7280;">
            Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan secara otomatis oleh Sistem Informasi Akademik.
        </div>
    </div>

    <div class="no-print rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="flex flex-col sm:flex-row items-start justify-between gap-6">
            <div>
                <div class="text-xl font-semibold">Profil</div>
                <div class="text-sm text-emerald-100/70 mt-1">Perbarui data profil dosen.</div>
            </div>
            
            <div class="flex flex-col items-end gap-4">
                {{-- Tombol PDF & Print di atas Foto --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('dosen.profil.pdf') }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        <i class="fa-solid fa-file-pdf text-rose-400"></i>
                        <span class="text-xs font-medium">PDF</span>
                    </a>
                    <button type="button" onclick="window.print()" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        <i class="fa-solid fa-print text-emerald-400"></i>
                        <span class="text-xs font-medium">Print</span>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <div class="text-sm font-semibold text-white">{{ $dosen?->nama ?? auth()->user()->name }}</div>
                        <div class="text-xs text-emerald-100/60">{{ $dosen?->nidn ?? '-' }}</div>
                        @if($dosen?->nomor_hp)
                            <div class="text-[10px] text-emerald-100/40 mt-1">
                                <i class="fa-solid fa-phone mr-1"></i>{{ $dosen->nomor_hp }}
                            </div>
                        @endif
                    </div>
                    @if ($dosen?->foto_path)
                        <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-emerald-500/20 shadow-lg shadow-emerald-900/20" alt="Foto" />
                    @else
                        <div class="h-16 w-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-2xl font-semibold text-emerald-300">
                            {{ mb_substr($dosen?->nama ?? auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('dosen.profil.update') }}" enctype="multipart/form-data" class="mt-8 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Nama Lengkap</label>
                    <input name="nama" value="{{ old('nama', $dosen?->nama ?? auth()->user()->name) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                    @error('nama') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">NIDN</label>
                    <input name="nidn" value="{{ old('nidn', $dosen?->nidn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('nidn') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Program Studi</label>
                    <input value="{{ $dosen?->program_studi ?? '-' }}" readonly class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 opacity-80" />
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Status Akademik</label>
                    <input value="{{ $dosen?->status_akademik ?? 'Dosen' }}" readonly class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 opacity-80" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Status Dosen</label>
                    <input value="{{ ($dosen?->status_dosen ?? 'aktif') === 'tidak aktif' ? 'Tidak Aktif' : 'Aktif' }}" readonly class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 opacity-80" />
                </div>
                <div></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Tempat Lahir</label>
                    <input name="tempat_lahir" value="{{ old('tempat_lahir', $dosen?->tempat_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('tempat_lahir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-emerald-100/80">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $dosen?->tanggal_lahir ? \Illuminate\Support\Carbon::parse($dosen->tanggal_lahir)->format('Y-m-d') : '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('tanggal_lahir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>
            </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Email</label>
                    <input type="email" name="email" value="{{ old('email', $dosen?->email ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('email') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Alamat</label>
                    <textarea name="alamat" rows="4" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">{{ old('alamat', $dosen?->alamat ?? '') }}</textarea>
                    @error('alamat') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">NIK</label>
                        <input name="nik" value="{{ old('nik', $dosen?->nik ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required />
                        @error('nik') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">NUPTK</label>
                        <input name="nuptk" value="{{ old('nuptk', $dosen?->nuptk ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nuptk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">NIP</label>
                        <input name="nip" value="{{ old('nip', $dosen?->nip ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nip') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Nomor SK</label>
                        <input name="nomor_sk" value="{{ old('nomor_sk', $dosen?->nomor_sk ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nomor_sk') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Jabatan Fungsional</label>
                    <input name="jabatan_fungsional" value="{{ old('jabatan_fungsional', $dosen?->jabatan_fungsional ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('jabatan_fungsional') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Nomor Telp</label>
                        <input name="nomor_hp" value="{{ old('nomor_hp', $dosen?->nomor_hp ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nomor_hp') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Kepangkatan</label>
                        <input name="kepangkatan" value="{{ old('kepangkatan', $dosen?->kepangkatan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('kepangkatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Pendidikan Terakhir</label>
                        <input name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $dosen?->pendidikan_terakhir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('pendidikan_terakhir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Rumpun Ilmu</label>
                        <input name="rumpun_ilmu" value="{{ old('rumpun_ilmu', $dosen?->rumpun_ilmu ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('rumpun_ilmu') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Status Serdos</label>
                        <input name="status_serdos" value="{{ old('status_serdos', $dosen?->status_serdos ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('status_serdos') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Status Pegawai</label>
                        <input name="status_pegawai" value="{{ old('status_pegawai', $dosen?->status_pegawai ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('status_pegawai') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Ikatan Kerja</label>
                        <input name="ikatan_kerja" value="{{ old('ikatan_kerja', $dosen?->ikatan_kerja ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('ikatan_kerja') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Pengangkatan</label>
                        <input type="date" name="tanggal_pengangkatan" value="{{ old('tanggal_pengangkatan', $dosen?->tanggal_pengangkatan ? \Illuminate\Support\Carbon::parse($dosen->tanggal_pengangkatan)->format('Y-m-d') : '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('tanggal_pengangkatan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Mata Kuliah</label>
                    <input name="mata_kuliah" value="{{ old('mata_kuliah', $dosen?->mata_kuliah ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                    @error('mata_kuliah') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Foto</label>
                    <input type="file" name="foto" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
                    @error('foto') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

            <div class="flex items-center justify-end">
                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</x-portal-layout>
