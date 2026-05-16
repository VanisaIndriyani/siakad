<x-portal-layout :title="'Pembayaran - '.config('app.name')" subtitle="Manajemen Pembayaran">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div style="display: flex; flex-direction: column; gap: 25px; padding-bottom: 50px;">
        <!-- Header -->
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px;">
            <div>
                <h1 style="color: white; font-size: 1.8rem; font-weight: 800; margin: 0; letter-spacing: -0.5px;">MANAJEMEN PEMBAYARAN</h1>
                <p style="color: rgba(52,211,153,0.6); font-size: 13px; font-weight: 500; margin-top: 5px;">Kelola tagihan semester, cicilan, dan verifikasi pembayaran mahasiswa.</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('keuangan.pembayaran.export.pdf', request()->query()) }}"
                   style="text-decoration: none; background-color: rgba(59,130,246,0.12); color: #bfdbfe; padding: 12px 18px; border-radius: 14px; font-weight: 900; font-size: 12px; display: flex; align-items: center; gap: 10px; border: 1px solid rgba(59,130,246,0.22); text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-file-pdf"></i>
                    PDF
                </a>
               
                <a href="{{ route('keuangan.pembayaran.create') }}"
                   style="text-decoration: none; background: linear-gradient(to right, #059669, #10b981); color: white; padding: 12px 25px; border-radius: 14px; font-weight: 800; font-size: 13px; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 20px rgba(16,185,129,0.3); border: 1px solid rgba(255,255,255,0.1); text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-plus-circle"></i>
                    Input Pembayaran Baru
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="no-print" style="background-color: #0d2a23 !important; padding: 20px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.08);">
            <form action="{{ route('keuangan.pembayaran.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 15px;">
                <div style="flex: 1; min-width: 250px; position: relative;">
                    <input type="text" name="q" value="{{ $q }}" 
                           style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 45px 0 15px; font-weight: 500; outline: none;" 
                           placeholder="Cari Nama Mahasiswa atau NPM..." />
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.2);"></i>
                </div>
                <div style="min-width: 200px;">
                    <select name="jenis_tagihan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; font-weight: 600; outline: none;">
                        <option value="" style="background-color: #0d2a23;">Semua Tagihan</option>
                        @foreach ($jenisTagihanList as $jt)
                            <option value="{{ $jt }}" @selected(($jenis_tagihan ?? '') === $jt) style="background-color: #0d2a23;">{{ $jt }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width: 170px;">
                    <select name="semester" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; font-weight: 600; outline: none;">
                        <option value="" style="background-color: #0d2a23;">Semua Semester</option>
                        @for ($s = 1; $s <= 14; $s++)
                            <option value="{{ $s }}" @selected((string) ($semester ?? '') === (string) $s) style="background-color: #0d2a23;">Semester {{ $s }}</option>
                        @endfor
                    </select>
                </div>
                <div style="min-width: 170px;">
                    <select name="angkatan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; font-weight: 600; outline: none;">
                        <option value="" style="background-color: #0d2a23;">Semua Angkatan</option>
                        @foreach ($angkatanList as $a)
                            <option value="{{ $a }}" @selected((string) ($angkatan ?? '') === (string) $a) style="background-color: #0d2a23;">{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width: 220px;">
                    <select name="jurusan" style="width: 100%; height: 50px; background-color: #0a1f1a !important; color: white !important; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1) !important; padding: 0 15px; font-weight: 600; outline: none;">
                        <option value="" style="background-color: #0d2a23;">Semua Jurusan</option>
                        @foreach (($jurusanList ?? []) as $j)
                            <option value="{{ $j }}" @selected((string) ($jurusan ?? '') === (string) $j) style="background-color: #0d2a23;">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="per_page" value="{{ $per_page ?? '10' }}" />
                <button type="submit" style="height: 50px; padding: 0 30px; background-color: rgba(255,255,255,0.05); color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-weight: 700; cursor: pointer;">
                    CARI DATA
                </button>
                <a href="{{ route('keuangan.pembayaran.index') }}" style="height: 50px; padding: 0 20px; background-color: rgba(239,68,68,0.1); color: #f87171; border-radius: 12px; border: 1px solid rgba(239,68,68,0.2); font-weight: 700; display: flex; align-items: center; text-decoration: none;">
                    RESET
                </a>
            </form>
        </div>

        <form id="bulkDeleteForm" action="{{ route('keuangan.pembayaran.bulk-delete') }}" method="POST" class="no-print" onsubmit="return confirm('Hapus semua pembayaran yang dicentang?')">
            @csrf
            @method('DELETE')
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                <div id="bulkDeleteHint" style="color: rgba(255,255,255,0.35); font-size: 12px; font-weight: 600;">
                    Pilih data dengan ceklis untuk hapus cepat.
                </div>
                <button id="bulkDeleteBtn" type="submit" disabled
                        style="height: 44px; padding: 0 16px; background-color: rgba(239,68,68,0.12); color: #f87171; border-radius: 12px; border: 1px solid rgba(239,68,68,0.22); font-weight: 900; cursor: pointer; display: inline-flex; align-items: center; gap: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: .55;">
                    <i class="fa-solid fa-trash-can"></i>
                    Hapus Terpilih (0)
                </button>
            </div>

        <!-- Table Card -->
        <div style="background-color: #0d2a23 !important; border-radius: 24px; border: 1px solid rgba(255,255,255,0.08); overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                    <thead>
                        <tr style="background-color: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <th style="padding: 20px 15px; width: 44px; text-align: center; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">
                                <input id="selectAllPayments" type="checkbox" style="width: 16px; height: 16px; accent-color: #10b981; cursor: pointer;" />
                            </th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Mahasiswa</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Semester</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Tagihan</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Angkatan</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Total Biaya</th>
                            <th style="padding: 20px 25px; text-align: left; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Sudah Dibayar</th>
                            <th style="padding: 20px 25px; text-align: center; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Status</th>
                            <th style="padding: 20px 25px; text-align: right; color: rgba(52,211,153,0.5); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px;">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pembayarans as $p)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); transition: background-color 0.3s;">
                                <td style="padding: 20px 15px; text-align: center;">
                                    <input class="payment-check" type="checkbox" name="ids[]" value="{{ $p->id }}"
                                           style="width: 16px; height: 16px; accent-color: #10b981; cursor: pointer;" />
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 700; font-size: 14px;">{{ $p->mahasiswa->nama_lengkap }}</div>
                                    <div style="color: rgba(52,211,153,0.5); font-family: monospace; font-size: 12px; margin-top: 3px; font-weight: 600;">{{ $p->mahasiswa->npm }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 600; font-size: 13px;">Semester {{ $p->semester }}</div>
                                    <div style="color: rgba(255,255,255,0.3); font-size: 11px; margin-top: 2px;">TA {{ $p->tahun_ajaran }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: rgba(255,255,255,0.9); font-weight: 800; font-size: 13px;">{{ $p->jenis_tagihan ?? '-' }}</div>
                                    <div style="color: rgba(255,255,255,0.35); font-size: 11px; margin-top: 2px;">{{ $p->catatan ?? '-' }}</div>
                                </td>
                                <td style="padding: 20px 25px;">
                                    <div style="color: white; font-weight: 700; font-size: 13px;">{{ $p->mahasiswa->angkatan ?? '-' }}</div>
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
                                <td colspan="9" style="padding: 60px 0; text-align: center;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 3rem; color: rgba(255,255,255,0.05); display: block; margin-bottom: 15px;"></i>
                                    <span style="color: rgba(255,255,255,0.2); font-size: 14px; font-weight: 500;">Belum ada data pembayaran yang tercatat.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </form>

        @if($pembayarans->hasPages())
            <div style="margin-top: 15px;">
                <div style="display:flex; align-items:center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div>
                        {{ $pembayarans->links() }}
                    </div>
                    @php
                        $baseQuery = request()->except(['page', 'per_page']);
                        $isAll = (string) ($per_page ?? '10') === 'all';
                    @endphp
                    @if (! $isAll)
                        <a href="{{ route('keuangan.pembayaran.index', array_merge($baseQuery, ['per_page' => 'all'])) }}"
                           style="text-decoration:none; height: 40px; padding: 0 14px; background-color: rgba(255,255,255,0.05); color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-weight: 800; font-size: 12px; display:inline-flex; align-items:center; gap: 8px;">
                            <i class="fa-solid fa-expand"></i>
                            Buka Semua
                        </a>
                    @else
                        <a href="{{ route('keuangan.pembayaran.index', array_merge($baseQuery, ['per_page' => '10'])) }}"
                           style="text-decoration:none; height: 40px; padding: 0 14px; background-color: rgba(255,255,255,0.05); color: white; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-weight: 800; font-size: 12px; display:inline-flex; align-items:center; gap: 8px;">
                            <i class="fa-solid fa-compress"></i>
                            Per Halaman
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        @media print {
            aside, nav, .no-print, button, a { display: none !important; }
            body { background: white !important; }
            table { min-width: 0 !important; }
        }
    </style>

    <script>
        (function () {
            const selectAll = document.getElementById('selectAllPayments');
            const checks = Array.from(document.querySelectorAll('.payment-check'));
            const btn = document.getElementById('bulkDeleteBtn');

            if (!btn || checks.length === 0) {
                if (selectAll) selectAll.disabled = true;
                return;
            }

            const update = () => {
                const checked = checks.filter((c) => c.checked).length;
                btn.textContent = `Hapus Terpilih (${checked})`;
                btn.disabled = checked === 0;
                btn.style.opacity = checked === 0 ? '.55' : '1';
                btn.style.cursor = checked === 0 ? 'not-allowed' : 'pointer';

                if (selectAll) {
                    selectAll.checked = checked === checks.length;
                    selectAll.indeterminate = checked > 0 && checked < checks.length;
                }
            };

            checks.forEach((c) => c.addEventListener('change', update));

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    checks.forEach((c) => { c.checked = selectAll.checked; });
                    update();
                });
            }

            update();
        })();
    </script>
</x-portal-layout>
