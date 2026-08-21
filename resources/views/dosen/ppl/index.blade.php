<x-portal-layout :title="'Bimbingan PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Bimbingan PPL</div>
            <div class="text-sm text-emerald-100/70">Daftar mahasiswa bimbingan sesuai SK.</div>
        </div>
    </div>

    <div class="mt-5 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-emerald-100/80">
                <tr>
                    <th class="text-left font-medium px-4 py-3 w-10">No</th>
                    <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                    <th class="text-left font-medium px-4 py-3">Instansi</th>
                    <th class="text-left font-medium px-4 py-3">Nomor SK</th>
                    <th class="text-left font-medium px-4 py-3 w-32">Tanggal SK</th>
                    <th class="text-right font-medium px-4 py-3 w-80">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($items as $i => $row)
                    @php
                        $latest = $row->latestMessage;
                        $lastRead = $row->dosen_last_read_at;
                        $hasUnread = $latest
                            && (int) $latest->sender_user_id !== (int) auth()->id()
                            && (! $lastRead || $latest->created_at?->gt($lastRead));
                    @endphp
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                            <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                            @if ($hasUnread)
                                <div class="mt-1 text-xs text-red-300 font-semibold">Ada pesan baru</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-emerald-100/85">{{ $row->instansi_nama }}</td>
                        <td class="px-4 py-3">
                            @if ($row->nomor_sk)
                                <span class="font-medium text-emerald-100">{{ $row->nomor_sk }}</span>
                            @else
                                <span class="text-emerald-100/40">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-emerald-100/85">
                            {{ $row->tanggal_sk?->format('d/m/Y') ?: '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                <a href="{{ route('dosen.ppl.bimbingan.show', $row) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-500/20 transition text-xs font-medium">
                                    <span style="position: relative; display: inline-flex; align-items: center; justify-content: center; width: 14px; height: 14px;">
                                        <i class="fa-solid fa-comments"></i>
                                        @if ($hasUnread)
                                            <span style="position: absolute; top: -4px; right: -6px; width: 8px; height: 8px; border-radius: 999px; background: #ef4444; border: 2px solid rgba(3, 105, 70, 0.85);"></span>
                                        @endif
                                    </span>
                                    <span class="text-xs font-medium">Bimbingan</span>
                                </a>
                                @if ($row->sk_pembimbing_path)
                                    <a href="{{ route('dosen.ppl-pengajuan.sk.preview', $row) }}" target="_blank"
                                       class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-xs font-medium">
                                        <i class="fa-solid fa-eye"></i>
                                        Preview
                                    </a>
                                    <a href="{{ route('dosen.ppl-pengajuan.sk.download', $row) }}"
                                       class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-blue-500/15 hover:bg-blue-500/25 border border-blue-500/20 transition text-xs font-medium">
                                        <i class="fa-solid fa-download"></i>
                                        Unduh
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70 text-xs">
                            Belum ada PPL yang ditetapkan untuk dosen ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-portal-layout>

