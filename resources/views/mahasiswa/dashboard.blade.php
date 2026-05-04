<x-portal-layout :title="'Dashboard Mahasiswa - '.config('app.name')" subtitle="Dashboard Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-4">
            <div class="flex items-center gap-4">
                @if ($mahasiswa?->foto_path)
                    <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="h-14 w-14 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
                @else
                    <div class="h-14 w-14 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-xl font-semibold">
                        {{ mb_substr($mahasiswa?->nama_lengkap ?? auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-semibold">Selamat datang</div>
                    <div class="mt-1 text-emerald-100/70 text-sm">
                        {{ $mahasiswa?->nama_lengkap ?? auth()->user()->name }}
                    </div>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                <div class="rounded-2xl bg-sky-500/10 border border-sky-400/15 p-4 min-h-24 flex flex-col justify-between">
                    <div class="text-emerald-100/70 text-sm">Total KRS</div>
                    <div class="mt-2 text-2xl font-semibold">{{ number_format($totalKrs) }}</div>
                </div>
                <div class="rounded-2xl bg-violet-500/10 border border-violet-400/15 p-4 min-h-24 flex flex-col justify-between">
                    <div class="text-violet-100/80 text-sm">Total KHS</div>
                    <div class="mt-2 text-2xl font-semibold">{{ number_format($totalKhs) }}</div>
                </div>
                <div class="rounded-2xl bg-amber-500/10 border border-amber-400/15 p-4 min-h-24 flex flex-col justify-between">
                    <div class="text-amber-100/80 text-sm">KRS Pending</div>
                    <div class="mt-2 text-2xl font-semibold">{{ number_format($krsStatusCounts['pending'] ?? 0) }}</div>
                </div>
                <div class="rounded-2xl bg-emerald-500/10 border border-emerald-400/15 p-4 min-h-24 flex flex-col justify-between">
                    <div class="text-emerald-100/80 text-sm">KRS Approved</div>
                    <div class="mt-2 text-2xl font-semibold">{{ number_format($krsStatusCounts['approved'] ?? 0) }}</div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-4">
                <div class="text-lg font-semibold">Ringkasan</div>
                <div class="mt-4 space-y-3 text-sm text-emerald-100/75">
                    <div class="flex items-center justify-between">
                        <span>NPM</span>
                        <span class="font-medium text-white">{{ $mahasiswa?->npm ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Angkatan</span>
                        <span class="font-medium text-white">{{ $mahasiswa?->angkatan ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Prodi</span>
                        <span class="font-medium text-white">{{ $mahasiswa?->program_studi ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>IPS Terakhir</span>
                        <span class="font-medium text-white">{{ $latestKhs?->ips ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>IPK Terakhir</span>
                        <span class="font-medium text-white">{{ $latestKhs?->ipk ?? '-' }}</span>
                    </div>
                </div>
            </div>

         
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white/5 border border-white/10 p-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <div class="text-lg font-semibold">Grafik IPS per Semester</div>
                <div class="text-sm text-emerald-100/70">Menampilkan IPS yang sudah diinput dosen.</div>
            </div>
        </div>
        <div class="mt-4 h-72">
            <canvas id="ipsChart" class="!w-full !h-full"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const labels = @json($chartLabels);
        const values = @json($chartValues);

        const ctx = document.getElementById('ipsChart');
        if (labels.length > 0) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'IPS',
                        data: values,
                        tension: 0.35,
                        fill: true,
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        borderColor: 'rgba(16, 185, 129, 0.95)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(16, 185, 129, 0.95)'
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            ticks: { color: 'rgba(236, 253, 245, 0.85)' },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        },
                        y: {
                            suggestedMin: 0,
                            suggestedMax: 4,
                            ticks: { color: 'rgba(236, 253, 245, 0.85)' },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        }
                    }
                }
            });
        } else {
            ctx.parentElement.innerHTML = '<div class="text-sm text-emerald-100/70">Belum ada data IPS untuk ditampilkan.</div>';
        }

        const krsStatusLabels = ['Pending', 'Approved', 'Rejected'];
        const krsStatusValues = [
            @json((int) ($krsStatusCounts['pending'] ?? 0)),
            @json((int) ($krsStatusCounts['approved'] ?? 0)),
            @json((int) ($krsStatusCounts['rejected'] ?? 0)),
        ];

        const krsCtx = document.getElementById('krsStatusChart');
        if (krsStatusValues.reduce((a, b) => a + b, 0) > 0) {
            new Chart(krsCtx, {
                type: 'doughnut',
                data: {
                    labels: krsStatusLabels,
                    datasets: [{
                        data: krsStatusValues,
                        backgroundColor: [
                            'rgba(234, 179, 8, 0.55)',
                            'rgba(16, 185, 129, 0.75)',
                            'rgba(239, 68, 68, 0.55)',
                        ],
                        borderColor: 'rgba(5, 46, 35, 1)',
                        borderWidth: 2,
                    }]
                },
                options: {
                    animation: false,
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: 'rgba(236, 253, 245, 0.85)',
                                boxWidth: 12,
                                boxHeight: 12,
                            }
                        }
                    }
                }
            });
        } else {
            krsCtx.parentElement.innerHTML = '<div class="text-sm text-emerald-100/70">Belum ada KRS untuk ditampilkan.</div>';
        }
    </script>
</x-portal-layout>
