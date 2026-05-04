<x-portal-layout :title="'Detail KRS - '.config('app.name')" subtitle="Detail KRS">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail KRS</div>
            <div class="text-sm text-emerald-100/70">Semester {{ $krs->semester }} • {{ $krs->tahun_ajaran ?? '-' }}</div>
        </div>
        <div class="flex items-center gap-2">
            @if ($krs->status_approval !== 'approved')
                <a href="{{ route('mahasiswa.krs.edit', $krs) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-pen"></i>
                    Edit
                </a>
            @endif
            <a href="{{ route('mahasiswa.krs.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
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

        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Kode</th>
                            <th class="text-left font-medium px-4 py-3">Nama</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($krs->items as $item)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-portal-layout>
