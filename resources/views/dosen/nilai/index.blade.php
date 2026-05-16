<x-portal-layout :title="'Input Nilai - '.config('app.name')" subtitle="Input Nilai">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div>
        <div class="text-xl font-semibold">Input Nilai</div>
        <div class="text-sm text-emerald-100/70">Pilih mata kuliah yang kamu ampu, lalu input nilai per mahasiswa.</div>
    </div>

    <form method="GET" class="mt-5 flex flex-col sm:flex-row gap-3">
        <input name="q" value="{{ $q }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode / nama mata kuliah..." />
        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
        <a href="{{ route('dosen.nilai.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                        <th class="text-left font-medium px-4 py-3">Jurusan</th>
                        <th class="text-left font-medium px-4 py-3">Semester</th>
                        <th class="text-left font-medium px-4 py-3">Peserta</th>
                        <th class="text-right font-medium px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($mataKuliah as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->kode }} - {{ $row->nama }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->jurusan }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->semester }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->peserta_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('dosen.nilai.edit', [$row, $row->semester]) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Input
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Tidak ada mata kuliah yang kamu ampu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $mataKuliah->links() }}
    </div>
</x-portal-layout>
