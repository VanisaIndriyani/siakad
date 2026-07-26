<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mata Kuliah Dosen</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .subtitle { text-align: center; font-size: 12px; margin-bottom: 18px; color: #4b5563; }
        .meta { margin-bottom: 14px; }
        .meta div { margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; }
        .muted { color: #6b7280; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <div class="title">Daftar Mata Kuliah Dosen</div>
    <div class="subtitle">{{ config('app.name') }}</div>

    <div class="meta">
        <div><strong>Nama Dosen:</strong> {{ $dosen->nama ?? '-' }}</div>
        <div><strong>NUPTK/NIDN:</strong> {{ $dosen->nuptk ?: ($dosen->nidn ?? '-') }}</div>
        <div><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 44px;">No</th>
                <th style="width: 90px;">Kode</th>
                <th>Mata Kuliah</th>
                <th style="width: 170px;">Jurusan</th>
                <th style="width: 70px;">Semester</th>
                <th style="width: 55px;">SKS</th>
                <th style="width: 120px;">RPS Admin</th>
                <th style="width: 120px;">RPS Dosen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->kode }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->jurusan }}</td>
                    <td class="center">S{{ $item->semester }}</td>
                    <td class="center">{{ $item->sks }}</td>
                    <td class="center">{{ $item->rps_admin_path ? 'Ada' : '-' }}</td>
                    <td class="center">{{ $item->rps_dosen_path ? 'Ada' : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center muted">Belum ada data mata kuliah.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
