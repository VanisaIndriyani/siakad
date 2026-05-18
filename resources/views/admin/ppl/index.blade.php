<x-portal-layout :title="'PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include(($routePrefix ?? 'admin') === 'admin' ? 'admin.partials.sidebar' : 'dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $isAdminView = ($routePrefix ?? 'admin') === 'admin';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">{{ $isAdminView ? 'PPL' : 'Pengajuan PPL' }}</div>
            <div class="text-sm text-emerald-100/70">{{ $isAdminView ? 'Review pengajuan instansi/sekolah dan tetapkan pembimbing.' : 'ACC pengajuan instansi/sekolah PPL.' }}</div>
        </div>
        @if ($isAdminView)
            <form id="bulkDeleteForm" method="POST" action="{{ route('admin.ppl.bulk-delete') }}" data-confirm="Hapus data PPL yang dicentang?">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-10 px-4 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition text-sm font-medium inline-flex items-center gap-2">
                    <i class="fa-solid fa-trash"></i>
                    Hapus Terpilih
                </button>
            </form>
        @endif
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ $isAdminView ? route('admin.ppl.index') : route('dosen.ppl-pengajuan.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari instansi / nama / NPM..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <select name="status" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="" style="background-color: #0d2a23; color: #fff;">Semua Status</option>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'assigned' => 'Assigned'] as $k => $v)
                    <option value="{{ $k }}" @selected($status === $k) style="background-color: #0d2a23; color: #fff;">{{ $v }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ $isAdminView ? route('admin.ppl.index') : route('dosen.ppl-pengajuan.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        @if ($isAdminView)
                            <th class="text-left font-medium px-4 py-3 w-12">
                                <input id="checkAllPpl" type="checkbox" class="h-4 w-4 rounded border-white/10 bg-white/5" />
                            </th>
                        @endif
                        <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                        <th class="text-left font-medium px-4 py-3">Instansi/Sekolah</th>
                        <th class="text-left font-medium px-4 py-3">Status</th>
                        <th class="text-left font-medium px-4 py-3">Pembimbing</th>
                        <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($items as $row)
                        @php
                            $badge = match ($row->status) {
                                'assigned' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                'approved' => 'bg-blue-500/15 border-blue-500/20 text-blue-100',
                                'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                            };
                        @endphp
                        <tr class="hover:bg-white/5">
                            @if ($isAdminView)
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="ids[]" form="bulkDeleteForm" class="ppl-check h-4 w-4 rounded border-white/10 bg-white/5" value="{{ $row->id }}" />
                                </td>
                            @endif
                            <td class="px-4 py-3">
                                <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/90">
                                <div class="font-medium text-white">{{ $row->instansi_nama }}</div>
                                @if ($row->instansi_alamat)
                                    <div class="text-xs text-emerald-100/60 mt-1">{{ \Illuminate\Support\Str::limit($row->instansi_alamat, 80) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                <div>{{ $row->dosenPembimbing?->nama ?: '-' }}</div>
                                @if ($row->dosenPembimbing2?->nama)
                                    <div class="text-xs text-emerald-100/60 mt-1">{{ $row->dosenPembimbing2?->nama }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($isAdminView)
                                        <a href="{{ route('admin.ppl.pdf', $row) }}" class="h-9 px-3 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-emerald-100" title="Print PDF">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    @endif
                                    <a href="{{ $isAdminView ? route('admin.ppl.show', $row) : route('dosen.ppl-pengajuan.show', $row) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="text-sm font-medium">Detail</span>
                                    </a>
                                    @if ($isAdminView)
                                        <form method="POST" action="{{ route('admin.ppl.destroy', $row) }}" data-confirm="Hapus data PPL ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/20 border border-red-500/25 transition text-red-100">
                                                <i class="fa-solid fa-trash"></i>
                                                <span class="text-sm font-medium">Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdminView ? 6 : 5 }}" class="px-4 py-10 text-center text-emerald-100/70">Belum ada pengajuan PPL.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $items->links() }}
    </div>

    @if ($isAdminView)
        <script>
            (function () {
                const checkAll = document.getElementById('checkAllPpl');
                const checks = document.querySelectorAll('.ppl-check');
                const form = document.getElementById('bulkDeleteForm');

                if (checkAll) {
                    checkAll.addEventListener('change', () => {
                        checks.forEach(c => c.checked = checkAll.checked);
                    });
                }

                if (form) {
                    form.addEventListener('submit', (e) => {
                        const anyChecked = Array.from(checks).some(c => c.checked);
                        if (!anyChecked) {
                            e.preventDefault();
                            alert('Pilih minimal 1 data PPL.');
                        }
                    });
                }
            })();
        </script>
    @endif
</x-portal-layout>

