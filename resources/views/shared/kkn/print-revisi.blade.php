<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kontrol Bimbingan KKN - {{ $posko->nama_posko }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header h1 { font-size: 16pt; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 14pt; margin: 5px 0 0; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-table td { vertical-align: top; padding: 3px 0; }
        .info-table td:first-child { width: 150px; font-weight: bold; }
        .info-table td:nth-child(2) { width: 20px; text-align: center; }
        .revisi-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .revisi-table th, .revisi-table td { border: 1px solid #000; padding: 10px; text-align: left; }
        .revisi-table th { background-color: #f2f2f2; text-align: center; text-transform: uppercase; font-size: 10pt; }
        .footer { margin-top: 50px; width: 100%; }
        .footer table { width: 100%; }
        .footer td { width: 50%; text-align: center; }
        .signature-space { height: 80px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
            @page { margin: 2cm; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #10b981; color: white; border: none; border-radius: 5px; font-weight: bold;">CETAK LAPORAN</button>
    </div>

    <div class="header">
        <h1>SIAKAD IAI DDI SIDRAP</h1>
        <h2>Kartu Kontrol Bimbingan Kuliah Kerja Nyata (KKN)</h2>
    </div>

    <table class="info-table">
        <tr>
            <td>Nama Posko</td>
            <td>:</td>
            <td>{{ $posko->nama_posko }}</td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>:</td>
            <td>{{ $posko->lokasi ?: '-' }}</td>
        </tr>
        <tr>
            <td>DPL</td>
            <td>:</td>
            <td>
                @foreach ($posko->pembimbingS as $dpl)
                    {{ $loop->iteration }}. {{ $dpl->nama }}<br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Anggota Posko</td>
            <td>:</td>
            <td>
                @foreach ($posko->pengajuans as $p)
                    {{ $p->mahasiswa?->nama_lengkap }} ({{ $p->mahasiswa?->npm }}){{ !$loop->last ? ',' : '' }}
                @endforeach
            </td>
        </tr>
    </table>

    <table class="revisi-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="60%">Uraian Bimbingan / Revisi</th>
                <th width="20%">Paraf DPL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posko->revisis->sortBy('tanggal') as $rev)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td align="center">{{ $rev->tanggal->format('d/m/Y') }}</td>
                    <td>{!! nl2br(e($rev->uraian_revisi)) !!}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" align="center" style="padding: 30px; font-style: italic; color: #888;">Belum ada riwayat bimbingan/revisi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <table>
            <tr>
                <td>
                    Mengetahui,<br>
                    Ketua Program Studi {{ $posko->pengajuans->first()?->mahasiswa?->program_studi ?? '................' }}
                    <div class="signature-space"></div>
                    ( ........................................... )<br>
                    NUPTK. .....................................
                </td>
                <td>
                    Sidrap, {{ now()->translatedFormat('d F Y') }}<br>
                    Dosen Pembimbing Lapangan
                    <div class="signature-space"></div>
                    <strong>( {{ $posko->pembimbingS->first()?->nama ?: '...........................................' }} )</strong><br>
                    NUPTK. {{ $posko->pembimbingS->first()?->nidn ?: '.....................................' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
