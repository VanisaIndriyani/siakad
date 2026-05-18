<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Profil Dosen - {{ $dosen?->nama }}</title>
    <style>
        @page { margin: 1.5cm; }
        * { font-family: 'Helvetica', 'Arial', sans-serif; box-sizing: border-box; }
        body { font-size: 10px; color: #111827; line-height: 1.4; }
        .kop-surat { width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 2px; }
        .kop-logo { width: 110px; text-align: left; vertical-align: middle; }
        .kop-logo img { width: 100px; height: auto; }
        .kop-text { text-align: center; vertical-align: middle; }
        .kop-title-1 { font-size: 16px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-title-2 { font-size: 22px; font-weight: 900; margin: 2px 0; line-height: 1; }
        .kop-title-3 { font-size: 16px; font-weight: 800; margin: 0; line-height: 1.1; }
        .kop-meta { font-size: 9px; font-weight: 700; margin-top: 3px; }
        .kop-alamat { font-size: 9px; margin: 2px 0; }
        .kop-line-2 { border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px; }
        .doc-title { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 6px 10px; border: 1px solid #000; vertical-align: top; }
        .label { width: 30%; background-color: #f3f4f6; font-weight: bold; }
        .value { width: 70%; }
        
        .photo-container { width: 120px; text-align: right; vertical-align: top; }
        .photo-box { width: 100px; height: 125px; border: 1px solid #000; padding: 2px; background: white; display: inline-block; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .section-title { background-color: #f3f4f6; font-weight: bold; padding: 5px 10px; border: 1px solid #000; border-bottom: none; margin-top: 15px; text-transform: uppercase; font-size: 11px; }
        .footer { margin-top: 30px; text-align: right; font-size: 9px; color: #6b7280; }
    </style>
</head>
<body>
    <table class="kop-surat">
        <tr>
            <td class="kop-logo">
                @php
                    $logoPath = public_path('img/lo.jpeg');
                    $logoBase64 = '';
                    if (file_exists($logoPath)) {
                        $logoData = file_get_contents($logoPath);
                        $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-title-1">INSTITUT AGAMA ISLAM</div>
                <div class="kop-title-2">DARUD DA'WAH WAL IRSYAD</div>
                <div class="kop-title-3">SIDENRENG RAPPANG</div>
                <div class="kop-meta">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                <div class="kop-alamat">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                <div class="kop-alamat">E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id</div>
            </td>
            <td style="width: 90px;"></td>
        </tr>
    </table>
    <div class="kop-line-2"></div>

    <div class="doc-title">PROFIL DOSEN</div>

    <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; border: none; padding: 0;">
                <table class="info-table">
                    <tr>
                        <td class="label">Nama Lengkap</td>
                        <td class="value">{{ $dosen?->nama ?? $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIDN</td>
                        <td class="value">{{ $dosen?->nidn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIK</td>
                        <td class="value">{{ $dosen?->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Program Studi</td>
                        <td class="value">{{ $dosen?->program_studi ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td class="photo-container" style="border: none; padding-left: 10px;">
                <div class="photo-box">
                    @if ($dosen?->foto_path && file_exists(storage_path('app/public/'.$dosen->foto_path)))
                        @php
                            $path = storage_path('app/public/'.$dosen->foto_path);
                            $type = pathinfo($path, PATHINFO_EXTENSION);
                            $data = file_get_contents($path);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        @endphp
                        <img src="{{ $base64 }}" alt="Foto" />
                    @else
                        <div style="width: 100%; height: 100%; background: #f3f4f6; color: #9ca3af; display: flex; align-items: center; justify-content: center; text-align: center; padding-top: 40px; font-size: 8px;">
                            No Photo
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">Data Personal & Akademik</div>
    <table class="info-table">
        <tr>
            <td class="label">Tempat / Tanggal Lahir</td>
            <td class="value">
                {{ $dosen?->tempat_lahir ?: '-' }} /
                {{ $dosen?->tanggal_lahir ? \Illuminate\Support\Carbon::parse($dosen->tanggal_lahir)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td class="value">{{ $dosen?->email ?? $user->email }}</td>
        </tr>
        <tr>
            <td class="label">Nomor Telp</td>
            <td class="value">{{ $dosen?->nomor_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="value">{{ $dosen?->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Dosen</td>
            <td class="value">{{ ($dosen?->status_dosen ?? 'aktif') === 'tidak aktif' ? 'Tidak Aktif' : 'Aktif' }}</td>
        </tr>
    </table>

    <div class="section-title">Data Kepegawaian</div>
    <table class="info-table">
        <tr>
            <td class="label">NUPTK</td>
            <td class="value">{{ $dosen?->nuptk ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIP</td>
            <td class="value">{{ $dosen?->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan Fungsional</td>
            <td class="value">{{ $dosen?->jabatan_fungsional ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Kepangkatan</td>
            <td class="value">{{ $dosen?->kepangkatan ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pendidikan Terakhir</td>
            <td class="value">{{ $dosen?->pendidikan_terakhir ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Rumpun Ilmu</td>
            <td class="value">{{ $dosen?->rumpun_ilmu ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Status Pegawai</td>
            <td class="value">{{ $dosen?->status_pegawai ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Ikatan Kerja</td>
            <td class="value">{{ $dosen?->ikatan_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Pengangkatan</td>
            <td class="value">{{ $dosen?->tanggal_pengangkatan ? \Illuminate\Support\Carbon::parse($dosen->tanggal_pengangkatan)->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nomor SK</td>
            <td class="value">{{ $dosen?->nomor_sk ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i') }} • Dokumen ini dihasilkan otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>

