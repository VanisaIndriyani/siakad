<div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-emerald-100/80">
                <tr>
                    <th class="text-left font-medium px-4 py-3 w-10">
                        <input type="checkbox" data-bulk="select-all"
                               class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                    </th>
                    <th class="text-left font-medium px-4 py-3">Dosen</th>
                    <th class="text-left font-medium px-4 py-3">NIDN</th>
                    <th class="text-left font-medium px-4 py-3">NUPTK</th>
                    <th class="text-left font-medium px-4 py-3">Program Studi</th>
                    <th class="text-left font-medium px-4 py-3">Nomor HP</th>
                    <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                    <th class="text-right font-medium px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                @forelse ($dosen as $row)
                    <tr class="hover:bg-white/5">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-bulk="row"
                                   class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if ($row->foto_path)
                                    <img src="{{ asset('storage/'.$row->foto_path) }}" class="h-10 w-10 rounded-xl object-cover ring-1 ring-white/10" alt="Foto" />
                                @else
                                    <div class="h-10 w-10 rounded-xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center font-semibold">
                                        {{ mb_substr($row->nama, 0, 1) }}
                                    </div>
                                @endif
                                <div class="leading-tight">
                                    <div class="font-medium text-white">{{ $row->nama }}</div>
                                    <div class="text-xs text-emerald-100/60">{{ $row->user?->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-emerald-100/80">{{ $row->nidn }}</td>
                        <td class="px-4 py-3 text-emerald-100/80">{{ $row->nuptk ?? '-' }}</td>
                        <td class="px-4 py-3 text-emerald-100/80">{{ $row->program_studi ?? '-' }}</td>
                        <td class="px-4 py-3 text-emerald-100/80">{{ $row->nomor_hp ?? '-' }}</td>
                        <td class="px-4 py-3 text-emerald-100/80">{{ $row->mata_kuliah ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.dosen.show', $row) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.dosen.edit', $row) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.dosen.destroy', $row) }}" data-confirm="Apakah kamu yakin ingin menghapus dosen ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center text-emerald-100/70">Data tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $dosen->links() }}
</div>
