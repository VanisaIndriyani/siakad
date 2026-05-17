<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Profil Dosen - {{ $dosen?->nama }}</title>
    <style>
        @page {
            margin: 1cm;
        }
        * {
            font-family: 'Helvetica', 'Arial', sans-serif;
            box-sizing: border-box;
        }
        body {
            font-size: 11px;
            color: #1f2937;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #10b981;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #064e3b;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .label {
            width: 30%;
            background-color: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        .value {
            width: 70%;
            color: #111827;
        }
        .photo-container {
            width: 120px;
            text-align: right;
            vertical-align: top;
        }
        .photo-box {
            width: 100px;
            height: 125px;
            border: 1px solid #d1d5db;
            padding: 2px;
            background: white;
            display: inline-block;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #9ca3af;
        }
        .section-title {
            background-color: #ecfdf5;
            color: #065f46;
            font-weight: bold;
            padding: 5px 10px;
            border: 1px solid #e5e7eb;
            border-bottom: none;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="title">Profil Dosen</div>
                <div class="subtitle">SIAKAD {{ config('app.name') }}</div>
            </td>
            <td style="text-align: right; vertical-align: bottom;">
                <div style="font-size: 10px; color: #6b7280;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="vertical-align: top;">
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
            <td class="photo-container">
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
                        <div style="width: 100%; height: 100%; background: #f3f4f6; color: #9ca3af; display: flex; align-items: center; justify-content: center; text-align: center; padding-top: 40px;">
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
        Dokumen ini dihasilkan secara otomatis oleh Sistem Informasi Akademik.
    </div>
</body>
</html>

