<x-portal-layout :title="'Bimbingan Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="min-w-0">
            <div class="text-xl font-semibold">Bimbingan Skripsi</div>
            <div class="mt-1 text-sm text-emerald-100/70 truncate">
                {{ $skripsi->mahasiswa?->nama_lengkap ?: '-' }} ({{ $skripsi->mahasiswa?->npm ?: '-' }}) • {{ $skripsi->judul }}
            </div>
        </div>
        <a href="{{ route('dosen.skripsi.bimbingan.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <div class="space-y-3">
            @forelse ($skripsi->messages->sortBy('id') as $msg)
                @php
                    $isMe = (int) $msg->sender_user_id === (int) auth()->id();
                @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl border px-4 py-3 text-sm whitespace-pre-line {{ $isMe ? 'bg-emerald-500/15 border-emerald-500/20 text-emerald-50' : 'bg-white/5 border-white/10 text-emerald-100/90' }}">
                        <div class="text-xs font-semibold mb-1 {{ $isMe ? 'text-emerald-100/80' : 'text-emerald-100/60' }}">
                            {{ $msg->sender?->name ?: 'User' }} • {{ $msg->created_at?->format('d/m/Y H:i') }}
                        </div>
                        {{ $msg->pesan }}
                    </div>
                </div>
            @empty
                <div class="text-center text-emerald-100/70 py-8">Belum ada pesan bimbingan.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-4 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="POST" action="{{ route('dosen.skripsi.bimbingan.store', $skripsi) }}" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <textarea name="pesan" rows="3"
                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                      placeholder="Tulis pesan...">{{ old('pesan') }}</textarea>
            <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium">
                Kirim
            </button>
        </form>
        @error('pesan')
            <div class="mt-2 text-sm text-red-200">{{ $message }}</div>
        @enderror
    </div>
</x-portal-layout>

