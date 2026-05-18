<x-portal-layout :title="'Kelola Cuti - '.config('app.name')" subtitle="Kelola Pengajuan Cuti">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Pengajuan Cuti (Prodi)</div>
            <div class="text-sm text-emerald-100/70">Pantau dan kelola status approval pengajuan cuti mahasiswa di prodi Anda.</div>
        </div>
    </div>

    <form method="GET" class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-3">
        <input name="q" value="{{ $q }}" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white" placeholder="Cari nama / NPM..." />
        <select name="status" class="h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400 text-white">
            <option value="" @selected($status === '') class="text-black">Semua Status</option>
            @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
                <option value="{{ $k }}" @selected($status === $k) class="text-black">{{ $v }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="flex-1 h-11 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white">Filter</button>
            <a href="{{ route('dosen.cuti.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white">Reset</a>
        </div>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">Tahun Ajaran</th>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($cuti as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">
                                <div class="font-medium text-white">{{ $row->mahasiswa?->nama_lengkap }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->tahun_ajaran }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->semester }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badge = match ($row->status) {
                                        'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                        'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                        default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('dosen.cuti.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white">
                                    <i class="fa-solid fa-eye"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $cuti->links() }}
    </div>
</x-portal-layout>
