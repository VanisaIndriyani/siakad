<x-portal-layout :title="'Absensi - '.config('app.name')" subtitle="Absensi Mahasiswa">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Absensi</div>
            <div class="text-sm text-emerald-100/70">Lihat rekap absensi per mata kuliah.</div>
        </div>
        <form method="GET" action="{{ route('mahasiswa.absensi.index') }}" class="flex items-center gap-2">
            <select name="semester" class="h-10 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400">
                @foreach (range(1, 8) as $s)
                    <option value="{{ $s }}" @selected((int) $semester === $s) class="text-black">Semester {{ $s }}</option>
                @endforeach
            </select>
            <button class="h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Lihat
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        @forelse ($rows as $row)
            <a href="{{ route('mahasiswa.absensi.show', [$row['mataKuliahId'], $row['semester']]) }}" class="group rounded-2xl bg-white/5 border border-white/10 p-5 hover:bg-white/10 transition">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="text-sm text-emerald-100/70">Mata Kuliah</div>
                        <div class="mt-1 font-semibold">{{ $row['mataKuliah']?->kode }} - {{ $row['mataKuliah']?->nama }}</div>
                        <div class="mt-1 text-sm text-emerald-100/70">Total pertemuan: <span class="text-emerald-100/90 font-medium">{{ $row['total'] }}</span></div>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-400/20 flex items-center justify-center group-hover:bg-emerald-500/15 transition">
                        <i class="fa-solid fa-chevron-right text-emerald-200"></i>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
                    <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                        <div class="text-emerald-100/70 text-xs">Hadir</div>
                        <div class="font-semibold">{{ $row['counts']['hadir'] }}</div>
                    </div>
                    <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                        <div class="text-emerald-100/70 text-xs">Alpha</div>
                        <div class="font-semibold">{{ $row['counts']['alpha'] }}</div>
                    </div>
                    <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                        <div class="text-emerald-100/70 text-xs">Izin</div>
                        <div class="font-semibold">{{ $row['counts']['izin'] }}</div>
                    </div>
                    <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                        <div class="text-emerald-100/70 text-xs">Sakit</div>
                        <div class="font-semibold">{{ $row['counts']['sakit'] }}</div>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 text-center text-emerald-100/70 lg:col-span-3">
                Belum ada data absensi untuk semester ini.
            </div>
        @endforelse
    </div>
</x-portal-layout>

