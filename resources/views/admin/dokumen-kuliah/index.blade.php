<x-portal-layout :title="'Upload SK & Roster - '.config('app.name')" subtitle="Upload SK Mengajar & Roster Kuliah Per Program Studi">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-col gap-2">
            <div class="text-xl font-semibold">Upload SK Mengajar & Roster Kuliah</div>
            <div class="text-sm text-emerald-100/70">Upload 1x per prodi → otomatis muncul untuk SEMUA dosen di prodi tersebut di halaman Input Nilai Dosen.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mata-kuliah.index') }}" class="h-11 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Mata Kuliah</span>
            </a>
        </div>
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
                    $listMk = $listMkPerProdi[$j] ?? collect();
                    $countMk = $listMk->count();
                    $groupedBySem = $listMk->groupBy('semester')->sortKeys();
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
                            <div class="min-w-0">
                                <div class="text-base font-bold text-emerald-100 tracking-tight leading-snug break-words">
                                    {{ $j }}
                                </div>
                                <div class="mt-1 text-[11px] text-emerald-100/60 leading-snug">
                                    <i class="fa-solid fa-cloud-arrow-up mr-1"></i>
                                    Upload 1x untuk seluruh dosen prodi ini
                                </div>
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

                            <form x-show="showFormSk" x-transition method="POST" action="{{ route('admin.dokumen-kuliah.upload-sk', $j) }}" enctype="multipart/form-data">
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
                                        Lihat PDF
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

                            <form x-show="showFormRoster" x-transition method="POST" action="{{ route('admin.dokumen-kuliah.upload-roster', $j) }}" enctype="multipart/form-data">
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

    <div class="mb-6 rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-400/25 inline-flex items-center justify-center">
                <i class="fa-solid fa-lightbulb text-amber-300"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-semibold text-amber-100">Cara Kerja Upload ini</div>
                <div class="text-xs text-emerald-100/70 mt-1.5 leading-relaxed space-y-1">
                    <p><i class="fa-solid fa-check text-emerald-400 mr-1"></i>Upload 1x per prodi → otomatis muncul untuk SEMUA dosen di prodi tersebut di halaman Input Nilai Dosen.</p>
                    <p><i class="fa-solid fa-check text-emerald-400 mr-1"></i>Cukup upload disini, nanti otomatis menyesuaikan prodi masing-masing dosen tanpa perlu setting per-dosen.</p>
                    <p><i class="fa-solid fa-check text-emerald-400 mr-1"></i>Jika ingin menimpa file lama, klik tombol <span class="font-semibold">"Ganti / Upload Ulang File"</span> lalu upload file baru.</p>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
