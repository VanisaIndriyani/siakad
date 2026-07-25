<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $item->judul }} - {{ config('app.name', 'SIAKAD') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        body { background: radial-gradient(circle at 20% 0%, #0f3d30 0%, #081c15 50%, #020604 100%); min-height: 100vh; }
    </style>
</head>
<body class="text-white">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-8">
        <div class="w-full max-w-3xl">
            <div class="text-center mb-5">
                <div class="inline-flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-200">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <div class="text-left">
                        <div class="text-xs text-emerald-200/70 uppercase tracking-wider">Informasi</div>
                        <div class="text-lg font-semibold">{{ config('app.name', 'SIAKAD') }}</div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white/5 border border-white/10 backdrop-blur p-5 sm:p-8 shadow-2xl">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-bold break-words">{{ $item->judul }}</h1>
                        @if($item->tanggal_aktif || $item->tanggal_kadaluarsa || $item->created_at)
                            <div class="mt-1 text-xs text-emerald-100/60">
                                @if($item->created_at) Diterbitkan: {{ $item->created_at->format('d M Y') }} @endif
                                @if($item->tanggal_kadaluarsa) <span class="mx-2 text-emerald-100/30">•</span> Berakhir: {{ $item->tanggal_kadaluarsa->format('d M Y') }} @endif
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button id="copyBtn" type="button" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-sky-500/15 hover:bg-sky-500/25 border border-sky-500/25 text-sky-100 text-sm font-medium transition">
                            <i class="fa-solid fa-link"></i>
                            Salin Link
                        </button>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($item->judul . ' - ' . url()->current()) }}" target="_blank" rel="noopener" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-green-600/80 hover:bg-green-500 border border-green-400/30 text-white text-sm font-medium transition">
                            <i class="fa-brands fa-whatsapp"></i>
                            Share
                        </a>
                    </div>
                </div>

                @if(!empty($item->deskripsi))
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-4 mb-4 text-sm leading-relaxed text-emerald-100/90 whitespace-pre-wrap">
                        {{ $item->deskripsi }}
                    </div>
                @endif

                @if(!empty($item->gambar_url))
                    <div class="rounded-2xl overflow-hidden border border-white/10 bg-black/20">
                        <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="w-full h-auto object-contain max-h-[75vh]" />
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-white/10 p-12 text-center text-emerald-100/50">
                        Tidak ada gambar.
                    </div>
                @endif
            </div>

            <div class="mt-4 text-center text-xs text-emerald-100/40">
                © {{ date('Y') }} {{ config('app.name', 'SIAKAD') }}
            </div>
        </div>
    </div>

    <script>
        document.getElementById('copyBtn')?.addEventListener('click', function () {
            const url = window.location.href;
            const done = () => {
                const btn = document.getElementById('copyBtn');
                if (!btn) return;
                const original = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin!';
                setTimeout(() => { btn.innerHTML = original; }, 1800);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(done).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); done(); } catch (e) {}
                    document.body.removeChild(ta);
                });
            }
        });
    </script>
</body>
</html>
