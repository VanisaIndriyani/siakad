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

    <div class="print-only" style="border: 1px solid #e5e7eb; border-radius: 14px; padding: 18px;">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
            <div>
                <div style="font-size: 18px; font-weight: 700;">Profil Dosen</div>
                <div style="margin-top: 4px; font-size: 12px; color: #4b5563;">{{ config('app.name') }}</div>
            </div>
            <div style="font-size: 12px; color: #4b5563; text-align: right;">
                <div>{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div style="margin-top: 14px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <tbody>
                    <tr>
                        <td style="width: 220px; padding: 8px; border: 1px solid #e5e7eb;">Nama Lengkap</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nama ?? auth()->user()->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">NIDN</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">NIK</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Tempat / Tanggal Lahir</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ trim(($dosen?->tempat_lahir ?? '').' / '.($dosen?->tanggal_lahir ?? '')) ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Email</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->email ?? auth()->user()->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Nomor Telp</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nomor_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">NUPTK</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nuptk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">NIP</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Jabatan Fungsional</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->jabatan_fungsional ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Kepangkatan</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->kepangkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Pendidikan Terakhir</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->pendidikan_terakhir ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Rumpun Ilmu</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->rumpun_ilmu ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Status Serdos</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->status_serdos ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Status Pegawai</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->status_pegawai ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Ikatan Kerja</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->ikatan_kerja ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Pengangkatan</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->tanggal_pengangkatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Nomor SK</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->nomor_sk ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Mata Kuliah</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->mata_kuliah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">Alamat</td>
                        <td style="padding: 8px; border: 1px solid #e5e7eb;">{{ $dosen?->alamat ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-xl font-semibold">Profil</div>
            <div class="text-sm text-emerald-100/70 mt-1">Perbarui data profil dosen.</div>

            <div class="no-print mt-4 flex items-center justify-end">
                <button type="button" onclick="window.print()" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-print"></i>
                    <span class="text-sm font-medium">Print</span>
                </button>
            </div>

            <form method="POST" action="{{ route('dosen.profil.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4 no-print">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Nama Lengkap</label>
                        <input value="{{ $dosen?->nama ?? auth()->user()->name }}" readonly class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 opacity-80" />
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">NIDN</label>
                        <input name="nidn" value="{{ old('nidn', $dosen?->nidn ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('nidn') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80">Tempat Lahir</label>
                        <input name="tempat_lahir" value="{{ old('tempat_lahir', $dosen?->tempat_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                        @error('tempat_lahir') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-sm text-emerald-100/80">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $dosen?->tanggal_lahir ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
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
                        <input type="date" name="tanggal_pengangkatan" value="{{ old('tanggal_pengangkatan', $dosen?->tanggal_pengangkatan ?? '') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
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

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center gap-4">
                @if ($dosen?->foto_path)
                    <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
                @else
                    <div class="h-16 w-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-2xl font-semibold">
                        {{ mb_substr($dosen?->nama ?? auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-semibold">{{ $dosen?->nama ?? auth()->user()->name }}</div>
                    <div class="text-sm text-emerald-100/70">{{ $dosen?->nidn ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-5 space-y-3 text-sm text-emerald-100/75">
                <div class="flex items-center justify-between">
                    <span>Email</span>
                    <span class="font-medium text-white">{{ $dosen?->email ?? auth()->user()->email }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>NIK</span>
                    <span class="font-medium text-white">{{ $dosen?->nik ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Nomor SK</span>
                    <span class="font-medium text-white">{{ $dosen?->nomor_sk ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
