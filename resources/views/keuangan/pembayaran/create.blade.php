<x-portal-layout :title="'Input Pembayaran - '.config('app.name')" subtitle="Input Pembayaran Baru">
    <x-slot:sidebar>
        @include('keuangan.partials.sidebar')
    </x-slot:sidebar>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Input Pembayaran Baru</h1>
                <p class="text-sm text-emerald-100/70">Masukkan data pembayaran semester untuk mahasiswa.</p>
            </div>
            <a href="{{ route('keuangan.pembayaran.index') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-white">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('keuangan.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-sm text-emerald-100/80 font-medium">Mahasiswa</label>
                        <select name="mahasiswa_id" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required>
                            <option value="" disabled selected class="text-black">Pilih Mahasiswa</option>
                            @foreach($mahasiswas as $m)
                                <option value="{{ $m->id }}" @selected(old('mahasiswa_id') == $m->id) class="text-black">
                                    {{ $m->npm }} - {{ $m->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        @error('mahasiswa_id') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm text-emerald-100/80 font-medium">Semester</label>
                        <input type="number" name="semester" value="{{ old('semester') }}" min="1" max="14" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required />
                        @error('semester') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm text-emerald-100/80 font-medium">Tahun Ajaran</label>
                        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', date('Y').'/'.(date('Y')+1)) }}" placeholder="Contoh: 2026/2027" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required />
                        @error('tahun_ajaran') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm text-emerald-100/80 font-medium">Total Biaya Semester (Rp)</label>
                        <input type="number" name="total_biaya" value="{{ old('total_biaya') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required />
                        @error('total_biaya') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-emerald-100/80 font-medium">Catatan / Keterangan Pembayaran</label>
                        <textarea name="catatan" rows="2" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" placeholder="Keterangan umum pembayaran...">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-4">
                <h2 class="text-lg font-bold text-emerald-400 flex items-center gap-2">
                    <i class="fa-solid fa-receipt"></i> Data Pembayaran Pertama (DP / Lunas)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm text-emerald-100/80 font-medium">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required />
                        @error('jumlah_bayar') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-sm text-emerald-100/80 font-medium">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" required />
                        @error('tanggal_bayar') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-emerald-100/80 font-medium">Bukti Pembayaran (Foto/Scan)</label>
                        <input type="file" name="bukti_pembayaran" accept="image/*" class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 file:bg-white/10 file:border-0 file:text-white file:px-4 file:py-2 file:rounded-xl" />
                        @error('bukti_pembayaran') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm text-emerald-100/80 font-medium">Keterangan Transaksi</label>
                        <input type="text" name="keterangan_bayar" value="{{ old('keterangan_bayar') }}" class="mt-2 w-full h-11 rounded-xl bg-white/5 border border-white/10 focus:ring-emerald-400 focus:border-emerald-400 text-white" placeholder="Contoh: Pembayaran melalui Bank Transfer" />
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="h-12 px-10 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition font-bold text-white shadow-lg shadow-emerald-600/20">
                    SIMPAN DATA PEMBAYARAN
                </button>
            </div>
        </form>
    </div>
</x-portal-layout>
