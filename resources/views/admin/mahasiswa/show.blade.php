<x-portal-layout :title="'Detail Mahasiswa - '.config('app.name')" subtitle="Detail Mahasiswa">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-direction: column; gap: 30px; padding-bottom: 50px;">
        <!-- Header Section -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                @if ($mahasiswa->foto_path)
                    <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" style="height: 85px; width: 85px; border-radius: 20px; object-fit: cover; border: 4px solid rgba(16,185,129,0.2); box-shadow: 0 10px 20px rgba(0,0,0,0.3);" alt="Foto" />
                @else
                    <div style="height: 85px; width: 85px; border-radius: 20px; background-color: rgba(16,185,129,0.1); border: 2px solid rgba(16,185,129,0.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 900; color: #10b981; box-shadow: 0 10px 20px rgba(0,0,0,0.3);">
                        {{ mb_substr($mahasiswa->nama_lengkap, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 style="color: white; font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">{{ $mahasiswa->nama_lengkap }}</h1>
                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 8px;">
                        <span style="background-color: rgba(16,185,129,0.15); color: #34d399; padding: 4px 12px; border-radius: 8px; font-family: monospace; font-size: 13px; font-weight: 700; border: 1px solid rgba(16,185,129,0.2);">{{ $mahasiswa->npm }}</span>
                        <span style="color: rgba(255,255,255,0.2);">|</span>
                        <span style="color: rgba(255,255,255,0.5); font-size: 14px; font-weight: 500;">{{ $mahasiswa->user?->email }}</span>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" style="text-decoration: none; background-color: rgba(255,255,255,0.05); color: #34d399; border: 1px solid rgba(52,211,153,0.3); padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                    <i class="fa-solid fa-pen-to-square"></i>
                    EDIT IDENTITAS
                </a>
                <a href="{{ route('admin.mahasiswa.index') }}" style="text-decoration: none; background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i>
                    KEMBALI
                </a>
            </div>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <!-- Left Column: Main Data -->
            <div style="flex: 2; min-width: 300px; display: flex; flex-direction: column; gap: 30px;">
                <!-- Section: Biodata -->
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="padding: 20px 30px; background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-id-card" style="color: #10b981; font-size: 1.2rem;"></i>
                            <h2 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Biodata Pribadi (PD-DIKTI)</h2>
                        </div>
                        <span style="background-color: rgba(16,185,129,0.1); color: #10b981; font-size: 9px; font-weight: 900; padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(16,185,129,0.2); letter-spacing: 1px;">READ ONLY</span>
                    </div>
                    <div style="padding: 30px;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">NIK</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->nik ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">NISN</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->nisn ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Tempat, Tanggal Lahir</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->tempat_lahir ?? '-' }}, {{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d F Y') : '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Jenis Kelamin</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->jenis_kelamin ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Agama</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->agama ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Kewarganegaraan</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->kewarganegaraan ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">NPWP</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->npwp ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(52,211,153,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Nomor Telp / HP</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->nomor_telp ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Alamat -->
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="padding: 20px 30px; background: linear-gradient(135deg, rgba(14,165,233,0.12) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="fa-solid fa-map-location-dot" style="color: #38bdf8; font-size: 1.2rem;"></i>
                            <h2 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Alamat Domisili</h2>
                        </div>
                    </div>
                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 30px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(56,189,248,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Jalan / Alamat Lengkap</label>
                            <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); line-height: 1.6;">{{ $mahasiswa->jalan ?? '-' }}</div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(56,189,248,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Dusun / RT / RW</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->dusun ?? '-' }} / RT: {{ $mahasiswa->rt ?? '-' }} / RW: {{ $mahasiswa->rw ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(56,189,248,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Kelurahan</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->kelurahan ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(56,189,248,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Kecamatan</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->kecamatan ?? '-' }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <label style="color: rgba(56,189,248,0.5); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Kode Pos</label>
                                <div style="color: white; font-size: 15px; font-weight: 700; background-color: rgba(255,255,255,0.03); padding: 12px 15px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">{{ $mahasiswa->kode_pos ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Orang Tua -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <!-- Ayah -->
                    <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden;">
                        <div style="padding: 20px 30px; background-color: rgba(59,130,246,0.1); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 12px;">
                            <i class="fa-solid fa-user-tie" style="color: #3b82f6;"></i>
                            <h3 style="color: white; font-size: 13px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Data Ayah</h3>
                        </div>
                        <div style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                            <div>
                                <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Nama Ayah</label>
                                <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->ayah_nama ?? '-' }}</div>
                            </div>
                            <div>
                                <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Pekerjaan</label>
                                <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->ayah_pekerjaan ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Ibu -->
                    <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden;">
                        <div style="padding: 20px 30px; background-color: rgba(236,72,153,0.1); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 12px;">
                            <i class="fa-solid fa-person-breastfeeding" style="color: #ec4899;"></i>
                            <h3 style="color: white; font-size: 13px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Data Ibu</h3>
                        </div>
                        <div style="padding: 25px; display: flex; flex-direction: column; gap: 20px;">
                            <div>
                                <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Nama Ibu Kandung</label>
                                <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->ibu_nama ?? '-' }}</div>
                            </div>
                            <div>
                                <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Pekerjaan</label>
                                <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->ibu_pekerjaan ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Data -->
            <div style="width: 350px; display: flex; flex-direction: column; gap: 30px;">
                <!-- Section: Akademik -->
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
                        <i class="fa-solid fa-graduation-cap" style="color: #f59e0b; font-size: 1.2rem;"></i>
                        <h3 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Akademik</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Program Studi</label>
                            <div style="color: #34d399; font-size: 14px; font-weight: 800; margin-top: 5px;">{{ $mahasiswa->program_studi ?? '-' }}</div>
                        </div>
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Angkatan</label>
                            <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->angkatan ?? '-' }}</div>
                        </div>
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Status Kuliah</label>
                            <div style="margin-top: 8px;">
                                <span style="background-color: rgba(16,185,129,0.2); color: #34d399; padding: 4px 12px; border-radius: 8px; font-size: 10px; font-weight: 800; text-transform: uppercase; border: 1px solid rgba(16,185,129,0.3);">{{ $mahasiswa->status_mahasiswa ?? 'Aktif' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: KPS -->
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 30px;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
                        <i class="fa-solid fa-id-card-clip" style="color: #a78bfa; font-size: 1.2rem;"></i>
                        <h3 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Status KPS</h3>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Penerima KPS</label>
                            <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px;">{{ $mahasiswa->penerima_kps ?? 'Tidak' }}</div>
                        </div>
                        <div>
                            <label style="color: rgba(255,255,255,0.3); font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Nomor KPS</label>
                            <div style="color: white; font-size: 14px; font-weight: 700; margin-top: 5px; font-family: monospace;">{{ $mahasiswa->no_kps ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Section: Danger Zone -->
                <div style="background-color: rgba(239,68,68,0.05); border-radius: 24px; border: 1px solid rgba(239,68,68,0.15); padding: 25px;">
                    <h3 style="color: #f87171; font-size: 12px; font-weight: 800; margin: 0 0 15px 0; text-transform: uppercase; letter-spacing: 1px;">Zona Berbahaya</h3>
                    <form action="{{ route('admin.mahasiswa.destroy', $mahasiswa) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 100%; height: 45px; background-color: rgba(239,68,68,0.1); color: #f87171; border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; font-size: 12px; font-weight: 800; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s;">
                            <i class="fa-solid fa-trash-can"></i>
                            HAPUS MAHASISWA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>