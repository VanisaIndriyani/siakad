<x-portal-layout :title="'Detail Posko KKN - '.config('app.name')" subtitle="KKN">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
        <div>
            <div class="text-xl font-semibold">Detail Posko KKN</div>
            <div class="text-sm text-emerald-100/70">{{ $posko->nama_posko }} - {{ $posko->lokasi ?: 'Lokasi belum ditentukan' }}</div>
        </div>
        <a href="{{ route('admin.kkn.posko.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Posko & Form Edit -->
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-emerald-400"></i>
                    Informasi Posko
                </h3>
                <form method="POST" action="{{ route('admin.kkn.posko.update', $posko) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-emerald-100/50 uppercase tracking-wider mb-1">Nama Posko</label>
                            <input name="nama_posko" value="{{ old('nama_posko', $posko->nama_posko) }}" required
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-100/50 uppercase tracking-wider mb-1">Lokasi</label>
                            <input name="lokasi" value="{{ old('lokasi', $posko->lokasi) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-emerald-100/50 uppercase tracking-wider mb-2">Dosen Pembimbing Lapangan (Maksimal 5)</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @php $assignedIds = $posko->pembimbingS->pluck('id')->toArray(); @endphp
                                @for ($i = 0; $i < 5; $i++)
                                    <select name="dosen_ids[]" class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition">
                                        <option value="" style="background-color: #0d2a23;">Pilih DPL {{ $i + 1 }}</option>
                                        @foreach ($dosenList as $d)
                                            <option value="{{ $d->id }}" @selected(old('dosen_ids.'.$i, $assignedIds[$i] ?? '') == $d->id) style="background-color: #0d2a23;">{{ $d->nama }}</option>
                                        @endforeach
                                    </select>
                                @endfor
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-100/50 uppercase tracking-wider mb-1">Nomor SK</label>
                            <input name="nomor_sk" value="{{ old('nomor_sk', $posko->nomor_sk) }}"
                                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-emerald-100/50 uppercase tracking-wider mb-1">Update SK Pembimbing (Opsional)</label>
                        <input type="file" name="sk_pembimbing_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full h-11 rounded-xl bg-white/5 border border-white/10 text-emerald-100/80 file:mr-4 file:h-11 file:border-0 file:bg-white/10 file:text-white file:px-4 file:cursor-pointer transition" />
                    </div>
                    @if ($posko->sk_pembimbing_path)
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                            <i class="fa-solid fa-file-pdf text-xl text-emerald-400"></i>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-emerald-100 truncate">{{ $posko->sk_pembimbing_name }}</div>
                                <div class="text-xs text-emerald-100/60">SK Pembimbing Aktif</div>
                            </div>
                            <a href="{{ asset('storage/'.$posko->sk_pembimbing_path) }}" target="_blank" class="h-8 px-3 inline-flex items-center rounded-lg bg-emerald-600 text-xs font-bold text-white transition hover:bg-emerald-500">LIHAT</a>
                        </div>
                    @endif
                    <div class="flex justify-end">
                        <button class="h-10 px-6 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-bold uppercase tracking-widest">Update Data</button>
                    </div>
                </form>
            </div>

            <!-- Plotting Mahasiswa -->
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-users text-emerald-400"></i>
                        Anggota Posko
                    </span>
                    <span class="text-xs font-bold bg-emerald-500/20 text-emerald-300 px-3 py-1 rounded-full">{{ $posko->pengajuans->count() }} Orang</span>
                </h3>

                <div class="overflow-hidden rounded-xl border border-white/5">
                    <table class="min-w-full text-sm">
                        <thead class="bg-white/5 text-emerald-100/60 uppercase text-[10px] font-bold tracking-wider">
                            <tr>
                                <th class="px-4 py-3 text-left">Mahasiswa</th>
                                <th class="px-4 py-3 text-left">Prodi</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse ($posko->pengajuans as $row)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-white">{{ $row->mahasiswa?->nama_lengkap }}</div>
                                        <div class="text-xs text-emerald-100/60 font-mono">{{ $row->mahasiswa?->npm }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-emerald-100/80">{{ $row->mahasiswa?->program_studi }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('admin.kkn.pengajuan.remove', $row) }}" data-confirm="Keluarkan mahasiswa ini dari posko?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                                                <i class="fa-solid fa-user-minus text-xs"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-emerald-100/40 italic">Belum ada anggota di posko ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Form Tambah Anggota -->
                <div class="mt-6 p-4 rounded-xl bg-white/5 border border-white/5">
                    <h4 class="text-sm font-bold text-emerald-100/70 mb-3 uppercase tracking-widest">Tambah Anggota Baru</h4>
                    <form method="POST" action="{{ route('admin.kkn.posko.assign', $posko) }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <select name="kkn_pengajuan_ids[]" multiple class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition min-h-[100px]">
                                @foreach ($availableStudents as $s)
                                    <option value="{{ $s->id }}" style="background-color: #0d2a23;">{{ $s->mahasiswa?->nama_lengkap }} ({{ $s->mahasiswa?->npm }}) - {{ $s->mahasiswa?->program_studi }}</option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-[10px] text-emerald-100/40 italic">Tahan Ctrl/Cmd untuk memilih lebih dari satu mahasiswa.</div>
                        </div>
                        <button type="submit" class="h-11 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-bold text-xs uppercase tracking-widest self-end sm:self-start">Plotting</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar / Chat Preview (Optional, for now just show files) -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-emerald-400"></i>
                    File & Laporan
                </h3>
                <div class="space-y-3">
                    @forelse ($posko->files as $f)
                        <div class="p-3 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition">
                            <div class="text-sm font-bold text-emerald-100 truncate" title="{{ $f->file_name }}">{{ $f->file_name }}</div>
                            <div class="text-[10px] text-emerald-100/50 mt-1 uppercase font-bold tracking-tighter">
                                Oleh: {{ $f->user?->name }} • {{ $f->created_at->format('d/m/Y') }}
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                                <a href="{{ route('files.kkn.download', $f) }}" class="flex-1 h-8 inline-flex items-center justify-center rounded-lg bg-emerald-600 text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-500 transition shadow-lg shadow-emerald-900/20">Download</a>
                                <form method="POST" action="{{ route('kkn.bimbingan.file.destroy', $f) }}" data-confirm="Hapus file ini?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="h-8 w-8 inline-flex items-center justify-center rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500/20 transition">
                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-emerald-100/40 text-sm italic">Belum ada file.</div>
                    @endforelse
                </div>
            </div>

            <!-- Chat Preview Link -->
            <div class="rounded-2xl bg-emerald-600/10 border border-emerald-500/20 p-6 text-center">
                <i class="fa-solid fa-comments text-3xl text-emerald-400 mb-3"></i>
                <h4 class="font-bold text-white mb-1">Forum Bimbingan</h4>
                <p class="text-xs text-emerald-100/60 mb-4">Chatting dengan DPL dan Mahasiswa anggota posko ini.</p>
                <div class="p-4 rounded-xl bg-[#0d2a23] border border-white/5 text-left text-xs space-y-3 max-h-[200px] overflow-y-auto mb-4 custom-scrollbar">
                    @forelse ($posko->messages->take(5) as $msg)
                        <div>
                            <span class="font-bold text-emerald-400">{{ $msg->sender?->name }}:</span>
                            <span class="text-emerald-100/80">{{ Str::limit($msg->pesan, 50) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-emerald-100/30 italic">Belum ada pesan.</div>
                    @endforelse
                </div>
                <button onclick="scrollToChat()" class="w-full h-11 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-widest transition">Buka Chat Lengkap</button>
            </div>
        </div>
    </div>

    <!-- Full Chat Section at bottom -->
    <div id="chatSection" class="mt-8 rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
        <div class="p-5 border-b border-white/10 bg-white/5 flex items-center gap-3">
            <i class="fa-solid fa-comments text-emerald-400"></i>
            <h3 class="font-bold text-white uppercase tracking-widest text-sm">Forum Diskusi Posko</h3>
        </div>
        <div class="h-[400px] overflow-y-auto p-6 space-y-4 custom-scrollbar bg-[#0d2a23]/50">
            @forelse ($posko->messages->sortBy('id') as $msg)
                @php $isMe = (int) $msg->sender_user_id === (int) auth()->id(); @endphp
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] {{ $isMe ? 'bg-emerald-600/20 border-emerald-500/30' : 'bg-white/5 border-white/10' }} border p-4 rounded-2xl">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black uppercase {{ $isMe ? 'text-emerald-400' : 'text-emerald-100/60' }}">{{ $msg->sender?->name }}</span>
                            <span class="text-[9px] text-emerald-100/30">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                        <div class="text-sm text-emerald-100/90 whitespace-pre-line leading-relaxed">{{ $msg->pesan }}</div>
                    </div>
                </div>
            @empty
                <div class="h-full flex items-center justify-center flex-col text-emerald-100/30">
                    <i class="fa-solid fa-message-slash text-4xl mb-3"></i>
                    <p class="text-sm italic">Mulai percakapan di sini...</p>
                </div>
            @endforelse
        </div>
        <div class="p-5 border-t border-white/10 bg-white/5">
            <form method="POST" action="{{ route('kkn.bimbingan.message', $posko) }}" class="flex gap-3">
                @csrf
                <textarea name="pesan" rows="1" required placeholder="Tulis pesan..." 
                          class="flex-1 rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30 transition resize-none"></textarea>
                <button class="h-11 w-11 flex items-center justify-center rounded-xl bg-emerald-600 text-white hover:bg-emerald-500 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script>
        function scrollToChat() {
            document.getElementById('chatSection').scrollIntoView({ behavior: 'smooth' });
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(52, 211, 153, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(52, 211, 153, 0.4); }
    </style>
</x-portal-layout>
