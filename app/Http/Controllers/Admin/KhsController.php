<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index(Request $request): View
    {
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

        $khs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.khs.index', [
            'khs' => $khs,
            'q' => $q,
            'semester' => $semester,
        ]);
    }

    public function show(Khs $khs): View
    {
        $khs->load(['mahasiswa', 'mahasiswa.user', 'items.mataKuliah']);

        return view('admin.khs.show', [
            'khs' => $khs,
        ]);
    }

    private function resolveKaprodi(?string $programStudi): ?\App\Models\Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return \App\Models\Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    public function downloadPdf(Khs $khs)
    {
        $khs->load(['mahasiswa', 'items.mataKuliah']);
        
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

    public function create(): View
    {
        return view('admin.khs.create', [
            'mahasiswa' => Mahasiswa::query()->orderBy('nama_lengkap')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
        ]);

        $khs = Khs::query()->firstOrCreate(
            [
                'mahasiswa_id' => $validated['mahasiswa_id'],
                'semester' => $validated['semester'],
            ],
            [
                'tahun_ajaran' => $validated['tahun_ajaran'] ?? null,
            ]
        );

        return redirect()->route('admin.khs.edit', $khs)->with('success', 'KHS berhasil dibuat. Silakan input nilai.');
    }

    public function edit(Khs $khs): View
    {
        $khs->load(['mahasiswa', 'items.mataKuliah']);

        return view('admin.khs.edit', [
            'khs' => $khs,
            'mataKuliah' => MataKuliah::query()->orderBy('semester')->orderBy('kode')->get(),
        ]);
    }

    public function update(Request $request, Khs $khs): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'ips' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'mata_kuliah_id' => ['nullable', 'array'],
            'mata_kuliah_id.*' => ['integer', 'exists:mata_kuliah,id'],
            'nilai_angka' => ['nullable', 'array'],
            'nilai_huruf' => ['nullable', 'array'],
        ]);

        $khs->update([
            'tahun_ajaran' => $validated['tahun_ajaran'] ?? $khs->tahun_ajaran,
            'ips' => $validated['ips'] ?? $khs->ips,
            'ipk' => $validated['ipk'] ?? $khs->ipk,
        ]);

        $mkIds = $validated['mata_kuliah_id'] ?? [];
        $mkIds = array_values(array_map('intval', (array) $mkIds));

        foreach ($mkIds as $mkId) {
            $angka = $validated['nilai_angka'][$mkId] ?? null;
            $huruf = $validated['nilai_huruf'][$mkId] ?? null;

            KhsItem::query()->updateOrCreate(
                [
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => $mkId,
                ],
                [
                    'nilai_angka' => $angka !== '' ? $angka : null,
                    'nilai_huruf' => $huruf !== '' ? $huruf : null,
                ]
            );
        }

        KhsItem::query()
            ->where('khs_id', $khs->id)
            ->when(count($mkIds) > 0, function ($sub) use ($mkIds) {
                $sub->whereNotIn('mata_kuliah_id', $mkIds);
            })
            ->delete();

        return redirect()->route('admin.khs.show', $khs)->with('success', 'KHS berhasil diperbarui.');
    }

    public function destroy(Khs $khs): RedirectResponse
    {
        $khs->delete();
        return back()->with('success', 'Data KHS berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:khs,id'],
        ]);

        Khs::query()->whereIn('id', $validated['ids'])->delete();

        return back()->with('success', 'Data KHS terpilih berhasil dihapus.');
    }

    public function rekapIndex(Request $request): View
    {
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
            ->withSum('khs as total_sks', function ($sub) {
                $sub->join('khs_items', 'khs_items.khs_id', '=', 'khs.id')
                    ->join('mata_kuliah', 'mata_kuliah.id', '=', 'khs_items.mata_kuliah_id')
                    ->whereNotNull('khs_items.nilai_huruf')
                    ->where('khs_items.nilai_huruf', '!=', 'E')
                    ->where('khs_items.nilai_huruf', '!=', '-');
            });

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }
        if ($prodi !== '') {
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
                $nilaiHuruf = (string) ($it->nilai_huruf ?? '-');
                $nilaiAngka = (float) ($it->nilai_angka ?? 0);
                $bobot = $sks * $nilaiAngka;

                if ($nilaiHuruf !== '-' && $nilaiHuruf !== '' && $nilaiHuruf !== 'E') {
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
        $data = $this->buildTranskipData($mahasiswa);

        return view('admin.khs.rekap-show', array_merge([
            'mahasiswa' => $mahasiswa,
        ], $data));
    }

    public function rekapPdf(Request $request, Mahasiswa $mahasiswa)
    {
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
