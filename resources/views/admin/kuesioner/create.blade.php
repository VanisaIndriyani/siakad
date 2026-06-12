<x-portal-layout :title="'Tambah Pertanyaan Kuesioner - '.config('app.name')" subtitle="Tambah Pertanyaan Kuesioner">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Tambah Pertanyaan Kuesioner</div>
            <div class="text-sm text-emerald-100/70">Tambahkan pertanyaan baru yang akan tampil pada form kuesioner mahasiswa.</div>
        </div>
        <a href="{{ route('admin.kuesioner.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <form method="POST" action="{{ route('admin.kuesioner.store') }}" class="rounded-2xl bg-white/5 border border-white/10 p-5">
        @csrf

        <div class="space-y-4">
            <div>
                <label class="text-sm text-emerald-100/80">Pertanyaan</label>
                <textarea name="question" rows="4" class="mt-2 w-full rounded-2xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" required>{{ old('question') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-emerald-100/80">Urutan Tampil</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:border-emerald-400 focus:ring-emerald-400" />
                </div>
                <div class="flex items-center gap-3 pt-8">
                    <input type="hidden" name="is_active" value="0" />
                    <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', '1') == '1') class="h-4 w-4 rounded border-white/10 bg-white/5 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0" />
                    <label for="is_active" class="text-sm text-emerald-100/80">Aktifkan pertanyaan ini</label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end">
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-medium">
                Simpan
            </button>
        </div>
    </form>
</x-portal-layout>
