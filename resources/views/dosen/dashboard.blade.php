<x-portal-layout :title="'Dashboard Dosen - '.config('app.name')" subtitle="Dashboard Dosen">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center gap-4">
                @if ($dosen?->foto_path)
                    <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
                @else
                    <div class="h-14 w-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-xl font-semibold">
                        {{ mb_substr($dosen?->nama ?? auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-semibold">Selamat datang</div>
                    <div class="mt-1 text-emerald-100/70 text-sm">
                        {{ $dosen?->nama ?? auth()->user()->name }}
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-sky-500/10 border border-sky-400/15 p-5">
                    <div class="text-emerald-100/70 text-sm">Total Mahasiswa</div>
                    <div class="mt-2 text-3xl font-semibold">{{ number_format($totalMahasiswa) }}</div>
                </div>
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-400/15 p-5">
                    <div class="text-emerald-100/80 text-sm">KRS Approved (Untuk Nilai)</div>
                    <div class="mt-2 text-3xl font-semibold">{{ number_format($krsUntukNilai) }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold">Informasi</div>
            <div class="mt-4 space-y-3 text-sm text-emerald-100/75">
                <div class="flex items-center justify-between">
                    <span>NIK</span>
                    <span class="font-medium text-white">{{ $dosen?->nik ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>NUPTK</span>
                    <span class="font-medium text-white">{{ $dosen?->nuptk ?: ($dosen?->nidn ?? '-') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Nomor SK</span>
                    <span class="font-medium text-white">{{ $dosen?->nomor_sk ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Status Dosen</span>
                    <span class="font-medium text-white">{{ ($dosen?->status_dosen ?? 'aktif') === 'tidak aktif' ? 'Tidak Aktif' : 'Aktif' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Mata Kuliah</span>
                    <span class="font-medium text-white">{{ $dosen?->mata_kuliah ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-lg font-semibold">Grafik KRS Approved per Semester</div>
                <div class="text-sm text-emerald-100/70">KRS approved yang terkait mata kuliah dosen (untuk input nilai).</div>
            </div>
        </div>
        <div class="mt-4 h-72">
            <canvas id="krsApprovedChart" class="!w-full !h-full"></canvas>
        </div>
    </div>

    @if(!empty($informasiAktif) && !empty($informasiAktif->gambar_url))
        <div id="informasi-modal"
             data-info-id="{{ $informasiAktif->id }}"
             data-info-key="info_dosen_{{ $informasiAktif->id }}_{{ $informasiAktif->updated_at?->timestamp ?? $informasiAktif->created_at?->timestamp }}"
             class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
            <div class="relative w-full max-w-2xl">
                <button type="button" id="informasi-close"
                    class="absolute -top-3 -right-3 z-10 h-11 w-11 inline-flex items-center justify-center rounded-full bg-rose-600 hover:bg-rose-500 text-white shadow-lg border border-white/10 transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>

                <div class="rounded-3xl bg-[#062b22] border border-white/10 overflow-hidden shadow-2xl">
                    <div class="flex items-start justify-between gap-4 px-5 pt-5 pb-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 text-xs text-amber-300/90 font-semibold uppercase tracking-wider">
                                <i class="fa-solid fa-bullhorn"></i>
                                Informasi Dosen
                            </div>
                            <div class="mt-1 text-lg sm:text-xl font-bold break-words text-white">
                                {{ $informasiAktif->judul }}
                            </div>
                            @if(!empty($informasiAktif->deskripsi))
                                <div class="mt-2 text-sm text-emerald-100/80 whitespace-pre-wrap">
                                    {{ $informasiAktif->deskripsi }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="px-5 pb-5">
                        <div class="rounded-2xl overflow-hidden border border-white/10 bg-black/30">
                            <img src="{{ $informasiAktif->gambar_url }}" alt="{{ $informasiAktif->judul }}"
                                 class="w-full h-auto max-h-[65vh] object-contain" />
                        </div>

                        <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs text-emerald-100/60">
                                @if($informasiAktif->created_at) Diterbitkan: {{ $informasiAktif->created_at->format('d M Y') }} @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="informasi-copy"
                                    class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-sky-500/15 hover:bg-sky-500/25 border border-sky-500/25 text-sky-100 text-xs font-medium transition">
                                    <i class="fa-solid fa-link"></i>
                                    Salin Link
                                </button>
                                <a target="_blank" rel="noopener"
                                   href="https://api.whatsapp.com/send?text={{ urlencode($informasiAktif->judul . ' - ' . $informasiAktif->share_url) }}"
                                   class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-green-600/90 hover:bg-green-500 border border-green-400/30 text-white text-xs font-medium transition">
                                    <i class="fa-brands fa-whatsapp"></i>
                                    Share
                                </a>
                                <a href="{{ route('dosen.informasi.index') }}"
                                   class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/10 text-emerald-100 text-xs font-medium transition">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    Lihat Semua
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($chartLabels);
        const values = @json($chartValues);

        const ctx = document.getElementById('krsApprovedChart');
        if (labels.length > 0) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Approved',
                        data: values,
                        borderRadius: 10,
                        backgroundColor: 'rgba(16, 185, 129, 0.55)',
                        borderColor: 'rgba(16, 185, 129, 0.95)',
                        borderWidth: 1
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            ticks: { color: 'rgba(236, 253, 245, 0.85)' },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        },
                        y: {
                            ticks: { color: 'rgba(236, 253, 245, 0.85)' },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        }
                    }
                }
            });
        } else {
            ctx.parentElement.innerHTML = '<div class="text-sm text-emerald-100/70">Tidak ada data approved untuk ditampilkan.</div>';
        }

        (function () {
            const modal = document.getElementById('informasi-modal');
            if (!modal) return;
            const key = modal.getAttribute('data-info-key') || ('info_dosen_' + (modal.getAttribute('data-info-id') || 'x'));
            const shareUrl = @json($informasiAktif?->share_url ?? '');

            const show = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            const hide = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                try { localStorage.setItem(key, '1'); } catch (e) {}
            };

            try {
                if (localStorage.getItem(key) !== '1') show();
            } catch (e) {
                show();
            }

            document.getElementById('informasi-close')?.addEventListener('click', hide);
            modal.addEventListener('click', function (e) {
                if (e.target === modal) hide();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) hide();
            });

            const copyBtn = document.getElementById('informasi-copy');
            copyBtn?.addEventListener('click', function () {
                const done = () => {
                    const orig = copyBtn.innerHTML;
                    copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
                    setTimeout(() => { copyBtn.innerHTML = orig; }, 1800);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(shareUrl).then(done).catch(function () {
                        const ta = document.createElement('textarea');
                        ta.value = shareUrl;
                        document.body.appendChild(ta);
                        ta.select();
                        try { document.execCommand('copy'); done(); } catch (err) {}
                        document.body.removeChild(ta);
                    });
                }
            });
        })();
    </script>
</x-portal-layout>
