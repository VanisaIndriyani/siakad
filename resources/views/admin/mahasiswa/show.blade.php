<x-portal-layout :title="'Detail Mahasiswa - '.config('app.name')" subtitle="Detail Mahasiswa">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div class="flex items-center gap-4">
            @if ($mahasiswa->foto_path)
                <img src="{{ asset('storage/'.$mahasiswa->foto_path) }}" class="h-16 w-16 rounded-2xl object-cover ring-1 ring-white/10" alt="Foto" />
            @else
                <div class="h-16 w-16 rounded-2xl bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-2xl font-semibold">
                    {{ mb_substr($mahasiswa->nama_lengkap, 0, 1) }}
                </div>
            @endif
            <div>
                <div class="text-xl font-semibold">{{ $mahasiswa->nama_lengkap }}</div>
                <div class="text-sm text-emerald-100/70">{{ $mahasiswa->user?->email }}</div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mahasiswa.edit', $mahasiswa) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-pen"></i>
                Edit
            </a>
            <a href="{{ route('admin.mahasiswa.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Biodata</div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">NPM</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->npm }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">NIK</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->nik ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Tempat, Tgl Lahir</div>
                    <div class="mt-1 font-medium">
                        {{ $mahasiswa->tempat_lahir ?? '-' }}
                        @if ($mahasiswa->tanggal_lahir) - {{ $mahasiswa->tanggal_lahir->format('d/m/Y') }} @endif
                    </div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Nomor Telp</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->nomor_telp ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Angkatan</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->angkatan ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Program Studi</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->program_studi ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Asal Sekolah</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->asal_sekolah ?? '-' }}</div>
                </div>
                <div class="rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Status</div>
                    <div class="mt-1 font-medium">{{ $mahasiswa->status_mahasiswa }}</div>
                </div>
                <div class="sm:col-span-2 rounded-xl bg-white/5 border border-white/10 p-4">
                    <div class="text-emerald-100/70">Alamat</div>
                    <div class="mt-1 font-medium whitespace-pre-line">{{ $mahasiswa->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="text-lg font-semibold mb-4">Akun Login</div>
            <div class="text-sm text-emerald-100/70">
                <div class="flex items-center justify-between gap-3 py-2 border-b border-white/10">
                    <span>Email</span>
                    <span class="font-medium text-white">{{ $mahasiswa->user?->email }}</span>
                </div>
                <div class="flex items-center justify-between gap-3 py-2">
                    <span>Password Default</span>
                    <span class="font-medium text-white">password</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.mahasiswa.destroy', $mahasiswa) }}" data-confirm="Apakah kamu yakin ingin menghapus mahasiswa ini?">
                @csrf
                @method('DELETE')
                <button class="mt-5 w-full h-11 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/20 transition font-medium">
                    Hapus Mahasiswa
                </button>
            </form>
        </div>
    </div>
</x-portal-layout>
