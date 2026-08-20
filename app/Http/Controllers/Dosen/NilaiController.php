<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Krs;
use App\Models\MataKuliah;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NilaiController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $dosen = $request->user()?->dosen;

        $query = MataKuliah::query();
        if ($dosen) {
            $query->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
            });
        } else {
            $query->whereRaw('1 = 0');
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('kode', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%")
                    ->orWhere('jurusan', 'like', "%{$q}%");
            });
        }

        $query->withCount(['krsItems as peserta_count' => function ($sub) {
            $sub->whereHas('krs', function ($q) {
                $q->where('status_approval', 'approved');
            });
        }]);

        $mataKuliah = $query
            ->orderBy('semester')
            ->orderBy('kode')
            ->paginate(10)
            ->withQueryString();

        return view('dosen.nilai.index', [
            'mataKuliah' => $mataKuliah,
            'q' => $q,
        ]);
    }

    public function edit(Request $request, MataKuliah $mataKuliah, int $semester): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen && in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true), 403);

        return view('dosen.nilai.edit', array_merge(
            $this->buildEditData($request, $mataKuliah, $semester),
            [
            'mataKuliah' => $mataKuliah,
            'semester' => $semester,
            ]
        ));
    }

    public function exportPdf(Request $request, MataKuliah $mataKuliah, int $semester)
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen && in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true), 403);

        $data = $this->buildEditData($request, $mataKuliah, $semester, false);

        $html = view('dosen.nilai.pdf', [
            'mataKuliah' => $mataKuliah,
            'semester' => $semester,
            'krs' => $data['krs'],
            'existing' => $data['existing'],
            'q' => $data['q'],
            'relatedDosen' => $dosen,
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
    }

    public function update(Request $request, MataKuliah $mataKuliah, int $semester): RedirectResponse
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen && in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true), 403);

        $validated = $request->validate([
            'nilai_tm' => ['nullable', 'array'],
            'nilai_tm.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_quis' => ['nullable', 'array'],
            'nilai_quis.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_mid' => ['nullable', 'array'],
            'nilai_mid.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_final' => ['nullable', 'array'],
            'nilai_final.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $nilaiTm = collect($validated['nilai_tm'] ?? [])
            ->mapWithKeys(fn ($v, $k) => [(int) $k => $v])
            ->all();
        $nilaiQuis = collect($validated['nilai_quis'] ?? [])
            ->mapWithKeys(fn ($v, $k) => [(int) $k => $v])
            ->all();
        $nilaiMid = collect($validated['nilai_mid'] ?? [])
            ->mapWithKeys(fn ($v, $k) => [(int) $k => $v])
            ->all();
        $nilaiFinal = collect($validated['nilai_final'] ?? [])
            ->mapWithKeys(fn ($v, $k) => [(int) $k => $v])
            ->all();

        $requestedMahasiswaIds = collect(array_merge(
            array_keys($nilaiTm),
            array_keys($nilaiQuis),
            array_keys($nilaiMid),
            array_keys($nilaiFinal)
        ))
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        if (count($requestedMahasiswaIds) === 0) {
            return redirect()
                ->route('dosen.nilai.edit', [$mataKuliah, $semester])
                ->with('success', 'Tidak ada perubahan nilai.');
        }

        $allowedMahasiswaIds = Krs::query()
            ->where('status_approval', 'approved')
            ->where('semester', $semester)
            ->whereHas('items', function ($sub) use ($mataKuliah) {
                $sub->where('mata_kuliah_id', $mataKuliah->id);
            })
            ->pluck('mahasiswa_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        $allowedSet = array_fill_keys($allowedMahasiswaIds, true);
        $targetMahasiswaIds = array_values(array_filter($requestedMahasiswaIds, fn ($id) => isset($allowedSet[$id])));

        $khsList = Khs::query()
            ->with(['items' => function ($sub) use ($mataKuliah) {
                $sub->where('mata_kuliah_id', $mataKuliah->id);
            }])
            ->where('semester', $semester)
            ->whereIn('mahasiswa_id', $targetMahasiswaIds)
            ->get()
            ->keyBy(fn ($khs) => (int) $khs->mahasiswa_id);

        $updatedMahasiswaIds = [];
        $missingMahasiswaIds = [];

        foreach ($targetMahasiswaIds as $mahasiswaId) {
            $khs = $khsList->get((int) $mahasiswaId);
            if (! $khs) {
                $missingMahasiswaIds[] = (int) $mahasiswaId;
                continue;
            }

            $khsItem = $khs->items->first();
            if (! $khsItem) {
                $missingMahasiswaIds[] = (int) $mahasiswaId;
                continue;
            }

            $tm = $nilaiTm[(int) $mahasiswaId] ?? null;
            $quis = $nilaiQuis[(int) $mahasiswaId] ?? null;
            $mid = $nilaiMid[(int) $mahasiswaId] ?? null;
            $final = $nilaiFinal[(int) $mahasiswaId] ?? null;

            $tm = $tm !== '' && $tm !== null ? (float) $tm : null;
            $quis = $quis !== '' && $quis !== null ? (float) $quis : null;
            $mid = $mid !== '' && $mid !== null ? (float) $mid : null;
            $final = $final !== '' && $final !== null ? (float) $final : null;

            $angka = self::hitungTotalNilai($tm, $quis, $mid, $final);
            $huruf = $angka !== null ? self::hurufFromAngka($angka) : null;

            $khsItem->update([
                'nilai_tm' => $tm,
                'nilai_quis' => $quis,
                'nilai_mid' => $mid,
                'nilai_final' => $final,
                'nilai_angka' => $angka,
                'nilai_huruf' => $huruf,
            ]);

            $updatedMahasiswaIds[] = (int) $mahasiswaId;
        }

        foreach ($updatedMahasiswaIds as $mahasiswaId) {
            $this->recalculateIpsIpk((int) $mahasiswaId, (int) $semester);
        }

        if (count($updatedMahasiswaIds) === 0) {
            return redirect()
                ->route('dosen.nilai.edit', [$mataKuliah, $semester])
                ->with('error', 'Nilai tidak tersimpan. KHS belum disiapkan Admin untuk mahasiswa pada mata kuliah ini.');
        }

        $message = 'Nilai berhasil disimpan.';
        if (count($missingMahasiswaIds) > 0) {
            $message .= ' Ada '.count($missingMahasiswaIds).' mahasiswa yang belum punya KHS/KHS item untuk mata kuliah ini.';
        }

        return redirect()
            ->route('dosen.nilai.edit', [$mataKuliah, $semester])
            ->with('success', $message);
    }

    public static function hitungTotalNilai(?float $tm, ?float $quis, ?float $mid, ?float $final): ?float
    {
        $filled = array_filter([$tm, $quis, $mid, $final], fn ($v) => $v !== null);
        if (count($filled) === 0) {
            return null;
        }

        $tmVal = $tm ?? 0;
        $quisVal = $quis ?? 0;
        $midVal = $mid ?? 0;
        $finalVal = $final ?? 0;

        $total = ($tmVal * 0.50) + ($quisVal * 0.20) + ($midVal * 0.10) + ($finalVal * 0.20);

        return round($total, 2);
    }

    private static function hurufChoices(): array
    {
        return ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'D', 'E'];
    }

    private static function hurufFromAngka(float $angka): string
    {
        if ($angka >= 85) {
            return 'A';
        }
        if ($angka >= 80) {
            return 'A-';
        }
        if ($angka >= 75) {
            return 'B+';
        }
        if ($angka >= 70) {
            return 'B';
        }
        if ($angka >= 65) {
            return 'B-';
        }
        if ($angka >= 60) {
            return 'C+';
        }
        if ($angka >= 55) {
            return 'C';
        }
        if ($angka >= 40) {
            return 'D';
        }

        return 'E';
    }

    private static function bobotFromHuruf(?string $huruf): ?float
    {
        return match ($huruf) {
            'A' => 4.0,
            'A-' => 3.7,
            'B+' => 3.3,
            'B' => 3.0,
            'B-' => 2.7,
            'C+' => 2.3,
            'C' => 2.0,
            'D' => 1.0,
            'E' => 0.0,
            default => null,
        };
    }

    private function recalculateIpsIpk(int $mahasiswaId, int $upToSemester): void
    {
        $khsList = Khs::query()
            ->with(['items.mataKuliah'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereBetween('semester', [1, $upToSemester])
            ->orderBy('semester')
            ->get();

        $ipsBySemester = [];
        $hasItemsBySemester = [];

        foreach ($khsList as $khs) {
            $items = $khs->items;
            $hasItems = $items->count() > 0;
            $hasItemsBySemester[(int) $khs->semester] = $hasItems;
            $allFilled = $hasItems && $items->every(fn ($it) => $it->nilai_angka !== null);

            if (! $hasItems) {
                $khs->update(['ips' => null]);
                $ipsBySemester[(int) $khs->semester] = null;
                continue;
            }

            if (! $allFilled) {
                $khs->update(['ips' => null]);
                $ipsBySemester[(int) $khs->semester] = null;
                continue;
            }

            $totalSks = 0.0;
            $totalBobot = 0.0;

            foreach ($items as $it) {
                $huruf = $it->nilai_huruf ?: ($it->nilai_angka !== null ? self::hurufFromAngka((float) $it->nilai_angka) : null);
                $bobot = self::bobotFromHuruf($huruf);
                $sks = (float) ($it->mataKuliah?->sks ?? 0);

                if ($bobot === null || $sks <= 0) {
                    continue;
                }

                $totalSks += $sks;
                $totalBobot += ($bobot * $sks);
            }

            $ips = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : null;
            $khs->update(['ips' => $ips]);
            $ipsBySemester[(int) $khs->semester] = $ips;
        }

        $cumulativeSks = 0.0;
        $cumulativeBobot = 0.0;
        $allSemestersComplete = true;

        foreach ($khsList as $khs) {
            $semester = (int) $khs->semester;
            $ips = $ipsBySemester[$semester] ?? null;
            $hasItems = $hasItemsBySemester[$semester] ?? false;

            if (! $hasItems) {
                $ipk = $cumulativeSks > 0 ? round($cumulativeBobot / $cumulativeSks, 2) : null;
                $khs->update(['ipk' => $ipk]);
                continue;
            }

            if ($ips === null) {
                $allSemestersComplete = false;
                $khs->update(['ipk' => null]);
                continue;
            }

            if (! $allSemestersComplete) {
                $khs->update(['ipk' => null]);
                continue;
            }

            foreach ($khs->items as $it) {
                $huruf = $it->nilai_huruf ?: ($it->nilai_angka !== null ? self::hurufFromAngka((float) $it->nilai_angka) : null);
                $bobot = self::bobotFromHuruf($huruf);
                $sks = (float) ($it->mataKuliah?->sks ?? 0);

                if ($bobot === null || $sks <= 0) {
                    continue;
                }

                $cumulativeSks += $sks;
                $cumulativeBobot += ($bobot * $sks);
            }

            $ipk = $cumulativeSks > 0 ? round($cumulativeBobot / $cumulativeSks, 2) : null;
            $khs->update(['ipk' => $ipk]);
        }
    }

    private function buildEditData(Request $request, MataKuliah $mataKuliah, int $semester, bool $paginate = true): array
    {
        $q = trim((string) $request->get('q', ''));
        $krsQuery = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->where('status_approval', 'approved')
            ->where('semester', $semester)
            ->whereHas('items', function ($sub) use ($mataKuliah) {
                $sub->where('mata_kuliah_id', $mataKuliah->id);
            });

        if ($q !== '') {
            $krsQuery->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $orderedQuery = $krsQuery->orderBy('mahasiswa_id');
        $krs = $paginate
            ? $orderedQuery->paginate(15)->withQueryString()
            : $orderedQuery->get();

        $rows = $paginate ? $krs->getCollection() : $krs;

        $mahasiswaIds = $rows
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
            'q' => $q,
        ];
    }
}
