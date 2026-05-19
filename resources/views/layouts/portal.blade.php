<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="google-site-verification" content="kqEhVNvUWQCklGA-taVSa5Md5SlUawyWmC3lbeEu_Lo" />
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media print {
                .no-print { display: none !important; }
                body { background: white !important; color: black !important; }
                .lg\:pl-72 { padding-left: 0 !important; }
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-brand-950 text-white">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <div class="fixed inset-0 z-40 lg:hidden" x-show="sidebarOpen" x-cloak>
                <div class="absolute inset-0 bg-black/60" @click="sidebarOpen = false"></div>
            </div>

            <aside class="no-print fixed inset-y-0 left-0 z-50 w-72 bg-white/5 border-r border-white/10 backdrop-blur-xl transform transition lg:translate-x-0 overflow-y-auto"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
                <div class="px-5 py-5 flex flex-col items-center text-center gap-3 border-b border-white/10">
                    <img src="{{ asset('img/lo.jpeg') }}" alt="Logo Kampus" class="h-16 w-auto max-w-[240px] rounded-2xl object-contain bg-white px-5 py-4 shadow-lg ring-1 ring-black/10" />
                    <div class="leading-tight">
                        <div class="font-semibold">{{ config('app.name') }}</div>
                        <div class="text-xs text-emerald-100/70 mt-1">Sistem Informasi Akademik</div>
                    </div>
                </div>

                <nav class="px-3 py-4 space-y-1">
                    {{ $sidebar }}
                </nav>
            </aside>

            <div class="lg:pl-72">
                <header class="no-print sticky top-0 z-30 h-16 flex items-center justify-between px-4 sm:px-6 bg-brand-950/80 backdrop-blur border-b border-white/10">
                    <div class="flex items-center gap-3">
                        <button type="button" class="lg:hidden inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10"
                                @click="sidebarOpen = true">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="text-sm text-emerald-100/70">{{ $subtitle ?? '' }}</div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden sm:block text-right leading-tight">
                            <div class="text-sm font-medium">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-emerald-100/70 capitalize">{{ auth()->user()->role }}</div>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 h-10 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span class="text-sm font-medium">Logout</span>
                            </button>
                        </form>
                    </div>
                </header>

                <main class="p-4 sm:p-6">
                    @if (session('success'))
                        <div class="mb-4 rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 rounded-xl border border-red-500/20 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        @php
            $waUser = auth()->user();
            $waRoleRaw = trim((string) ($waUser?->role ?? ''));
            $waRoleKey = strtolower($waRoleRaw);
            $showWa = $waUser && in_array($waRoleKey, ['mahasiswa', 'dosen'], true);
            $waNumber = '6282371114136';
            $waRole = $waRoleKey === 'mahasiswa' ? 'Mahasiswa' : ($waRoleKey === 'dosen' ? 'Dosen' : 'User');
            $waNama = (string) ($waUser?->name ?? '');
            $waNpm = (string) ($waUser?->mahasiswa?->npm ?? '');
            $waNuptk = (string) ($waUser?->dosen?->nuptk ?? '');
            $waUrl = (string) request()->fullUrl();
            $waIdentity = $waRole.($waNama !== '' ? (': '.$waNama) : '');
            if ($waNpm !== '') {
                $waIdentity .= ' (NPM: '.$waNpm.')';
            }
            if ($waNuptk !== '') {
                $waIdentity .= ' (NUPTK: '.$waNuptk.')';
            }
            $waTemplate = "Assalamu'alaikum Admin,\n\nSaya {$waIdentity} melaporkan kendala pada web SIAKAD.\n\nKendala: [tuliskan error/kendala]\nHalaman: {$waUrl}\nWaktu: ".now()->format('d/m/Y H:i')."\n\nTerima kasih.";
            $waText = rawurlencode($waTemplate);
        @endphp
        @if ($showWa)
            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank" rel="noopener"
               class="no-print"
               style="position: fixed; right: 20px; bottom: 20px; z-index: 9999; width: 56px; height: 56px; border-radius: 9999px; background: #ef4444; border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 18px 40px rgba(0,0,0,0.35); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
               title="Kontak Admin WhatsApp • Apabila ada kendala terkait web SIAKAD">
                <i class="fa-brands fa-whatsapp" style="font-size: 26px; color: #fff;"></i>
                <span style="position: absolute; left: -9999px;">WhatsApp</span>
            </a>
        @endif

        <div id="confirmModal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4">
            <div id="confirmModalOverlay" class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative w-full max-w-md rounded-2xl bg-brand-950 border border-white/10 shadow-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-white/10 flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-amber-500/15 border border-amber-400/20 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-amber-200"></i>
                    </div>
                    <div class="flex-1">
                        <div class="text-base font-semibold">Konfirmasi</div>
                        <div id="confirmModalMessage" class="mt-1 text-sm text-emerald-100/75"></div>
                    </div>
                    <button type="button" id="confirmModalCloseX" class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <div class="px-5 py-4 flex items-center justify-end gap-2">
                    <button type="button" id="confirmModalCancel" class="h-10 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                        Batal
                    </button>
                    <button type="button" id="confirmModalConfirm" class="h-10 px-4 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition font-medium">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        <script>
            (function () {
                const modal = document.getElementById('confirmModal');
                const messageEl = document.getElementById('confirmModalMessage');
                const overlay = document.getElementById('confirmModalOverlay');
                const closeX = document.getElementById('confirmModalCloseX');
                const cancelBtn = document.getElementById('confirmModalCancel');
                const confirmBtn = document.getElementById('confirmModalConfirm');

                let pendingForm = null;
                let lastFocused = null;

                function openModal(message, form) {
                    pendingForm = form;
                    lastFocused = document.activeElement;
                    if (messageEl) {
                        messageEl.textContent = message || '';
                    }
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }
                    if (confirmBtn) {
                        confirmBtn.focus();
                    }
                }

                function closeModal() {
                    if (modal) {
                        modal.classList.remove('flex');
                        modal.classList.add('hidden');
                    }
                    pendingForm = null;
                    if (lastFocused && typeof lastFocused.focus === 'function') {
                        lastFocused.focus();
                    }
                    lastFocused = null;
                }

                function confirmModal() {
                    if (!pendingForm) {
                        closeModal();
                        return;
                    }
                    pendingForm.dataset.confirmed = '1';
                    const formToSubmit = pendingForm;
                    closeModal();
                    formToSubmit.submit();
                }

                if (closeX) closeX.addEventListener('click', closeModal);
                if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
                if (confirmBtn) confirmBtn.addEventListener('click', confirmModal);
                if (overlay) overlay.addEventListener('click', closeModal);

                document.addEventListener('keydown', function (e) {
                    if (!modal || modal.classList.contains('hidden')) return;
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        closeModal();
                    }
                });

                document.addEventListener('submit', function (e) {
                    const form = e.target;
                    if (!(form instanceof HTMLFormElement)) return;
                    if (form.dataset.confirmed === '1') {
                        form.dataset.confirmed = '';
                        return;
                    }

                    const methodInput = form.querySelector('input[name="_method"]');
                    const isDelete = methodInput && String(methodInput.value || '').toUpperCase() === 'DELETE';
                    const message = form.getAttribute('data-confirm') || (isDelete ? 'Apakah kamu yakin ingin menghapus data ini?' : '');

                    if (message) {
                        e.preventDefault();
                        openModal(message, form);
                    }
                }, true);
            })();
        </script>
    </body>
</html>
