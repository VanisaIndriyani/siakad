<x-portal-layout :title="'Kalender Akademik - '.config('app.name')" subtitle="Kalender Akademik">
    <x-slot:sidebar>
        @include('admin.partials.sidebar')
    </x-slot:sidebar>

    <style>
        .cal-wrap { max-width: 520px; margin: 0 auto; }
        .cal-card { background: #ffffff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 18px; padding: 16px; color: #111827; }
        .cal-titlebar { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .cal-back { height: 44px; width: 44px; border-radius: 999px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; color: #111827; }
        .cal-title { flex: 1; text-align: center; font-size: 22px; font-weight: 900; letter-spacing: -0.2px; }
        .cal-spacer { height: 44px; width: 44px; }
        .cal-monthbar { display: flex; align-items: center; justify-content: space-between; margin-top: 6px; }
        .cal-monthbtn { height: 40px; width: 40px; border-radius: 999px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: #111827; }
        .cal-monthtitle { font-size: 18px; font-weight: 900; }
        .cal-dow { display: grid; grid-template-columns: repeat(7, 1fr); margin-top: 14px; gap: 6px; }
        .cal-dow div { text-align: center; font-size: 12px; font-weight: 800; color: rgba(17, 24, 39, 0.45); padding: 4px 0; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin-top: 8px; }
        .cal-day { position: relative; height: 40px; border-radius: 10px; border: 1px solid rgba(17, 24, 39, 0.10); background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px; color: #111827; user-select: none; }
        .cal-day--muted { background: #f3f4f6; color: rgba(17, 24, 39, 0.35); border-color: rgba(17, 24, 39, 0.06); cursor: default; }
        .cal-day--today { outline: 2px solid rgba(16, 185, 129, 0.30); outline-offset: 2px; }
        .cal-day--selected { background: rgba(59, 130, 246, 0.10); border-color: rgba(59, 130, 246, 0.35); }
        .cal-mark { position: absolute; left: 6px; right: 6px; bottom: 4px; height: 4px; border-radius: 999px; background: #2563eb; opacity: 0.9; }
        .cal-section { margin-top: 18px; }
        .cal-section h2 { font-size: 20px; font-weight: 900; margin: 0; color: #111827; }
        .cal-reset { height: 36px; padding: 0 12px; border-radius: 10px; border: 1px solid rgba(17, 24, 39, 0.12); background: #fff; font-weight: 800; cursor: pointer; }
        .cal-list { display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 12px; }
        .cal-item { background: #fff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 14px; padding: 14px; display: flex; gap: 12px; align-items: flex-start; }
        .cal-icon { height: 44px; width: 44px; border-radius: 12px; background: rgba(59, 130, 246, 0.10); border: 1px solid rgba(59, 130, 246, 0.18); display: flex; align-items: center; justify-content: center; color: #2563eb; flex: 0 0 auto; }
        .cal-item-title { font-size: 16px; font-weight: 900; margin: 0; color: #111827; }
        .cal-item-meta { margin-top: 4px; font-size: 13px; font-weight: 700; color: rgba(17, 24, 39, 0.55); }
        .cal-empty { background: #fff; border: 1px solid rgba(17, 24, 39, 0.12); border-radius: 14px; padding: 24px; text-align: center; color: rgba(17, 24, 39, 0.60); font-weight: 800; }
        @media (max-width: 420px) {
            .cal-card { padding: 14px; }
            .cal-title { font-size: 20px; }
            .cal-monthtitle { font-size: 17px; }
        }
    </style>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="text-xl font-semibold">Kalender Akademik</div>
            <div class="text-sm text-emerald-100/70">Kelola kegiatan akademik (UJIAN, INPUT NILAI, KRS, dsb).</div>
        </div>
        <a href="{{ route('admin.kalender-akademik.create') }}" class="h-10 px-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 transition">
            <i class="fa-solid fa-plus"></i>
            <span class="text-sm font-medium">Tambah</span>
        </a>
    </div>

    @php
        $eventsForCalendar = ($eventsAll ?? collect())->map(function ($event) {
            $start = $event->tanggal_mulai?->format('Y-m-d');
            $end = $event->tanggal_selesai?->format('Y-m-d') ?: $start;
            return [
                'id' => (int) $event->id,
                'judul' => (string) $event->judul,
                'start' => (string) $start,
                'end' => (string) $end,
                'kategori' => $event->kategori ? (string) $event->kategori : null,
                'deskripsi' => $event->deskripsi ? (string) $event->deskripsi : null,
            ];
        })->values()->all();
    @endphp

    <div class="mt-5 cal-wrap">
        <div class="cal-card">
            <div class="cal-titlebar">
                <a href="{{ route('admin.dashboard') }}" class="cal-back" aria-label="Kembali">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="cal-title">Kalender Akademik</div>
                <div class="cal-spacer"></div>
            </div>

            <div class="cal-monthbar">
                <button id="calPrev" type="button" class="cal-monthbtn" aria-label="Bulan sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div id="calTitle" class="cal-monthtitle"></div>
                <button id="calNext" type="button" class="cal-monthbtn" aria-label="Bulan berikutnya">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div class="cal-dow">
                <div>M</div><div>S</div><div>S</div><div>R</div><div>K</div><div>J</div><div>S</div>
            </div>
            <div id="calGrid" class="cal-grid"></div>
        </div>

        <div class="cal-section">
            <div style="display:flex; align-items:center; justify-content:space-between; gap: 12px;">
                <h2>Semua Kegiatan</h2>
                <button id="calReset" type="button" class="cal-reset" style="display:none;">Reset</button>
            </div>
            <div id="calList" class="cal-list"></div>
        </div>
    </div>

    <div class="mt-5 rounded-2xl bg-white/5 border border-white/10 p-5">
        <form method="GET" action="{{ route('admin.kalender-akademik.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input name="q" value="{{ $q }}" placeholder="Cari judul/kategori..."
                   class="h-11 w-full rounded-xl bg-white/5 border border-white/10 px-4 text-sm text-white placeholder:text-emerald-100/40 focus:outline-none focus:ring-2 focus:ring-emerald-500/30" />
            <div class="flex items-center gap-2">
                <button class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">Cari</button>
                <a href="{{ route('admin.kalender-akademik.index') }}" class="h-11 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium inline-flex items-center">Reset</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-white/10 bg-white/5">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-white/5 text-emerald-100/80">
                    <tr>
                        <th class="text-left font-medium px-4 py-3">Judul</th>
                        <th class="text-left font-medium px-4 py-3">Tanggal</th>
                        <th class="text-left font-medium px-4 py-3">Kategori</th>
                        <th class="text-right font-medium px-4 py-3 w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse ($events as $event)
                        <tr class="hover:bg-white/5">
                            <td class="px-4 py-3 font-medium">{{ $event->judul }}</td>
                            <td class="px-4 py-3 text-emerald-100/80">
                                @php
                                    $mulai = $event->tanggal_mulai?->format('d/m/Y');
                                    $selesai = $event->tanggal_selesai?->format('d/m/Y');
                                @endphp
                                {{ $mulai }}@if ($selesai && $selesai !== $mulai) - {{ $selesai }}@endif
                            </td>
                            <td class="px-4 py-3 text-emerald-100/80">{{ $event->kategori ?: '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.kalender-akademik.edit', $event) }}" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                                        <i class="fa-solid fa-pen"></i>
                                        <span class="text-sm font-medium">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.kalender-akademik.destroy', $event) }}" data-confirm="Hapus kegiatan ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-9 px-3 inline-flex items-center gap-2 rounded-xl bg-red-500/15 hover:bg-red-500/25 border border-red-500/25 transition text-red-100">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span class="text-sm font-medium">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-emerald-100/70">Belum ada kegiatan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $events->links() }}
    </div>

    <script>
        (function () {
            const events = @js($eventsForCalendar);

            const monthNames = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            function pad2(n) {
                return String(n).padStart(2, '0');
            }

            function formatDMY(date) {
                return `${pad2(date.getDate())}/${pad2(date.getMonth() + 1)}/${date.getFullYear()}`;
            }

            function parseISO(iso) {
                if (!iso) return null;
                const parts = String(iso).split('-').map((v) => parseInt(v, 10));
                if (parts.length !== 3) return null;
                return new Date(parts[0], parts[1] - 1, parts[2]);
            }

            function startOfMonth(d) {
                return new Date(d.getFullYear(), d.getMonth(), 1);
            }

            function endOfMonth(d) {
                return new Date(d.getFullYear(), d.getMonth() + 1, 0);
            }

            function sameDay(a, b) {
                return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
            }

            function isoDay(d) {
                return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
            }

            function overlaps(startA, endA, startB, endB) {
                return startA <= endB && endA >= startB;
            }

            function inRange(day, start, end) {
                return day >= start && day <= end;
            }

            const normalized = events.map((e) => {
                const start = parseISO(e.start);
                const end = parseISO(e.end) || start;
                return {
                    ...e,
                    startDate: start,
                    endDate: end,
                };
            }).filter((e) => e.startDate instanceof Date && !isNaN(e.startDate));

            const $grid = document.getElementById('calGrid');
            const $title = document.getElementById('calTitle');
            const $list = document.getElementById('calList');
            const $btnPrev = document.getElementById('calPrev');
            const $btnNext = document.getElementById('calNext');
            const $btnReset = document.getElementById('calReset');

            let current = startOfMonth(new Date());
            let selectedDay = null;

            function eventsForMonth(monthDate) {
                const s = startOfMonth(monthDate);
                const e = endOfMonth(monthDate);
                return normalized
                    .filter((ev) => overlaps(ev.startDate, ev.endDate || ev.startDate, s, e))
                    .sort((a, b) => (a.startDate - b.startDate) || String(a.judul).localeCompare(String(b.judul)));
            }

            function eventsForDay(dayDate) {
                return normalized
                    .filter((ev) => inRange(dayDate, ev.startDate, ev.endDate || ev.startDate))
                    .sort((a, b) => (a.startDate - b.startDate) || String(a.judul).localeCompare(String(b.judul)));
            }

            function setSelectedDay(dayDate) {
                selectedDay = dayDate;
                if ($btnReset) {
                    $btnReset.style.display = selectedDay ? 'inline-flex' : 'none';
                }
                render();
            }

            function renderGrid() {
                const monthStart = startOfMonth(current);
                const today = new Date();

                const firstDow = monthStart.getDay();
                const offset = (firstDow + 6) % 7;
                const gridStart = new Date(monthStart);
                gridStart.setDate(monthStart.getDate() - offset);

                const monthEvents = eventsForMonth(current);
                const daysWithEvents = new Set();
                for (const ev of monthEvents) {
                    let d = new Date(ev.startDate);
                    const end = ev.endDate || ev.startDate;
                    while (d <= end) {
                        if (d.getMonth() === current.getMonth() && d.getFullYear() === current.getFullYear()) {
                            daysWithEvents.add(isoDay(d));
                        }
                        d.setDate(d.getDate() + 1);
                    }
                }

                $grid.innerHTML = '';

                for (let i = 0; i < 42; i++) {
                    const d = new Date(gridStart);
                    d.setDate(gridStart.getDate() + i);

                    const isCurrentMonth = d.getMonth() === current.getMonth() && d.getFullYear() === current.getFullYear();
                    const isToday = sameDay(d, today);
                    const isSelected = selectedDay && sameDay(d, selectedDay);
                    const hasEvents = daysWithEvents.has(isoDay(d));

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'cal-day';
                    btn.textContent = String(d.getDate());

                    if (isCurrentMonth) {
                        btn.addEventListener('click', function () {
                            setSelectedDay(new Date(d.getFullYear(), d.getMonth(), d.getDate()));
                        });
                    } else {
                        btn.disabled = true;
                        btn.classList.add('cal-day--muted');
                    }

                    if (isToday && isCurrentMonth) btn.classList.add('cal-day--today');
                    if (isSelected && isCurrentMonth) btn.classList.add('cal-day--selected');

                    if (hasEvents && isCurrentMonth) {
                        const mark = document.createElement('div');
                        mark.className = 'cal-mark';
                        btn.appendChild(mark);
                    }

                    $grid.appendChild(btn);
                }

                $title.textContent = `${monthNames[current.getMonth()]} ${current.getFullYear()}`;
            }

            function renderList() {
                const items = selectedDay ? eventsForDay(selectedDay) : eventsForMonth(current);

                $list.innerHTML = '';

                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'cal-empty';
                    empty.textContent = selectedDay ? 'Tidak ada kegiatan di tanggal ini.' : 'Belum ada kegiatan di bulan ini.';
                    $list.appendChild(empty);
                    return;
                }

                for (const ev of items) {
                    const card = document.createElement('div');
                    card.className = 'cal-item';

                    const iconWrap = document.createElement('div');
                    iconWrap.className = 'cal-icon';
                    iconWrap.innerHTML = '<i class="fa-solid fa-calendar-days"></i>';

                    const title = document.createElement('div');
                    title.className = 'cal-item-title';
                    title.textContent = ev.judul;

                    const meta = document.createElement('div');
                    meta.className = 'cal-item-meta';

                    const s = ev.startDate;
                    const e = ev.endDate || ev.startDate;
                    const same = s && e && sameDay(s, e);
                    const dateText = same ? formatDMY(s) : `${formatDMY(s)} - ${formatDMY(e)}`;

                    meta.textContent = dateText + (ev.kategori ? ` • ${ev.kategori}` : '');

                    const content = document.createElement('div');
                    content.style.flex = '1';
                    content.appendChild(title);
                    content.appendChild(meta);

                    card.appendChild(iconWrap);
                    card.appendChild(content);

                    if (ev.deskripsi) {
                        const desc = document.createElement('div');
                        desc.style.marginTop = '10px';
                        desc.style.fontSize = '13px';
                        desc.style.fontWeight = '700';
                        desc.style.color = 'rgba(17, 24, 39, 0.70)';
                        desc.style.whiteSpace = 'pre-line';
                        desc.textContent = ev.deskripsi;
                        content.appendChild(desc);
                    }

                    $list.appendChild(card);
                }
            }

            function render() {
                renderGrid();
                renderList();
            }

            if ($btnPrev) {
                $btnPrev.addEventListener('click', function () {
                    current = startOfMonth(new Date(current.getFullYear(), current.getMonth() - 1, 1));
                    selectedDay = null;
                    render();
                });
            }
            if ($btnNext) {
                $btnNext.addEventListener('click', function () {
                    current = startOfMonth(new Date(current.getFullYear(), current.getMonth() + 1, 1));
                    selectedDay = null;
                    render();
                });
            }
            if ($btnReset) {
                $btnReset.addEventListener('click', function () {
                    selectedDay = null;
                    render();
                });
            }

            render();
        })();
    </script>
</x-portal-layout>
