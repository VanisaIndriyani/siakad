<x-portal-layout :title="'Revisi Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Revisi Skripsi</div>
            <div class="mt-1 text-sm text-emerald-100/70">{{ $skripsi->judul }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('mahasiswa.skripsi.revisi.pdf', $skripsi) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-print"></i>
                Print
            </a>
            <a href="{{ route('mahasiswa.skripsi.bimbingan', $skripsi) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3 w-44">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3 w-52">Dari</th>
                        <th class="text-left font-medium px-4 py-3">Revisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($skripsi->revisis->sortByDesc('id') as $i => $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">{{ $i + 1 }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ ($row->tanggal ?: $row->created_at)?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->creator?->name ?: 'User' }}</td>
                            <td class="px-4 py-3 text-emerald-100/90 whitespace-pre-line">{{ $row->revisi }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada revisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-portal-layout>

