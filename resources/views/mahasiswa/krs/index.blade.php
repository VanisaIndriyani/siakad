<x-portal-layout :title="'KRS - '.config('app.name')" subtitle="KRS Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">KRS</div>
            <div class="text-sm text-emerald-100/70">Kelola KRS (buat / edit sebelum disetujui).</div>
        </div>
        <a href="{{ route('mahasiswa.krs.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Buat KRS</span>
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Tahun Akademik</th>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($krs as $row)
                        @php
                            $badge = match ($row->status_approval) {
                                'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                            };
                            $mkCodes = $row->items
                                ->map(fn ($i) => $i->mataKuliah?->kode)
                                ->filter()
                                ->sort()
                                ->values();
                            $mkPreview = $mkCodes->take(3)->implode(', ');
                            $mkMore = max(0, $mkCodes->count() - 3);
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->semester }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ trim((string) ($row->tahun_ajaran ?? '')) !== '' ? $row->tahun_ajaran : '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div class="font-medium text-white">{{ $row->items_count }} MK</div>
                                @if ($mkPreview !== '')
                                    <div class="mt-1 text-xs text-emerald-100/60">
                                        {{ $mkPreview }}@if ($mkMore > 0), +{{ $mkMore }} lagi @endif
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                                    {{ strtoupper($row->status_approval) }}
                                </span>
                                @if ($row->status_approval === 'rejected' && $row->catatan_approval)
                                    <div class="mt-1.5 text-[11px] text-red-200/80 italic max-w-[180px] leading-tight">
                                        Ket: {{ $row->catatan_approval }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('mahasiswa.krs.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>
                                    @if ($row->status_approval !== 'approved')
                                        <a href="{{ route('mahasiswa.krs.edit', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-pen"></i>
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada KRS.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $krs->links() }}
    </div>
</x-portal-layout>
