<x-portal-layout :title="'Detail Pembayaran - '.config('app.name')" subtitle="Detail & Riwayat Cicilan">
    <x-slot:sidebar>
        @include('keuangan.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-direction: column; gap: 30px; padding-bottom: 50px;">
        <!-- Header -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 20px;">
                <div style="height: 70px; width: 70px; border-radius: 20px; background-color: rgba(16,185,129,0.1); border: 2px solid rgba(16,185,129,0.2); display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-receipt" style="color: #10b981; font-size: 2rem;"></i>
                </div>
                <div>
                    <h1 style="color: white; font-size: 1.6rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">DETAIL PEMBAYARAN</h1>
                    <p style="color: rgba(52,211,153,0.6); font-size: 14px; font-weight: 500; margin-top: 5px;">{{ $pembayaran->mahasiswa->nama_lengkap }} ({{ $pembayaran->mahasiswa->npm }})</p>
                </div>
            </div>
            <a href="{{ route('keuangan.pembayaran.index') }}" 
               style="text-decoration: none; background-color: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-left"></i>
                KEMBALI
            </a>
        </div>

        <div style="display: flex; flex-wrap: wrap; gap: 30px;">
            <!-- Left Column: Statistics & Action -->
            <div style="flex: 1; min-width: 320px; display: flex; flex-direction: column; gap: 30px;">
                <!-- Card: Summary -->
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <h3 style="color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 25px;">Informasi Tagihan</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 700; text-transform: uppercase;">Semester / TA</span>
                            <div style="color: white; font-size: 15px; font-weight: 700; margin-top: 5px;">Semester {{ $pembayaran->semester }} ({{ $pembayaran->tahun_ajaran }})</div>
                        </div>
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 700; text-transform: uppercase;">Total Tagihan</span>
                            <div style="color: #34d399; font-size: 1.4rem; font-weight: 900; margin-top: 5px;">Rp {{ number_format($pembayaran->total_biaya, 0, ',', '.') }}</div>
                        </div>
                        <div style="padding: 15px; background-color: rgba(255,255,255,0.02); border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                            <span style="color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 700; text-transform: uppercase;">Sudah Dibayar</span>
                            <div style="color: #38bdf8; font-size: 1.4rem; font-weight: 900; margin-top: 5px;">Rp {{ number_format($pembayaran->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                        <div style="padding: 15px; background-color: rgba(239,68,68,0.05); border-radius: 15px; border: 1px solid rgba(239,68,68,0.1);">
                            <span style="color: #f87171; font-size: 10px; font-weight: 800; text-transform: uppercase;">Sisa Hutang</span>
                            <div style="color: #fca5a5; font-size: 1.4rem; font-weight: 900; margin-top: 5px;">Rp {{ number_format($pembayaran->total_biaya - $pembayaran->total_dibayar, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; padding-top: 20px; border-t: 1px solid rgba(255,255,255,0.05); text-align: center;">
                        @php
                            $statusStyle = match($pembayaran->status_pembayaran) {
                                'Lunas' => 'background-color: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.3);',
                                'Cicil' => 'background-color: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3);',
                                default => 'background-color: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3);'
                            };
                        @endphp
                        <span style="{{ $statusStyle }} padding: 8px 25px; border-radius: 12px; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px;">
                            {{ $pembayaran->status_pembayaran }}
                        </span>
                    </div>
                </div>

                <!-- Card: Form Tambah Cicilan -->
                @if($pembayaran->status_pembayaran !== 'Lunas')
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 30px;">
                    <h3 style="color: white; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-plus-circle" style="color: #10b981;"></i>
                        Tambah Cicilan
                    </h3>
                    
                    <form action="{{ route('keuangan.pembayaran.add-cicilan', $pembayaran) }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
                        @csrf
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 700; text-transform: uppercase;">Jumlah Bayar (Rp)*</label>
                            <input type="number" name="jumlah_bayar" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 700;" placeholder="0" required />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 700; text-transform: uppercase;">Tanggal Bayar*</label>
                            <input type="date" name="tanggal_bayar" value="{{ date('Y-m-d') }}" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none; font-weight: 600;" required />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 700; text-transform: uppercase;">Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 12px 15px; outline: none; font-size: 12px;" />
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <label style="color: rgba(255,255,255,0.4); font-size: 10px; font-weight: 700; text-transform: uppercase;">Keterangan</label>
                            <input type="text" name="keterangan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; outline: none;" placeholder="Contoh: Cicilan ke-2" />
                        </div>
                        <button type="submit" style="width: 100%; height: 50px; background: linear-gradient(to right, #059669, #10b981); color: white; border: none; border-radius: 12px; font-weight: 800; cursor: pointer; text-transform: uppercase; letter-spacing: 1px; margin-top: 10px;">
                            SIMPAN PEMBAYARAN
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Right Column: History -->
            <div style="flex: 2; min-width: 320px; display: flex; flex-direction: column; gap: 30px;">
                <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
                    <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(16,185,129,0.05) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; gap: 15px;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: #10b981; font-size: 1.2rem;"></i>
                        <h2 style="color: white; font-size: 14px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Riwayat Transaksi</h2>
                    </div>

                    <div style="padding: 30px; display: flex; flex-direction: column; gap: 15px;">
                        @forelse($pembayaran->details as $detail)
                            <div style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <div style="height: 50px; width: 50px; background-color: rgba(16,185,129,0.1); border-radius: 15px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa-solid fa-money-bill-transfer" style="color: #10b981; font-size: 1.3rem;"></i>
                                    </div>
                                    <div>
                                        <div style="color: white; font-weight: 800; font-size: 1.1rem;">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</div>
                                        <div style="color: rgba(255,255,255,0.3); font-size: 12px; margin-top: 3px; font-weight: 600;">{{ $detail->tanggal_bayar->format('d F Y') }}</div>
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 20px;">
                                    <div style="text-align: right;">
                                        <div style="color: rgba(255,255,255,0.6); font-size: 13px; font-weight: 600;">{{ $detail->keterangan ?? 'Tanpa keterangan' }}</div>
                                        @if($detail->bukti_pembayaran)
                                            <a href="{{ asset('storage/'.$detail->bukti_pembayaran) }}" target="_blank" 
                                               style="text-decoration: none; color: #34d399; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px; margin-top: 5px; background-color: rgba(16,185,129,0.1); padding: 4px 12px; border-radius: 6px; border: 1px solid rgba(16,185,129,0.2);">
                                                <i class="fa-solid fa-image"></i> LIHAT BUKTI
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 60px 0; color: rgba(255,255,255,0.1);">
                                <i class="fa-solid fa-receipt" style="font-size: 3rem; margin-bottom: 15px; display: block;"></i>
                                <span style="font-size: 14px; font-weight: 600;">Belum ada transaksi untuk tagihan ini.</span>
                            </div>
                        @endforelse
                    </div>

                    @if($pembayaran->catatan)
                        <div style="margin: 0 30px 30px 30px; padding: 20px; border-radius: 16px; background-color: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.1); display: flex; gap: 15px;">
                            <i class="fa-solid fa-circle-info" style="color: #f59e0b; font-size: 1.1rem; margin-top: 2px;"></i>
                            <div>
                                <div style="color: #f59e0b; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Catatan Tambahan:</div>
                                <div style="color: rgba(255,255,255,0.7); font-size: 13px; line-height: 1.6;">{{ $pembayaran->catatan }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>