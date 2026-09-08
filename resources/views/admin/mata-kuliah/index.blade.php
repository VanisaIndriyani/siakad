<x-portal-layout :title="'Mata Kuliah - '.config('app.name')" subtitle="Manajemen Mata Kuliah">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">Mata Kuliah</div>
            <div class="text-sm text-emerald-100/70">Pilih jurusan → pilih semester → baru tampil daftar mata kuliah.</div>
        </div>
        <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
            <i class="fa-solid fa-file-pdf"></i>
            <span class="text-sm font-medium">PDF</span>
        </a>
    </div>

    <div class="mt-8 mb-10">
        <div class="mb-5 relative inline-block">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-full bg-gradient-to-b from-emerald-400 via-teal-400 to-emerald-600 shadow-[0_0_15px_rgba(45,212,191,0.55)]"></div>
            <div class="pl-4 sm:pl-5">
             
                <h2 class="text-2xl sm:text-[26px] font-bold tracking-tight leading-tight">Upload SK Mengajar &amp; Roster Kuliah Per Prodi</h2>
            
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 items-stretch">
            @foreach ($jurusanList as $idx => $j)
                @php
                    $sk = $skPerProdi->get($j);
                    $rs = $rosterPerProdi->get($j);
                    $skAda = $sk && !empty($sk->file_pdf) && \Illuminate\Support\Facades\Storage::disk('public')->exists($sk->file_pdf);
                    $rsAda = $rs && !empty($rs->file_pdf) && \Illuminate\Support\Facades\Storage::disk('public')->exists($rs->file_pdf);
                    $prodiIcon = match(true) {
                        $idx === 0 => 'fa-mosque',
                        $idx === 1 => 'fa-children',
                        $idx === 2 => 'fa-scale-balanced',
                        $idx === 3 => 'fa-landmark-dome',
                        $idx === 4 => 'fa-building-columns',
                        default => 'fa-chart-line',
                    };
                    $prodiAccent = match(true) {
                        $idx === 0 => 'from-emerald-500/40 to-teal-500/40',
                        $idx === 1 => 'from-pink-500/40 to-rose-500/40',
                        $idx === 2 => 'from-violet-500/40 to-purple-500/40',
                        $idx === 3 => 'from-amber-500/40 to-orange-500/40',
                        $idx === 4 => 'from-cyan-500/40 to-sky-500/40',
                        default => 'from-lime-500/40 to-green-500/40',
                    };
                @endphp
                <div x-data="{ showFormSk: @js(!$skAda), showFormRoster: @js(!$rsAda) }" class="group relative flex flex-col rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10 p-5 sm:p-6 shadow-[0_0_50px_-18px_rgba(16,185,129,0.22)] hover:shadow-[0_0_70px_-12px_rgba(16,185,129,0.35)] hover:border-emerald-400/25 hover:-translate-y-0.5 transition-all duration-300">
                    <div class="absolute -top-px left-6 right-6 h-px rounded-full bg-gradient-to-r {{ $prodiAccent }} opacity-70 group-hover:opacity-100 transition-opacity"></div>
                    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br {{ $prodiAccent }} border border-white/10 inline-flex items-center justify-center shadow-[0_0_25px_-4px_rgba(16,185,129,0.55)]">
                                <i class="fa-solid {{ $prodiIcon }} text-white text-[15px]"></i>
                            </div>
                           
                        </div>
                        <div class="flex flex-wrap gap-1.5 shrink-0 sm:justify-end">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $skAda ? 'bg-emerald-500/15 border-emerald-400/30 text-emerald-100 shadow-[0_0_18px_-6px_rgba(16,185,129,0.7)]' : 'bg-red-500/12 border-red-500/25 text-red-200' }}">
                                <i class="fa-solid fa-file-signature text-[10px]"></i>
                                <span>SK {{ $skAda ? 'TERSEDIA ✓' : 'BELUM' }}</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $rsAda ? 'bg-blue-500/15 border-blue-400/30 text-blue-100 shadow-[0_0_18px_-6px_rgba(59,130,246,0.7)]' : 'bg-red-500/12 border-red-500/25 text-red-200' }}">
                                <i class="fa-solid fa-calendar-days text-[10px]"></i>
                                <span>Roster {{ $rsAda ? 'TERSEDIA ✓' : 'BELUM' }}</span>
                            </span>
                        </div>
                    </div>

                    <div class="space-y-3 mt-auto">
                        <div class="rounded-xl border border-emerald-400/20 bg-emerald-500/5 p-4 shadow-[inset_0_0_30px_-18px_rgba(16,185,129,0.45)]">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="inline-flex items-center gap-2 text-emerald-100">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-500/15 border border-emerald-400/25 inline-flex items-center justify-center">
                                        <i class="fa-solid fa-file-signature text-emerald-300 text-[11px]"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold leading-tight">SK Mengajar</div>
                                     
                                    </div>
                                </div>
                                @if($skAda)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($sk->file_pdf) }}" target="_blank" rel="noopener" class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-emerald-500/15 hover:bg-emerald-500/25 border border-emerald-400/30 text-[11px] font-semibold text-emerald-100 transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        Lihat PDF
                                    </a>
                                @endif
                            </div>

                            @if($skAda)
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-emerald-500/10 border border-emerald-400/20 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-red-500/15 border border-red-500/25 inline-flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-red-300 text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[12px] font-semibold text-emerald-50 truncate" title="{{ basename($sk->file_pdf) }}">{{ basename($sk->file_pdf) }}</div>
                                        <div class="text-[10px] text-emerald-100/60 mt-0.5 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px] text-emerald-400"></i>
                                            Sudah diupload — semua dosen prodi ini bisa mengunduh
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="showFormSk = !showFormSk" class="w-full h-9 mb-3 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-emerald-400/25 hover:bg-emerald-500/10 text-[11px] font-semibold text-emerald-100 transition">
                                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                    <span x-text="showFormSk ? 'Batal Ganti File' : 'Ganti / Upload Ulang File'"></span>
                                </button>
                            @endif

                            <form x-show="showFormSk" x-transition method="POST" action="{{ route('admin.mata-kuliah.upload-sk', $j) }}" enctype="multipart/form-data">
                                @csrf
                                @error('sk_pdf')
                                    <div class="text-[11px] px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/25 text-red-200 mb-3 font-medium">{{ $message }}</div>
                                @enderror
                                <label class="block mb-2 text-[11px] font-semibold text-emerald-100/80">Pilih File PDF (max 10MB)</label>
                                <div class="flex flex-col sm:flex-row gap-2.5">
                                    <label class="relative flex-1 group/file cursor-pointer">
                                        <input type="file" name="file_pdf" accept="application/pdf,.pdf" required class="peer sr-only sk-input-{{ $idx }}" onchange="var n=this.files[0]?.name ?? '';this.parentElement.querySelector('.sk-filename-{{ $idx }}').textContent = n || 'Belum pilih file PDF';" />
                                        <div class="h-11 px-3 rounded-lg border border-dashed border-emerald-400/30 bg-emerald-500/5 hover:bg-emerald-500/10 peer-focus:border-emerald-400 peer-focus:ring-2 peer-focus:ring-emerald-400/40 transition inline-flex items-center gap-2.5 w-full">
                                            <div class="w-7 h-7 rounded-md bg-emerald-500/20 border border-emerald-400/25 inline-flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-folder-open text-emerald-300 text-[11px]"></i>
                                            </div>
                                            <div class="text-xs text-emerald-100/75 font-medium truncate flex-1 sk-filename-{{ $idx }}">Klik / seret file PDF kesini</div>
                                            <div class="h-7 px-2.5 rounded-md bg-emerald-500/20 border border-emerald-400/25 text-[10px] font-bold text-emerald-100 inline-flex items-center gap-1 shrink-0">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                                Pilih
                                            </div>
                                        </div>
                                    </label>
                                    <button type="submit" class="h-11 px-5 inline-flex items-center justify-center gap-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 shadow-[0_0_25px_-5px_rgba(16,185,129,0.65)] hover:shadow-[0_0_32px_-2px_rgba(16,185,129,0.8)] transition text-xs font-bold text-white shrink-0">
                                        <i class="fa-solid fa-cloud-arrow-up text-[12px]"></i>
                                        {{ $skAda ? 'TIMPAA FILE' : 'UPLOAD' }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="rounded-xl border border-blue-400/20 bg-blue-500/5 p-4 shadow-[inset_0_0_30px_-18px_rgba(59,130,246,0.45)]">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="inline-flex items-center gap-2 text-blue-100">
                                    <div class="w-7 h-7 rounded-lg bg-blue-500/15 border border-blue-400/25 inline-flex items-center justify-center">
                                        <i class="fa-solid fa-calendar-days text-blue-300 text-[11px]"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold leading-tight">Roster Kuliah</div>
                                      
                                    </div>
                                </div>
                                @if($rsAda)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($rs->file_pdf) }}" target="_blank" rel="noopener" class="h-8 px-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-500/15 hover:bg-blue-500/25 border border-blue-400/30 text-[11px] font-semibold text-blue-100 transition">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        Lihat PDFUpload 1x per prodi → otomatis muncul untuk SEMUA dosen di prodi tersebut di halaman Input Nilai Dosen. Cukup upload disini, nanti otomatis menyesuaikan prodi masing-masing dosen tanpa perlu setting per-dosen.
                                    </a>
                                @endif
                            </div>

                            @if($rsAda)
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-blue-500/10 border border-blue-400/20 mb-3">
                                    <div class="w-10 h-10 rounded-lg bg-red-500/15 border border-red-500/25 inline-flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-file-pdf text-red-300 text-sm"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-[12px] font-semibold text-blue-50 truncate" title="{{ basename($rs->file_pdf) }}">{{ basename($rs->file_pdf) }}</div>
                                        <div class="text-[10px] text-blue-100/60 mt-0.5 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px] text-blue-400"></i>
                                            Sudah diupload — semua dosen prodi ini bisa mengunduh
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="showFormRoster = !showFormRoster" class="w-full h-9 mb-3 px-3 inline-flex items-center justify-center gap-1.5 rounded-lg border border-blue-400/25 hover:bg-blue-500/10 text-[11px] font-semibold text-blue-100 transition">
                                    <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                    <span x-text="showFormRoster ? 'Batal Ganti File' : 'Ganti / Upload Ulang File'"></span>
                                </button>
                            @endif

                            <form x-show="showFormRoster" x-transition method="POST" action="{{ route('admin.mata-kuliah.upload-roster', $j) }}" enctype="multipart/form-data">
                                @csrf
                                @error('roster_pdf')
                                    <div class="text-[11px] px-3 py-2 rounded-lg bg-red-500/10 border border-red-500/25 text-red-200 mb-3 font-medium">{{ $message }}</div>
                                @enderror
                                <label class="block mb-2 text-[11px] font-semibold text-blue-100/80">Pilih File PDF (max 10MB)</label>
                                <div class="flex flex-col sm:flex-row gap-2.5">
                                    <label class="relative flex-1 group/file cursor-pointer">
                                        <input type="file" name="file_pdf" accept="application/pdf,.pdf" required class="peer sr-only roster-input-{{ $idx }}" onchange="var n=this.files[0]?.name ?? '';this.parentElement.querySelector('.roster-filename-{{ $idx }}').textContent = n || 'Belum pilih file PDF';" />
                                        <div class="h-11 px-3 rounded-lg border border-dashed border-blue-400/30 bg-blue-500/5 hover:bg-blue-500/10 peer-focus:border-blue-400 peer-focus:ring-2 peer-focus:ring-blue-400/40 transition inline-flex items-center gap-2.5 w-full">
                                            <div class="w-7 h-7 rounded-md bg-blue-500/20 border border-blue-400/25 inline-flex items-center justify-center shrink-0">
                                                <i class="fa-solid fa-folder-open text-blue-300 text-[11px]"></i>
                                            </div>
                                            <div class="text-xs text-blue-100/75 font-medium truncate flex-1 roster-filename-{{ $idx }}">Klik / seret file PDF kesini</div>
                                            <div class="h-7 px-2.5 rounded-md bg-blue-500/20 border border-blue-400/25 text-[10px] font-bold text-blue-100 inline-flex items-center gap-1 shrink-0">
                                                <i class="fa-solid fa-plus text-[9px]"></i>
                                                Pilih
                                            </div>
                                        </div>
                                    </label>
                                    <button type="submit" class="h-11 px-5 inline-flex items-center justify-center gap-1.5 rounded-lg bg-blue-500 hover:bg-blue-400 active:bg-blue-600 shadow-[0_0_25px_-5px_rgba(59,130,246,0.65)] hover:shadow-[0_0_32px_-2px_rgba(59,130,246,0.8)] transition text-xs font-bold text-white shrink-0">
                                        <i class="fa-solid fa-cloud-arrow-up text-[12px]"></i>
                                        {{ $rsAda ? 'TIMPAA FILE' : 'UPLOAD' }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($jurusanList as $j)
            <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $j]) }}"
               class="group rounded-2xl border p-5 transition {{ $jurusan === $j ? 'bg-emerald-500/15 border-emerald-400/25' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                <div class="text-lg font-semibold">{{ $j }}</div>
                <div class="mt-1 text-sm text-emerald-100/70">Klik untuk pilih semester</div>
            </a>
        @endforeach
    </div>

    @if ($jurusan)
        <div class="mt-6 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <div class="text-lg font-semibold">{{ $jurusan }}</div>
                    <div class="text-sm text-emerald-100/70">Pilih semester untuk menampilkan mata kuliah.</div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
                        <i class="fa-solid fa-file-pdf"></i>
                        <span class="text-sm font-medium">PDF</span>
                    </a>
                    <a href="{{ route('admin.mata-kuliah.index') }}" class="h-10 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        Reset
                    </a>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (range(1, 8) as $s)
                    <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $jurusan, 'semester' => $s]) }}"
                       class="h-10 px-4 inline-flex items-center justify-center rounded-xl border transition {{ (int) $semester === $s ? 'bg-emerald-500/15 border-emerald-400/25 hover:bg-emerald-500/20' : 'bg-white/5 border-white/10 hover:bg-white/10' }}">
                        Semester {{ $s }}
                    </a>
                @endforeach
            </div>

            @if ($semester)
                <div class="mt-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <form method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="hidden" name="jurusan" value="{{ $jurusan }}" />
                        <input type="hidden" name="semester" value="{{ $semester }}" />
                        <input name="q" value="{{ $q }}" class="w-full sm:max-w-md h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" placeholder="Cari kode / nama..." />
                        <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">Cari</button>
                        <a href="{{ route('admin.mata-kuliah.index', ['jurusan' => $jurusan, 'semester' => $semester]) }}" class="h-11 px-4 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            Reset
                        </a>
                    </form>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.mata-kuliah.export-pdf', array_filter(['q' => $q, 'jurusan' => $jurusan, 'semester' => $semester])) }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 transition text-emerald-100">
                            <i class="fa-solid fa-file-pdf"></i>
                            <span class="text-sm font-medium">PDF</span>
                        </a>
                        <a href="{{ route('admin.mata-kuliah.create', ['jurusan' => $jurusan, 'semester' => $semester]) }}" class="h-11 px-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                            <i class="fa-solid fa-plus"></i>
                            <span class="text-sm font-medium">Tambah MK</span>
                        </a>
                    </div>
                </div>

                <div x-data="{
                    selectedIds: [],
                    onBulkChange(e) {
                        const t = e.target;
                        if (!(t instanceof HTMLInputElement)) return;

                        if (t.dataset.bulk === 'select-all') {
                            const rows = Array.from(this.$el.querySelectorAll('input[data-bulk=row]'));
                            rows.forEach((cb) => { cb.checked = t.checked; });
                        }

                        this.syncSelectedFromDom();
                    },
                    syncSelectedFromDom() {
                        const rows = Array.from(this.$el.querySelectorAll('input[data-bulk=row]'));
                        this.selectedIds = rows.filter((cb) => cb.checked).map((cb) => cb.value);

                        const selectAll = this.$el.querySelector('input[data-bulk=select-all]');
                        if (!selectAll) return;

                        if (rows.length === 0) {
                            selectAll.checked = false;
                            selectAll.indeterminate = false;
                            return;
                        }

                        const checkedCount = rows.filter((cb) => cb.checked).length;
                        selectAll.checked = checkedCount === rows.length;
                        selectAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
                    },
                }">
                    <form x-ref="bulkForm" method="POST" action="{{ route('admin.mata-kuliah.bulk-delete') }}" @change="onBulkChange($event)"
                          data-confirm="Apakah kamu yakin ingin menghapus mata kuliah yang dipilih?">
                        @csrf
                        @method('DELETE')

                        <div class="flex items-center justify-between gap-3 mb-3 mt-5">
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
                                            <th class="text-left font-medium px-4 py-3 w-10">
                                                <input type="checkbox" data-bulk="select-all"
                                                       class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                                            </th>
                                            <th class="text-left font-medium px-4 py-3">Kode</th>
                                            <th class="text-left font-medium px-4 py-3">Nama</th>
                                            <th class="text-left font-medium px-4 py-3">SKS</th>
                                            <th class="text-left font-medium px-4 py-3">Dosen</th>
                                            <th class="text-right font-medium px-4 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/10">
                                        @forelse ($mataKuliah as $row)
                                            <tr class="hover:bg-white/5">
                                                <td class="px-4 py-3">
                                                    <input type="checkbox" name="ids[]" value="{{ $row->id }}" data-bulk="row"
                                                           class="h-4 w-4 rounded border-white/20 bg-white/5 text-emerald-500 focus:ring-emerald-500/40" />
                                                </td>
                                                <td class="px-4 py-3 font-medium">{{ $row->kode }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">{{ $row->nama }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">{{ $row->sks }}</td>
                                                <td class="px-4 py-3 text-emerald-100/80">
                                                    <div class="font-medium text-white">{{ $row->dosen?->nama ?? '-' }}</div>
                                                    @if ($row->dosen2?->nama)
                                                        <div class="text-xs text-emerald-100/70 mt-1">{{ $row->dosen2?->nama }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <a href="{{ route('admin.mata-kuliah.edit', $row) }}" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition" title="Edit">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </a>
                                                        <form method="POST" action="{{ route('admin.mata-kuliah.destroy', $row) }}" data-confirm="Apakah kamu yakin ingin menghapus mata kuliah ini?">
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
                                                <td colspan="6" class="px-4 py-10 text-center text-emerald-100/70">Belum ada mata kuliah untuk jurusan dan semester ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="mt-4">
                    {{ $mataKuliah->links() }}
                </div>
            @endif
        </div>
    @endif
</x-portal-layout>
