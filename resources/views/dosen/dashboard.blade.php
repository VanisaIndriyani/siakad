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
                    <span>NIDN</span>
                    <span class="font-medium text-white">{{ $dosen?->nidn ?? '-' }}</span>
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
    </script>
</x-portal-layout>
