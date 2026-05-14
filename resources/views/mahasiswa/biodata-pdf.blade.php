<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; line-height: 1.5; }
            .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 10px; margin-bottom: 20px; }
            .header h1 { font-size: 20px; margin: 0; text-transform: uppercase; color: #065f46; }
            .header p { margin: 5px 0 0 0; color: #6b7280; font-size: 12px; }
            
            .section-title { background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 5px 10px; font-weight: bold; font-size: 12px; color: #065f46; margin: 20px 0 10px 0; text-transform: uppercase; }
            
            .info-table { width: 100%; border-collapse: collapse; }
            .info-table td { padding: 6px 4px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
            .label { width: 35%; color: #4b5563; font-weight: 500; }
            .value { width: 65%; color: #111827; font-weight: bold; }
            
            .photo-container { position: absolute; top: 100px; right: 0; width: 120px; text-align: center; }
            .photo { width: 100px; height: 130px; border: 2px solid #e5e7eb; padding: 2px; object-fit: cover; }
            
            .footer { margin-top: 50px; text-align: right; }
            .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
            
            .page-break { page-break-after: always; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>BIODATA MAHASISWA (PD-DIKTI)</h1>
            <p>{{ config('app.name') }} - SISTEM INFORMASI AKADEMIK</p>
        </div>

        @php
            $base64Foto = null;
            if ($mahasiswa->foto_path) {
                $path = public_path('storage/' . $mahasiswa->foto_path);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64Foto = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
        @endphp

        <div class="photo-container">
            @if ($base64Foto)
                <img src="{{ $base64Foto }}" class="photo" />
            @else
                <div style="width: 100px; height: 130px; border: 1px dashed #ccc; line-height: 130px; color: #ccc; text-align: center; font-size: 10px;">PAS FOTO</div>
            @endif
        </div>

        <div class="section-title">A. IDENTITAS PRIBADI</div>
        <table class="info-table">
            <tr><td class="label">Nama Lengkap</td><td class="value">{{ $mahasiswa->nama_lengkap }}</td></tr>
            <tr><td class="label">NPM</td><td class="value">{{ $mahasiswa->npm }}</td></tr>
            <tr><td class="label">NIK</td><td class="value">{{ $mahasiswa->nik ?? '-' }}</td></tr>
            <tr><td class="label">NISN</td><td class="value">{{ $mahasiswa->nisn ?? '-' }}</td></tr>
            <tr><td class="label">NPWP</td><td class="value">{{ $mahasiswa->npwp ?? '-' }}</td></tr>
            <tr><td class="label">Tempat Lahir</td><td class="value">{{ $mahasiswa->tempat_lahir ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Lahir</td><td class="value">{{ $mahasiswa->tanggal_lahir ? $mahasiswa->tanggal_lahir->format('d F Y') : '-' }}</td></tr>
            <tr><td class="label">Jenis Kelamin</td><td class="value">{{ $mahasiswa->jenis_kelamin ?? '-' }}</td></tr>
            <tr><td class="label">Agama</td><td class="value">{{ $mahasiswa->agama ?? '-' }}</td></tr>
            <tr><td class="label">Kewarganegaraan</td><td class="value">{{ $mahasiswa->kewarganegaraan ?? '-' }}</td></tr>
            <tr><td class="label">Email</td><td class="value">{{ $user->email }}</td></tr>
            <tr><td class="label">Nomor Telp/HP</td><td class="value">{{ $mahasiswa->nomor_telp ?? '-' }}</td></tr>
        </table>

        <div class="section-title">B. ALAMAT DOMISILI</div>
        <table class="info-table">
            <tr><td class="label">Alamat Lengkap</td><td class="value">{{ $mahasiswa->jalan ?? '-' }}</td></tr>
            <tr><td class="label">Dusun / RT / RW</td><td class="value">{{ $mahasiswa->dusun ?? '-' }} / RT: {{ $mahasiswa->rt ?? '-' }} / RW: {{ $mahasiswa->rw ?? '-' }}</td></tr>
            <tr><td class="label">Kelurahan</td><td class="value">{{ $mahasiswa->kelurahan ?? '-' }}</td></tr>
            <tr><td class="label">Kecamatan</td><td class="value">{{ $mahasiswa->kecamatan ?? '-' }}</td></tr>
            <tr><td class="label">Kode Pos</td><td class="value">{{ $mahasiswa->kode_pos ?? '-' }}</td></tr>
            <tr><td class="label">Jenis Tinggal</td><td class="value">{{ $mahasiswa->jenis_tinggal ?? '-' }}</td></tr>
            <tr><td class="label">Alat Transportasi</td><td class="value">{{ $mahasiswa->alat_transportasi ?? '-' }}</td></tr>
        </table>

        <div class="section-title">C. STATUS SOSIAL</div>
        <table class="info-table">
            <tr><td class="label">Penerima KPS</td><td class="value">{{ $mahasiswa->penerima_kps ?? 'Tidak' }}</td></tr>
            <tr><td class="label">Nomor KPS</td><td class="value">{{ $mahasiswa->no_kps ?? '-' }}</td></tr>
        </table>

        <div class="page-break"></div>

        <div class="section-title">D. DATA ORANG TUA (AYAH)</div>
        <table class="info-table">
            <tr><td class="label">NIK Ayah</td><td class="value">{{ $mahasiswa->ayah_nik ?? '-' }}</td></tr>
            <tr><td class="label">Nama Ayah</td><td class="value">{{ $mahasiswa->ayah_nama ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Lahir Ayah</td><td class="value">{{ $mahasiswa->ayah_tanggal_lahir ? $mahasiswa->ayah_tanggal_lahir->format('d F Y') : '-' }}</td></tr>
            <tr><td class="label">Pendidikan Ayah</td><td class="value">{{ $mahasiswa->ayah_pendidikan ?? '-' }}</td></tr>
            <tr><td class="label">Pekerjaan Ayah</td><td class="value">{{ $mahasiswa->ayah_pekerjaan ?? '-' }}</td></tr>
            <tr><td class="label">Penghasilan Ayah</td><td class="value">{{ $mahasiswa->ayah_penghasilan ?? '-' }}</td></tr>
        </table>

        <div class="section-title">E. DATA ORANG TUA (IBU)</div>
        <table class="info-table">
            <tr><td class="label">NIK Ibu</td><td class="value">{{ $mahasiswa->ibu_nik ?? '-' }}</td></tr>
            <tr><td class="label">Nama Ibu Kandung</td><td class="value">{{ $mahasiswa->ibu_nama ?? '-' }}</td></tr>
            <tr><td class="label">Tanggal Lahir Ibu</td><td class="value">{{ $mahasiswa->ibu_tanggal_lahir ? $mahasiswa->ibu_tanggal_lahir->format('d F Y') : '-' }}</td></tr>
            <tr><td class="label">Pendidikan Ibu</td><td class="value">{{ $mahasiswa->ibu_pendidikan ?? '-' }}</td></tr>
            <tr><td class="label">Pekerjaan Ibu</td><td class="value">{{ $mahasiswa->ibu_pekerjaan ?? '-' }}</td></tr>
            <tr><td class="label">Penghasilan Ibu</td><td class="value">{{ $mahasiswa->ibu_penghasilan ?? '-' }}</td></tr>
        </table>

        <div class="section-title">F. DATA AKADEMIK</div>
        <table class="info-table">
            <tr><td class="label">Program Studi</td><td class="value">{{ $mahasiswa->program_studi ?? '-' }}</td></tr>
            <tr><td class="label">Angkatan</td><td class="value">{{ $mahasiswa->angkatan ?? '-' }}</td></tr>
            <tr><td class="label">Status Mahasiswa</td><td class="value">{{ $mahasiswa->status_mahasiswa ?? 'Aktif' }}</td></tr>
        </table>

        <div class="footer">
            <p>Dicetak pada: {{ date('d F Y H:i:s') }}</p>
            <p>Mahasiswa bersangkutan,</p>
            <div class="signature"></div>
            <p><strong>{{ $mahasiswa->nama_lengkap }}</strong></p>
        </div>
    </body>
</html>