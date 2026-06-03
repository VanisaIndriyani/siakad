<x-portal-layout :title="'Pendaftaran KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Pendaftaran KKN</div>
            <div class="text-sm text-emerald-100/70">Kelola pendaftaran KKN mahasiswa.</div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" 
                    id="btnBulkDelete"
                    onclick="submitBulkDelete()"
                    class="h-10 px-4 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 transition text-sm font-medium hidden items-center gap-2 text-red-100">
                <i class="fa-solid fa-trash"></i>
                Hapus Terpilih (<span id="selectedCount">0</span>)
            </button>
            <a href="{{ route('admin.kkn.posko.index') }}" class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium inline-flex items-center gap-2">
                <i class="fa-solid fa-tent"></i>
                Manajemen Posko
            </a>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('admin.kkn.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari nama / NPM..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <select name="status" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="" style="background-color: #0d2a23; color: #fff;">Semua Status</option>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $k => $v)
                    <option value="{{ $k }}" @selected($status === $k) style="background-color: #0d2a23; color: #fff;">{{ $v }}</option>
                @endforeach
            </select>
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('admin.kkn.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.kkn.bulk-delete') }}">
        @csrf
        @method('DELETE')
        <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white/5 text-emerald-100/80">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" id="selectAll" class="rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/30">
                            </th>
                            <th class="text-left font-medium px-4 py-3">Mahasiswa</th>
                            <th class="text-left font-medium px-4 py-3">Program Studi</th>
                            <th class="text-left font-medium px-4 py-3">Status</th>
                            <th class="text-left font-medium px-4 py-3">Posko</th>
                            <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($items as $row)
                            @php
                                $badge = match ($row->status) {
                                    'approved' => 'bg-emerald-500/15 border-emerald-500/20 text-emerald-100',
                                    'rejected' => 'bg-red-500/15 border-red-500/20 text-red-100',
                                    default => 'bg-yellow-500/15 border-yellow-500/20 text-yellow-100',
                                };
                            @endphp
                            <tr class="hover:bg-white/5">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $row->id }}" class="item-checkbox rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/30">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $row->mahasiswa?->nama_lengkap ?: '-' }}</div>
                                    <div class="text-xs text-emerald-100/60">{{ $row->mahasiswa?->npm ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-emerald-100/80">{{ $row->mahasiswa?->program_studi ?: '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badge }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-emerald-100/80">
                                    {{ $row->posko?->nama_posko ?: 'Belum diplot' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" 
                                                onclick="openStatusModal({{ $row->id }}, '{{ $row->status }}', '{{ $row->catatan_admin }}')"
                                                class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                            <i class="fa-solid fa-pen"></i>
                                            <span class="text-sm font-medium">Status</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Belum ada pendaftaran KKN.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>

    <div class="mt-5">
        {{ $items->links() }}
    </div>

    <!-- Status Modal -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeStatusModal()"></div>
            <div class="relative w-full max-w-md transform rounded-2xl bg-[#0d2a23] border border-white/10 p-6 shadow-2xl transition-all">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Update Status Pendaftaran</h3>
                    <button onclick="closeStatusModal()" class="text-white/40 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST" action="">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-emerald-100/70 mb-1">Status</label>
                            <select id="modalStatus" name="status" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                <option value="approved" style="background-color: #0d2a23; color: #fff;">Setujui</option>
                                <option value="rejected" style="background-color: #0d2a23; color: #fff;">Tolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-emerald-100/70 mb-1">Catatan Admin (Opsional)</label>
                            <textarea id="modalCatatan" name="catatan_admin" rows="4" class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeStatusModal()" class="h-11 px-6 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-white text-sm font-medium">Batal</button>
                        <button type="submit" class="h-11 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white text-sm font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openStatusModal(id, status, catatan) {
            const form = document.getElementById('statusForm');
            form.action = `/admin/kkn/${id}/status`;
            document.getElementById('modalStatus').value = status === 'pending' ? 'approved' : status;
            document.getElementById('modalCatatan').value = catatan || '';
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Bulk Delete Logic
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkDeleteButton() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            selectedCount.innerText = checkedCount;
            if (checkedCount > 0) {
                btnBulkDelete.classList.remove('hidden');
                btnBulkDelete.classList.add('flex');
            } else {
                btnBulkDelete.classList.add('hidden');
                btnBulkDelete.classList.remove('flex');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkDeleteButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.item-checkbox:checked').length === checkboxes.length) selectAll.checked = true;
                updateBulkDeleteButton();
            });
        });

        function submitBulkDelete() {
            if (confirm('Apakah Anda yakin ingin menghapus data pendaftaran KKN yang terpilih?')) {
                document.getElementById('bulkDeleteForm').submit();
            }
        }
    </script>
</x-portal-layout>
