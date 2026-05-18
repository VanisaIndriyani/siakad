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
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; padding: 0 !important; }
            .chat-stream { max-height: none !important; overflow: visible !important; }
            .chat-card { border: none !important; background: transparent !important; padding: 0 !important; }
            .chat-wrap { max-width: none !important; margin: 0 !important; padding: 0 !important; border: none !important; background: transparent !important; box-shadow: none !important; }
            .chat-head { background: transparent !important; color: black !important; border-bottom: 1px solid #000 !important; padding: 10px 0 !important; }
            .chat-item { page-break-inside: avoid; border: 1px solid #ddd !important; margin-bottom: 10px !important; }
            .chat-item-me { background: #f9fafb !important; }
            .chat-item-other { background: white !important; }
        }
        .print-only { display: none; }
    </style>

    <div class="chat-wrap">
        {{-- Print Only Kop Surat --}}
        <div class="print-only" style="margin-bottom: 30px;">
            <table style="width: 100%; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 2px;">
                <tr>
                    <td style="width: 110px; vertical-align: middle;">
                        @php
                            $logoPath = public_path('img/lo.jpeg');
                            $logoBase64 = '';
                            if (file_exists($logoPath)) {
                                $logoData = file_get_contents($logoPath);
                                $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                                $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                            }
                        @endphp
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" style="width: 100px; height: auto;">
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <div style="font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1;">INSTITUT AGAMA ISLAM</div>
                        <div style="font-size: 24px; font-weight: 900; margin: 2px 0; line-height: 1;">DARUD DA'WAH WAL IRSYAD</div>
                        <div style="font-size: 18px; font-weight: 800; margin: 0; line-height: 1.1;">SIDENRENG RAPPANG</div>
                        <div style="font-size: 10px; font-weight: 700; margin-top: 3px;">TERAKREDITASI INSTITUSI • SK : 576/SK/BAN-PT/Akred/PT/IV/2021</div>
                        <div style="font-size: 10px; margin: 2px 0;">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
                        <div style="font-size: 10px; margin: 2px 0;">E-mail : iaiddisidrap@gmail.com Website : www.yppddisrapp.ac.id</div>
                    </td>
                    <td style="width: 90px;"></td>
                </tr>
            </table>
            <div style="border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px;"></div>
            <div style="text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase;">Riwayat Bimbingan Skripsi</div>
            <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse; font-size: 11px;">
                <tr>
                    <td style="width: 120px; font-weight: bold; padding: 4px 0;">Nama Mahasiswa</td>
                    <td style="padding: 4px 0;">: {{ $skripsi->mahasiswa?->nama_lengkap }} ({{ $skripsi->mahasiswa?->npm }})</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 4px 0;">Judul Skripsi</td>
                    <td style="padding: 4px 0;">: {{ $skripsi->judul }}</td>
                </tr>
            </table>
        </div>
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
                <div style="display:flex; gap: 10px; align-items: center; flex-wrap: wrap;" class="no-print">
                    <a href="{{ route('mahasiswa.skripsi.bimbingan.pdf', $skripsi) }}" class="chat-back">
                        <i class="fa-solid fa-file-pdf" style="color: #f43f5e;"></i>
                        PDF
                    </a>
                    <button type="button" onclick="window.print()" class="chat-back">
                        <i class="fa-solid fa-print" style="color: #10b981;"></i>
                        Print
                    </button>
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

            <form method="POST" action="{{ route('mahasiswa.skripsi.bimbingan.store', $skripsi) }}" class="chat-form no-print">
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
