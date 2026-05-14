<x-portal-layout :title="'Detail KRS - '.config('app.name')" subtitle="Detail KRS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail KRS</div>
            <div class="text-sm text-emerald-100/70">{{ $krs->mahasiswa?->nama_lengkap }} • Semester {{ $krs->semester }}</div>
        </div>
        <a href="{{ route('admin.krs.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Mata Kuliah</div>
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-4 py-3">Kode</th>
                                <th class="text-left font-medium px-4 py-3">Nama</th>
                                <th class="text-left font-medium px-4 py-3">SKS</th>
                                <th class="text-left font-medium px-4 py-3">Semester</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @foreach ($krs->items as $item)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3 font-medium">{{ $item->mataKuliah?->kode }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->nama }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->semester }}</td>
                                </tr>
                            @endforeach
                            @if ($krs->items->count() === 0)
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Approval</div>

            <div class="text-sm text-emerald-100/70 space-y-3">
                <div class="flex items-center justify-between">
                    <span>Status</span>
                    <span class="font-medium text-white uppercase">{{ $krs->status_approval }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Tahun Ajaran</span>
                    <span class="font-medium text-white">{{ $krs->tahun_ajaran ?? '-' }}</span>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.krs.status', $krs) }}" class="mt-5 space-y-3">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-sm text-emerald-100/80">Status Approval</label>
                    <select name="status_approval" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>
                        @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
                            <option value="{{ $k }}" @selected(old('status_approval', $krs->status_approval) === $k) class="text-black">{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('status_approval') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="text-sm text-emerald-100/80">Keterangan / Catatan</label>
                    <textarea name="catatan_approval" rows="3" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Contoh: Pengajuan KRS direject karena mata kuliah penuh atau alasan lainnya.">{{ old('catatan_approval', $krs->catatan_approval) }}</textarea>
                    @error('catatan_approval') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <button class="w-full h-11 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                    Simpan
                </button>
            </form>
        </div>
    </div>
</x-portal-layout>
