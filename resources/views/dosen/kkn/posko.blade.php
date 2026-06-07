<x-portal-layout :title="'Posko '.$posko->nama_posko.' - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include(auth()->user()->isDosen() ? 'dosen.partials.sidebar' : 'mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <a href="{{ auth()->user()->isDosen() ? route('dosen.kkn.index') : route('mahasiswa.kkn.index') }}" class="h-9 w-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <h1 class="text-2xl font-black text-white uppercase tracking-tight">{{ $posko->nama_posko }}</h1>
            </div>
            <div class="mt-2 flex items-center gap-2 text-xs font-bold text-emerald-100/40 uppercase tracking-widest ml-12">
                <i class="fa-solid fa-location-dot"></i>
                {{ $posko->lokasi ?: 'Lokasi segera ditentukan' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Sidebar Info (Left) -->
        <div class="lg:col-span-4 space-y-6 order-2 lg:order-1">
            <!-- DPL Info -->
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-6 shadow-xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full"></div>
                <div class="relative z-10">
                    <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-4">Dosen Pembimbing</div>
                <div class="space-y-4">
                    @foreach ($posko->pembimbingS as $dpl)
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-xl font-black text-emerald-400">
                                {{ mb_substr($dpl->nama, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-white truncate">{{ $dpl->nama }}</div>
                                <div class="text-[10px] text-emerald-100/50 mt-1">NUPTK: {{ $dpl->nidn ?: '-' }}</div>
                            </div>
                        </div>
                    @endforeach
                    @if ($posko->pembimbingS->isEmpty())
                        <div class="text-sm font-medium text-emerald-100/40 italic">Belum ditentukan</div>
                    @endif
                </div>
                    @if ($posko->sk_pembimbing_path)
                        <a href="{{ asset('storage/'.$posko->sk_pembimbing_path) }}" target="_blank" class="mt-6 flex items-center gap-4 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition group">
                            <div class="h-10 w-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-400 group-hover:scale-110 transition">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-[10px] font-black text-white truncate uppercase tracking-widest">SK Pembimbing</div>
                                <div class="text-[9px] text-emerald-100/40 mt-0.5">Download Dokumen</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Anggota Info -->
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-6 shadow-xl">
                <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em] mb-4">Anggota Posko ({{ $posko->pengajuans->count() }})</div>
                <div class="space-y-3">
                    @foreach ($posko->pengajuans as $p)
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition group">
                            <div class="h-10 w-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-sm font-black text-white/40 group-hover:text-emerald-400 transition">
                                {{ mb_substr($p->mahasiswa?->nama_lengkap, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-white truncate">{{ $p->mahasiswa?->nama_lengkap }}</div>
                                <div class="text-[9px] font-mono text-emerald-100/30 mt-0.5 uppercase tracking-tighter">{{ $p->mahasiswa?->program_studi }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- File Section -->
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em]">Laporan & Dokumen</div>
                    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="h-8 w-8 rounded-lg bg-emerald-600/20 text-emerald-400 border border-emerald-500/20 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition shadow-lg shadow-emerald-900/20">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse ($posko->files->sortByDesc('id') as $f)
                        <div class="p-3 rounded-2xl bg-white/5 border border-white/5 hover:border-white/10 transition group">
                            <div class="flex items-start gap-3">
                                <div class="h-9 w-9 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition">
                                    <i class="fa-solid fa-file-arrow-up"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-white truncate" title="{{ $f->file_name }}">{{ $f->file_name }}</div>
                                    <div class="text-[9px] text-emerald-100/30 mt-1 uppercase font-black tracking-tighter">
                                        {{ $f->user?->name }} • {{ $f->created_at->format('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <a href="{{ route('files.kkn.download', $f) }}" class="flex-1 h-8 rounded-xl bg-emerald-600 text-[9px] font-black uppercase tracking-widest flex items-center justify-center hover:bg-emerald-500 transition border border-emerald-500/20 shadow-lg shadow-emerald-900/20">Download File</a>
                                @if (auth()->id() === (int)$f->user_id || auth()->user()->isAdmin())
                                    <form method="POST" action="{{ route('kkn.bimbingan.file.destroy', $f) }}" data-confirm="Hapus file ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-xl bg-red-500/10 text-red-400 flex items-center justify-center hover:bg-red-500/20 transition border border-red-500/10">
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <i class="fa-solid fa-folder-open text-3xl text-white/10 mb-2"></i>
                            <p class="text-[10px] font-bold text-emerald-100/20 uppercase tracking-[0.2em]">Belum ada file</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Revisi Section -->
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 p-6 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <div class="text-[10px] font-black text-emerald-100/30 uppercase tracking-[0.2em]">Riwayat Bimbingan & Revisi</div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('kkn.bimbingan.revisi.print', $posko) }}" target="_blank" class="h-9 px-4 rounded-xl bg-white/5 text-white border border-white/10 flex items-center justify-center gap-2 hover:bg-white/10 transition text-[10px] font-black uppercase tracking-widest">
                            <i class="fa-solid fa-print"></i>
                            Cetak
                        </a>
                        <button onclick="document.getElementById('revisiModal').classList.remove('hidden')" class="h-9 px-4 rounded-xl bg-emerald-600 text-white flex items-center justify-center gap-2 hover:bg-emerald-500 transition text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-900/20">
                            <i class="fa-solid fa-plus"></i>
                            Input Revisi
                        </button>
                    </div>
                </div>
                <div class="space-y-4">
                    @forelse ($posko->revisis->sortByDesc('tanggal') as $rev)
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/5 relative overflow-hidden group">
                            <div class="absolute top-0 left-0 w-1 h-full bg-emerald-500/30"></div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">{{ $rev->tanggal->format('d F Y') }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-bold text-emerald-100/30 uppercase bg-white/5 px-2 py-0.5 rounded-full">{{ $rev->user?->name }}</span>
                                    @if (auth()->id() === (int)$rev->user_id || auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('kkn.bimbingan.revisi.destroy', $rev) }}" data-confirm="Hapus data revisi ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400/50 hover:text-red-400 transition">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="text-xs text-emerald-100/80 leading-relaxed italic">"{{ $rev->uraian_revisi }}"</div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <i class="fa-solid fa-clipboard-check text-3xl text-white/10 mb-2"></i>
                            <p class="text-[10px] font-bold text-emerald-100/20 uppercase tracking-[0.2em]">Belum ada revisi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Revisi Modal (Dosen Only) -->
        <div id="revisiModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
                <div class="relative w-full max-w-md transform rounded-3xl bg-[#0d2a23] border border-white/10 p-8 shadow-2xl transition-all">
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                                <i class="fa-solid fa-pen-to-square text-emerald-400"></i>
                            </div>
                            <h3 class="text-lg font-black text-white uppercase tracking-widest">Input Revisi Laporan</h3>
                        </div>
                        <button onclick="document.getElementById('revisiModal').classList.add('hidden')" class="text-white/40 hover:text-white transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('kkn.bimbingan.revisi.store', $posko) }}" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-emerald-100/40 uppercase tracking-[0.2em] mb-3">Tanggal Bimbingan</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                                   class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-emerald-100/40 uppercase tracking-[0.2em] mb-3">Uraian Bimbingan / Revisi</label>
                            <textarea name="uraian_revisi" rows="5" required placeholder="Contoh: Perbaiki bab 1 bagian latar belakang..."
                                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black tracking-[0.2em] uppercase text-xs transition-all shadow-xl shadow-emerald-900/40">
                            Simpan Revisi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chat Section (Right) -->
        <div class="lg:col-span-8 order-1 lg:order-2">
            <div class="rounded-3xl bg-[#0d2a23] border border-white/10 shadow-2xl flex flex-col h-[700px] overflow-hidden relative">
                <!-- Chat Header -->
                <div class="p-5 border-b border-white/5 bg-white/5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center">
                            <i class="fa-solid fa-comments text-emerald-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-black text-white uppercase tracking-widest">Forum Diskusi</div>
                            <div class="text-[10px] text-emerald-400 font-bold uppercase tracking-tighter mt-0.5 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Diskusi Bimbingan DPL & Anggota
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Body -->
                <div id="chatBody" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-gradient-to-b from-transparent to-black/10">
                    @forelse ($posko->messages->sortBy('id') as $msg)
                        @php $isMe = (int) $msg->sender_user_id === (int) auth()->id(); @endphp
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} animate-in fade-in slide-in-from-bottom-2 duration-300">
                            <div class="max-w-[85%] group">
                                @if (!$isMe)
                                    <div class="text-[10px] font-black text-emerald-100/40 uppercase tracking-widest mb-1.5 ml-3 flex items-center gap-2">
                                        {{ $msg->sender?->name }}
                                        @if ($msg->sender?->isDosen())
                                            <span class="bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded text-[8px]">DPL</span>
                                        @endif
                                    </div>
                                @endif
                                <div class="relative">
                                    <div class="px-4 py-3 rounded-2xl text-sm leading-relaxed {{ $isMe ? 'bg-emerald-600 text-white rounded-tr-none shadow-lg shadow-emerald-900/20' : 'bg-white/5 border border-white/10 text-emerald-100/90 rounded-tl-none' }}">
                                        <div class="whitespace-pre-line">{{ $msg->pesan }}</div>
                                    </div>
                                    <div class="mt-1.5 px-1 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300 {{ $isMe ? 'justify-end' : 'justify-start' }}">
                                        <span class="text-[9px] font-bold text-emerald-100/30 uppercase">{{ $msg->created_at->format('H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center p-8 opacity-20">
                            <div class="h-24 w-24 bg-white/5 rounded-full flex items-center justify-center mb-4">
                                <i class="fa-solid fa-message-slash text-5xl"></i>
                            </div>
                            <h4 class="text-xl font-black uppercase tracking-widest text-white">Belum Ada Pesan</h4>
                            <p class="text-sm font-medium mt-2">Mulai percakapan dengan anggota posko dan DPL Anda.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Chat Footer -->
                <div class="p-5 border-t border-white/5 bg-white/5">
                    <form method="POST" action="{{ route('kkn.bimbingan.message', $posko) }}" class="flex items-end gap-3 bg-black/20 border border-white/5 rounded-2xl p-2 focus-within:border-emerald-500/30 transition-all duration-300">
                        @csrf
                        <textarea name="pesan" rows="1" required placeholder="Tulis bimbingan atau pesan diskusi..." 
                                  oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                  class="flex-1 bg-transparent border-0 px-3 py-2.5 text-sm text-white focus:ring-0 focus:outline-none resize-none max-h-32 custom-scrollbar placeholder:text-white/20"></textarea>
                        <button class="h-11 w-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center hover:bg-emerald-500 transition shadow-lg shadow-emerald-900/40 transform active:scale-95 group">
                            <i class="fa-solid fa-paper-plane group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" onclick="this.parentElement.parentElement.classList.add('hidden')"></div>
            <div class="relative w-full max-w-md transform rounded-3xl bg-[#0d2a23] border border-white/10 p-8 shadow-2xl transition-all">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                            <i class="fa-solid fa-cloud-arrow-up text-emerald-400"></i>
                        </div>
                        <h3 class="text-lg font-black text-white uppercase tracking-widest">Upload File KKN</h3>
                    </div>
                    <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="text-white/40 hover:text-white transition">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('kkn.bimbingan.file', $posko) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-emerald-100/40 uppercase tracking-[0.2em] mb-3">Pilih File (Max 20MB)</label>
                        <div class="relative group">
                            <input type="file" name="file" required
                                   onchange="document.getElementById('fileNameLabel').innerText = this.files[0].name"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div class="h-32 rounded-2xl border-2 border-dashed border-white/10 group-hover:border-emerald-500/30 group-hover:bg-emerald-500/5 transition flex flex-col items-center justify-center text-center p-6">
                                <i class="fa-solid fa-file-circle-plus text-3xl text-white/10 group-hover:text-emerald-400 mb-2 transition"></i>
                                <span id="fileNameLabel" class="text-xs font-bold text-emerald-100/40 group-hover:text-emerald-100 transition">Klik atau seret file ke sini</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-emerald-100/40 uppercase tracking-[0.2em] mb-3">Keterangan Singkat</label>
                        <input name="keterangan" placeholder="Contoh: Laporan Mingguan ke-1"
                               class="h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
                    </div>
                    <button type="submit" class="w-full h-14 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-black tracking-[0.2em] uppercase text-xs transition-all shadow-xl shadow-emerald-900/40">
                        Unggah Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            const chatBody = document.getElementById('chatBody');
            chatBody.scrollTop = chatBody.scrollHeight;
        };
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(52, 211, 153, 0.15); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(52, 211, 153, 0.3); }
    </style>
</x-portal-layout>
