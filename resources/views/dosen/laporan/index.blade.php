<x-portal-layout :title="'Laporan - '.config('app.name')" subtitle="Laporan">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Laporan Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Chat laporan untuk pengajuan yang belum di-approve.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('dosen.laporan.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari judul / nama / NPM..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <select name="status" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                @foreach (['open' => 'Open', 'closed' => 'Closed', '' => 'Semua'] as $k => $v)
                    <option value="{{ $k }}" @selected($status === $k) style="background-color: #0d2a23; color: #fff;">{{ $v }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('dosen.laporan.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
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
                            $jenisLabel = match($row->jenis) {
                                'skripsi' => 'Skripsi',
                                'ppl' => 'PPL',
                                'krs' => 'KRS',
                                default => strtoupper($row->jenis)
                            };
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $items->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->judul }}</div>
                                @if ($row->latestMessage?->pesan)
                                    <div class="text-xs text-emerald-100/60 mt-1 line-clamp-1">{{ $row->latestMessage->pesan }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $jenisLabel }} #{{ $row->pengajuan_id }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge }}">
                                    {{ $row->status === 'open' ? 'Open' : 'Closed' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/70">{{ $row->last_message_at?->format('d/m/Y H:i') ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    <a href="{{ route('dosen.laporan.show', $row) }}"
                                       class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-comments"></i>
                                        Buka
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-emerald-100/70">Belum ada laporan.</td>
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

