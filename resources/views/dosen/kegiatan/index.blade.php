<x-portal-layout :title="'Kegiatan - '.config('app.name')" subtitle="Daftar Kegiatan & Seminar">
    <x-slot:sidebar>
        @include('dosen.partials.sidebar')
    </x-slot:sidebar>

    @php
        $totalKegiatan = $items->total() ?? 0;
        $totalSaya = count($kegiatanSayaIds);
        $totalHadir = collect($pesertaSaya)->filter(fn($v) => !empty($v))->count();

        $namaPanggil = $dosen->nama ?? auth()->user()->name ?? 'Bapak/Ibu';
        $namaPotong = strlen($namaPanggil) > 32 ? substr($namaPanggil,0,32).'..' : $namaPanggil;

        $jam = now()->hour;
        if ($jam < 11) $salam = 'Pagi';
        elseif ($jam < 15) $salam = 'Siang';
        elseif ($jam < 18) $salam = 'Sore';
        else $salam = 'Malam';

        $inisial = strtoupper(trim(collect(explode(' ', $namaPanggil))->take(2)->map(fn($w) => $w[0] ?? '')->implode('')));
        if ($inisial === '') $inisial = 'D';
    @endphp

    <style>
    .mhs-hero {
        position: relative; overflow: hidden; border-radius: 24px;
        background: #047857;
        background-image: linear-gradient(135deg,#065f46 0%,#059669 50%,#0f766e 100%);
        border: 1px solid rgba(16,185,129,.25);
        padding: 24px 28px; margin-bottom: 24px;
        box-shadow: 0 20px 50px -20px rgba(5,150,105,.45);
        color: #fff;
    }
    .mhs-hero .blur-a, .mhs-hero .blur-b { position: absolute; border-radius: 9999px; filter: blur(64px); pointer-events: none; opacity: .35; }
    .mhs-hero .blur-a { top:-96px; right:-80px; width:288px; height:288px; background: rgba(255,255,255,.5); }
    .mhs-hero .blur-b { bottom:-112px; left:-64px; width:288px; height:288px; background: rgba(56,189,248,.45); }
    .mhs-hero .watermark { position:absolute; top:24px; right:32px; font-size: 88px; color:#fff; opacity:.07; }
    .mhs-hero-top { display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
    @media (min-width:1024px){ .mhs-hero-top{ align-items:center; } }
    .mhs-head { display:flex; align-items:center; gap: 16px; }
    .mhs-avatar-wrap { position: relative; flex-shrink: 0; }
    .mhs-avatar {
        width: 84px; height: 84px; border-radius: 16px; object-fit: cover;
        border: 2px solid rgba(255,255,255,.4);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,.18), 0 8px 10px -6px rgba(0,0,0,.1);
    }
    .mhs-avatar-initial {
        width: 84px; height: 84px; border-radius: 16px;
        background: linear-gradient(135deg,#6ee7b7,#2dd4bf);
        color:#064e3b; font-weight: 900; font-size: 30px;
        display: flex; align-items: center; justify-content: center;
        border: 2px solid rgba(255,255,255,.4);
        box-shadow: 0 20px 25px -5px rgba(0,0,0,.18), 0 8px 10px -6px rgba(0,0,0,.1);
    }
    .mhs-avatar-dot {
        position: absolute; bottom:-4px; right:-4px; width:24px; height:24px;
        border-radius: 9999px; background:#34d399; border:2px solid #064e3b;
        display:flex; align-items:center; justify-content:center; font-size:10px; color:#064e3b;
    }
    .chip-salam {
        display: inline-flex; align-items:center; gap:6px; padding: 0 12px; height: 24px;
        border-radius:9999px; background: rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.2);
        backdrop-filter: blur(8px); font-size:12px; color:#fff; margin-bottom:6px;
    }
    .mhs-h1 { font-size:28px; font-weight:900; line-height:1.15; letter-spacing:-.01em; color:#fff; margin: 0 0 4px; }
    @media (max-width:640px){ .mhs-h1{ font-size:24px; } }
    .mhs-sub { font-size:15px; line-height:1.5; color:rgba(209,250,229,.85); margin: 4px 0 0; max-width: 580px; }
    .mhs-cta-row { display:flex; align-items:center; gap:12px; flex-wrap: wrap; }
    .btn-sertif {
        position: relative; display:inline-flex; align-items:center; gap:8px; padding:0 18px; height:44px;
        border-radius: 12px; background:#fff; color:#065f46; font-weight:700; font-size:14px;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,.1); border:0; cursor:pointer; overflow:hidden;
        text-decoration:none !important; transition: background .2s;
    }
    .btn-sertif:hover{ background:#ecfdf5; }
    .btn-sertif-sweep{ position:absolute; inset:0; background: linear-gradient(90deg, transparent, rgba(110,231,183,.6), transparent); transform: translateX(-100%); transition: transform 1s;}
    .btn-sertif:hover .btn-sertif-sweep{ transform: translateX(100%); }
    .btn-sertif-badge{ display:inline-flex; align-items:center; justify-content:center; padding:0 6px; height:20px; border-radius:6px; background:#059669; color:#fff; font-size:11px; font-weight:800; margin-left:2px; }
    .btn-jelajah {
        display:inline-flex; align-items:center; gap:8px; padding:0 16px; height:44px;
        border-radius:12px; background:rgba(255,255,255,.15); color:#fff; font-weight:500; font-size:14px;
        border:1px solid rgba(255,255,255,.2); backdrop-filter: blur(8px); cursor:pointer;
        text-decoration:none !important; transition: background .2s;
    }
    .btn-jelajah:hover{ background: rgba(255,255,255,.25); }
    .stat-grid { display: grid; grid-template-columns: 1fr; gap: 12px 16px; position: relative; }
    @media(min-width:640px){ .stat-grid{ grid-template-columns: repeat(3,1fr); } }
    .stat-card {
        border-radius:16px; background: rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
        backdrop-filter: blur(8px); padding: 16px 20px; transition: transform .25s, background .2s;
    }
    .stat-card:hover{ transform: translateY(-2px); background: rgba(255,255,255,.17); }
    .stat-head { display:flex; align-items:center; justify-content: space-between; margin-bottom: 10px; }
    .stat-icon {
        width:40px; height:40px; border-radius:12px; background: rgba(255,255,255,.2);
        display:flex; align-items:center; justify-content:center; color:#fff; font-size:18px;
        transition: transform .25s;
    }
    .stat-card:hover .stat-icon{ transform: scale(1.1); }
    .stat-icon.amber{ background: rgba(251,191,36,.25); color:#fef3c7; }
    .stat-icon.sky  { background: rgba(125,211,252,.25); color:#e0f2fe; }
    .chip-mini { display:inline-flex; align-items:center; padding:0 8px; height:20px; border-radius:9999px; font-size:11px; font-weight:500; border:1px solid;}
    .chip-mini.putih{ background:rgba(255,255,255,.15); border-color: rgba(255,255,255,.2); color:#ecfdf5; }
    .chip-mini.amber{ background:rgba(251,191,36,.2); border-color: rgba(252,211,77,.3); color:#fffbeb; }
    .chip-mini.emerald{ background:rgba(16,185,129,.2); border-color: rgba(110,231,183,.3); color:#ecfdf5; }
    .stat-val  { font-size: 32px; font-weight:900; line-height:1; letter-spacing:-.02em; color:#fff; margin:0 0 4px;}
    .stat-label{ font-size:13px; color: rgba(236,253,245,.85); }
    .filter-wrap {
        border-radius:24px; background: rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
        padding:16px 20px; margin-bottom: 24px; backdrop-filter: blur(8px); transition: background .2s;
    }
    .filter-wrap:hover{ background: rgba(255,255,255,.07); }
    .filter-head {
        display:flex; flex-direction:column; justify-content: space-between; align-items:flex-start; gap:16px;
        margin-bottom: 16px;
    }
    @media(min-width:640px){ .filter-head{ flex-direction:row; align-items:center; } }
    .filter-head-left { display:flex; align-items:center; gap:12px; }
    .filter-head-icon {
        width:40px; height:40px; border-radius:16px;
        background: linear-gradient(135deg, rgba(5,150,105,.4), rgba(52,211,153,.2));
        border:1px solid rgba(52,211,153,.3);
        display:flex; align-items:center; justify-content:center; color:#a7f3d0; font-size:18px;
    }
    .filter-head-title { color:#fff; font-weight:600; line-height:1.2; }
    .filter-head-sub   { color: rgba(167,243,208,.6); font-size: 12px; margin-top: 2px; }
    .filter-info { display:flex; align-items:center; gap:8px; font-size:12px; color: rgba(167,243,208,.6); }
    .filter-info b{ color:#fff; font-weight:700; }
    .filter-grid { display:grid; grid-template-columns:1fr; gap:12px; align-items: end; }
    @media(min-width:768px){ .filter-grid{ grid-template-columns: repeat(12,1fr); } }
    .f-5 { grid-column: span 5 / span 5; }
    .f-4 { grid-column: span 4 / span 4; }
    .f-3 { grid-column: span 3 / span 3; }
    @media(max-width:767px){ .f-3, .f-4, .f-5{ grid-column: span 1 / span 1; } }
    .f-label { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:500; color: rgba(167,243,208,.8); margin-bottom:6px; }
    .f-label i{ color:#34d399; }
    .f-input, .f-select, .f-btn, .f-reset {
        height:44px; border-radius:12px; background: rgba(255,255,255,.05); color:#fff;
        border:1px solid rgba(255,255,255,.1); transition: border .2s, box-shadow .2s;
        font-size: 14px; padding: 0 14px; width:100%;
    }
    .f-input::placeholder{ color: rgba(167,243,208,.4); }
    .f-input:focus, .f-select:focus {
        outline: none; border-color: #34d399;
        box-shadow: 0 0 0 4px rgba(52,211,153,.18);
    }
    .f-select {
        appearance:none; padding: 0 14px 0 40px; cursor:pointer;
    }
    .f-group { position: relative; }
    .f-group .ic{ position: absolute; left:14px; top:50%; transform: translateY(-50%); color:#6ee7b7; font-size:14px; }
    .f-btnrow { display:flex; align-items:flex-end; gap:8px; }
    .f-btn {
        flex:1; border:0; cursor:pointer; font-weight:700; color:#fff;
        background: linear-gradient(90deg,#059669,#0d9488);
        box-shadow: 0 12px 25px -12px rgba(5,150,105,.8);
        display:inline-flex; align-items:center; justify-content:center; gap:8px;
    }
    .f-btn:hover{ background: linear-gradient(90deg,#10b981,#14b8a6); }
    .f-reset {
        width:44px; flex: 0 0 44px; padding:0;
        display:inline-flex; align-items:center; justify-content:center; color: rgba(209,250,229,.9);
    }
    .f-reset:hover{ background: rgba(255,255,255,.15); }
    .card-grid { display: grid; grid-template-columns: 1fr; gap: 16px 20px; }
    @media(min-width:640px){ .card-grid{ grid-template-columns: repeat(2,1fr); } }
    @media(min-width:1024px){ .card-grid{ grid-template-columns: repeat(3,1fr); } }
    .act-card {
        position: relative; display:flex; flex-direction: column;
        border-radius:24px; background: rgba(255,255,255,.05);
        border:1px solid rgba(255,255,255,.1); overflow: hidden;
        transition: transform .3s, box-shadow .3s, border-color .3s, background .3s;
    }
    .act-card:hover {
        transform: translateY(-4px); border-color: rgba(52,211,153,.4);
        background: rgba(255,255,255,.08);
        box-shadow: 0 25px 60px -25px rgba(5,150,105,.4);
    }
    .act-thumb {
        position: relative; height: 192px; overflow: hidden;
        background: linear-gradient(135deg, rgba(16,185,129,.5), rgba(20,184,166,.5), rgba(2,132,199,.5));
        display:flex; align-items:center; justify-content:center;
    }
    .act-card:hover .act-thumb img { transform: scale(1.07); }
    .act-thumb img { width:100%; height:100%; object-fit: cover; transition: transform .7s; }
    .thumb-placeholder-icon {
        font-size:72px; color: rgba(255,255,255,.7); position:relative; transition: transform .5s;
    }
    .act-card:hover .thumb-placeholder-icon{ transform: scale(1.1) rotate(3deg); }
    .thumb-radial { position:absolute; inset:0; opacity:.3;
        background-image: radial-gradient(circle at 20% 20%, #fff 0, transparent 35%), radial-gradient(circle at 80% 70%, #6ee7b7 0, transparent 40%);
    }
    .chip-left-top  { position:absolute; top:12px; left:12px; display:flex; align-items:center; gap:8px; }
    .chip-right-top { position:absolute; top:12px; right:12px; display:flex; flex-direction: column; align-items: flex-end; gap:6px; }
    .chip {
        display:inline-flex; align-items:center; gap:6px; padding:0 10px; height:28px;
        border-radius:9999px; font-size:11px; font-weight:600;
        border:1px solid; backdrop-filter: blur(10px); box-shadow: 0 2px 8px rgba(0,0,0,.08);
    }
    .chip i{ font-size:10px; }
    .chip-purple{ background: rgba(168,85,247,.15); color: #ddd6fe; border-color: rgba(168,85,247,.30);}
    .chip-sky   { background: rgba(14,165,233,.15); color: #bae6fd; border-color: rgba(14,165,233,.30);}
    .chip-amber { background: rgba(245,158,11,.15);  color: #fde68a; border-color: rgba(245,158,11,.30); }
    .chip-fuchsia{background: rgba(217,70,239,.15);  color: #f5d0fe; border-color: rgba(217,70,239,.30); }
    .chip-rose  { background: rgba(244,63,94,.15);  color: #fecdd3; border-color: rgba(244,63,94,.30); }
    .chip-emerald{background: rgba(16,185,129,.15);  color: #a7f3d0; border-color: rgba(16,185,129,.30); }
    .chip-white-opa{ background: rgba(15,23,42,.7); border:1px solid rgba(255,255,255,.15); color: rgba(255,255,255,.8);}
    .chip-pulse {
        background: rgba(244,63,94,.9); color:#fff; border-color: rgba(253,164,175,.4);
        box-shadow: 0 6px 20px -8px rgba(244,63,94,.8);
        animation: pulse 2s cubic-bezier(.4,0,.6,1) infinite;
    }
    @keyframes pulse {
        0%,100%{ opacity: 1; }
        50%{ opacity: .85; }
    }
    .chip-hadir { background:#10b981; color:#fff; border-color: rgba(110,231,183,.5); }
    .chip-daftar{ background: linear-gradient(90deg,#fbbf24,#fcd34d); color:#78350f; border-color: rgba(252,211,77,.6); }
    .chip-sertif{
        background: rgba(14,165,233,.95); color:#fff; border-color: rgba(125,211,252,.4);
        box-shadow: 0 8px 25px -10px rgba(14,165,233,.9);
    }
    .act-body { padding:16px 20px; display:flex; flex-direction: column; flex:1; gap:14px; }
    .act-title {
        font-weight:800; font-size:17px; line-height:1.35; color:#fff;
        margin: 0 0 6px; transition: color .2s;
    }
    .act-card:hover .act-title{ color:#6ee7b7; }
    .act-org {
        display: inline-flex; align-items:center; gap:6px;
        font-size:12px; color: rgba(167,243,208,.65);
    }
    .act-meta {
        display: grid; grid-template-columns: repeat(2,1fr);
        column-gap:10px; row-gap:8px;
        padding:12px 0;
        border-top:1px solid rgba(255,255,255,.05);
        border-bottom:1px solid rgba(255,255,255,.05);
        font-size:12.5px; color: rgba(236,253,245,.9);
    }
    .meta-item { display:flex; align-items:flex-start; gap:8px; }
    .meta-item.full { grid-column: span 2 / span 2; }
    .meta-chip {
        margin-top:2px; width:24px; height:24px; border-radius:8px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
        background: rgba(16,185,129,.15); border:1px solid rgba(16,185,129,.20);
    }
    .meta-chip i{ font-size:11px; color:#6ee7b7; }
    .meta-chip.rose{ background: rgba(244,63,94,.15); border-color: rgba(244,63,94,.20);}
    .meta-chip.rose i{ color:#fda4af; }
    .meta-chip.sky { background: rgba(14,165,233,.15); border-color: rgba(14,165,233,.20);}
    .meta-chip.sky i { color:#7dd3fc; }
    .meta-label { font-size:10px; letter-spacing:.08em; text-transform: uppercase; color: rgba(167,243,208,.5); font-weight:600; }
    .meta-val   { font-weight:600; line-height:1.35; }
    .meta-val.dim { color: rgba(209,250,229,.6); font-weight:400; }
    .flow-head { display:flex; align-items:center; justify-content: space-between; margin-bottom: 6px;}
    .flow-title { font-size:10px; letter-spacing: .1em; text-transform: uppercase; color: rgba(167,243,208,.5); font-weight:700;}
    .flow-count { display:flex; align-items:center; gap:4px; font-size:11px; color: rgba(167,243,208,.6); }
    .flow-count b{ color:#fff; font-weight:700; }
    .flow-grid { display: grid; grid-template-columns: repeat(3,1fr); gap:6px; font-size: 10.5px; }
    .flow-item { position: relative; }
    .flow-chip {
        position: relative; display:flex; align-items:center; gap:6px;
        border-radius:12px; border:1px solid;
        padding:6px 8px; transition: background .2s, border-color .2s;
        overflow: hidden;
    }
    .flow-chip.done  { background: rgba(16,185,129,.20); border-color: rgba(52,211,153,.4); color: #d1fae5;}
    .flow-chip.on    { background: rgba(255,255,255,.10); border-color: rgba(255,255,255,.15); color:#fff; }
    .flow-chip.off   { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.05); color: rgba(167,243,208,.35);}
    .flow-chip i.fc  { font-size:10px; }
    .flow-chip i.done{ color:#6ee7b7; }
    .flow-chip .lbl  { font-weight:600; flex:1; min-width:0; overflow:hidden; text-overflow: ellipsis; white-space: nowrap;}
    .flow-chip .check{ margin-left:auto; color:#6ee7b7; font-size:10px; }
    .flow-arrow {
        position: absolute; right:-6px; top:50%; transform: translateY(-50%); font-size:8px; z-index:2;
        color: rgba(167,243,208,.25);
    }
    .flow-arrow.ok { color:#6ee7b7; }
    .cta-row {
        display:flex; align-items:center; justify-content: space-between; gap:8px; margin-top:auto; padding-top:8px;
    }
    .cta-hint { font-size:11px; color: rgba(167,243,208,.55); max-width:55%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .cta-hint.ok  { color:#6ee7b7; font-weight:500; }
    .cta-btn {
        flex-shrink:0; height:40px; padding:0 14px; border-radius:12px;
        display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#fff;
        text-decoration:none !important; transition: filter .2s, transform .2s;
        cursor:pointer; border:0;
    }
    .cta-btn:hover{ filter: brightness(1.07); transform: translateY(-1px); }
    .cta-ikut  { background: linear-gradient(90deg,#059669,#14b8a6); box-shadow: 0 12px 25px -15px rgba(5,150,105,.8);}
    .cta-tiket { background: linear-gradient(90deg,#f59e0b,#f97316); box-shadow: 0 12px 25px -15px rgba(245,158,11,.8);}
    .cta-ser   { background: linear-gradient(90deg,#0891b2,#4f46e5); box-shadow: 0 12px 25px -15px rgba(8,145,178,.8);}
    .pager {
        display:flex; flex-direction:column; align-items:flex-start; justify-content:space-between; gap:16px;
        border-radius:24px; background: rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
        padding:16px 20px; margin-top: 32px; backdrop-filter: blur(8px);
    }
    @media(min-width:640px){ .pager{ flex-direction:row; align-items:center; } }
    .pager-info { display:flex; align-items:center; gap:8px; font-size:12px; color: rgba(167,243,208,.7);}
    .pager-info b{ color:#fff; font-weight:800; }
    .empty-wrap {
        position: relative; border-radius:24px; background: rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1);
        padding: 48px 64px; text-align:center; overflow:hidden;
    }
    @media(max-width:640px){ .empty-wrap{ padding: 48px 24px; } }
    .empty-blur { position:absolute; top:-80px; left:50%; transform: translateX(-50%); width:320px; height:320px; border-radius:9999px; background: rgba(16,185,129,.1); filter: blur(60px); }
    .empty-icon {
        position: relative; display:inline-flex; align-items:center; justify-content:center;
        width:96px; height:96px; border-radius:28px;
        background: linear-gradient(135deg, rgba(16,185,129,.25), rgba(56,189,248,.2));
        border:1px solid rgba(255,255,255,.15); margin-bottom: 24px;
    }
    .empty-icon i{ font-size:48px; color:#a7f3d0; }
    .empty-title { font-size:20px; font-weight:800; color:#fff; position: relative; margin:0 0 8px; }
    .empty-sub   { font-size:14px; color: rgba(167,243,208,.65); max-width:460px; margin: 0 auto 24px; position: relative;}
    .empty-btn {
        position: relative; display:inline-flex; align-items:center; gap:8px; padding: 0 20px; height:44px;
        border-radius:12px; background:#059669; color:#fff; font-weight:600; font-size:14px;
        transition: background .2s; text-decoration:none !important;
    }
    .empty-btn:hover{ background:#10b981; }
    </style>

    <script>
        if ('scrollTo' in window) {
            try { window.scrollTo({ top: 0, left: 0, behavior: 'auto' }); } catch(e){}
        }
        if ('scrollRestoration' in history) { history.scrollRestoration = 'manual'; }
        document.addEventListener('DOMContentLoaded', function(){
            setTimeout(function(){
                try { window.scrollTo(0,0); } catch(e){}
            }, 50);
        });
    </script>

    <section class="mhs-hero">
        <div class="blur-a"></div>
        <div class="blur-b"></div>
        <div class="watermark"><i class="fa-solid fa-calendar-star"></i></div>

        <div class="mhs-hero-top">
            <div class="mhs-head">
                <div class="mhs-avatar-wrap">
                    @if(!empty($dosen->foto) || !empty(optional(auth()->user())->foto))
                        <img
                            src="{{ \Illuminate\Support\Facades\Storage::url($dosen->foto ?? auth()->user()->foto) }}"
                            alt="avatar" class="mhs-avatar" />
                    @else
                        <div class="mhs-avatar-initial">{{ $inisial }}</div>
                    @endif
                    <span class="mhs-avatar-dot"><i class="fa-solid fa-sparkles"></i></span>
                </div>
                <div>
                    <div class="chip-salam">
                        <i class="fa-solid fa-hand-sparkles"></i>
                        Selamat {{ $salam }},
                    </div>
                    <h1 class="mhs-h1">{{ $namaPotong }} 👋</h1>
                    <p class="mhs-sub">
                        Yuk ikuti kegiatan &amp; seminar kampus! Setiap kehadiran Anda akan tercatat otomatis dan
                        <strong style="color:#fff;">sertifikat bisa langsung didownload.</strong>
                    </p>
                </div>
            </div>

            <div class="mhs-cta-row">
                <a href="{{ route('dosen.kegiatan.sertifikat-saya') }}" class="btn-sertif">
                    <span class="btn-sertif-sweep"></span>
                    <i class="fa-solid fa-certificate" style="color:#059669; font-size:16px;"></i>
                    Sertifikat Saya
                    <span class="btn-sertif-badge">{{ $totalHadir }}</span>
                </a>
                <a href="#daftar-kegiatan" class="btn-jelajah">
                    <i class="fa-solid fa-list"></i>
                    Jelajahi Kegiatan
                </a>
            </div>
        </div>

        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon"><i class="fa-solid fa-calendar-week"></i></div>
                    <span class="chip-mini putih">Terbaru</span>
                </div>
                <div class="stat-val">{{ $totalKegiatan }}</div>
                <div class="stat-label">Total Kegiatan Tersedia</div>
            </div>
            <div class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon amber"><i class="fa-solid fa-user-plus"></i></div>
                    <span class="chip-mini amber">Diproses</span>
                </div>
                <div class="stat-val">{{ $totalSaya }}</div>
                <div class="stat-label">Kegiatan yang Anda Ikuti</div>
            </div>
            <div class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon sky"><i class="fa-solid fa-award"></i></div>
                    <span class="chip-mini emerald">Selesai</span>
                </div>
                <div class="stat-val">{{ $totalHadir }}</div>
                <div class="stat-label">Sertifikat Diterima</div>
            </div>
        </div>
    </section>

    <form method="GET" action="{{ route('dosen.kegiatan.index') }}" class="filter-wrap">
        <div class="filter-head">
            <div class="filter-head-left">
                <div class="filter-head-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <div>
                    <div class="filter-head-title">Cari &amp; Filter Kegiatan</div>
                    <div class="filter-head-sub">Temukan kegiatan yang cocok dengan minat Anda di bawah ini.</div>
                </div>
            </div>
            <div class="filter-info">
                <i class="fa-solid fa-bullseye" style="color:#34d399;"></i>
                Menampilkan <b>{{ $items->firstItem() ?? 0 }}</b>
                - <b>{{ $items->lastItem() ?? 0 }}</b>
                dari <b>{{ $totalKegiatan }}</b> kegiatan
            </div>
        </div>

        <div class="filter-grid">
            <div class="f-5">
                <label class="f-label">
                    <i class="fa-solid fa-keyboard"></i> Cari Kegiatan
                </label>
                <div class="f-group">
                    <i class="fa-solid fa-search ic"></i>
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Judul, lokasi, penyelenggara, narasumber..."
                           class="f-input" />
                </div>
            </div>

            <div class="f-4">
                <label class="f-label">
                    <i class="fa-solid fa-layer-group"></i> Kategori / Jenis
                </label>
                <div class="f-group">
                    <i class="fa-solid fa-tags ic"></i>
                    <select name="jenis" class="f-select">
                        <option value="">✨ Semua Jenis Kegiatan</option>
                        @foreach($jenisList as $j)
                            <option value="{{ $j }}" @selected($jenis === $j)>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="f-3">
                <div class="f-btnrow">
                    <button type="submit" class="f-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        Terapkan
                    </button>
                    <a href="{{ route('dosen.kegiatan.index') }}" class="f-reset" title="Reset filter">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <section id="daftar-kegiatan">
        @if($items->isEmpty())
            <div class="empty-wrap">
                <div class="empty-blur"></div>
                <div class="empty-icon">
                    <i class="fa-solid fa-calendar-xmark"></i>
                </div>
                <div class="empty-title">Belum ada kegiatan yang tersedia</div>
                <div class="empty-sub">
                    Belum ada kegiatan untuk ditampilkan saat ini. Silakan kembali lagi nanti ya, atau coba hapus filter pencarian Anda.
                </div>
                <a href="{{ route('dosen.kegiatan.index') }}" class="empty-btn">
                    <i class="fa-solid fa-house"></i>
                    Tampilkan Semua Kegiatan
                </a>
            </div>
        @else
            <div class="card-grid">
                @foreach($items as $item)
                    @php
                        $terdaftar = in_array($item->id, $kegiatanSayaIds, true);
                        $statusHadir = $terdaftar ? ($pesertaSaya[$item->id] ?? false) : null;
                        $sisaHari = $item->tanggal_kegiatan ? now()->startOfDay()->diffInDays($item->tanggal_kegiatan->startOfDay(), false) : null;

                        $jenisLc = strtolower(trim($item->jenis_kegiatan ?? ''));
                        if (str_contains($jenisLc, 'seminar'))       $chipClass = 'chip-purple';
                        elseif (str_contains($jenisLc, 'workshop'))  $chipClass = 'chip-sky';
                        elseif (str_contains($jenisLc, 'pelatihan')) $chipClass = 'chip-amber';
                        elseif (str_contains($jenisLc, 'konferensi'))$chipClass = 'chip-fuchsia';
                        elseif (str_contains($jenisLc, 'rapat'))     $chipClass = 'chip-rose';
                        else                                          $chipClass = 'chip-emerald';
                    @endphp
                    <article class="act-card">
                        <div class="act-thumb">
                            @if(!empty($item->gambar_url))
                                <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" />
                            @else
                                <div class="thumb-radial"></div>
                                <i class="fa-solid fa-calendar-check thumb-placeholder-icon"></i>
                            @endif

                            <div class="chip-left-top">
                                <span class="chip {{ $chipClass }}">
                                    <i class="fa-solid fa-tag"></i>
                                    {{ $item->jenis_kegiatan ?: 'Umum' }}
                                </span>
                                @if(!is_null($sisaHari) && $sisaHari >= 0)
                                    @if($sisaHari === 0)
                                        <span class="chip chip-pulse">
                                            <i class="fa-solid fa-fire"></i> HARI INI
                                        </span>
                                    @elseif($sisaHari <= 7)
                                        <span class="chip chip-amber" style="background:rgba(251,191,36,.9); color:#78350f; border-color: rgba(252,211,77,.6);">
                                            <i class="fa-solid fa-hourglass-half"></i> {{ $sisaHari }} hari lagi
                                        </span>
                                    @else
                                        <span class="chip chip-white-opa">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ \Illuminate\Support\Carbon::parse($item->tanggal_kegiatan)->locale('id')->shortMonthName }}
                                        </span>
                                    @endif
                                @endif
                            </div>

                            <div class="chip-right-top">
                                @if($item->sertifikat_aktif)
                                    <span class="chip chip-sertif">
                                        <i class="fa-solid fa-certificate"></i>
                                        Dapat Sertifikat
                                    </span>
                                @endif
                                @if($terdaftar)
                                    @if($statusHadir)
                                        <span class="chip chip-hadir">
                                            <i class="fa-solid fa-circle-check"></i> Sudah Hadir
                                        </span>
                                    @else
                                        <span class="chip chip-daftar">
                                            <i class="fa-solid fa-user-check"></i> Sudah Daftar
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="act-body">
                            <div>
                                <h3 class="act-title">{{ $item->judul }}</h3>
                                @if($item->penyelenggara)
                                    <div class="act-org">
                                        <i class="fa-solid fa-building-columns" style="color:#34d399; font-size:11px;"></i>
                                        {{ $item->penyelenggara }}
                                    </div>
                                @endif
                            </div>

                            <div class="act-meta">
                                <div class="meta-item">
                                    <div class="meta-chip">
                                        <i class="fa-regular fa-calendar"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Tanggal</div>
                                        <div class="meta-val">{{ $item->tanggal_kegiatan?->format('d M Y') ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="meta-item">
                                    <div class="meta-chip">
                                        <i class="fa-regular fa-clock"></i>
                                    </div>
                                    <div>
                                        <div class="meta-label">Waktu</div>
                                        <div class="meta-val">
                                            @if($item->waktu_mulai)
                                                {{ substr($item->waktu_mulai,0,5) }}
                                                @if($item->waktu_selesai)
                                                    <span class="meta-val.dim">- {{ substr($item->waktu_selesai,0,5) }}</span>
                                                @endif
                                            @else -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($item->lokasi)
                                    <div class="meta-item full">
                                        <div class="meta-chip rose">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div style="min-width:0;">
                                            <div class="meta-label">Lokasi</div>
                                            <div class="meta-val" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $item->lokasi }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if($item->narasumber)
                                    <div class="meta-item full">
                                        <div class="meta-chip sky">
                                            <i class="fa-solid fa-user-tie"></i>
                                        </div>
                                        <div style="min-width:0;">
                                            <div class="meta-label">Narasumber / Instruktur</div>
                                            <div class="meta-val" style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $item->narasumber }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="flow-head">
                                    <div class="flow-title">Status Anda</div>
                                    <div class="flow-count">
                                        <i class="fa-solid fa-users" style="color:#34d399;"></i>
                                        <b>{{ $item->peserta_count }}</b> peserta
                                    </div>
                                </div>
                                <div class="flow-grid">
                                    @php
                                        $steps = [
                                            ['label'=>'Daftar',    'icon'=>'fa-file-signature',  'active'=>true,           'done'=>$terdaftar],
                                            ['label'=>'Hadir',     'icon'=>'fa-clipboard-check', 'active'=>$terdaftar,     'done'=>$statusHadir],
                                            ['label'=>'Sertifikat','icon'=>'fa-certificate',     'active'=>!empty($statusHadir) && !empty($item->sertifikat_aktif), 'done'=>!empty($statusHadir) && !empty($item->sertifikat_aktif)],
                                        ];
                                    @endphp
                                    @foreach($steps as $idx => $s)
                                        <div class="flow-item">
                                            <div class="flow-chip {{ $s['done'] ? 'done' : ($s['active'] ? 'on' : 'off') }}">
                                                <i class="fa-solid {{ $s['icon'] }} fc {{ $s['done'] ? 'done' : '' }}"></i>
                                                <span class="lbl">{{ $s['label'] }}</span>
                                                @if($s['done'])<i class="fa-solid fa-circle-check check"></i>@endif
                                            </div>
                                            @if($idx < 2)
                                                <i class="fa-solid fa-caret-right flow-arrow {{ ($s['done'] && $steps[$idx+1]['active']) ? 'ok' : '' }}"></i>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="cta-row">
                                <div class="cta-hint {{ ($terdaftar && $statusHadir) ? 'ok' : '' }}">
                                    @if($terdaftar && $statusHadir)
                                        <span style="display:inline-flex; align-items:center; gap:6px;">
                                            <i class="fa-solid fa-party-horn"></i> Selesai ✓
                                        </span>
                                    @elseif($terdaftar)
                                        <i class="fa-solid fa-hourglass-start" style="color:#fbbf24;"></i>
                                        Menunggu waktu kegiatan
                                    @else
                                        <i class="fa-regular fa-bell"></i>
                                        Ayo daftar sebelum penuh!
                                    @endif
                                </div>
                                <a href="{{ route('dosen.kegiatan.show', $item) }}" class="cta-btn
                                    @if($terdaftar && $statusHadir) cta-ser
                                    @elseif($terdaftar) cta-tiket
                                    @else cta-ikut
                                    @endif">
                                    @if($terdaftar && $statusHadir)
                                        Lihat Sertifikat <i class="fa-solid fa-file-invoice"></i>
                                    @elseif($terdaftar)
                                        Tiket Saya <i class="fa-solid fa-ticket"></i>
                                    @else
                                        Ikuti Sekarang <i class="fa-solid fa-arrow-right"></i>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            @if($items->hasPages())
                <div class="pager">
                    <div class="pager-info">
                        <i class="fa-solid fa-turn-up" style="color:#34d399;"></i>
                        Menampilkan halaman <b>{{ $items->currentPage() }}</b>
                        dari <b>{{ $items->lastPage() }}</b>
                        (Total <b>{{ $totalKegiatan }}</b> kegiatan)
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        {{ $items->onEachSide(1)->links() }}
                    </div>
                </div>
            @endif
        @endif
    </section>
</x-portal-layout>
