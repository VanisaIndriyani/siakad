<x-portal-layout :title="'Detail KHS - '.config('app.name')" subtitle="Detail KHS">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail KHS</div>
            <div class="text-sm text-emerald-100/70">{{ $khs->mahasiswa?->nama_lengkap }} • Semester {{ $khs->semester }}</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.khs.edit', $khs) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-pen"></i>
                Edit
            </a>
            <a href="{{ route('admin.khs.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Nilai</div>
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                                <th class="text-left font-medium px-4 py-3">SKS</th>
                                <th class="text-left font-medium px-4 py-3">Nilai Angka</th>
                                <th class="text-left font-medium px-4 py-3">Nilai Huruf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($khs->items as $item)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $item->mataKuliah?->kode }} - {{ $item->mataKuliah?->nama }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->mataKuliah?->sks }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->nilai_angka ?? '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $item->nilai_huruf ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada nilai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Ringkasan</div>
            <div class="text-sm text-emerald-100/70 space-y-3">
                <div class="flex items-center justify-between">
                    <span>Tahun Ajaran</span>
                    <span class="font-medium text-white">{{ $khs->tahun_ajaran ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>IPS</span>
                    <span class="font-medium text-white">{{ $khs->ips ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>IPK</span>
                    <span class="font-medium text-white">{{ $khs->ipk ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
