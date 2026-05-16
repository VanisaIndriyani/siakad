<x-portal-layout :title="'Bimbingan Skripsi - '.config('app.name')" subtitle="Skripsi">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <style>
        .chat-wrap { max-width: 920px; margin: 0 auto; }
        .chat-card { background: #ffffff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 18px; padding: 16px; color: #111827; }
        .chat-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .chat-head .title { font-size: 18px; font-weight: 900; margin: 0; }
        .chat-head .sub { margin-top: 4px; font-size: 13px; font-weight: 700; color: rgba(17, 24, 39, 0.55); }
        .chat-back { height: 40px; padding: 0 14px; border-radius: 999px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; color: #111827; font-weight: 900; font-size: 13px; }
        .chat-stream { margin-top: 14px; border-radius: 14px; border: 1px solid rgba(17, 24, 39, 0.10); background: #f9fafb; padding: 12px; max-height: 58vh; overflow: auto; }
        .chat-row { display: flex; margin: 8px 0; }
        .chat-row.me { justify-content: flex-end; }
        .chat-bubble { max-width: 78%; border-radius: 14px; padding: 10px 12px; border: 1px solid rgba(17, 24, 39, 0.12); background: #ffffff; }
        .chat-row.me .chat-bubble { background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.25); }
        .chat-meta { font-size: 11px; font-weight: 800; color: rgba(17, 24, 39, 0.55); margin-bottom: 6px; display: flex; gap: 8px; flex-wrap: wrap; }
        .chat-text { font-size: 13px; font-weight: 700; color: rgba(17, 24, 39, 0.88); white-space: pre-line; }
        .chat-form { margin-top: 12px; display: flex; gap: 10px; align-items: flex-end; }
        .chat-input { flex: 1; min-height: 44px; max-height: 120px; resize: vertical; border-radius: 14px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; padding: 12px 12px; font-size: 13px; font-weight: 700; color: #111827; outline: none; }
        .chat-input:disabled { background: #f3f4f6; color: rgba(17, 24, 39, 0.45); }
        .chat-send { height: 44px; padding: 0 16px; border-radius: 14px; border: 1px solid rgba(16, 185, 129, 0.25); background: linear-gradient(to right, #059669, #10b981); color: #fff; font-weight: 900; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .chat-send:disabled { opacity: 0.55; cursor: not-allowed; }
        .chat-warn { margin-top: 14px; border-radius: 14px; border: 1px solid rgba(245, 158, 11, 0.25); background: rgba(245, 158, 11, 0.10); padding: 12px 14px; font-weight: 800; color: rgba(120, 53, 15, 0.95); }
        .chat-empty { padding: 18px; text-align: center; color: rgba(17, 24, 39, 0.55); font-weight: 900; }
    </style>

    <div class="chat-wrap">
        <div class="chat-card">
            <div class="chat-head">
                <div>
                    <div class="title">Bimbingan Skripsi</div>
                    <div class="sub">
                        {{ $skripsi->judul }} • Pembimbing:
                        {{ $skripsi->dosenPembimbing?->nama ?: '-' }}
                        @if ($skripsi->dosenPembimbing2?->nama)
                            , {{ $skripsi->dosenPembimbing2?->nama }}
                        @endif
                    </div>
                </div>
                <div style="display:flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="{{ route('mahasiswa.skripsi.revisi', $skripsi) }}" class="chat-back">
                        <i class="fa-solid fa-list-check"></i>
                        Revisi
                    </a>
                    <a href="{{ route('mahasiswa.skripsi.show', $skripsi) }}" class="chat-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            @if (! $skripsi->dosen_pembimbing_id && ! $skripsi->dosen_pembimbing_id_2)
                <div class="chat-warn">
                    Bimbingan belum bisa dimulai karena pembimbing belum ditetapkan oleh Admin/Prodi.
                </div>
            @endif

            <div id="chatStream" class="chat-stream">
                @forelse ($skripsi->messages->sortBy('id') as $msg)
                    @php
                        $isMe = (int) $msg->sender_user_id === (int) auth()->id();
                    @endphp
                    <div class="chat-row {{ $isMe ? 'me' : '' }}">
                        <div class="chat-bubble">
                            <div class="chat-meta">
                                <span>{{ $msg->sender?->name ?: 'User' }}</span>
                                <span>•</span>
                                <span>{{ $msg->created_at?->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="chat-text">{{ $msg->pesan }}</div>
                        </div>
                    </div>
                @empty
                    <div class="chat-empty">Belum ada pesan bimbingan.</div>
                @endforelse
            </div>

            <form method="POST" action="{{ route('mahasiswa.skripsi.bimbingan.store', $skripsi) }}" class="chat-form">
                @csrf
                <textarea name="pesan" rows="2" {{ ($skripsi->dosen_pembimbing_id || $skripsi->dosen_pembimbing_id_2) ? '' : 'disabled' }}
                          class="chat-input"
                          placeholder="Tulis pesan...">{{ old('pesan') }}</textarea>
                <button {{ ($skripsi->dosen_pembimbing_id || $skripsi->dosen_pembimbing_id_2) ? '' : 'disabled' }} class="chat-send">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim
                </button>
            </form>

            @error('pesan')
                <div style="margin-top: 8px; color: #dc2626; font-size: 12px; font-weight: 800;">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <script>
        (function () {
            const el = document.getElementById('chatStream');
            if (!el) return;
            el.scrollTop = el.scrollHeight;
        })();
    </script>
</x-portal-layout>
