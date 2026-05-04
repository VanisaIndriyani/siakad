<x-portal-layout :title="'Tambah Dosen - '.config('app.name')" subtitle="Tambah Dosen">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Dosen</div>
            <div class="text-sm text-emerald-100/70">Lengkapi data dosen.</div>
        </div>
        <a href="{{ route('admin.dosen.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.dosen.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf
        @include('admin.dosen.partials.form')
        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
