<x-portal-layout :title="'Manajemen Posko KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Manajemen Posko KKN</div>
            <div class="text-sm text-emerald-100/70">Kelola posko, pembimbing, dan plotting mahasiswa.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.kkn.index') }}" class="h-10 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Pendaftaran
            </a>
            <a href="{{ route('admin.kkn.posko.create') }}" class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Tambah Posko
            </a>
        </div>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Nama Posko</th>
                        <th class="text-left font-medium px-4 py-3">Lokasi</th>
                        <th class="text-left font-medium px-4 py-3">Pembimbing (DPL)</th>
                        <th class="text-left font-medium px-4 py-3">Anggota</th>
                        <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($poskos as $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $row->nama_posko }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $row->lokasi ?: '-' }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div class="font-medium text-white">{{ $row->dosenPembimbing?->nama ?: '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->nomor_sk ?: 'SK belum ada' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-emerald-500/15 border border-emerald-500/20 px-2.5 py-0.5 text-xs font-semibold text-emerald-100">
                                    {{ $row->pengajuans->count() }} Mahasiswa
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kkn.posko.show', $row) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="text-sm font-medium">Detail</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kkn.posko.destroy', $row) }}" data-confirm="Hapus posko ini? Mahasiswa di dalamnya akan menjadi unassigned.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition text-red-100">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-emerald-100/70">Belum ada posko KKN.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $poskos->links() }}
    </div>
</x-portal-layout>
