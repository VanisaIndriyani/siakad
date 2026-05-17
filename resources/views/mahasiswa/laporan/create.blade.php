<x-portal-layout :title="'Buat Laporan - '.config('app.name')" subtitle="Laporan">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <div class="text-xl font-semibold">Buat Laporan</div>
            <div class="text-sm text-emerald-100/70">Pilih pengajuan yang masih Pending, lalu tulis pesan untuk Admin/Prodi.</div>
        </div>
        <a href="{{ route('mahasiswa.laporan.index') }}"
           class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @php
        $hasSkripsi = $pendingSkripsi->count() > 0;
        $hasPpl = $pendingPpl->count() > 0;
        $hasKrs = $pendingKrs->count() > 0;
        $hasPending = $hasSkripsi || $hasPpl || $hasKrs;
        $defaultJenis = old('jenis') ?: ($hasSkripsi ? 'skripsi' : ($hasPpl ? 'ppl' : 'krs'));
    @endphp

    @if (! $hasPending)
        <div class="mt-5 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 p-5 text-yellow-100">
            Tidak ada pengajuan Pending yang bisa dibuat laporan saat ini.
        </div>
    @else
        <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
            <form method="POST" action="{{ route('mahasiswa.laporan.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Jenis</label>
                        <select id="jenisSelect" name="jenis"
                                class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                            <option value="skripsi" @selected($defaultJenis === 'skripsi') {{ $hasSkripsi ? '' : 'disabled' }} style="background-color: #0d2a23; color: #fff;">Skripsi</option>
                            <option value="ppl" @selected($defaultJenis === 'ppl') {{ $hasPpl ? '' : 'disabled' }} style="background-color: #0d2a23; color: #fff;">PPL</option>
                            <option value="krs" @selected($defaultJenis === 'krs') {{ $hasKrs ? '' : 'disabled' }} style="background-color: #0d2a23; color: #fff;">KRS</option>
                        </select>
                        @error('jenis') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Pilih Pengajuan (Pending)</label>

                        <div id="pengajuanSkripsiWrap">
                            <select name="pengajuan_id"
                                    class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                @foreach ($pendingSkripsi as $s)
                                    <option value="{{ $s->id }}" @selected((string) old('pengajuan_id') === (string) $s->id) style="background-color: #0d2a23; color: #fff;">
                                        #{{ $s->id }} • {{ \Illuminate\Support\Str::limit($s->judul, 60) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="pengajuanPplWrap" style="display:none;">
                            <select name="pengajuan_id"
                                    class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                @foreach ($pendingPpl as $p)
                                    <option value="{{ $p->id }}" @selected((string) old('pengajuan_id') === (string) $p->id) style="background-color: #0d2a23; color: #fff;">
                                        #{{ $p->id }} • {{ \Illuminate\Support\Str::limit($p->instansi_nama, 60) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="pengajuanKrsWrap" style="display:none;">
                            <select name="pengajuan_id"
                                    class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                                @foreach ($pendingKrs as $k)
                                    <option value="{{ $k->id }}" @selected((string) old('pengajuan_id') === (string) $k->id) style="background-color: #0d2a23; color: #fff;">
                                        #{{ $k->id }} • Semester {{ $k->semester }} ({{ $k->tahun_ajaran }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @error('pengajuan_id') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Judul Laporan</label>
                    <input name="judul" value="{{ old('judul') }}"
                           class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                           placeholder="Contoh: Pengajuan belum di-approve" />
                    @error('judul') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-emerald-100/70 mb-1">Pesan</label>
                    <textarea name="pesan" rows="5"
                              class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                              placeholder="Tulis laporan kamu...">{{ old('pesan') }}</textarea>
                    @error('pesan') <div class="mt-2 text-sm text-red-200">{{ $message }}</div> @enderror
                </div>

                <button class="h-11 px-5 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition text-sm font-medium inline-flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    Kirim Laporan
                </button>
            </form>
        </div>
    @endif

    <script>
        (function () {
            const jenisSelect = document.getElementById('jenisSelect');
            const skripsiWrap = document.getElementById('pengajuanSkripsiWrap');
            const pplWrap = document.getElementById('pengajuanPplWrap');
            const krsWrap = document.getElementById('pengajuanKrsWrap');
            if (!jenisSelect || !skripsiWrap || !pplWrap || !krsWrap) return;
            const skripsiSelect = skripsiWrap.querySelector('select');
            const pplSelect = pplWrap.querySelector('select');
            const krsSelect = krsWrap.querySelector('select');

            function render() {
                const v = (jenisSelect.value || 'skripsi').toLowerCase();
                
                skripsiWrap.style.display = v === 'skripsi' ? '' : 'none';
                pplWrap.style.display = v === 'ppl' ? '' : 'none';
                krsWrap.style.display = v === 'krs' ? '' : 'none';

                if (skripsiSelect) skripsiSelect.disabled = v !== 'skripsi';
                if (pplSelect) pplSelect.disabled = v !== 'ppl';
                if (krsSelect) krsSelect.disabled = v !== 'krs';
            }

            render();
            jenisSelect.addEventListener('change', render);
        })();
    </script>
</x-portal-layout>
