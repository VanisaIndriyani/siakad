<x-portal-layout :title="'Bimbingan PPL - '.config('app.name')" subtitle="PPL">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
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
        .chat-send { height: 44px; padding: 0 16px; border-radius: 14px; border: 1px solid rgba(16, 185, 129, 0.25); background: linear-gradient(to right, #059669, #10b981); color: #fff; font-weight: 900; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
        .chat-empty { padding: 18px; text-align: center; color: rgba(17, 24, 39, 0.55); font-weight: 900; }
    </style>

    <div class="chat-wrap">
        <div class="chat-card">
            <div class="chat-head">
                <div>
                    <div class="title">Bimbingan PPL</div>
                    <div class="sub">{{ $ppl->mahasiswa?->nama_lengkap ?: '-' }} ({{ $ppl->mahasiswa?->npm ?: '-' }}) • {{ $ppl->instansi_nama }}</div>
                </div>
                <div style="display:flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <a href="{{ route('dosen.ppl.bimbingan.pdf', $ppl) }}" class="chat-back">
                        <i class="fa-solid fa-print"></i>
                        Print
                    </a>
                    <a href="{{ route('dosen.ppl.bimbingan.index') }}" class="chat-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div id="chatStream" class="chat-stream">
                @forelse ($ppl->messages->sortBy('id') as $msg)
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

            <form method="POST" action="{{ route('dosen.ppl.bimbingan.store', $ppl) }}" class="chat-form">
                @csrf
                <textarea name="pesan" rows="2" class="chat-input" placeholder="Tulis pesan...">{{ old('pesan') }}</textarea>
                <button class="chat-send">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim
                </button>
            </form>

            @error('pesan')
                <div style="margin-top: 8px; color: #dc2626; font-size: 12px; font-weight: 800;">{{ $message }}</div>
            @enderror

            <div style="margin-top: 14px; border-top: 1px solid rgba(17, 24, 39, 0.10); padding-top: 14px;">
                <div style="display:flex; align-items:center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                    <div style="font-size: 13px; font-weight: 900;">Laporan PPL</div>
                    <div style="font-size: 12px; color: rgba(17, 24, 39, 0.55); font-weight: 800;">Dosen & Mahasiswa bisa upload file laporan/hasil periksa.</div>
                </div>

                {{-- Form Upload Dosen --}}
                <form method="POST" action="{{ route('dosen.ppl.bimbingan.file.store', $ppl) }}" enctype="multipart/form-data" style="margin-top: 12px; padding: 12px; border-radius: 12px; background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.25);">
                    @csrf
                    <div style="font-size: 11px; font-weight: 800; color: #059669; text-transform: uppercase; margin-bottom: 8px;">Upload Hasil Periksa (Dosen)</div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;">
                        <div style="flex: 1; min-width: 200px;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: rgba(17, 24, 39, 0.6); margin-bottom: 4px;">Pilih File (PDF/DOC/DOCX)</label>
                            <input type="file" name="file" required style="width: 100%; font-size: 12px; color: #111827;">
                        </div>
                        <div style="flex: 2; min-width: 250px;">
                            <label style="display: block; font-size: 11px; font-weight: 700; color: rgba(17, 24, 39, 0.6); margin-bottom: 4px;">Keterangan</label>
                            <input type="text" name="keterangan" required placeholder="Contoh: Hasil periksa bab 1..." style="width: 100%; height: 34px; border-radius: 8px; border: 1px solid rgba(17, 24, 39, 0.12); padding: 0 10px; font-size: 12px; font-weight: 700;">
                        </div>
                        <button type="submit" class="chat-send" style="height: 34px; border-radius: 8px;">
                            <i class="fa-solid fa-upload"></i>
                            Upload
                        </button>
                    </div>
                </form>

                <div style="margin-top: 14px; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; min-width: 720px;">
                        <thead>
                            <tr style="background: #f3f4f6; border: 1px solid rgba(17, 24, 39, 0.10);">
                                <th style="text-align:left; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60); width: 44px;">No</th>
                                <th style="text-align:left; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60);">Nama File</th>
                                <th style="text-align:left; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60);">Oleh</th>
                                <th style="text-align:left; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60);">Keterangan</th>
                                <th style="text-align:left; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60); width: 140px;">Tanggal</th>
                                <th style="text-align:right; padding: 10px 12px; font-size: 11px; letter-spacing: 0.6px; text-transform: uppercase; color: rgba(17, 24, 39, 0.60); width: 210px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ppl->files->sortByDesc('id') as $i => $f)
                                <tr style="border-bottom: 1px solid rgba(17, 24, 39, 0.08);">
                                    <td style="padding: 10px 12px; font-weight: 900; color: rgba(17, 24, 39, 0.70);">{{ $i + 1 }}</td>
                                    <td style="padding: 10px 12px; font-weight: 900; color: rgba(17, 24, 39, 0.90);">{{ $f->file_name }}</td>
                                    <td style="padding: 10px 12px; font-weight: 700; color: rgba(17, 24, 39, 0.80);">
                                        @if($f->creator)
                                            <div style="color: #111827;">{{ $f->creator->name }}</div>
                                            <div style="font-size: 10px; color: rgba(17, 24, 39, 0.5);">{{ ucfirst($f->creator->role) }}</div>
                                        @else
                                            <span style="color: rgba(17, 24, 39, 0.4);">---</span>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 12px; font-weight: 700; color: rgba(17, 24, 39, 0.80); white-space: pre-line;">{{ $f->keterangan ?: '-' }}</td>
                                    <td style="padding: 10px 12px; font-weight: 800; color: rgba(17, 24, 39, 0.60);">{{ $f->created_at?->format('d/m/Y H:i') }}</td>
                                    <td style="padding: 10px 12px;">
                                        <div style="display:flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap;">
                                            <a href="{{ route('files.ppl.preview', $f) }}" target="_blank" class="chat-back" style="height: 34px; padding: 0 12px;">
                                                <i class="fa-solid fa-eye"></i>
                                                Preview
                                            </a>
                                            <a href="{{ route('files.ppl.download', $f) }}" class="chat-back" style="height: 34px; padding: 0 12px;">
                                                <i class="fa-solid fa-download"></i>
                                                Download
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 14px; text-align:center; color: rgba(17, 24, 39, 0.55); font-weight: 900;">Belum ada file diupload.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
