<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            @page { margin: 16mm 14mm 16mm 17mm; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
            table { width: 100%; border-collapse: collapse; }
            .kop-surat { width: 100%; padding-bottom: 5px; }
            .kop-logo { width: 130px; text-align: left; vertical-align: middle; padding-top: 2px; }
            .kop-logo img { width: 125px; height: auto; }
            .kop-text { text-align: center; vertical-align: middle; }
            .kop-title-1 { color: #000; font-size: 20px; font-weight: 800; margin: 0; line-height: 1.12; }
            .kop-title-2 { color: #000; font-size: 28px; font-weight: 900; margin: 1px 0 0; letter-spacing: 0.4px; line-height: 1.06; }
            .kop-title-3 { color: #000; font-size: 20px; font-weight: 900; margin: 1px 0 0; line-height: 1.12; }
            .kop-meta { color: #000; font-size: 12px; margin-top: 3px; line-height: 1.2; }
            .kop-line-1 { border-top: 4px solid #000; margin-top: 7px; }
            .kop-line-2 { border-top: 2px solid #000; margin-top: 3px; }
            
            .doc-title { text-align: center; font-size: 14px; font-weight: 900; margin: 14px 0 10px; text-transform: uppercase; }
            .info-table { width: 100%; margin-bottom: 20px; }
            .info-table td { padding: 3px 0; vertical-align: top; }
            .info-table .label { width: 120px; font-weight: bold; }
            
            .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .data-table th { background: #f8fafc; color: #111827; font-weight: 700; text-align: center; padding: 8px; border: 1px solid #000; }
            .data-table td { padding: 8px; border: 1px solid #000; vertical-align: top; }
            .footer { margin-top: 20px; font-size: 10px; color: #64748b; text-align: right; }
        </style>
    </head>
    <body>
        @php
            $logoPath = public_path('img/lo.jpeg');
            $logoBase64 = null;
            if (is_string($logoPath) && file_exists($logoPath)) {
                $data = file_get_contents($logoPath);
                $logoBase64 = 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode($data);
            }

            $kop1 = 'INSTITUT AGAMA ISLAM';
            $kop2 = "DARUD DA'WAH WAL IRSYAD";
            $kop3 = 'SIDENRENG RAPPANG';
            $kop4 = 'TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021';
            $kop5 = 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang';
            $kop6 = 'E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id';
        @endphp

        <table class="kop-surat">
            <tr>
                <td class="kop-logo">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" />
                    @endif
                </td>
                <td class="kop-text">
                    <div class="kop-title-1">{{ $kop1 }}</div>
                    <div class="kop-title-2">{{ $kop2 }}</div>
                    <div class="kop-title-3">{{ $kop3 }}</div>
                    <div class="kop-meta" style="font-weight: 700;">{{ $kop4 }}</div>
                    <div class="kop-meta">{{ $kop5 }}</div>
                    <div class="kop-meta">{{ $kop6 }}</div>
                </td>
                <td style="width: 90px;"></td>
            </tr>
        </table>
        <div class="kop-line-1"></div>
        <div class="kop-line-2"></div>

        <div class="doc-title">Bimbingan PPL</div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Mahasiswa</td>
                <td>: {{ $ppl->mahasiswa?->nama_lengkap ?: '-' }} ({{ $ppl->mahasiswa?->npm ?: '-' }})</td>
            </tr>
            <tr>
                <td class="label">Instansi/Sekolah</td>
                <td>: {{ $ppl->instansi_nama }}</td>
            </tr>
            <tr>
                <td class="label">Pembimbing 1</td>
                <td>: {{ $ppl->dosenPembimbing?->nama ?: '-' }}</td>
            </tr>
            @if($ppl->dosenPembimbing2)
            <tr>
                <td class="label">Pembimbing 2</td>
                <td>: {{ $ppl->dosenPembimbing2?->nama }}</td>
            </tr>
            @endif
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th style="width: 150px;">Dari</th>
                    <th>Pesan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $i => $msg)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td style="text-align: center;">{{ $msg->created_at?->format('d/m/Y H:i') ?: '-' }}</td>
                        <td>{{ $msg->sender?->name ?: 'User' }}</td>
                        <td style="white-space: pre-line;">{{ $msg->pesan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">Belum ada pesan bimbingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan otomatis oleh Sistem Informasi Akademik.</div>
    </body>
</html>

