<x-portal-layout :title="'Riwayat Pembayaran - '.config('app.name')" subtitle="Riwayat Pembayaran">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-direction: column; gap: 30px; padding-bottom: 50px;">
        <!-- Header -->
        <div style="display: flex; flex-direction: column; gap: 5px;">
            <h1 style="color: white; font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">RIWAYAT PEMBAYARAN</h1>
            <p style="color: rgba(52,211,153,0.6); font-size: 14px; font-weight: 500;">Daftar pembayaran semester dan status tagihan akademik Anda.</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 25px;">
            @forelse($pembayarans as $p)
            <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
                <!-- Card Header -->
                <div style="padding: 25px 30px; background: linear-gradient(135deg, rgba(16,185,129,0.1) 0%, transparent 100%); border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
                    <div>
                        <div style="color: rgba(52,211,153,0.6); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;">Semester {{ $p->semester }}</div>
                        <div style="color: white; font-size: 1.4rem; font-weight: 800; margin-top: 2px;">TA {{ $p->tahun_ajaran }}</div>
                    </div>
                    
                    <div style="display: flex; flex-wrap: wrap; gap: 30px; align-items: center;">
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <span style="color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Total Tagihan</span>
                            <span style="color: #34d399; font-size: 1.1rem; font-weight: 800;">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <span style="color: rgba(255,255,255,0.3); font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Telah Dibayar</span>
                            <span style="color: #38bdf8; font-size: 1.1rem; font-weight: 800;">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            @php
                                $statusStyle = match($p->status_pembayaran) {
                                    'Lunas' => 'background-color: rgba(16,185,129,0.2); color: #10b981; border: 1px solid rgba(16,185,129,0.3);',
                                    'Cicil' => 'background-color: rgba(245,158,11,0.2); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3);',
                                    default => 'background-color: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3);'
                                };
                            @endphp
                            <span style="{{ $statusStyle }} padding: 6px 16px; border-radius: 10px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                                {{ $p->status_pembayaran }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                @php
                    $persen = $p->total_biaya > 0 ? ($p->total_dibayar / $p->total_biaya) * 100 : 0;
                    $persen = min(100, $persen);
                @endphp
                <div style="height: 6px; background-color: rgba(255,255,255,0.03); width: 100%; position: relative;">
                    <div style="height: 100%; background: linear-gradient(to right, #10b981, #34d399); width: {{ $persen }}%; border-radius: 0 3px 3px 0; box-shadow: 0 0 10px rgba(16,185,129,0.3);"></div>
                </div>

                <div style="padding: 30px;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                        <i class="fa-solid fa-clock-rotate-left" style="color: rgba(52,211,153,0.4); font-size: 12px;"></i>
                        <h3 style="color: rgba(255,255,255,0.5); font-size: 12px; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 1px;">Riwayat Transaksi</h3>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        @forelse($p->details as $detail)
                        <div style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04); border-radius: 16px; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="height: 40px; width: 40px; background-color: rgba(16,185,129,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-receipt" style="color: #10b981;"></i>
                                </div>
                                <div>
                                    <div style="color: white; font-weight: 700; font-size: 14px;">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</div>
                                    <div style="color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 2px;">{{ $detail->tanggal_bayar->format('d F Y') }}</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 15px;">
                                @if($detail->keterangan)
                                    <span style="color: rgba(255,255,255,0.4); font-size: 12px; font-style: italic;">{{ $detail->keterangan }}</span>
                                @endif
                                @if($detail->bukti_pembayaran)
                                <a href="{{ asset('storage/'.$detail->bukti_pembayaran) }}" target="_blank" 
                                   style="text-decoration: none; background-color: rgba(16,185,129,0.1); color: #34d399; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 6px; border: 1px solid rgba(16,185,129,0.2);">
                                    <i class="fa-solid fa-image"></i> BUKTI
                                </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div style="text-align: center; padding: 20px; color: rgba(255,255,255,0.2); font-size: 13px; font-style: italic;">Belum ada riwayat transaksi.</div>
                        @endforelse
                    </div>
                    
                    @if($p->catatan)
                    <div style="margin-top: 25px; padding: 20px; border-radius: 16px; background-color: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.1); display: flex; gap: 15px;">
                        <i class="fa-solid fa-circle-info" style="color: #f59e0b; font-size: 1.1rem; margin-top: 2px;"></i>
                        <div>
                            <div style="color: #f59e0b; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Catatan Bagian Keuangan:</div>
                            <div style="color: rgba(255,255,255,0.7); font-size: 13px; line-height: 1.6;">{{ $p->catatan }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); padding: 80px 40px; text-align: center;">
                <div style="height: 100px; width: 100px; background-color: rgba(255,255,255,0.03); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px auto;">
                    <i class="fa-solid fa-money-bill-transfer" style="font-size: 3rem; color: rgba(255,255,255,0.05);"></i>
                </div>
                <h3 style="color: white; font-size: 1.1rem; font-weight: 700; margin: 0;">Belum Ada Riwayat</h3>
                <p style="color: rgba(255,255,255,0.3); font-size: 14px; margin-top: 10px;">Data pembayaran semester Anda belum tercatat di sistem.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-portal-layout>