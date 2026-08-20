<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KhsController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return [
                'routePrefix' => 'admin',
                'canManage' => true,
                'programStudi' => null,
            ];
        }

        if ($user?->isDosen()) {
            $dosen = $user->dosen;
            $programStudi = trim((string) ($dosen?->program_studi ?? ''));
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');

            $canManage = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);
            abort_unless($canManage, 403);

            return [
                'routePrefix' => 'dosen',
                'canManage' => true,
                'programStudi' => $programStudi ?: '---',
            ];
        }

        abort(403);
    }

    private function scopeByProdi($query, ?string $programStudi, string $mahasiswaAlias = 'mahasiswa')
    {
        if (empty($programStudi)) {
            return $query;
        }

        return $query->whereHas($mahasiswaAlias, function ($sub) use ($programStudi) {
            $sub->where('program_studi', $programStudi);
        });
    }

    public function index(Request $request): View
    {
        $context = $this->resolveContext($request);
        $programStudi = $context['programStudi'] ?? null;

        $q = trim((string) $request->get('q', ''));
        $semester = trim((string) $request->get('semester', ''));

        $query = Khs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($semester !== '') {
            $query->where('semester', $semester);
        }

        $this->scopeByProdi($query, $programStudi);

        $khs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.khs.index', [
            'khs' => $khs,
            'q' => $q,
            'semester' => $semester,
            'routePrefix' => $context['routePrefix'],
            'programStudi' => $programStudi,
        ]);
    }

    public function show(Request $request, Khs $khs): View
    {
        $context = $this->resolveContext($request);
        $khs->load(['mahasiswa', 'mahasiswa.user', 'items.mataKuliah']);

        if (!empty($context['programStudi'] ?? null)) {
            abort_unless((string) ($khs->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        return view('admin.khs.show', [
            'khs' => $khs,
            'routePrefix' => $context['routePrefix'],
        ]);
    }

    private function resolveKaprodi(?string $programStudi): ?Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    public function downloadPdf(Request $request, Khs $khs)
    {
        $context = $this->resolveContext($request);
        $khs->load(['mahasiswa', 'items.mataKuliah']);

        if (!empty($context['programStudi'] ?? null)) {
            abort_unless((string) ($khs->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $kaprodi = $this->resolveKaprodi($khs->mahasiswa->program_studi ?? null);

        $html = view('mahasiswa.khs.pdf', [
            'khs' => $khs,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'khs-'.$khs->mahasiswa->npm.'-'.$khs->semester.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function create(Request $request): View
    {
        $context = $this->resolveContext($request);
        $programStudi = $context['programStudi'] ?? null;

        $mahasiswaQuery = Mahasiswa::query()->orderBy('nama_lengkap');
        if (!empty($programStudi)) {
            $mahasiswaQuery->where('program_studi', $programStudi);
        }

        return view('admin.khs.create', [
            'mahasiswa' => $mahasiswaQuery->get(),
            'routePrefix' => $context['routePrefix'],
            'programStudi' => $programStudi,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $programStudi = $context['programStudi'] ?? null;

        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
        ]);

        if (!empty($programStudi)) {
            $mhs = Mahasiswa::query()->find($validated['mahasiswa_id']);
            abort_unless($mhs && (string) ($mhs->program_studi ?? '') === $programStudi, 403);
        }

        $khs = Khs::query()->firstOrCreate(
            [
                'mahasiswa_id' => $validated['mahasiswa_id'],
                'semester' => $validated['semester'],
            ],
            [
                'tahun_ajaran' => $validated['tahun_ajaran'] ?? null,
            ]
        );

        $editRoute = $context['routePrefix'] === 'admin' ? 'admin.khs.edit' : 'dosen.khs.edit';

        return redirect()->route($editRoute, $khs)->with('success', 'KHS berhasil dibuat. Silakan input nilai.');
    }

    public function edit(Request $request, Khs $khs): View
    {
        $context = $this->resolveContext($request);
        $khs->load(['mahasiswa', 'items.mataKuliah']);

        if (!empty($context['programStudi'] ?? null)) {
            abort_unless((string) ($khs->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        return view('admin.khs.edit', [
            'khs' => $khs,
            'mataKuliah' => MataKuliah::query()->orderBy('semester')->orderBy('kode')->get(),
            'routePrefix' => $context['routePrefix'],
        ]);
    }

    public function update(Request $request, Khs $khs): RedirectResponse
    {
        $context = $this->resolveContext($request);

        if (!empty($context['programStudi'] ?? null)) {
            $khs->loadMissing('mahasiswa');
            abort_unless((string) ($khs->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $validated = $request->validate([
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'ips' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'mata_kuliah_id' => ['nullable', 'array'],
            'mata_kuliah_id.*' => ['integer', 'exists:mata_kuliah,id'],
            'nilai_angka' => ['nullable', 'array'],
            'nilai_huruf' => ['nullable', 'array'],
            'nilai_tm' => ['nullable', 'array'],
            'nilai_quis' => ['nullable', 'array'],
            'nilai_mid' => ['nullable', 'array'],
            'nilai_final' => ['nullable', 'array'],
        ]);

        $khs->update([
            'tahun_ajaran' => $validated['tahun_ajaran'] ?? $khs->tahun_ajaran,
        ]);

        $mkIds = $validated['mata_kuliah_id'] ?? [];
        $mkIds = array_values(array_map('intval', (array) $mkIds));

        $nilaiAngka = $validated['nilai_angka'] ?? [];
        $nilaiHuruf = $validated['nilai_huruf'] ?? [];
        $nilaiTm = $validated['nilai_tm'] ?? [];
        $nilaiQuis = $validated['nilai_quis'] ?? [];
        $nilaiMid = $validated['nilai_mid'] ?? [];
        $nilaiFinal = $validated['nilai_final'] ?? [];

        foreach ($mkIds as $mkId) {
            $angka = $nilaiAngka[$mkId] ?? null;
            $huruf = $nilaiHuruf[$mkId] ?? null;
            $tm = $nilaiTm[$mkId] ?? null;
            $quis = $nilaiQuis[$mkId] ?? null;
            $mid = $nilaiMid[$mkId] ?? null;
            $final = $nilaiFinal[$mkId] ?? null;

            $angka = $angka !== '' ? $angka : null;
            $huruf = $huruf !== '' ? $huruf : null;
            $tm = $tm !== '' ? $tm : null;
            $quis = $quis !== '' ? $quis : null;
            $mid = $mid !== '' ? $mid : null;
            $final = $final !== '' ? $final : null;

            if ($angka === null && ($tm !== null || $quis !== null || $mid !== null || $final !== null)) {
                $angka = \App\Http\Controllers\Dosen\NilaiController::hitungTotalNilai(
                    $tm !== null ? (float) $tm : null,
                    $quis !== null ? (float) $quis : null,
                    $mid !== null ? (float) $mid : null,
                    $final !== null ? (float) $final : null,
                );
                $huruf = $angka !== null ? \App\Http\Controllers\Dosen\NilaiController::hurufFromAngka((float) $angka) : null;
            } elseif ($angka !== null && $huruf === null) {
                $huruf = \App\Http\Controllers\Dosen\NilaiController::hurufFromAngka((float) $angka);
            }

            KhsItem::query()->updateOrCreate(
                [
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => $mkId,
                ],
                [
                    'nilai_tm' => $tm,
                    'nilai_quis' => $quis,
                    'nilai_mid' => $mid,
                    'nilai_final' => $final,
                    'nilai_angka' => $angka,
                    'nilai_huruf' => $huruf,
                ]
            );
        }

        KhsItem::query()
            ->where('khs_id', $khs->id)
            ->when(count($mkIds) > 0, function ($sub) use ($mkIds) {
                $sub->whereNotIn('mata_kuliah_id', $mkIds);
            })
            ->delete();

        $semester = (int) $khs->semester;
        $upToSemester = $semester > 0 ? 8 : 8;
        if ($semester > 0) {
            $upToSemester = max(
                $semester,
                (int) Khs::query()->where('mahasiswa_id', $khs->mahasiswa_id)->max('semester')
            );
        }
        \App::call(\App\Http\Controllers\Dosen\NilaiController::class . '@callableRecalculate', [
            'mahasiswaId' => (int) $khs->mahasiswa_id,
            'upToSemester' => $upToSemester,
        ]);

        $customIps = $validated['ips'] ?? null;
        $customIpk = $validated['ipk'] ?? null;
        if ($customIps !== null || $customIpk !== null) {
            $khs->refresh();
            $khs->update([
                'ips' => $customIps !== '' ? $customIps : $khs->ips,
                'ipk' => $customIpk !== '' ? $customIpk : $khs->ipk,
            ]);
        }

        $showRoute = $context['routePrefix'] === 'admin' ? 'admin.khs.show' : 'dosen.khs.show';

        return redirect()->route($showRoute, $khs)->with('success', 'KHS berhasil diperbarui. IPS/IPK dihitung otomatis.');
    }

    public function destroy(Request $request, Khs $khs): RedirectResponse
    {
        $context = $this->resolveContext($request);

        if (!empty($context['programStudi'] ?? null)) {
            $khs->loadMissing('mahasiswa');
            abort_unless((string) ($khs->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $khs->delete();
        return back()->with('success', 'Data KHS berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $programStudi = $context['programStudi'] ?? null;

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:khs,id'],
        ]);

        $ids = array_map('intval', (array) $validated['ids']);
        $query = Khs::query()->whereIn('id', $ids);
        $this->scopeByProdi($query, $programStudi);
        $query->delete();

        return back()->with('success', 'Data KHS terpilih berhasil dihapus.');
    }

    public function rekapIndex(Request $request): View
    {
        $context = $this->resolveContext($request);
        $programStudi = $context['programStudi'] ?? null;

        $q = trim((string) $request->get('q', ''));
        $prodi = trim((string) $request->get('prodi', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));

        $query = Mahasiswa::query()
            ->with(['user'])
            ->withCount([
                'khs as total_semester_terisi' => function ($sub) {
                    $sub->whereHas('items');
                },
            ])
            ->addSelect([
                'total_sks' => KhsItem::query()
                    ->selectRaw('COALESCE(SUM(mata_kuliah.sks),0)')
                    ->join('mata_kuliah', 'mata_kuliah.id', '=', 'khs_items.mata_kuliah_id')
                    ->join('khs', 'khs.id', '=', 'khs_items.khs_id')
                    ->whereColumn('khs.mahasiswa_id', 'mahasiswa.id')
                    ->whereNotNull('khs_items.nilai_huruf')
                    ->where('khs_items.nilai_huruf', '!=', 'E')
                    ->where('khs_items.nilai_huruf', '!=', '-'),
            ]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if (!empty($programStudi)) {
            $query->where('program_studi', $programStudi);
        } elseif ($prodi !== '') {
            $query->where('program_studi', $prodi);
        }

        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }

        $mahasiswaList = $query->orderBy('program_studi')->orderBy('angkatan')->orderBy('npm')->paginate(15)->withQueryString();
        $prodiList = Mahasiswa::query()->distinct()->orderBy('program_studi')->pluck('program_studi')->filter()->values();
        $angkatanList = Mahasiswa::query()->distinct()->orderByDesc('angkatan')->pluck('angkatan')->filter()->values();

        return view('admin.khs.rekap-index', [
            'mahasiswaList' => $mahasiswaList,
            'q' => $q,
            'prodi' => $prodi,
            'angkatan' => $angkatan,
            'prodiList' => $prodiList,
            'angkatanList' => $angkatanList,
            'routePrefix' => $context['routePrefix'],
            'programStudi' => $programStudi,
        ]);
    }

    private function buildTranskipData(Mahasiswa $mahasiswa): array
    {
        $khsList = Khs::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->with(['items.mataKuliah'])
            ->orderBy('semester')
            ->get()
            ->keyBy('semester');

        $bobotHuruf = [
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00,
        ];

        $toBobot = function (?string $huruf) use ($bobotHuruf): ?float {
            $huruf = (string) $huruf;
            $huruf = trim($huruf);
            if ($huruf === '' || $huruf === '-') return null;
            return $bobotHuruf[$huruf] ?? null;
        };

        $semesterRows = [];
        $totalSksKumulatif = 0;
        $totalBobotKumulatif = 0;

        for ($s = 1; $s <= 8; $s++) {
            $khs = $khsList->get($s);
            $items = $khs ? $khs->items->sortBy(fn ($it) => (string) ($it->mataKuliah?->kode ?? '')) : collect();

            $sksSemester = 0;
            $bobotSemester = 0;
            $rows = [];
            foreach ($items as $it) {
                $mk = $it->mataKuliah;
                $sks = (int) ($mk?->sks ?? 0);
                $nilaiHurufRaw = (string) ($it->nilai_huruf ?? '');
                $nilaiHuruf = $nilaiHurufRaw !== '' ? $nilaiHurufRaw : '-';
                $bobotNilai = $toBobot($nilaiHuruf);
                $nilaiAngka = $bobotNilai;
                $bobot = ($bobotNilai !== null && $sks > 0) ? (float) $sks * $bobotNilai : 0.0;

                $lulusMatkul = $nilaiHuruf !== '-' && $nilaiHuruf !== '' && $nilaiHuruf !== 'E';

                if ($lulusMatkul && $bobotNilai !== null && $sks > 0) {
                    $sksSemester += $sks;
                    $bobotSemester += $bobot;
                }

                $rows[] = (object) [
                    'kode' => $mk?->kode ?? '-',
                    'nama' => $mk?->nama ?? '-',
                    'sks' => $sks,
                    'nilai_huruf' => $nilaiHuruf,
                    'nilai_angka' => $nilaiAngka,
                    'bobot' => $bobot,
                ];
            }

            $ips = $sksSemester > 0 ? round($bobotSemester / $sksSemester, 2) : null;
            $totalSksKumulatif += $sksSemester;
            $totalBobotKumulatif += $bobotSemester;
            $ipkSementara = $totalSksKumulatif > 0 ? round($totalBobotKumulatif / $totalSksKumulatif, 2) : null;

            $semesterRows[] = (object) [
                'semester' => $s,
                'tahun_ajaran' => $khs?->tahun_ajaran ?? '-',
                'rows' => $rows,
                'sks_semester' => $sksSemester,
                'bobot_semester' => $bobotSemester,
                'ips' => $ips,
                'sks_kumulatif' => $totalSksKumulatif,
                'ipk_kumulatif' => $ipkSementara,
            ];
        }

        $ipkAkhir = $totalSksKumulatif > 0 ? round($totalBobotKumulatif / $totalSksKumulatif, 2) : 0;

        $predikat = '-';
        if ($ipkAkhir >= 3.51) $predikat = 'Dengan Pujian (Sangat Memuaskan)';
        elseif ($ipkAkhir >= 3.01) $predikat = 'Sangat Memuaskan';
        elseif ($ipkAkhir >= 2.76) $predikat = 'Memuaskan';
        elseif ($ipkAkhir >= 2.00) $predikat = 'Cukup';

        $kaprodi = $this->resolveKaprodi($mahasiswa->program_studi ?? null);

        return [
            'semesterRows' => $semesterRows,
            'totalSks' => $totalSksKumulatif,
            'ipkAkhir' => $ipkAkhir,
            'predikat' => $predikat,
            'kaprodi' => $kaprodi,
        ];
    }

    public function rekapShow(Request $request, Mahasiswa $mahasiswa): View
    {
        $context = $this->resolveContext($request);

        if (!empty($context['programStudi'] ?? null)) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        $data = $this->buildTranskipData($mahasiswa);

        return view('admin.khs.rekap-show', array_merge([
            'mahasiswa' => $mahasiswa,
            'routePrefix' => $context['routePrefix'],
        ], $data));
    }

    public function rekapPdf(Request $request, Mahasiswa $mahasiswa)
    {
        $context = $this->resolveContext($request);

        if (!empty($context['programStudi'] ?? null)) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        $data = $this->buildTranskipData($mahasiswa);

        $logoCandidates = [
            public_path('img/lo.jpeg'),
            public_path('img/logo.png'),
            base_path('../img/lo.jpeg'),
            base_path('../img/logo.png'),
            base_path('../public/img/lo.jpeg'),
            base_path('../public/img/logo.png'),
        ];

        $logoPath = null;
        foreach ($logoCandidates as $candidate) {
            if (is_string($candidate) && is_file($candidate) && is_readable($candidate)) {
                $logoPath = $candidate;
                break;
            }
        }

        $logoBase64 = null;
        if ($logoPath) {
            $logoData = @file_get_contents($logoPath);
            if ($logoData !== false) {
                $ext = strtolower((string) pathinfo($logoPath, PATHINFO_EXTENSION));
                $ext = $ext === 'jpg' ? 'jpeg' : $ext;
                $logoBase64 = 'data:image/'.$ext.';base64,'.base64_encode($logoData);
            }
        }

        $fotoBase64 = null;
        if ($mahasiswa->foto_path) {
            $fotoPath = storage_path('app/public/'.$mahasiswa->foto_path);
            if (is_file($fotoPath) && is_readable($fotoPath)) {
                $fotoData = @file_get_contents($fotoPath);
                if ($fotoData !== false) {
                    $ext = strtolower((string) pathinfo($fotoPath, PATHINFO_EXTENSION));
                    $ext = $ext === 'jpg' ? 'jpeg' : $ext;
                    $fotoBase64 = 'data:image/'.$ext.';base64,'.base64_encode($fotoData);
                }
            }
        }

        $html = view('admin.khs.rekap-pdf', array_merge([
            'mahasiswa' => $mahasiswa,
            'logoBase64' => $logoBase64,
            'fotoBase64' => $fotoBase64,
        ], $data))->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'TRANSKRIP-NILAI-'.($mahasiswa->npm ?? 'mahasiswa').'.pdf';
        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
        ]);
    }
}
