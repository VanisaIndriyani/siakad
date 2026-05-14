<x-portal-layout :title="'Input Pembayaran - '.config('app.name')" subtitle="Input Pembayaran Baru">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 30px; padding-bottom: 50px;">
        <!-- Header -->
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
            <div>
                <h1 style="color: white; font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">INPUT PEMBAYARAN</h1>
                <p style="color: rgba(52,211,153,0.6); font-size: 14px; font-weight: 500; margin-top: 5px;">Mendaftarkan tagihan semester baru untuk mahasiswa.</p>
            </div>
            <a href="{{ route('keuangan.pembayaran.index') }}" 
               style="text-decoration: none; background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-left"></i>
                KEMBALI
            </a>
        </div>

        <form action="{{ route('keuangan.pembayaran.store') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 30px;">
            @csrf
            
            <!-- Card Form -->
            <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(16,185,129,0.12) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 15px;">
                    <div style="height: 40px; width: 40px; border-radius: 10px; background-color: rgba(16,185,129,0.2); border: 1px solid rgba(16,185,129,0.3); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-file-invoice-dollar" style="color: #34d399;"></i>
                    </div>
                    <h2 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Detail Tagihan Semester</h2>
                </div>

                <div style="padding: 30px; display: flex; flex-direction: column; gap: 25px;">
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Mode Input*</label>
                        <select name="mode" id="mode" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" required>
                            @php $mode = old('mode', 'single'); @endphp
                            <option value="single" @selected($mode === 'single') style="background-color: #0d2a23;">Single (1 Mahasiswa)</option>
                            <option value="angkatan" @selected($mode === 'angkatan') style="background-color: #0d2a23;">Per Angkatan</option>
                            <option value="all" @selected($mode === 'all') style="background-color: #0d2a23;">Semua Mahasiswa</option>
                        </select>
                        @error('mode') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                    </div>

                    <!-- Mahasiswa/Angkatan Selection -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Pilih Mahasiswa</label>
                            <select name="mahasiswa_id" id="mahasiswa_id" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;">
                                <option value="" style="background-color: #0d2a23;">-- Pilih Mahasiswa --</option>
                                @foreach ($mahasiswa as $m)
                                    <option value="{{ $m->id }}" @selected(old('mahasiswa_id') == $m->id) style="background-color: #0d2a23;">
                                        {{ $m->nama_lengkap }} ({{ $m->npm }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mahasiswa_id') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Angkatan</label>
                            <select name="angkatan" id="angkatan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;">
                                <option value="" style="background-color: #0d2a23;">-- Pilih Angkatan --</option>
                                @foreach ($angkatanList as $a)
                                    <option value="{{ $a }}" @selected((string) old('angkatan') === (string) $a) style="background-color: #0d2a23;">{{ $a }}</option>
                                @endforeach
                            </select>
                            @error('angkatan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <!-- Semester -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Semester*</label>
                            <select name="semester" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" required>
                                @foreach (range(1, 8) as $s)
                                    <option value="{{ $s }}" @selected(old('semester') == $s) style="background-color: #0d2a23;">Semester {{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Tahun Ajaran -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tahun Ajaran*</label>
                            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y').'/'.(date('Y')+1)) }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" placeholder="Contoh: 2026/2027" required />
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Jenis Tagihan*</label>
                            <select name="jenis_tagihan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" required>
                                <option value="" disabled @selected(!old('jenis_tagihan')) style="background-color: #0d2a23;">-- Pilih Jenis Tagihan --</option>
                                @foreach ($jenisTagihanList as $jt)
                                    <option value="{{ $jt }}" @selected(old('jenis_tagihan') === $jt) style="background-color: #0d2a23;">{{ $jt }}</option>
                                @endforeach
                            </select>
                            @error('jenis_tagihan') <div style="color: #f87171; font-size: 11px; margin-top: 4px;">{{ $message }}</div> @enderror
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Catatan / Keterangan</label>
                            <input type="text" name="catatan" value="{{ old('catatan') }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" placeholder="Contoh: Pembayaran UKT" />
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <!-- Total Biaya -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Total Biaya Semester (Rp)*</label>
                            <div style="position: relative;">
                                <input type="number" name="total_biaya" value="{{ old('total_biaya') }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: #34d399 !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px 0 45px; font-weight: 800; outline: none; font-size: 1.1rem;" placeholder="0" required />
                                <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2); font-weight: 800;">Rp</span>
                            </div>
                        </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <!-- Jumlah Bayar Awal -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Bayar Awal (Opsional)</label>
                            <div style="position: relative;">
                                <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar', 0) }}" 
                                    style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px 0 45px; font-weight: 700; outline: none;" placeholder="0" />
                                <span style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2); font-weight: 800;">Rp</span>
                            </div>
                        </div>
                        <!-- Tanggal Bayar -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" />
                        </div>
                    </div>

                    <!-- Bukti -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(52,211,153,0.8); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Bukti Bayar Awal</label>
                            <input type="file" name="bukti_pembayaran" 
                                style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 12px 15px; outline: none; font-size: 12px;" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div style="display: flex; justify-content: center; md:justify-content: flex-end;">
                <button type="submit" 
                    style="background: linear-gradient(to right, #059669, #10b981); color: white; border: none; padding: 18px 60px; border-radius: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; box-shadow: 0 15px 30px rgba(16,185,129,0.3);">
                    <i class="fa-solid fa-paper-plane"></i>
                    SIMPAN TAGIHAN
                </button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const modeEl = document.getElementById('mode');
            const mahasiswaEl = document.getElementById('mahasiswa_id');
            const angkatanEl = document.getElementById('angkatan');

            function sync() {
                const mode = modeEl ? modeEl.value : 'single';
                const isSingle = mode === 'single';
                const isAngkatan = mode === 'angkatan';

                if (mahasiswaEl) {
                    mahasiswaEl.disabled = !isSingle;
                    mahasiswaEl.required = isSingle;
                }
                if (angkatanEl) {
                    angkatanEl.disabled = !isAngkatan;
                    angkatanEl.required = isAngkatan;
                }
            }

            if (modeEl) modeEl.addEventListener('change', sync);
            sync();
        })();
    </script>
</x-portal-layout>
