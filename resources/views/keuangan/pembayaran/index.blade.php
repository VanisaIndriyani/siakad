<x-portal-layout :title="'Pembayaran - '.config('app.name')" subtitle="Manajemen Pembayaran">
    <x-slot:sidebar>
        @include('keuangan.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-direction: column; gap: 25px; padding-bottom: 50px;">
        <!-- Header -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            <div>
                <h1 style="color: white; font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">MANAJEMEN PEMBAYARAN</h1>
                <p style="color: rgba(52,211,153,0.6); font-size: 13px; font-weight: 500; margin-top: 5px;">Kelola tagihan semester, cicilan, dan verifikasi pembayaran mahasiswa.</p>
            </div>
            <a href="{{ route('keuangan.pembayaran.create') }}" 
               style="text-decoration: none; background: linear-gradient(to right, #059669, #10b981); color: white; padding: 12px 25px; border-radius: 14px; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(16,185,129,0.3); border: 1px solid rgba(255,255,255,0.1); text-transform: uppercase; letter-spacing: 1px;">
                <i class="fa-solid fa-plus-circle"></i>
                Input Pembayaran Baru
            </a>
        </div>

        <!-- Filter & Search -->
        <div style="background-color: #0d2a23 !important; padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.08);">
            <form action="{{ route('keuangan.pembayaran.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 250px; position: relative;">
                    <input type="text" name="q" value="{{ $q }}" 
                           style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 45px 0 15px; font-weight: 500; outline: none;" 
                           placeholder="Cari Nama Mahasiswa atau NPM..." />
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2);"></i>
                </div>
                <button type="submit" style="height: 50px; padding: 0 30px; background-color: rgba(255,255,255,0.05); color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-weight: 700; cursor: pointer;">
                    CARI DATA
                </button>
                @if($q)
                    <a href="{{ route('keuangan.pembayaran.index') }}" style="height: 50px; padding: 0 20px; background-color: rgba(239,68,68,0.1); color: #f87171; border-radius: 12px; border: 1px solid rgba(239,68,68,0.2); font-weight: 700; display: flex; align-items: center; text-decoration: none;">
                        RESET
                    </a>
                @endif
            </form>
        </div>

        <!-- Table Card -->
        <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                    <thead>
                        <tr style="background-color: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Mahasiswa</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Semester</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Total Biaya</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Sudah Dibayar</th>
                            <th style="padding: 20px 25px; text-align: center; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Status</th>
                            <th style="padding: 20px 25px; text-align: right; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembayarans as $p)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background-color 0.3s;">
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 700; font-size: 14px;">{{ $p->mahasiswa->nama_lengkap }}</div>
                                    <div style="color: rgba(52,211,153,0.5); font-family: monospace; font-size: 12px; margin-top: 3px; font-weight: 600;">{{ $p->mahasiswa->npm }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 600; font-size: 13px;">Semester {{ $p->semester }}</div>
                                    <div style="color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 2px;">TA {{ $p->tahun_ajaran }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: #34d399; font-weight: 800; font-size: 14px;">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 700; font-size: 14px;">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</div>
                                    @if($p->total_biaya > $p->total_dibayar)
                                        <div style="color: #f87171; font-size: 10px; font-weight: 600; margin-top: 2px;">Sisa: Rp {{ number_format($p->total_biaya - $p->total_dibayar, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td style="padding: 20px 25px; text-align: center;">
                                    @php
                                        $statusStyle = match($p->status_pembayaran) {
                                            'Lunas' => 'background-color: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.2);',
                                            'Cicil' => 'background-color: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.2);',
                                            default => 'background-color: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2);'
                                        };
                                    @endphp
                                    <span style="{{ $statusStyle }} padding: 5px 15px; border-radius: 8px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">
                                        {{ $p->status_pembayaran }}
                                    </span>
                                </td>
                                <td style="padding: 20px 25px; text-align: right;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
                                        <a href="{{ route('keuangan.pembayaran.show', $p) }}" 
                                           style="text-decoration: none; height: 38px; padding: 0 15px; background-color: rgba(255,255,255,0.05); color: white; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                            <i class="fa-solid fa-file-invoice-dollar" style="color: #34d399;"></i>
                                            DETAIL
                                        </a>
                                        <form action="{{ route('keuangan.pembayaran.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data pembayaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="height: 38px; width: 38px; background-color: rgba(239,68,68,0.1); color: #f87171; border-radius: 10px; border: 1px solid rgba(239,68,68,0.2); cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 60px 0; text-align: center;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: rgba(255,255,255,0.05); display: block; margin-bottom: 15px;"></i>
                                    <span style="color: rgba(255,255,255,0.2); font-size: 14px; font-weight: 500;">Belum ada data pembayaran yang tercatat.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($pembayarans->hasPages())
            <div style="margin-top: 15px;">
                {{ $pembayarans->links() }}
            </div>
        @endif
    </div>
</x-portal-layout>