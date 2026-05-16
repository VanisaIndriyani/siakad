<x-portal-layout :title="'Detail PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    @php
        $badge = match ($ppl->status) {
            'assigned' => ['bg' => 'rgba(16,185,129,0.12)', 'bd' => 'rgba(16,185,129,0.25)', 'tx' => '#065f46'],
            'approved' => ['bg' => 'rgba(59,130,246,0.12)', 'bd' => 'rgba(59,130,246,0.25)', 'tx' => '#1e40af'],
            'rejected' => ['bg' => 'rgba(239,68,68,0.12)', 'bd' => 'rgba(239,68,68,0.25)', 'tx' => '#7f1d1d'],
            default => ['bg' => 'rgba(245,158,11,0.14)', 'bd' => 'rgba(245,158,11,0.28)', 'tx' => '#78350f'],
        };
    @endphp

    <style>
        .detail-wrap { max-width: 920px; margin: 0 auto; }
        .detail-card { background: #ffffff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 18px; padding: 16px; color: #111827; }
        .detail-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .detail-title { font-size: 20px; font-weight: 900; margin: 0; }
        .detail-sub { margin-top: 6px; font-size: 13px; font-weight: 700; color: rgba(17, 24, 39, 0.55); }
        .pill { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 999px; border: 1px solid; font-weight: 900; font-size: 12px; letter-spacing: 0.6px; }
        .btn { height: 40px; padding: 0 14px; border-radius: 999px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #111827; font-weight: 900; font-size: 13px; }
        .btn-primary { border-color: rgba(16, 185, 129, 0.25); background: linear-gradient(to right, #059669, #10b981); color: #fff; }
        .grid2 { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 12px; }
        @media (min-width: 768px) { .grid2 { grid-template-columns: 1.4fr 1fr; } }
        .box { background: #ffffff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 16px; padding: 14px; }
        .label { font-size: 12px; font-weight: 900; color: rgba(17, 24, 39, 0.55); text-transform: uppercase; letter-spacing: 0.8px; }
        .value { margin-top: 6px; font-size: 14px; font-weight: 900; color: #111827; }
        .text { margin-top: 10px; font-size: 13px; font-weight: 700; color: rgba(17, 24, 39, 0.80); white-space: pre-line; }
        .note { border-radius: 16px; padding: 14px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; }
        .note.warn { border-color: rgba(245, 158, 11, 0.28); background: rgba(245, 158, 11, 0.10); }
        .note.danger { border-color: rgba(239, 68, 68, 0.25); background: rgba(239, 68, 68, 0.10); }
        .note.success { border-color: rgba(16, 185, 129, 0.25); background: rgba(16, 185, 129, 0.10); }
    </style>

    <div class="detail-wrap">
        <div class="detail-card">
            <div class="detail-head">
                <div>
                    <div class="detail-title">Detail PPL</div>
                    <div class="detail-sub">Pantau status, pembimbing, dan SK.</div>
                </div>
                <div style="display:flex; align-items:center; gap: 10px; flex-wrap: wrap;">
                    <span class="pill" style="background: {{ $badge['bg'] }}; border-color: {{ $badge['bd'] }}; color: {{ $badge['tx'] }};">
                        {{ strtoupper($ppl->status) }}
                    </span>
                    @if ($ppl->dosen_pembimbing_id || $ppl->dosen_pembimbing_id_2)
                        <a href="{{ route('mahasiswa.ppl.bimbingan', $ppl) }}" class="btn btn-primary">
                            <i class="fa-solid fa-comments"></i>
                            Bimbingan
                        </a>
                    @endif
                    <a href="{{ route('mahasiswa.ppl.index') }}" class="btn">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="grid2">
                <div class="box">
                    <div class="label">Instansi / Sekolah</div>
                    <div class="value">{{ $ppl->instansi_nama }}</div>
                    @if ($ppl->instansi_alamat)
                        <div class="text">{{ $ppl->instansi_alamat }}</div>
                    @endif
                    @if ($ppl->keterangan)
                        <div class="text">{{ $ppl->keterangan }}</div>
                    @endif
                </div>

                <div class="box">
                    <div class="label">Info</div>
                    <div class="text" style="margin-top: 10px;">
                        <div style="display:flex; gap: 10px; align-items:flex-start;">
                            <div style="width: 140px; color: rgba(17, 24, 39, 0.60); font-weight: 900;">Pembimbing</div>
                            <div style="flex: 1; font-weight: 900;">
                                <div>{{ $ppl->dosenPembimbing?->nama ?: '-' }}</div>
                                @if ($ppl->dosenPembimbing2?->nama)
                                    <div style="margin-top: 6px;">{{ $ppl->dosenPembimbing2?->nama }}</div>
                                @endif
                            </div>
                        </div>
                        <div style="display:flex; gap: 10px; align-items:flex-start; margin-top: 8px;">
                            <div style="width: 140px; color: rgba(17, 24, 39, 0.60); font-weight: 900;">Nomor SK</div>
                            <div style="flex: 1; font-weight: 900;">{{ $ppl->nomor_sk ?: '-' }}</div>
                        </div>
                        <div style="display:flex; gap: 10px; align-items:flex-start; margin-top: 8px;">
                            <div style="width: 140px; color: rgba(17, 24, 39, 0.60); font-weight: 900;">Tanggal SK</div>
                            <div style="flex: 1; font-weight: 900;">{{ $ppl->tanggal_sk ? $ppl->tanggal_sk->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div style="display:flex; gap: 10px; align-items:flex-start; margin-top: 8px;">
                            <div style="width: 140px; color: rgba(17, 24, 39, 0.60); font-weight: 900;">File SK</div>
                            <div style="flex: 1; font-weight: 900;">
                                @if ($ppl->sk_pembimbing_path)
                                    <a href="{{ route('mahasiswa.ppl.sk.preview', $ppl) }}" target="_blank" class="btn" style="height: 34px; padding: 0 12px; font-size: 12px;">
                                        <i class="fa-solid fa-eye"></i>
                                        Preview
                                    </a>
                                    <a href="{{ route('mahasiswa.ppl.sk.download', $ppl) }}" class="btn" style="height: 34px; padding: 0 12px; font-size: 12px;">
                                        <i class="fa-solid fa-download"></i>
                                        Download
                                    </a>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div style="display:flex; gap: 10px; align-items:flex-start; margin-top: 8px;">
                            <div style="width: 140px; color: rgba(17, 24, 39, 0.60); font-weight: 900;">Diajukan</div>
                            <div style="flex: 1; font-weight: 900;">{{ $ppl->created_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($ppl->catatan_admin)
                @php
                    $noteClass = match ($ppl->status) {
                        'rejected' => 'danger',
                        'assigned' => 'success',
                        default => 'warn',
                    };
                @endphp
                <div class="note {{ $noteClass }}" style="margin-top: 12px;">
                    <div class="label">Catatan Admin/Prodi</div>
                    <div class="text" style="margin-top: 8px;">{{ $ppl->catatan_admin }}</div>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>

