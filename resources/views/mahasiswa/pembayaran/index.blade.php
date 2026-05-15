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
                        <div style="margin-top: 8px; display: inline-flex; align-items: center; gap: 8px; background-color: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); padding: 6px 10px; border-radius: 999px;">
                            <i class="fa-solid fa-tags" style="color: rgba(52,211,153,0.7); font-size: 11px;"></i>
                            <span style="color: rgba(255,255,255,0.75); font-size: 12px; font-weight: 800;">{{ $p->jenis_tagihan ?? 'Tagihan' }}</span>
                        </div>
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
                            @php
                                $pendingCount = $p->details->where('status_approval', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span style="margin-left: 10px; background-color: rgba(245,158,11,0.18); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); padding: 6px 12px; border-radius: 10px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                                    Menunggu {{ $pendingCount }}
                                </span>
                            @endif
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
                        @php
                            $approvalStyle = match((string) ($detail->status_approval ?? 'approved')) {
                                'pending' => 'background-color: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25);',
                                'rejected' => 'background-color: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.25);',
                                default => 'background-color: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.25);',
                            };
                            $approvalLabel = match((string) ($detail->status_approval ?? 'approved')) {
                                'pending' => 'MENUNGGU',
                                'rejected' => 'DITOLAK',
                                default => 'DITERIMA',
                            };
                        @endphp
                        <div style="background-color: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.04); border-radius: 16px; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; gap: 15px;">
                            <div style="display: flex; align-items: center; gap: 20px;">
                                <div style="height: 40px; width: 40px; background-color: rgba(16,185,129,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-receipt" style="color: #10b981;"></i>
                                </div>
                                <div>
                                    <div style="color: white; font-weight: 700; font-size: 14px;">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</div>
                                    <div style="color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 2px;">{{ $detail->tanggal_bayar->format('d F Y') }}</div>
                                    <div style="margin-top: 8px;">
                                        <span style="{{ $approvalStyle }} padding: 5px 10px; border-radius: 10px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                                            {{ $approvalLabel }}
                                        </span>
                                    </div>
                                    @if($detail->status_approval === 'rejected' && $detail->catatan_approval)
                                        <div style="margin-top: 8px; color: rgba(255,255,255,0.55); font-size: 11px; font-weight: 600;">
                                            Catatan: {{ $detail->catatan_approval }}
                                        </div>
                                    @endif
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
                                <a href="{{ asset('storage/'.$detail->bukti_pembayaran) }}" target="_blank" style="text-decoration: none;">
                                    <img src="{{ asset('storage/'.$detail->bukti_pembayaran) }}" alt="Bukti Pembayaran"
                                         style="width: 72px; height: 54px; object-fit: cover; border-radius: 10px; border: 1px solid rgba(255,255,255,0.12);" />
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

                    @if($p->status_pembayaran !== 'Lunas')
                        <div style="margin-top: 25px; padding: 22px; border-radius: 20px; background: rgba(16,185,129,0.05); border: 1px solid rgba(16,185,129,0.12);">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
                                <i class="fa-solid fa-cloud-arrow-up" style="color: #34d399;"></i>
                                <div style="color: white; font-weight: 900; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Upload Bukti Pembayaran</div>
                            </div>
                            <form action="{{ route('mahasiswa.pembayaran.upload', $p) }}" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-items: end;">
                                @csrf
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <label style="color: rgba(255,255,255,0.45); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Jumlah Bayar (Rp)*</label>
                                    <input type="hidden" name="jumlah_bayar" id="jumlah_bayar_{{ $p->id }}" />
                                    <input type="text" inputmode="numeric" autocomplete="off" placeholder="0" required
                                           data-currency-target="jumlah_bayar_{{ $p->id }}"
                                           style="width: 100%; height: 46px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 14px; outline: none; font-weight: 700;" />
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <label style="color: rgba(255,255,255,0.45); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Tanggal Bayar</label>
                                    <input type="hidden" name="tanggal_bayar" id="tanggal_bayar_{{ $p->id }}" value="{{ date('Y-m-d') }}" />
                                    <input type="text" inputmode="numeric" autocomplete="off" placeholder="dd/mm/yyyy"
                                           value="{{ now()->format('d/m/Y') }}"
                                           data-date-target="tanggal_bayar_{{ $p->id }}"
                                           style="width: 100%; height: 46px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 14px; outline: none; font-weight: 700;" />
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <label style="color: rgba(255,255,255,0.45); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Bukti Pembayaran*</label>
                                    <input type="file" name="bukti_pembayaran" accept="image/*" required
                                           style="width: 100%; height: 46px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 10px 14px; outline: none; font-size: 12px;" />
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <label style="color: rgba(255,255,255,0.45); font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Keterangan</label>
                                    <input type="text" name="keterangan" maxlength="255" placeholder="Contoh: Transfer BRI"
                                           style="width: 100%; height: 46px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 14px; outline: none; font-weight: 600;" />
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <button type="submit"
                                            style="height: 46px; padding: 0 18px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #059669); border: none; color: white; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; width: 100%;">
                                        Upload
                                    </button>
                                </div>
                            </form>
                            @error('jumlah_bayar') <div style="margin-top: 12px; color: #f87171; font-size: 12px; font-weight: 700;">{{ $message }}</div> @enderror
                            @error('bukti_pembayaran') <div style="margin-top: 6px; color: #f87171; font-size: 12px; font-weight: 700;">{{ $message }}</div> @enderror
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

    <script>
        (function () {
            const formatRupiah = (digits) => {
                const cleaned = String(digits || '').replace(/[^\d]/g, '');
                if (!cleaned) return '';
                return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            const syncCurrencyInput = (displayInput) => {
                const targetId = displayInput.getAttribute('data-currency-target');
                const hiddenInput = targetId ? document.getElementById(targetId) : null;
                const digits = String(displayInput.value || '').replace(/[^\d]/g, '');
                displayInput.value = formatRupiah(digits);
                if (hiddenInput) hiddenInput.value = digits;
            };

            document.querySelectorAll('input[data-currency-target]').forEach((el) => {
                el.addEventListener('input', () => syncCurrencyInput(el));
                el.addEventListener('blur', () => syncCurrencyInput(el));
                syncCurrencyInput(el);
                const form = el.closest('form');
                if (form) {
                    form.addEventListener('submit', () => syncCurrencyInput(el));
                }
            });

            const formatTanggal = (digits) => {
                const cleaned = String(digits || '').replace(/[^\d]/g, '').slice(0, 8);
                if (!cleaned) return '';
                if (cleaned.length <= 2) return cleaned;
                if (cleaned.length <= 4) return `${cleaned.slice(0, 2)}/${cleaned.slice(2)}`;
                return `${cleaned.slice(0, 2)}/${cleaned.slice(2, 4)}/${cleaned.slice(4)}`;
            };

            const toIsoDate = (ddmmyyyyDigits) => {
                const cleaned = String(ddmmyyyyDigits || '').replace(/[^\d]/g, '');
                if (cleaned.length !== 8) return '';
                const d = parseInt(cleaned.slice(0, 2), 10);
                const m = parseInt(cleaned.slice(2, 4), 10);
                const y = parseInt(cleaned.slice(4, 8), 10);
                if (!y || m < 1 || m > 12 || d < 1 || d > 31) return '';
                const dt = new Date(y, m - 1, d);
                if (dt.getFullYear() !== y || dt.getMonth() !== (m - 1) || dt.getDate() !== d) return '';
                const mm = String(m).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                return `${y}-${mm}-${dd}`;
            };

            const syncTanggalInput = (displayInput) => {
                const targetId = displayInput.getAttribute('data-date-target');
                const hiddenInput = targetId ? document.getElementById(targetId) : null;
                const digits = String(displayInput.value || '').replace(/[^\d]/g, '').slice(0, 8);
                displayInput.value = formatTanggal(digits);
                if (hiddenInput) {
                    hiddenInput.value = toIsoDate(digits);
                }
            };

            document.querySelectorAll('input[data-date-target]').forEach((el) => {
                el.addEventListener('input', () => syncTanggalInput(el));
                el.addEventListener('blur', () => syncTanggalInput(el));
                syncTanggalInput(el);
                const form = el.closest('form');
                if (form) {
                    form.addEventListener('submit', () => syncTanggalInput(el));
                }
            });
        })();
    </script>
</x-portal-layout>
