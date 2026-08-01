<x-portal-layout :title="'Transkip Nilai - '.$mahasiswa->nama_lengkap" subtitle="Rekapitulasi Semester 1 s/d 8">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Transkip Nilai Preview</div>
            <div class="text-sm text-emerald-100/70">{{ $mahasiswa->nama_lengkap }} • {{ $mahasiswa->npm }}</div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.rekap-nilai.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                <span class="text-sm font-medium">Kembali</span>
            </a>
            <a href="{{ route('admin.rekap-nilai.pdf', $mahasiswa, ['inline' => 1]) }}" target="_blank" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-eye"></i>
                <span class="text-sm font-medium">Lihat PDF</span>
            </a>
            <a href="{{ route('admin.rekap-nilai.pdf', $mahasiswa) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">Unduh PDF</span>
            </a>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm text-emerald-100/70">Program Studi</div>
            <div class="mt-1 text-lg font-semibold">{{ $mahasiswa->program_studi ?? '-' }}</div>
            <div class="text-xs text-emerald-100/60 mt-1">Angkatan {{ $mahasiswa->angkatan ?? '-' }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
            <div class="text-sm text-emerald-100/70">Total SKS</div>
            <div class="mt-1 text-lg font-semibold">{{ $totalSks }} SKS</div>
        </div>
        <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 p-5">
            <div class="text-sm text-emerald-100/80">IPK Akhir</div>
            <div class="mt-1 text-2xl font-extrabold">{{ number_format($ipkAkhir, 2, ',', '.') }}</div>
            <div class="text-xs text-emerald-100/70 mt-1">{{ $predikat }}</div>
        </div>
    </div>

    <div class="mt-6 space-y-5">
        @foreach ($semesterRows as $sem)
            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="flex items-center justify-between px-5 py-3 bg-white/5 border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <div class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-600 text-white font-extrabold">
                            {{ $sem->semester }}
                        </div>
                        <div>
                            <div class="font-semibold">Semester {{ $sem->semester }}</div>
                            <div class="text-xs text-emerald-100/60">{{ $sem->tahun_ajaran }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-xs">
                        <div class="text-right">
                            <div class="text-emerald-100/60">IPS</div>
                            <div class="font-extrabold text-emerald-300">{{ $sem->ips !== null ? number_format($sem->ips, 2, ',', '.') : '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-emerald-100/60">SKS</div>
                            <div class="font-extrabold">{{ $sem->sks_semester }}</div>
                        </div>
                        <div class="text-right border-l border-white/10 pl-3">
                            <div class="text-emerald-100/60">IPK Kum</div>
                            <div class="font-extrabold text-amber-300">{{ $sem->ipk_kumulatif !== null ? number_format($sem->ipk_kumulatif, 2, ',', '.') : '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/70 text-xs">
                            <tr>
                                <th class="text-left font-medium px-4 py-2 w-12">No</th>
                                <th class="text-left font-medium px-4 py-2">Kode</th>
                                <th class="text-left font-medium px-4 py-2">Mata Kuliah</th>
                                <th class="text-center font-medium px-4 py-2 w-16">SKS</th>
                                <th class="text-center font-medium px-4 py-2 w-20">Nilai</th>
                                <th class="text-center font-medium px-4 py-2 w-24">Bobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @if (count($sem->rows) === 0)
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-emerald-100/60 text-sm">Belum ada data nilai.</td>
                                </tr>
                            @else
                                @foreach ($sem->rows as $row)
                                    <tr class="hover:bg-white/5">
                                        <td class="px-4 py-2 text-emerald-100/60">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $row->kode }}</td>
                                        <td class="px-4 py-2">{{ $row->nama }}</td>
                                        <td class="px-4 py-2 text-center">{{ $row->sks }}</td>
                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-flex items-center justify-center min-w-[44px] h-8 px-2 rounded-lg text-sm font-extrabold
                                                @if(in_array($row->nilai_huruf, ['A','A-','B+','B','B-'])) bg-emerald-500/15 border border-emerald-500/20 text-emerald-200
                                                @elseif(in_array($row->nilai_huruf, ['C+','C','C-'])) bg-amber-500/15 border border-amber-500/20 text-amber-100
                                                @elseif(in_array($row->nilai_huruf, ['D+','D'])) bg-orange-500/15 border border-orange-500/20 text-orange-100
                                                @elseif($row->nilai_huruf === 'E') bg-red-500/15 border border-red-500/20 text-red-200
                                                @else bg-white/5 border border-white/10 text-emerald-100/60
                                                @endif">
                                                {{ $row->nilai_huruf }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-center font-semibold">{{ number_format($row->bobot, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</x-portal-layout>
