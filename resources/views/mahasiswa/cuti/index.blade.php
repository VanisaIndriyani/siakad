<x-portal-layout :title="'Pengajuan Cuti - '.config('app.name')" subtitle="Cuti Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Pengajuan Cuti</div>
            <div class="text-sm text-emerald-100/70">Ajukan permohonan cuti akademik Anda di sini.</div>
        </div>
        <a href="{{ route('mahasiswa.cuti.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Buat Pengajuan</span>
        </a>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Tahun Ajaran</th>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Alasan</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($cuti as $row)
                        @php
                            $badge = match ($row->status) {
                                'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                            };
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->tahun_ajaran }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">Semester {{ $row->semester }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div class="max-w-[300px] truncate" title="{{ $row->alasan }}">
                                    {{ $row->alasan }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs {{ $badge }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('mahasiswa.cuti.show', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        Detail
                                    </a>
                                    @if ($row->status === 'approved')
                                        <a href="{{ route('mahasiswa.cuti.pdf', $row) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition text-emerald-400">
                                            <i class="fa-solid fa-file-pdf"></i>
                                            Cetak
                                        </a>
                                    @endif
                                    @if ($row->status === 'pending')
                                        <form method="POST" action="{{ route('mahasiswa.cuti.destroy', $row) }}" onsubmit="return confirm('Hapus pengajuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-9 px-3 inline-flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition text-red-400">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada pengajuan cuti.</td>
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
