<x-portal-layout :title="'Detail Dosen - '.config('app.name')" subtitle="Detail Dosen">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div class="flex items-center gap-4">
            @if ($dosen->foto_path)
                <img src="{{ asset('storage/'.$dosen->foto_path) }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
            @else
                <div class="h-16 w-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-2xl font-semibold">
                    {{ mb_substr($dosen->nama, 0, 1) }}
                </div>
            @endif
            <div>
                <div class="text-xl font-semibold">{{ $dosen->nama }}</div>
                <div class="text-sm text-emerald-100/70">{{ $dosen->user?->email }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.dosen.edit', $dosen) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-pen"></i>
                Edit
            </a>
            <a href="{{ route('admin.dosen.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Profil</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">NIDN</div>
                    <div class="mt-1 font-medium">{{ $dosen->nidn }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">NUPTK</div>
                    <div class="mt-1 font-medium">{{ $dosen->nuptk ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Nomor HP</div>
                    <div class="mt-1 font-medium">{{ $dosen->nomor_hp ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:col-span-2">
                    <div class="text-emerald-100/70">Mata Kuliah</div>
                    <div class="mt-1 font-medium">{{ $dosen->mata_kuliah ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:col-span-2">
                    <div class="text-emerald-100/70">Alamat</div>
                    <div class="mt-1 font-medium whitespace-pre-line">{{ $dosen->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Akun Login</div>
            <div class="text-sm text-emerald-100/70">
                <div class="flex items-center justify-between gap-3 py-2 border-b border-white/10">
                    <span>Email</span>
                    <span class="font-medium text-white">{{ $dosen->user?->email }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 py-2">
                    <span>Password Default</span>
                    <span class="font-medium text-white">password</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.dosen.destroy', $dosen) }}" data-confirm="Apakah kamu yakin ingin menghapus dosen ini?">
                @csrf
                @method('DELETE')
                <button class="mt-5 w-full h-11 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition font-medium">
                    Hapus Dosen
                </button>
            </form>
        </div>
    </div>
</x-portal-layout>
