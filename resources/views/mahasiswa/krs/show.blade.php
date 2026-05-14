<x-portal-layout :title="'Detail KRS - '.config('app.name')" subtitle="Detail KRS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            .lg\:pl-72 { padding-left: 0 !important; }
            main { padding: 0 !important; }
            body { background: #fff !important; color: #000 !important; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>

    @php
        $mahasiswa = auth()->user()->mahasiswa;
        $items = $krs->items->sortBy(fn ($item) => (string) ($item->mataKuliah?->kode ?? ''));
        $totalSks = $items->sum(fn ($item) => (int) ($item->mataKuliah?->sks ?? 0));
    @endphp

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Kartu Rencana Studi (KRS)</div>
            <div class="text-sm text-emerald-100/70">
                {{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}
                @if ($mahasiswa?->npm)
                    • {{ $mahasiswa->npm }}
                @endif
                • {{ $krs->tahun_ajaran ?? '-' }} • Semester {{ $krs->semester }}
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if ($krs->status_approval !== 'approved')
                <a href="{{ route('mahasiswa.krs.edit', $krs) }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-pen"></i>
                    Edit
                </a>
            @endif
            <button type="button" onclick="window.print()" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-print"></i>
                Cetak
            </button>
            <a href="{{ route('mahasiswa.krs.index') }}" class="no-print h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="flex flex-wrap items-center gap-3 text-sm">
            @php
                $badge = match ($krs->status_approval) {
                    'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                };
            @endphp
            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                {{ strtoupper($krs->status_approval) }}
            </span>
            @if ($krs->status_approval === 'approved')
                <span class="text-emerald-100/70">Disetujui oleh:</span>
                <span class="font-medium">Admin</span>
            @elseif ($krs->status_approval === 'rejected')
                <span class="text-emerald-100/70">Ditolak oleh:</span>
                <span class="font-medium">Admin</span>
            @else
                <span class="text-emerald-100/70">Menunggu verifikasi Admin</span>
            @endif
        </div>

        @if ($krs->catatan_approval)
            <div class="mt-4 p-4 rounded-xl bg-white/5 border border-white/10">
                <div class="text-xs font-semibold text-emerald-100/60 uppercase tracking-wider">Keterangan Admin:</div>
                <div class="mt-1 text-sm text-emerald-100/90 whitespace-pre-line">{{ $krs->catatan_approval }}</div>
            </div>
        @endif

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3 w-16">No</th>
                            <th class="text-left font-medium px-4 py-3">Kode Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                            <th class="text-left font-medium px-4 py-3">Semester</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($items as $item)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 text-emerald-100/80">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $krs->semester }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($items->count() > 0)
                        <tfoot class="bg-white/5">
                            <tr>
                                <td class="px-4 py-3 font-medium" colspan="3">Total SKS</td>
                                <td class="px-4 py-3 font-medium">{{ $totalSks }}</td>
                                <td class="px-4 py-3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-portal-layout>
