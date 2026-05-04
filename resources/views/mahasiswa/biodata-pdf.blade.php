<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
            h1 { font-size: 18px; margin: 0 0 10px 0; }
            .muted { color: #6b7280; font-size: 11px; }
            .card { border: 1px solid #e5e7eb; padding: 14px; border-radius: 10px; }
            .row { display: table; width: 100%; }
            .col { display: table-cell; vertical-align: top; }
            .col-right { width: 120px; }
            .avatar { width: 110px; height: 110px; border: 1px solid #e5e7eb; border-radius: 12px; object-fit: cover; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            td { padding: 6px 0; border-bottom: 1px solid #f3f4f6; }
            td.label { width: 180px; color: #6b7280; }
        </style>
    </head>
    <body>
        @php
            $fotoUri = null;
            if ($mahasiswa->foto_path) {
                $file = public_path('storage/'.$mahasiswa->foto_path);
                $fotoUri = 'file:///' . str_replace('\\', '/', $file);
            }
        @endphp

        <h1>Biodata Mahasiswa</h1>
        <div class="muted">{{ config('app.name') }} • Sistem Informasi Akademik</div>

        <div class="card" style="margin-top: 14px;">
            <div class="row">
                <div class="col">
                    <div style="font-size: 16px; font-weight: 700;">{{ $mahasiswa->nama_lengkap }}</div>
                    <div class="muted">{{ $user->email }}</div>
                </div>
                <div class="col col-right" style="text-align: right;">
                    @if ($fotoUri)
                        <img src="{{ $fotoUri }}" class="avatar" alt="Foto" />
                    @endif
                </div>
            </div>

            <table>
                <tr>
                    <td class="label">NPM</td>
                    <td>{{ $mahasiswa->npm }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td>{{ $mahasiswa->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Tempat, Tgl Lahir</td>
                    <td>
                        {{ $mahasiswa->tempat_lahir ?? '-' }}
                        @if ($mahasiswa->tanggal_lahir) - {{ $mahasiswa->tanggal_lahir->format('d/m/Y') }} @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Nomor Telp</td>
                    <td>{{ $mahasiswa->nomor_telp ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Angkatan</td>
                    <td>{{ $mahasiswa->angkatan ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Program Studi</td>
                    <td>{{ $mahasiswa->program_studi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Asal Sekolah</td>
                    <td>{{ $mahasiswa->asal_sekolah ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Status Mahasiswa</td>
                    <td>{{ $mahasiswa->status_mahasiswa }}</td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td>{{ $mahasiswa->alamat ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </body>
</html>
