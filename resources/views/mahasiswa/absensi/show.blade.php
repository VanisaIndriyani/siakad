<x-portal-layout :title="'Detail Absensi - '.config('app.name')" subtitle="Detail Absensi">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail Absensi</div>
            <div class="text-sm text-emerald-100/70">{{ $mataKuliah->kode }} - {{ $mataKuliah->nama }} • Semester {{ $semester }}</div>
        </div>
        <a href="{{ route('mahasiswa.absensi.index', ['semester' => $semester]) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Pertemuan</th>
                        <th class="text-left font-medium px-4 py-3">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-left font-medium px-4 py-3">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $item)
                        @php
                            $status = $item->status;
                            $badge = match ($status) {
                                'hadir' => 'bg-emerald-500/15 border-emerald-400/25 text-emerald-100',
                                'izin' => 'bg-sky-500/15 border-sky-400/25 text-sky-100',
                                'sakit' => 'bg-amber-500/15 border-amber-400/25 text-amber-100',
                                'alpha' => 'bg-rose-500/15 border-rose-400/25 text-rose-100',
                                default => 'bg-white/5 border-white/10 text-emerald-100/70',
                            };
                            $label = match ($status) {
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpha',
                                default => '-',
                            };
                        @endphp
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">Pertemuan {{ $item->absensi->pertemuan }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $item->absensi->tanggal?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full border {{ $badge }}">{{ $label }}</span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada data absensi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-portal-layout>

