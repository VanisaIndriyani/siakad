<x-portal-layout title="Manajemen User" subtitle="Manajemen User">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <div x-data="{
        showPassword: {}
    }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-2xl font-bold text-white">Manajemen User</div>
            <div class="text-sm text-emerald-100/70 mt-1">Kelola data login Keuangan, Dosen, dan Mahasiswa.</div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.user.pdf', request()->query()) }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                <i class="fa-solid fa-file-pdf text-rose-400"></i>
                PDF
            </a>
            <button type="button" onclick="window.print()" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                <i class="fa-solid fa-print text-emerald-400"></i>
                Print
            </button>
        </div>
    </div>

    <div class="mt-6 rounded-2xl bg-white/5 border border-white/10 p-5 no-print">
        <form method="GET" action="{{ route('admin.user.index') }}" class="flex flex-col lg:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari nama / email / username..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <select name="role" class="h-11 w-full lg:w-56 rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="" style="background-color: #0d2a23; color: #fff;">Semua Role</option>
                <option value="admin" @selected($role === 'admin') style="background-color: #0d2a23; color: #fff;">Admin</option>
                <option value="keuangan" @selected($role === 'keuangan') style="background-color: #0d2a23; color: #fff;">Keuangan</option>
                <option value="dosen" @selected($role === 'dosen') style="background-color: #0d2a23; color: #fff;">Dosen</option>
                <option value="mahasiswa" @selected($role === 'mahasiswa') style="background-color: #0d2a23; color: #fff;">Mahasiswa</option>
            </select>
            <div class="flex items-center gap-2">
                <button class="h-11 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-500 transition text-sm font-medium text-white">Cari</button>
                <a href="{{ route('admin.user.index') }}" class="h-11 px-6 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center text-white">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3 w-14">No</th>
                        <th class="text-left font-medium px-4 py-3">Nama</th>
                        <th class="text-left font-medium px-4 py-3">Email / Username</th>
                        <th class="text-left font-medium px-4 py-3">Role</th>
                        <th class="text-left font-medium px-4 py-3">Password</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 text-white">
                    @forelse ($users as $i => $u)
                        <tr class="hover:bg-white/5 transition">
                            <td class="px-4 py-4">{{ $users->firstItem() + $i }}</td>
                            <td class="px-4 py-4 font-medium">{{ $u->name }}</td>
                            <td class="px-4 py-4">
                                <div class="text-white">{{ $u->email }}</div>
                                <div class="text-xs text-emerald-100/50 mt-0.5">{{ $u->username }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $roleColor = match($u->role) {
                                        'admin' => 'bg-rose-500/10 border-rose-500/20 text-rose-300',
                                        'keuangan' => 'bg-amber-500/10 border-amber-500/20 text-amber-300',
                                        'dosen' => 'bg-blue-500/10 border-blue-500/20 text-blue-300',
                                        'mahasiswa' => 'bg-emerald-500/10 border-emerald-500/20 text-emerald-300',
                                        default => 'bg-slate-500/10 border-slate-500/20 text-slate-300',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-lg border text-[8px] font-bold uppercase tracking-tighter {{ $roleColor }}">
                                    {{ $u->role }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <code class="text-[10px] text-emerald-100/40 font-mono truncate max-w-[200px]" x-show="!showPassword['{{ $u->id }}']">
                                        ••••••••
                                    </code>
                                    <code class="text-[10px] text-emerald-300 font-mono" x-show="showPassword['{{ $u->id }}']" x-cloak>
                                        {{ $u->password_plain ?? '(Belum tercatat)' }}
                                    </code>
                                    <button @click="showPassword['{{ $u->id }}'] = !showPassword['{{ $u->id }}']" class="text-emerald-100/50 hover:text-emerald-400 transition no-print">
                                        <i class="fa-solid" :class="showPassword['{{ $u->id }}'] ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-emerald-100/50">Data user tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-4 py-4 border-t border-white/10 no-print">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Print Only Layout --}}
<div class="print-only fixed inset-0 bg-white text-black p-0 z-[9999]">
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
                    <div style="font-size: 10px; margin: 2px 0;">E-mail : iaiddisrapp@gmail.com Website : www.yppddisrapp.ac.id</div>
                </td>
                <td style="width: 90px;"></td>
            </tr>
        </table>
        <div style="border-top: 1px solid #000; margin-top: 2px; margin-bottom: 20px;"></div>
        
        <div style="text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase;">Laporan Data Akun User</div>

        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr style="background: #f3f4f6;">
                    <th style="border: 1px solid #000; padding: 6px;">No</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: left;">Nama</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: left;">Email</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: left;">Role</th>
                    <th style="border: 1px solid #000; padding: 6px; text-align: left;">Password</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $i => $u)
                    @php
                        $roleStyle = match($u->role) {
                            'admin' => 'color: #e11d48; font-weight: bold;',
                            'keuangan' => 'color: #d97706; font-weight: bold;',
                            'dosen' => 'color: #2563eb; font-weight: bold;',
                            'mahasiswa' => 'color: #059669; font-weight: bold;',
                            default => 'color: #4b5563;',
                        };
                    @endphp
                    <tr>
                        <td style="border: 1px solid #000; padding: 6px; text-align: center;">{{ $i + 1 }}</td>
                        <td style="border: 1px solid #000; padding: 6px;">{{ $u->name }}</td>
                        <td style="border: 1px solid #000; padding: 6px;">{{ $u->email }}</td>
                        <td style="border: 1px solid #000; padding: 6px; font-size: 8px; {{ $roleStyle }}">{{ strtoupper($u->role) }}</td>
                        <td style="border: 1px solid #000; padding: 6px; font-family: monospace; font-size: 8px;">{{ $u->password_plain ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 20px; text-align: right; font-size: 9px;">
            Dicetak pada: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

<style>
    @media print {
        .print-only { display: block !important; position: static !important; width: 100% !important; height: auto !important; margin: 0 !important; padding: 0 !important; }
        .no-print, header, aside, .lg\:pl-72 > header, .lg\:pl-72 > main > div:not(.print-only), #confirmModal { display: none !important; }
        .lg\:pl-72 { padding: 0 !important; margin: 0 !important; }
        main { padding: 0 !important; margin: 0 !important; }
        body { background: white !important; color: black !important; }
    }
    .print-only { display: none; }
</style>
</x-portal-layout>
