<x-portal-layout :title="'Penasehat Akademik - '.config('app.name')" subtitle="Penasehat Akademik">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Penasehat Akademik</div>
            <div class="mt-2 text-sm text-emerald-100/70">
                Bimbingan dan komunikasi dengan dosen penasehat akademik.
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('mahasiswa.penasehat-akademik.print') }}" target="_blank" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-print"></i>
                <span class="text-sm font-medium">Print</span>
            </a>
            <a href="{{ route('mahasiswa.penasehat-akademik.pdf') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span class="text-sm font-medium">PDF</span>
            </a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Riwayat Bimbingan</div>
                <div class="mt-3 space-y-3">
                    @forelse ($mahasiswa->bimbinganAkademikMessages->sortBy('id') as $msg)
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                            <div class="text-xs font-semibold text-emerald-100/60 mb-1">
                                {{ $msg->sender?->name ?: 'User' }} • {{ $msg->created_at?->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-emerald-100/90 whitespace-pre-line">{{ $msg->pesan }}</div>
                        </div>
                    @empty
                        <div class="text-center text-emerald-100/70 py-8">Belum ada pesan bimbingan.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm font-semibold">Kirim Pesan</div>
                <form method="POST" action="{{ route('mahasiswa.penasehat-akademik.message') }}" class="mt-3 space-y-3">
                    @csrf
                    <textarea name="pesan" rows="4" placeholder="Tulis pesan..." required
                              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">{{ old('pesan') }}</textarea>
                    <button class="h-11 w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                        Kirim Pesan
                    </button>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
                <div class="text-sm text-emerald-100/70">Dosen Penasehat Akademik</div>
                <div class="mt-1 font-medium">{{ $mahasiswa->dosenPenasehat?->nama ?: '-' }}</div>
                <div class="mt-2 text-sm text-emerald-100/70">
                    SK:
                    <span class="font-medium">{{ $mahasiswa->nomor_sk_penasehat ?: '-' }}</span>
                    @if ($mahasiswa->tanggal_sk_penasehat)
                        <span>•</span>
                        <span class="font-medium">{{ $mahasiswa->tanggal_sk_penasehat->format('d/m/Y') }}</span>
                    @endif
                </div>
                @if ($mahasiswa->sk_penasehat_path)
                    <div class="mt-3 flex items-center gap-2 flex-wrap">
                        <a href="{{ route('mahasiswa.penasehat-akademik.sk.preview') }}" target="_blank"
                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-eye"></i>
                            <span class="text-sm font-medium">Preview SK</span>
                        </a>
                        <a href="{{ route('mahasiswa.penasehat-akademik.sk.download') }}"
                           class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                            <i class="fa-solid fa-download"></i>
                            <span class="text-sm font-medium">Download</span>
                        </a>
                        <div class="text-sm text-emerald-100/70 truncate">
                            {{ $mahasiswa->sk_penasehat_name ?: basename($mahasiswa->sk_penasehat_path) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
