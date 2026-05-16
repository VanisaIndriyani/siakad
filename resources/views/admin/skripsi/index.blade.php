<x-portal-layout :title="'Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Skripsi</div>
            <div class="text-sm text-emerald-100/70">Review pengajuan judul dan tetapkan pembimbing.</div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('admin.skripsi.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari judul / nama / NPM..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <select name="status" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Semua Status</option>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'assigned' => 'Assigned'] as $k => $v)
                    <option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('admin.skripsi.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-left font-medium px-4 py-3">Pembimbing</th>
                        <th class="text-right font-medium px-4 py-3 w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $row)
                        @php
                            $badge = match ($row->status) {
                                'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
                                'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                            };
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/90">{{ $row->judul }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->dosenPembimbing?->nama_lengkap ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.skripsi.show', $row) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                    <i class="fa-solid fa-eye"></i>
                                    <span class="text-sm font-medium">Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada pengajuan skripsi.</td>
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

