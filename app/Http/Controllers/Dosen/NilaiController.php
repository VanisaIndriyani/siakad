<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Krs;
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

        $query = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->where('status_approval', 'approved');

        if ($dosen) {
            $query->whereHas('items.mataKuliah', function ($sub) use ($dosen) {
                $sub->where(function ($q) use ($dosen) {
                    $q->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                });
            });

            $query->withCount(['items as items_count' => function ($sub) use ($dosen) {
                $sub->whereHas('mataKuliah', function ($q) use ($dosen) {
                    $q->where(function ($qq) use ($dosen) {
                        $qq->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                    });
                });
            }]);
        } else {
            $query->withCount('items');
        }

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $krs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('dosen.nilai.index', [
            'krs' => $krs,
            'q' => $q,
        ]);
    }

    public function edit(Krs $krs): View
    {
        abort_unless($krs->status_approval === 'approved', 404);

        $dosen = request()->user()?->dosen;
        if ($dosen) {
            $allowed = $krs->items()->whereHas('mataKuliah', function ($q) use ($dosen) {
                $q->where(function ($qq) use ($dosen) {
                    $qq->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                });
            })->exists();

            abort_unless($allowed, 403);
        }

        $krs->load(['mahasiswa', 'items.mataKuliah']);
        $items = $dosen
            ? $krs->items->filter(fn ($it) => in_array((int) $dosen->id, [(int) ($it->mataKuliah?->dosen_id ?? 0), (int) ($it->mataKuliah?->dosen_id_2 ?? 0)], true))->values()
            : $krs->items;

        $khs = Khs::query()
            ->where('mahasiswa_id', $krs->mahasiswa_id)
            ->where('semester', $krs->semester)
            ->first();

        if (! $khs) {
            abort(403, 'KHS belum disiapkan Admin.');
        }

        $khs->load(['items']);

        $existing = $khs->items->keyBy('mata_kuliah_id');

        return view('dosen.nilai.edit', [
            'krs' => $krs,
            'khs' => $khs,
            'existing' => $existing,
            'items' => $items,
        ]);
    }

    public function update(Request $request, Krs $krs): RedirectResponse
    {
        abort_unless($krs->status_approval === 'approved', 404);

        $dosen = $request->user()?->dosen;
        if ($dosen) {
            $allowed = $krs->items()->whereHas('mataKuliah', function ($q) use ($dosen) {
                $q->where(function ($qq) use ($dosen) {
                    $qq->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                });
            })->exists();

            abort_unless($allowed, 403);
        }

        $validated = $request->validate([
            'nilai_angka' => ['nullable', 'array'],
            'nilai_angka.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'nilai_huruf' => ['nullable', 'array'],
            'nilai_huruf.*' => ['nullable', 'string', Rule::in(self::hurufChoices())],
        ]);

        $khs = Khs::query()
            ->where('mahasiswa_id', $krs->mahasiswa_id)
            ->where('semester', $krs->semester)
            ->first();

        if (! $khs) {
            return redirect()->route('dosen.nilai.index')->with('error', 'KHS belum disiapkan Admin.');
        }

        $krs->load(['items.mataKuliah']);
        $mkIds = $dosen
            ? $krs->items
                ->filter(fn ($it) => in_array((int) $dosen->id, [(int) ($it->mataKuliah?->dosen_id ?? 0), (int) ($it->mataKuliah?->dosen_id_2 ?? 0)], true))
                ->pluck('mata_kuliah_id')
                ->map(fn ($v) => (int) $v)
                ->values()
                ->all()
            : $krs->items->pluck('mata_kuliah_id')->map(fn ($v) => (int) $v)->values()->all();

        $khsItems = $khs->items()->whereIn('mata_kuliah_id', $mkIds)->get()->keyBy('mata_kuliah_id');
        if ($khsItems->count() !== count($mkIds)) {
            return redirect()->route('dosen.nilai.index')->with('error', 'KHS belum lengkap disiapkan Admin untuk mata kuliah pada KRS ini.');
        }

        foreach ($mkIds as $mkId) {
            $angka = $validated['nilai_angka'][$mkId] ?? null;
            $angka = $angka !== '' ? $angka : null;
            $angka = $angka !== null ? (float) $angka : null;
            $huruf = $angka !== null ? self::hurufFromAngka($angka) : null;

            $khsItems[$mkId]->update([
                'nilai_angka' => $angka,
                'nilai_huruf' => $huruf,
            ]);
        }

        $this->recalculateIpsIpk($krs->mahasiswa_id, (int) $krs->semester);

        return redirect()->route('dosen.nilai.index')->with('success', 'Nilai berhasil disimpan.');
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
}
