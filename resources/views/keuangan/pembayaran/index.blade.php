<x-portal-layout :title="'Pembayaran - '.config('app.name')" subtitle="Manajemen Pembayaran">
    <x-slot:sidebar>
        @include('keuangan.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold text-white">Pembayaran</div>
            <div class="text-sm text-emerald-100/70">Kelola pembayaran semester mahasiswa dan cicilan.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.pembayaran.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-plus"></i>
                <span class="text-sm font-medium">Input Pembayaran</span>
            </a>
        </div>
    </div>

    <div class="mt-5">
        <form action="{{ route('keuangan.pembayaran.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 mb-4">
            <div class="relative w-full sm:max-w-md">
                <input type="text"
                       name="q"
                       value="{{ $q }}"
                       class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-white/40 focus:ring-emerald-400 focus:border-emerald-400"
                       placeholder="Cari Nama / NPM..." />
                <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
            </div>
            <button type="submit" class="h-11 px-6 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 transition text-white">
                Cari
            </button>
            @if($q)
                <a href="{{ route('keuangan.pembayaran.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white/60">
                    Reset
                </a>
            @endif
        </form>

        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-white">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-left font-medium px-4 py-3">Semester</th>
                            <th class="text-left font-medium px-4 py-3">Total Biaya</th>
                            <th class="text-left font-medium px-4 py-3">Dibayar</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-right font-medium px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($pembayarans as $p)
                            <tr class="hover:bg-white/5 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $p->mahasiswa->nama_lengkap }}</div>
                                    <div class="text-xs text-emerald-100/50">{{ $p->mahasiswa->npm }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $p->semester }} ({{ $p->tahun_ajaran }})</td>
                                <td class="px-4 py-3 font-semibold text-emerald-400">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($p->total_dibayar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $badge = match($p->status_pembayaran) {
                                            'Lunas' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20',
                                            'Cicil' => 'bg-amber-500/20 text-amber-400 border-amber-500/20',
                                            default => 'bg-red-500/20 text-red-400 border-red-500/20'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $badge }}">
                                        {{ strtoupper($p->status_pembayaran) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('keuangan.pembayaran.show', $p) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-circle-info"></i>
                                            Detail & Cicilan
                                        </a>
                                        <form action="{{ route('keuangan.pembayaran.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data pembayaran ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition text-red-400">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-emerald-100/50">Data pembayaran tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $pembayarans->links() }}
        </div>
    </div>
</x-portal-layout>
