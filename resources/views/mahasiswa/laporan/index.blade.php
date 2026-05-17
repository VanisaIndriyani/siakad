<x-portal-layout :title="'Lapor Pengajuan - '.config('app.name')" subtitle="Laporan">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <div class="text-xl font-semibold">Lapor Pengajuan</div>
            <div class="text-sm text-emerald-100/70">Chat dengan Admin, Ketua Prodi, dan Sekretaris Prodi untuk pengajuan yang belum di-approve.</div>
        </div>
        <a href="{{ route('mahasiswa.laporan.create') }}"
           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
            <i class="fa-solid fa-plus"></i>
            Buat Laporan
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Jenis</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-left font-medium px-4 py-3 w-48">Terakhir</th>
                        <th class="text-right font-medium px-4 py-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $i => $row)
                        @php
                            $badge = $row->status === 'open'
                                ? 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100'
                                : 'bg-zinc-500/15 border-zinc-500/20 text-zinc-100';
                            $jenisLabel = $row->jenis === 'skripsi' ? 'Skripsi' : 'PPL';
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $items->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->judul }}</div>
                                @if ($row->latestMessage?->pesan)
                                    <div class="text-xs text-emerald-100/60 mt-1 line-clamp-1">{{ $row->latestMessage->pesan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $jenisLabel }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $row->status === 'open' ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/70">
                                {{ $row->last_message_at?->format('d/m/Y H:i') ?: '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('mahasiswa.laporan.show', $row) }}"
                                       class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-comments"></i>
                                        Buka
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-emerald-100/70">
                                Belum ada laporan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $items->links() }}
    </div>
</x-portal-layout>

