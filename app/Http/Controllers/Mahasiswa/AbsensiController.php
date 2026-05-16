<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AbsensiItem;
use App\Models\MataKuliah;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $semester = (int) $request->get('semester', 1);
        if ($semester < 1 || $semester > 8) {
            $semester = 1;
        }

        if (! $mahasiswa) {
            return view('mahasiswa.absensi.index', [
                'semester' => $semester,
                'rows' => collect(),
            ]);
        }

        $items = AbsensiItem::query()
            ->with(['absensi.mataKuliah'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('absensi', function ($q) use ($semester) {
                $q->where('semester', $semester);
            })
            ->get();

        $rows = $items
            ->groupBy(function (AbsensiItem $item) {
                return (int) $item->absensi->mata_kuliah_id;
            })
            ->map(function ($group, int $mataKuliahId) use ($semester) {
                $mk = $group->first()?->absensi?->mataKuliah;
                $counts = [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'kosong' => 0,
                ];

                foreach ($group as $item) {
                    if (! $item->status) {
                        $counts['kosong']++;
                        continue;
                    }
                    $counts[$item->status] = ($counts[$item->status] ?? 0) + 1;
                }

                return [
                    'mataKuliahId' => $mataKuliahId,
                    'mataKuliah' => $mk,
                    'semester' => $semester,
                    'counts' => $counts,
                    'total' => $group->count(),
                ];
            })
            ->values()
            ->sortBy(function (array $row) {
                return $row['mataKuliah']?->kode ?? '';
            })
            ->values();

        return view('mahasiswa.absensi.index', [
            'semester' => $semester,
            'rows' => $rows,
        ]);
    }

    public function show(Request $request, MataKuliah $mataKuliah, int $semester): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        abort_unless($mahasiswa, 403);
        abort_unless($semester >= 1 && $semester <= 8, 404);

        $items = AbsensiItem::query()
            ->with('absensi')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('absensi', function ($q) use ($mataKuliah, $semester) {
                $q->where('semester', $semester)->where('mata_kuliah_id', $mataKuliah->id);
            })
            ->get()
            ->sortBy(function (AbsensiItem $item) {
                return $item->absensi->pertemuan;
            })
            ->values();

        return view('mahasiswa.absensi.show', [
            'mataKuliah' => $mataKuliah,
            'semester' => $semester,
            'items' => $items,
        ]);
    }

    public function pdf(Request $request, MataKuliah $mataKuliah, int $semester)
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        abort_unless($mahasiswa, 403);
        abort_unless($semester >= 1 && $semester <= 8, 404);

        $items = AbsensiItem::query()
            ->with('absensi')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('absensi', function ($q) use ($mataKuliah, $semester) {
                $q->where('semester', $semester)->where('mata_kuliah_id', $mataKuliah->id);
            })
            ->get()
            ->sortBy(function (AbsensiItem $item) {
                return $item->absensi->pertemuan;
            })
            ->values();

        $html = view('mahasiswa.absensi.pdf', [
            'mahasiswa' => $mahasiswa,
            'mataKuliah' => $mataKuliah,
            'semester' => $semester,
            'items' => $items,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'absensi-'.$mahasiswa->npm.'-'.$mataKuliah->kode.'-semester-'.$semester.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
