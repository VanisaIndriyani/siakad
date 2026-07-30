<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Krs;
use App\Models\MataKuliah;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NilaiMonitoringController extends Controller
{
    private const STATUS_OPTIONS = [
        'TIDAK_ADA_PESERTA' => 'Tidak ada peserta',
        'BELUM_SIAP' => 'Belum disiapkan (KHS)',
        'BELUM_LENGKAP' => 'Belum lengkap',
        'SUDAH_LENGKAP' => 'Sudah lengkap',
    ];

    public function index(Request $request): View
    {
        return view('admin.nilai-monitoring.index', $this->buildIndexData($request, true));
    }

    public function exportPdf(Request $request)
    {
        try {
            $data = $this->buildIndexData($request, false);
            $html = view('admin.nilai-monitoring.pdf', $data)->render();

            $dompdf = new Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="rekap-input-nilai-dosen.pdf"',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.nilai-monitoring.index', $request->only(['q', 'semester', 'status', 'all', 'page']))
                ->with('error', 'Gagal generate PDF. Coba gunakan filter atau matikan opsi Buka Semua.');
        }
    }

    public function exportDetailPdf(Request $request, MataKuliah $mataKuliah, int $semester)
    {
        try {
            if ($semester < 1 || $semester > 8) {
                $semester = (int) Krs::query()->max('semester') ?: 1;
            }

            $mataKuliah->load(['dosen', 'dosen2']);
            $relatedDosen = $mataKuliah->dosen ?? $mataKuliah->dosen2;

            $data = $this->buildDetailNilaiData($mataKuliah, $semester);
            $html = view('dosen.nilai.pdf', [
                'mataKuliah' => $mataKuliah,
                'semester' => $semester,
                'krs' => $data['krs'],
                'existing' => $data['existing'],
                'q' => '',
                'relatedDosen' => $relatedDosen,
            ])->render();

            $dompdf = new Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('legal', 'landscape');
            $dompdf->render();

            $filename = 'nilai-'.$mataKuliah->kode.'-semester-'.$semester.'.pdf';

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.nilai-monitoring.index', $request->only(['q', 'semester', 'status', 'all', 'page']))
                ->with('error', 'Gagal generate PDF detail nilai mata kuliah.');
        }
    }

    private function buildIndexData(Request $request, bool $forUi): array
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $status = strtoupper(trim((string) $request->get('status', '')));
        $showAll = $request->boolean('all');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 15;

        if ($semester < 1 || $semester > 8) {
            $semester = (int) Krs::query()->max('semester');
            if ($semester < 1) {
                $semester = 1;
            }
        }

        if (! array_key_exists($status, self::STATUS_OPTIONS)) {
            $status = '';
        }

        $query = $this->buildQuery($semester, $q, $status);

        if ($showAll) {
            $rows = $query->get();
            $paginator = null;
        } else {
            $paginator = $query->paginate($perPage, ['*'], 'page', $page)->withQueryString();
            $rows = $forUi ? $paginator : collect($paginator->items());
        }

        $rowList = $rows instanceof Collection ? $rows : collect($rows->items());
        $summary = [
            'total' => $rowList->count(),
            'sudah_lengkap' => $rowList->where('status_input', 'SUDAH_LENGKAP')->count(),
            'belum_lengkap' => $rowList->where('status_input', 'BELUM_LENGKAP')->count(),
            'belum_siap' => $rowList->where('status_input', 'BELUM_SIAP')->count(),
            'tidak_ada_peserta' => $rowList->where('status_input', 'TIDAK_ADA_PESERTA')->count(),
        ];

        return [
            'rows' => $rows,
            'paginator' => $paginator,
            'semester' => $semester,
            'q' => $q,
            'status' => $status,
            'showAll' => $showAll,
            'statusOptions' => self::STATUS_OPTIONS,
            'summary' => $summary,
        ];
    }

    private function buildQuery(int $semester, string $q, string $status)
    {
        $pesertaExpr = 'COUNT(DISTINCT krs.mahasiswa_id)';
        $punyaExpr = 'COUNT(DISTINCT CASE WHEN khsi.id IS NOT NULL THEN krs.mahasiswa_id END)';
        $terisiExpr = 'COUNT(DISTINCT CASE WHEN khsi.nilai_angka IS NOT NULL THEN krs.mahasiswa_id END)';

        $query = DB::table('mata_kuliah as mk')
            ->leftJoin('dosen as d1', 'd1.id', '=', 'mk.dosen_id')
            ->leftJoin('dosen as d2', 'd2.id', '=', 'mk.dosen_id_2')
            ->leftJoin('krs_items as krsi', 'krsi.mata_kuliah_id', '=', 'mk.id')
            ->leftJoin('krs', function ($join) use ($semester) {
                $join->on('krs.id', '=', 'krsi.krs_id')
                    ->where('krs.status_approval', '=', 'approved')
                    ->where('krs.semester', '=', $semester);
            })
            ->leftJoin('khs', function ($join) {
                $join->on('khs.mahasiswa_id', '=', 'krs.mahasiswa_id')
                    ->on('khs.semester', '=', 'krs.semester');
            })
            ->leftJoin('khs_items as khsi', function ($join) {
                $join->on('khsi.khs_id', '=', 'khs.id')
                    ->on('khsi.mata_kuliah_id', '=', 'mk.id');
            })
            ->where('mk.semester', '=', $semester)
            ->select([
                'mk.id',
                'mk.kode',
                'mk.nama',
                'mk.jurusan',
                'mk.semester',
                'mk.dosen_id',
                'mk.dosen_id_2',
                'd1.nama as dosen_1',
                'd2.nama as dosen_2',
            ])
            ->selectRaw($pesertaExpr.' as peserta_approved')
            ->selectRaw($punyaExpr.' as punya_khs_item')
            ->selectRaw($terisiExpr.' as nilai_terisi')
            ->selectRaw(
                "CASE
                    WHEN {$pesertaExpr} = 0 THEN 'TIDAK_ADA_PESERTA'
                    WHEN {$punyaExpr} < {$pesertaExpr} THEN 'BELUM_SIAP'
                    WHEN {$terisiExpr} < {$pesertaExpr} THEN 'BELUM_LENGKAP'
                    ELSE 'SUDAH_LENGKAP'
                END as status_input"
            )
            ->groupBy([
                'mk.id',
                'mk.kode',
                'mk.nama',
                'mk.jurusan',
                'mk.semester',
                'mk.dosen_id',
                'mk.dosen_id_2',
                'd1.nama',
                'd2.nama',
            ]);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('mk.kode', 'like', "%{$q}%")
                    ->orWhere('mk.nama', 'like', "%{$q}%")
                    ->orWhere('d1.nama', 'like', "%{$q}%")
                    ->orWhere('d2.nama', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->having('status_input', '=', $status);
        }

        return $query->orderBy('mk.kode')->orderBy('mk.id');
    }

    private function buildDetailNilaiData(MataKuliah $mataKuliah, int $semester): array
    {
        $krsQuery = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->where('status_approval', 'approved')
            ->where('semester', $semester)
            ->whereHas('items', function ($sub) use ($mataKuliah) {
                $sub->where('mata_kuliah_id', $mataKuliah->id);
            })
            ->orderBy('mahasiswa_id');

        $krs = $krsQuery->get();

        $mahasiswaIds = $krs
            ->pluck('mahasiswa_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        $khsList = Khs::query()
            ->with(['items' => function ($sub) use ($mataKuliah) {
                $sub->where('mata_kuliah_id', $mataKuliah->id);
            }])
            ->where('semester', $semester)
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->get();

        $existing = $khsList->mapWithKeys(function ($khs) {
            $item = $khs->items->first();
            return [(int) $khs->mahasiswa_id => $item];
        });

        return [
            'krs' => $krs,
            'existing' => $existing,
        ];
    }
}

