<x-portal-layout :title="'Kalender Akademik - '.config('app.name')" subtitle="Kalender Akademik">
    <x-slot:sidebar>
        @include('mahasiswa.partials.sidebar')
    </x-slot:sidebar>

    @php
        $eventsPayload = $events->map(function ($event) {
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

    <div class="max-w-3xl mx-auto">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-5">
            <div class="flex items-center justify-between gap-3">
                <button id="calPrev" type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <div class="text-center">
                    <div id="calTitle" class="text-lg font-semibold"></div>
                    <div id="calSubtitle" class="text-xs text-emerald-100/60 mt-1"></div>
                </div>
                <button id="calNext" type="button" class="h-10 w-10 inline-flex items-center justify-center rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>

            <div class="mt-4 grid grid-cols-7 gap-1 text-xs text-emerald-100/70 font-semibold">
                <div class="text-center py-2">M</div>
                <div class="text-center py-2">S</div>
                <div class="text-center py-2">S</div>
                <div class="text-center py-2">R</div>
                <div class="text-center py-2">K</div>
                <div class="text-center py-2">J</div>
                <div class="text-center py-2">S</div>
            </div>

            <div id="calGrid" class="grid grid-cols-7 gap-1"></div>
        </div>

        <div class="mt-6">
            <div class="flex items-center justify-between gap-3">
                <div class="text-lg font-semibold">Semua Kegiatan</div>
                <button id="calReset" type="button" class="hidden h-10 px-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition text-sm font-medium">
                    Reset Tanggal
                </button>
            </div>
            <div id="calList" class="mt-3 grid grid-cols-1 gap-3"></div>
        </div>
    </div>

    <script>
        (function () {
            const events = @js($eventsPayload);

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
            const $subtitle = document.getElementById('calSubtitle');
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
                $btnReset.classList.toggle('hidden', !selectedDay);
                render();
            }

            function renderGrid() {
                const monthStart = startOfMonth(current);
                const monthEnd = endOfMonth(current);
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
                    btn.className = [
                        'relative',
                        'h-11',
                        'rounded-xl',
                        'border',
                        'transition',
                        'text-sm',
                        'font-semibold',
                        'flex',
                        'items-center',
                        'justify-center',
                        isCurrentMonth ? 'bg-white/0 border-white/10 text-white hover:bg-white/5' : 'bg-white/0 border-white/0 text-emerald-100/30',
                        isToday ? 'ring-2 ring-emerald-500/30' : '',
                        isSelected ? 'bg-emerald-500/15 border-emerald-500/25' : '',
                    ].filter(Boolean).join(' ');
                    btn.textContent = String(d.getDate());

                    if (isCurrentMonth) {
                        btn.addEventListener('click', function () {
                            setSelectedDay(new Date(d.getFullYear(), d.getMonth(), d.getDate()));
                        });
                    } else {
                        btn.disabled = true;
                    }

                    if (hasEvents && isCurrentMonth) {
                        const mark = document.createElement('div');
                        mark.className = 'absolute left-2 right-2 bottom-1 h-[3px] rounded-full bg-emerald-400/70';
                        btn.appendChild(mark);
                    }

                    $grid.appendChild(btn);
                }

                $title.textContent = `${monthNames[current.getMonth()]} ${current.getFullYear()}`;

                if (selectedDay) {
                    $subtitle.textContent = `Filter tanggal: ${formatDMY(selectedDay)}`;
                } else {
                    const total = monthEvents.length;
                    $subtitle.textContent = total > 0 ? `${total} kegiatan bulan ini` : 'Belum ada kegiatan bulan ini';
                }
            }

            function renderList() {
                const items = selectedDay ? eventsForDay(selectedDay) : eventsForMonth(current);

                $list.innerHTML = '';

                if (!items.length) {
                    const empty = document.createElement('div');
                    empty.className = 'rounded-2xl bg-white/5 border border-white/10 p-10 text-center text-emerald-100/70';
                    empty.textContent = selectedDay ? 'Tidak ada kegiatan di tanggal ini.' : 'Belum ada kegiatan di bulan ini.';
                    $list.appendChild(empty);
                    return;
                }

                for (const ev of items) {
                    const card = document.createElement('div');
                    card.className = 'rounded-2xl bg-white/5 border border-white/10 p-5';

                    const title = document.createElement('div');
                    title.className = 'text-base font-semibold';
                    title.textContent = ev.judul;

                    const meta = document.createElement('div');
                    meta.className = 'mt-1 text-sm text-emerald-100/70';

                    const s = ev.startDate;
                    const e = ev.endDate || ev.startDate;
                    const same = s && e && sameDay(s, e);
                    const dateText = same ? formatDMY(s) : `${formatDMY(s)} - ${formatDMY(e)}`;

                    meta.textContent = dateText + (ev.kategori ? ` • ${ev.kategori}` : '');

                    card.appendChild(title);
                    card.appendChild(meta);

                    if (ev.deskripsi) {
                        const desc = document.createElement('div');
                        desc.className = 'mt-3 text-sm text-emerald-100/80 whitespace-pre-line';
                        desc.textContent = ev.deskripsi;
                        card.appendChild(desc);
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
