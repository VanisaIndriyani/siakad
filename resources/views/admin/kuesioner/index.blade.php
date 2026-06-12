<x-portal-layout :title="'Kuesioner - '.config('app.name')" subtitle="Kuesioner">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    @php
        $formatScore = function ($value) {
            return $value !== null ? number_format((float) $value, 2) : '-';
        };
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Kuesioner</div>
            <div class="text-sm text-emerald-100/70">Kelola pertanyaan dan lihat rekap hasil pengisian kuesioner mahasiswa.</div>
        </div>
        <a href="{{ route('admin.kuesioner.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Tambah Pertanyaan</span>
        </a>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Total Respon</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['responses_count'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Mahasiswa Mengisi</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['students_count'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Pertanyaan Aktif</div>
            <div class="mt-2 text-3xl font-semibold">{{ $summary['questions_count'] }}</div>
        </div>
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-sm text-emerald-100/70">Rata-rata Skor</div>
            <div class="mt-2 text-3xl font-semibold">{{ $formatScore($summary['average_score']) }}</div>
        </div>
    </div>

    <div class="mt-8">
        <div class="text-lg font-semibold">Rekap Per Mata Kuliah</div>
        <div class="mt-1 text-sm text-emerald-100/70">Persentase dihitung dari seluruh jawaban kuesioner pada mata kuliah terkait.</div>
    </div>

    <form method="GET" class="mt-4 flex flex-col sm:flex-row gap-3">
        <input name="q" value="{{ $q }}" class="w-full sm:max-w-lg h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode, mata kuliah, atau dosen..." />
        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
        <a href="{{ route('admin.kuesioner.index') }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Reset</a>
    </form>

    <div class="mt-4" x-data="{
        selectedIds: [],
        onBulkChange(e) {
            const t = e.target;
            if (!(t instanceof HTMLInputElement)) return;
            if (t.dataset.bulk === 'select-all-course') {
                const rows = this.$refs.bulkCourseForm.querySelectorAll('input[data-bulk=course-row]');
                rows.forEach((cb) => { cb.checked = t.checked; });
            }
            this.syncSelectedFromDom();
        },
        syncSelectedFromDom() {
            const rows = Array.from(this.$refs.bulkCourseForm.querySelectorAll('input[data-bulk=course-row]'));
            this.selectedIds = rows.filter((cb) => cb.checked).map((cb) => cb.value);
            const selectAll = this.$refs.bulkCourseForm.querySelector('input[data-bulk=select-all-course]');
            if (!selectAll) return;
            if (rows.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                return;
            }
            const checkedCount = rows.filter((cb) => cb.checked).length;
            selectAll.checked = checkedCount === rows.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
        }
    }">
        <form x-ref="bulkCourseForm" method="POST" action="{{ route('admin.kuesioner.bulk-delete-course') }}" @change="onBulkChange($event)"
              data-confirm="Apakah kamu yakin ingin menghapus hasil kuesioner untuk mata kuliah yang dipilih?">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 mb-3">
                <button type="submit"
                        :disabled="selectedIds.length === 0"
                        class="h-10 px-4 inline-flex items-center gap-2 rounded-xl border transition"
                        :class="selectedIds.length === 0
                            ? 'bg-white/5 border-white/10 text-white/40 cursor-not-allowed'
                            : 'bg-red-500/15 hover:bg-red-500/25 border-red-500/20 text-red-100'">
                    <i class="fa-solid fa-trash"></i>
                    <span class="text-sm font-medium" x-text="`Hapus Terpilih (${selectedIds.length})`"></span>
                </button>
            </div>

            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="px-4 py-3 text-left w-10">
                                    <input type="checkbox" data-bulk="select-all-course" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                </th>
                                <th class="text-left font-medium px-4 py-3">Mata Kuliah</th>
                                <th class="text-left font-medium px-4 py-3">Dosen</th>
                                <th class="text-left font-medium px-4 py-3">Respon</th>
                                <th class="text-left font-medium px-4 py-3">Rata-rata</th>
                                @foreach ($scoreLabels as $score => $label)
                                    <th class="text-left font-medium px-4 py-3">{{ $label }}</th>
                                @endforeach
                                <th class="text-right font-medium px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($courseSummaries as $course)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="mata_kuliah_ids[]" value="{{ $course->id }}" data-bulk="course-row" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $course->kode }} - {{ $course->nama }}</div>
                                        <div class="text-xs text-emerald-100/60">Semester {{ $course->semester }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">
                                        <div>{{ $course->dosen_1 ?? '-' }}</div>
                                        @if ($course->dosen_2)
                                            <div class="text-xs text-emerald-100/60 mt-1">{{ $course->dosen_2 }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $course->responses_count }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $formatScore($course->average_score) }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_1_pct !== null ? number_format((float) $course->score_1_pct, 2).'%' : '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_2_pct !== null ? number_format((float) $course->score_2_pct, 2).'%' : '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_3_pct !== null ? number_format((float) $course->score_3_pct, 2).'%' : '-' }}</td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $course->score_4_pct !== null ? number_format((float) $course->score_4_pct, 2).'%' : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.kuesioner.show', $course->id) }}" class="h-9 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                                <i class="fa-solid fa-eye"></i>
                                                Detail
                                            </a>
                                            <button type="button" onclick="document.getElementById('delete-course-form-{{ $course->id }}').requestSubmit()" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-10 text-center text-emerald-100/70">Belum ada data kuesioner.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        @foreach ($courseSummaries as $course)
            <form id="delete-course-form-{{ $course->id }}" method="POST" action="{{ route('admin.kuesioner.bulk-delete-course') }}" class="hidden"
                  data-confirm="Apakah kamu yakin ingin menghapus hasil kuesioner untuk mata kuliah ini?">
                @csrf
                @method('DELETE')
                <input type="hidden" name="mata_kuliah_ids[]" value="{{ $course->id }}" />
            </form>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $courseSummaries->links() }}
    </div>

    <div class="mt-8">
        <div class="text-lg font-semibold">Daftar Pertanyaan</div>
        <div class="mt-1 text-sm text-emerald-100/70">Admin dapat menambah, mengedit, menonaktifkan, atau menghapus pertanyaan yang belum pernah dipakai.</div>
    </div>

    <div class="mt-4" x-data="{
        selectedIds: [],
        onBulkChange(e) {
            const t = e.target;
            if (!(t instanceof HTMLInputElement)) return;
            if (t.dataset.bulk === 'select-all-question') {
                const rows = this.$refs.bulkQuestionForm.querySelectorAll('input[data-bulk=question-row]');
                rows.forEach((cb) => { cb.checked = t.checked; });
            }
            this.syncSelectedFromDom();
        },
        syncSelectedFromDom() {
            const rows = Array.from(this.$refs.bulkQuestionForm.querySelectorAll('input[data-bulk=question-row]'));
            this.selectedIds = rows.filter((cb) => cb.checked).map((cb) => cb.value);
            const selectAll = this.$refs.bulkQuestionForm.querySelector('input[data-bulk=select-all-question]');
            if (!selectAll) return;
            if (rows.length === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
                return;
            }
            const checkedCount = rows.filter((cb) => cb.checked).length;
            selectAll.checked = checkedCount === rows.length;
            selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
        }
    }">
        <form x-ref="bulkQuestionForm" method="POST" action="{{ route('admin.kuesioner.bulk-delete') }}" @change="onBulkChange($event)"
              data-confirm="Apakah kamu yakin ingin menghapus pertanyaan yang dipilih?">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between gap-3 mb-3">
                <button type="submit"
                        :disabled="selectedIds.length === 0"
                        class="h-10 px-4 inline-flex items-center gap-2 rounded-xl border transition"
                        :class="selectedIds.length === 0
                            ? 'bg-white/5 border-white/10 text-white/40 cursor-not-allowed'
                            : 'bg-red-500/15 hover:bg-red-500/25 border-red-500/20 text-red-100'">
                    <i class="fa-solid fa-trash"></i>
                    <span class="text-sm font-medium" x-text="`Hapus Terpilih (${selectedIds.length})`"></span>
                </button>
            </div>

            <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/80">
                            <tr>
                                <th class="px-4 py-3 text-left w-10">
                                    <input type="checkbox" data-bulk="select-all-question" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                </th>
                                <th class="text-left font-medium px-4 py-3">Urutan</th>
                                <th class="text-left font-medium px-4 py-3">Pertanyaan</th>
                                <th class="text-left font-medium px-4 py-3">Status</th>
                                <th class="text-left font-medium px-4 py-3">Dipakai</th>
                                <th class="text-right font-medium px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse ($questions as $question)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="ids[]" value="{{ $question->id }}" data-bulk="question-row" class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $question->sort_order }}</td>
                                    <td class="px-4 py-3 text-emerald-100/90">{{ $question->question }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $question->is_active ? 'bg-emerald-500/15 border border-emerald-500/20 text-emerald-100' : 'bg-white/5 border border-white/10 text-emerald-100/70' }}">
                                            {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $question->answers_count }} jawaban</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.kuesioner.edit', $question) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Edit">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <button type="button" onclick="document.getElementById('delete-question-form-{{ $question->id }}').requestSubmit()" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition text-red-100" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Pertanyaan kuesioner belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        @foreach ($questions as $question)
            <form id="delete-question-form-{{ $question->id }}" method="POST" action="{{ route('admin.kuesioner.destroy', $question) }}" class="hidden"
                  data-confirm="Apakah kamu yakin ingin menghapus pertanyaan ini?">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</x-portal-layout>
