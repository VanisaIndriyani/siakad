<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            @page { margin: 18px 18px 22px 18px; }
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
            .header { border: 1px solid #e5e7eb; padding: 12px 14px; border-radius: 10px; }
            .title { font-size: 14px; font-weight: 700; margin: 0; }
            .meta { margin-top: 6px; font-size: 10px; color: #334155; }
            .meta-row { margin-top: 3px; }
            .muted { color: #64748b; }
            .card { margin-top: 12px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
            table { width: 100%; border-collapse: collapse; }
            thead th { background: #f1f5f9; color: #0f172a; font-weight: 700; text-align: left; padding: 7px 8px; border-bottom: 1px solid #e5e7eb; }
            tbody td { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
            tbody tr:nth-child(even) td { background: #fafafa; }
            .w-no { width: 34px; }
            .w-date { width: 120px; }
            .w-by { width: 150px; }
            .footer { margin-top: 10px; font-size: 10px; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="title">Revisi Skripsi</div>
            <div class="meta">
                <div class="meta-row"><span class="muted">Mahasiswa:</span> {{ $skripsi->mahasiswa?->nama_lengkap ?: '-' }} ({{ $skripsi->mahasiswa?->npm ?: '-' }})</div>
                <div class="meta-row"><span class="muted">Judul:</span> {{ $skripsi->judul }}</div>
                <div class="meta-row"><span class="muted">Pembimbing:</span> {{ $skripsi->dosenPembimbing?->nama ?: '-' }}@if ($skripsi->dosenPembimbing2?->nama), {{ $skripsi->dosenPembimbing2?->nama }}@endif</div>
                <div class="meta-row"><span class="muted">Dicetak:</span> {{ now()->format('d/m/Y H:i') }}@if (!empty($printedBy)) • <span class="muted">Oleh:</span> {{ $printedBy }}@endif</div>
            </div>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th class="w-no">No</th>
                        <th class="w-date">Tanggal</th>
                        <th class="w-by">Dari</th>
                        <th>Revisi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($revisis as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $row->created_at?->format('d/m/Y H:i') ?: '-' }}</td>
                            <td>{{ $row->creator?->name ?: 'User' }}</td>
                            <td style="white-space: pre-line;">{{ $row->revisi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 16px; color: #64748b;">Belum ada revisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer">Dokumen ini dihasilkan otomatis oleh {{ config('app.name') }}.</div>
    </body>
</html>

