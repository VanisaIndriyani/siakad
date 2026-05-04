<x-portal-layout :title="'Daftar Mahasiswa - '.config('app.name')" subtitle="Daftar Mahasiswa">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Daftar Mahasiswa</div>
            <div class="text-sm text-emerald-100/70">Lihat data mahasiswa.</div>
        </div>
    </div>

    <form method="GET" class="mt-5 flex flex-col sm:flex-row gap-3">
        <input name="q" value="{{ $q }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari nama / NPM / prodi / angkatan..." />
        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
        <a href="{{ route('dosen.mahasiswa.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
    </form>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Nama</th>
                        <th class="text-left font-medium px-4 py-3">NPM</th>
                        <th class="text-left font-medium px-4 py-3">Angkatan</th>
                        <th class="text-left font-medium px-4 py-3">Prodi</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($mahasiswa as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->nama_lengkap }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->npm }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->angkatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->program_studi ?? '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->status_mahasiswa }}</td>
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
        {{ $mahasiswa->links() }}
    </div>
</x-portal-layout>
