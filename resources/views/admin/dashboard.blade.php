<x-portal-layout
    :title="((auth()->user()->role === 'keuangan') ? 'Dashboard Keuangan' : (auth()->user()->role === 'akademik' ? 'Dashboard Akademik' : 'Dashboard Admin')) . ' - ' . config('app.name')"
    :subtitle="(auth()->user()->role === 'keuangan') ? 'Dashboard Keuangan' : (auth()->user()->role === 'akademik' ? 'Dashboard Akademik' : 'Dashboard Admin')"
>
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @if (in_array(auth()->user()->role, ['admin', 'akademik'], true))
            <div class="rounded-2xl bg-emerald-500/10 border border-emerald-400/15 p-5">
                <div class="text-emerald-100/70 text-sm">Total Mahasiswa</div>
                <div class="mt-2 text-3xl font-semibold">{{ number_format($totalMahasiswa) }}</div>
            </div>
            <div class="rounded-2xl bg-sky-500/10 border border-sky-400/15 p-5">
                <div class="text-sky-100/80 text-sm">Total Dosen</div>
                <div class="mt-2 text-3xl font-semibold">{{ number_format($totalDosen) }}</div>
            </div>
            <div class="rounded-2xl bg-amber-500/10 border border-amber-400/15 p-5">
                <div class="text-amber-100/80 text-sm">Total KRS</div>
                <div class="mt-2 text-3xl font-semibold">{{ number_format($totalKrs) }}</div>
            </div>
            <div class="rounded-2xl bg-violet-500/10 border border-violet-400/15 p-5">
                <div class="text-violet-100/80 text-sm">Total KHS</div>
                <div class="mt-2 text-3xl font-semibold">{{ number_format($totalKhs) }}</div>
            </div>
        @endif

        @if (auth()->user()->role === 'keuangan')
            <div class="rounded-2xl bg-emerald-500/10 border border-emerald-400/15 p-5">
                <div class="text-emerald-100/70 text-sm">Total Tagihan (Rp)</div>
                <div class="mt-2 text-2xl font-semibold">{{ number_format($totalBiaya, 0, ',', '.') }}</div>
            </div>

            <div class="rounded-2xl bg-sky-500/10 border border-sky-400/15 p-5">
                <div class="text-sky-100/80 text-sm">Total Dibayar (Rp)</div>
                <div class="mt-2 text-2xl font-semibold">{{ number_format($totalDibayar, 0, ',', '.') }}</div>
            </div>

            <div class="rounded-2xl bg-red-500/10 border border-red-400/15 p-5">
                <div class="text-red-100/80 text-sm">Sisa Piutang (Rp)</div>
                <div class="mt-2 text-2xl font-semibold">{{ number_format($totalBiaya - $totalDibayar, 0, ',', '.') }}</div>
            </div>

            <div class="rounded-2xl bg-violet-500/10 border border-violet-400/15 p-5">
                <div class="text-violet-100/80 text-sm">Target Mahasiswa</div>
                <div class="mt-2 text-3xl font-semibold">{{ number_format($totalMahasiswa) }}</div>
            </div>
        @endif
    </div>

    <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-4">
        <div class="xl:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-lg font-semibold">Mahasiswa per Angkatan</div>
                    <div class="text-sm text-emerald-100/70">Statistik jumlah mahasiswa berdasarkan angkatan.</div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-72">
                    <canvas id="angkatanChart" class="!w-full !h-full"></canvas>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <div class="text-lg font-semibold">Komposisi User</div>
                    <div class="text-sm text-emerald-100/70">Distribusi role pengguna sistem.</div>
                </div>
            </div>
            <div class="mt-4">
                <div class="h-72">
                    <canvas id="roleChart" class="!w-full !h-full"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const angkatanLabels = @json($chartLabels);
        const angkatanValues = @json($chartValues);

        const roleLabels = @json($roleLabels);
        const roleValues = @json($roleValues);

        const ctx = document.getElementById('angkatanChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: angkatanLabels,
                datasets: [{
                    label: 'Mahasiswa',
                    data: angkatanValues,
                    borderRadius: 10,
                    backgroundColor: 'rgba(16, 185, 129, 0.55)',
                    borderColor: 'rgba(16, 185, 129, 0.95)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)'
                        }
                    }
                }
            }
        });

        const roleCtx = document.getElementById('roleChart');
        new Chart(roleCtx, {
            type: 'doughnut',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleValues,
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.6)',
                        'rgba(16, 185, 129, 0.4)',
                        'rgba(16, 185, 129, 0.2)'
                    ],
                    borderColor: 'rgba(16, 185, 129, 0.8)',
                    borderWidth: 1,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            padding: 20,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</x-portal-layout>
