<x-portal-layout :title="'Detail KRS - '.config('app.name')" subtitle="Detail KRS">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail KRS</div>
            <div class="text-sm text-emerald-100/70">{{ $krs->mahasiswa?->nama_lengkap }} • Semester {{ $krs->semester }}</div>
        </div>
        <a href="{{ route('dosen.krs.approval') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="text-left font-medium px-4 py-3">Kode</th>
                            <th class="text-left font-medium px-4 py-3">Nama</th>
                            <th class="text-left font-medium px-4 py-3">SKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($krs->items as $item)
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                            </tr>
                        @endforeach
                        @if ($krs->items->count() === 0)
                            <tr>
                                <td colspan="3" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-end">
            <form method="POST" action="{{ route('dosen.krs.update', $krs) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status_approval" value="rejected" />
                <button class="h-11 px-6 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition font-medium">
                    Tolak
                </button>
            </form>
            <form method="POST" action="{{ route('dosen.krs.update', $krs) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status_approval" value="approved" />
                <button class="h-11 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Setujui
                </button>
            </form>
        </div>
    </div>
</x-portal-layout>
