<x-portal-layout :title="'Bimbingan KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <div class="text-xl font-semibold text-white uppercase tracking-tight">Bimbingan KKN</div>
            <div class="text-sm text-emerald-100/60 font-medium">Daftar pembimbingan KKN per SK dan posko yang Anda bimbing.</div>
        </div>
    </div>

    <div class="rounded-2xl bg-white/5 border border-white/10 p-5 mb-6">
        <div class="text-sm font-semibold mb-3 text-emerald-100">Daftar Pembimbingan KKN (sesuai SK Pembimbing)</div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-3 py-2 w-10">No</th>
                        <th class="text-left font-medium px-3 py-2">Mahasiswa</th>
                        <th class="text-left font-medium px-3 py-2">Prodi</th>
                        <th class="text-left font-medium px-3 py-2">Nomor SK</th>
                        <th class="text-left font-medium px-3 py-2 w-28">Tanggal SK</th>
                        <th class="text-left font-medium px-3 py-2">Posko</th>
                        <th class="text-right font-medium px-3 py-2 w-56">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $i => $row)
                        <tr class="hover:bg-white/5">
                            <td class="px-3 py-3">{{ $i + 1 }}</td>
                            <td class="px-3 py-3">
                                <div class="font-semibold">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                            </td>
                            <td class="px-3 py-3 text-emerald-100/85">{{ $row->mahasiswa?->program_studi ?: '-' }}</td>
                            <td class="px-3 py-3">
                                @if ($row->nomor_sk)
                                    <span class="font-medium text-emerald-100">{{ $row->nomor_sk }}</span>
                                @else
                                    <span class="text-emerald-100/40">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-emerald-100/85">
                                {{ $row->tanggal_sk?->format('d/m/Y') ?: '-' }}
                            </td>
                            <td class="px-3 py-3 text-emerald-100/85">
                                {{ $row->posko?->nama_posko ?: '-' }}
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    <a href="{{ route('dosen.kkn-pengajuan.index', ['q' => $row->mahasiswa?->npm]) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs font-medium">
                                        <i class="fa-solid fa-list"></i>
                                        Detail
                                    </a>
                                    @if ($row->sk_pembimbing_path)
                                        <a href="{{ route('dosen.kkn-pengajuan.sk.preview', $row) }}" target="_blank"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-xs font-medium">
                                            <i class="fa-solid fa-eye"></i>
                                            Preview
                                        </a>
                                        <a href="{{ route('dosen.kkn-pengajuan.sk.download', $row) }}"
                                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/20 transition text-xs font-medium">
                                            <i class="fa-solid fa-download"></i>
                                            Unduh SK
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-3 py-10 text-center text-emerald-100/60 text-xs">
                                Belum ada pengajuan KKN yang menetapkan Anda sebagai pembimbing sesuai SK.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-sm font-semibold text-emerald-100 mb-3">Daftar Posko KKN (DPL)</div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($poskos as $posko)
            <div class="group relative rounded-3xl bg-[#0d2a23] border border-white/10 overflow-hidden shadow-xl hover:border-emerald-500/30 transition-all duration-500">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full group-hover:bg-emerald-500/20 transition-all duration-500"></div>
                
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-emerald-600/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform duration-500">
                            <i class="fa-solid fa-tent text-2xl"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-1">Anggota</div>
                            <div class="text-lg font-black text-white leading-none">{{ $posko->pengajuans->count() }}</div>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-white leading-tight mb-2 group-hover:text-emerald-400 transition-colors duration-500">{{ $posko->nama_posko }}</h3>
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-100/40 uppercase tracking-widest mb-6">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $posko->lokasi ?: 'Lokasi segera ditentukan' }}
                    </div>

                    <div class="space-y-4 pt-6 border-t border-white/5">
                        <div class="flex items-center justify-between text-[10px] font-black text-emerald-100/30 uppercase tracking-widest">
                            <span>Nomor SK</span>
                            <span class="text-emerald-100/60">{{ $posko->nomor_sk ?: '-' }}</span>
                        </div>
                        
                        <div class="flex flex-wrap gap-1.5 pt-2">
                            @foreach ($posko->pengajuans->take(3) as $p)
                                <div class="h-8 w-8 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-[10px] font-black text-white/40" title="{{ $p->mahasiswa?->nama_lengkap }}">
                                    {{ mb_substr($p->mahasiswa?->nama_lengkap, 0, 1) }}
                                </div>
                            @endforeach
                            @if ($posko->pengajuans->count() > 3)
                                <div class="h-8 px-2 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-[9px] font-black text-white/40">
                                    +{{ $posko->pengajuans->count() - 3 }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('dosen.kkn.posko', $posko) }}" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black tracking-[0.2em] uppercase text-xs flex items-center justify-center gap-3 transition-all shadow-lg shadow-emerald-900/40 group-hover:shadow-emerald-900/60">
                            Masuk Bimbingan
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-2 lg:col-span-3 py-20 text-center">
                <div class="h-24 w-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-campground text-5xl text-white/10"></i>
                </div>
                <h3 class="text-2xl font-black uppercase tracking-widest text-white/30 leading-none">Belum Ada Posko</h3>
                <p class="text-sm text-emerald-100/20 mt-3 font-medium">Anda belum ditugaskan sebagai DPL pada posko KKN manapun.</p>
            </div>
        @endforelse
    </div>
</x-portal-layout>
