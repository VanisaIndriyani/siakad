<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiItem;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AbsensiController extends Controller
{
    private const JURUSAN = [
        'Pendidikan Agama Islam',
        'Pendidikan Islam Anak Usia Dini',
        'Hukum Keluarga Islam',
        'Hukum Tata Negara',
        'Perbankan Syariah',
        'Ekonomi Syariah',
    ];

    public function index(Request $request): View
    {
        $routePrefix = $request->user()?->isDosen() ? 'dosen' : 'admin';
        $sidebarView = $request->user()?->isDosen() ? 'dosen.partials.sidebar' : 'admin.partials.sidebar';

        $jurusan = trim((string) $request->get('jurusan', ''));
        $semester = (int) $request->get('semester', 0);
        $mataKuliahId = (int) $request->get('mata_kuliah_id', 0);

        $dosen = $request->user()?->isDosen() ? $request->user()?->dosen : null;
        if ($request->user()?->isDosen() && ! $dosen) {
            abort(403);
        }

        $mataKuliahQuery = MataKuliah::query()->orderBy('semester')->orderBy('kode');
        if ($dosen) {
            $mataKuliahQuery->where(function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
            });
        }
        if ($semester >= 1 && $semester <= 8) {
            $mataKuliahQuery->where('semester', $semester);
        }
        if ($jurusan !== '' && $semester >= 1 && $semester <= 8) {
            $mataKuliahQuery->where('jurusan', $jurusan);
        }

        $mataKuliah = $mataKuliahQuery->get();

        $sessions = collect();
        if ($jurusan !== '' && $semester >= 1 && $semester <= 8 && $mataKuliahId > 0) {
            $sessions = Absensi::query()
                ->with('mataKuliah')
                ->where('jurusan', $jurusan)
                ->where('semester', $semester)
                ->where('mata_kuliah_id', $mataKuliahId)
                ->withCount('items')
                ->withCount(['items as terisi_count' => function ($q) {
                    $q->whereNotNull('status');
                }])
                ->orderBy('pertemuan')
                ->get();
        }

        return view('admin.absensi.index', [
            'jurusanList' => self::JURUSAN,
            'mataKuliah' => $mataKuliah,
            'jurusan' => $jurusan,
            'semester' => $semester ?: null,
            'mataKuliahId' => $mataKuliahId ?: null,
            'sessions' => $sessions,
            'routePrefix' => $routePrefix,
            'sidebarView' => $sidebarView,
        ]);
    }

    public function downloadManual(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $dosen = $user->isDosen() ? $user->dosen : null;
        if ($user->isDosen() && ! $dosen) {
            abort(403);
        }

        $validated = $request->validate([
            'jurusan' => ['required', 'string'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'mata_kuliah_id' => [
                'required',
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $request->input('semester'))
                    ->where('jurusan', (string) $request->input('jurusan'))
                    ->when($dosen, function ($q) use ($dosen) {
                        $q->where(function ($qq) use ($dosen) {
                            $qq->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                        });
                    }),
            ],
        ]);

        $jurusan = (string) $validated['jurusan'];
        $semester = (int) $validated['semester'];
        $mataKuliahId = (int) $validated['mata_kuliah_id'];

        $mk = MataKuliah::query()
            ->with(['dosen', 'dosen2'])
            ->where('id', $mataKuliahId)
            ->firstOrFail();

        $mahasiswa = Mahasiswa::query()
            ->where('program_studi', $jurusan)
            ->whereHas('krs', function ($q) use ($mataKuliahId) {
                $q->where('status_approval', 'approved')
                    ->whereHas('items', function ($qi) use ($mataKuliahId) {
                        $qi->where('mata_kuliah_id', $mataKuliahId);
                    });
            })
            ->orderBy('npm')
            ->get();

        $kaprodiNama = $this->resolveKaprodiNama($jurusan);

        $dosenNama = $dosen?->nama;
        if (! $dosenNama) {
            $dosenParts = collect([$mk->dosen?->nama, $mk->dosen2?->nama])
                ->filter(fn ($v) => trim((string) $v) !== '')
                ->values();
            $dosenNama = $dosenParts->isNotEmpty() ? $dosenParts->implode(' / ') : null;
        }

        $html = view('admin.absensi.manual-pdf', [
            'jurusan' => $jurusan,
            'semester' => $semester,
            'mk' => $mk,
            'mahasiswa' => $mahasiswa,
            'kaprodiNama' => $kaprodiNama,
            'dosenNama' => $dosenNama,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $safeKode = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($mk?->kode ?? 'MK'));
        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="Absensi_Manual_'.$safeKode.'.pdf"',
        ]);
    }

    private function resolveKaprodiNama(?string $programStudi): ?string
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->value('nama');
    }

    public function entry(Request $request): View
    {
        $routePrefix = $request->user()?->isDosen() ? 'dosen' : 'admin';
        $sidebarView = $request->user()?->isDosen() ? 'dosen.partials.sidebar' : 'admin.partials.sidebar';

        $dosen = $request->user()?->isDosen() ? $request->user()?->dosen : null;
        if ($request->user()?->isDosen() && ! $dosen) {
            abort(403);
        }

        $validated = $request->validate([
            'jurusan' => ['required', 'string'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'mata_kuliah_id' => [
                'required',
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $request->input('semester'))
                    ->where('jurusan', (string) $request->input('jurusan'))
                    ->when($dosen, function ($q) use ($dosen) {
                        $q->where(function ($qq) use ($dosen) {
                            $qq->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                        });
                    }),
            ],
            'pertemuan' => ['required', 'integer', 'min:1', 'max:16'],
        ]);

        $absensi = Absensi::query()->firstOrCreate(
            [
                'jurusan' => $validated['jurusan'],
                'semester' => $validated['semester'],
                'mata_kuliah_id' => $validated['mata_kuliah_id'],
                'pertemuan' => $validated['pertemuan'],
            ],
            [
                'created_by_user_id' => $request->user()?->id,
            ]
        );

        $mahasiswaIds = Mahasiswa::query()
            ->where('program_studi', $validated['jurusan'])
            ->whereHas('krs', function ($q) use ($validated) {
                $q->where('status_approval', 'approved')
                    ->whereHas('items', function ($qi) use ($validated) {
                        $qi->where('mata_kuliah_id', $validated['mata_kuliah_id']);
                    });
            })
            ->pluck('id')
            ->all();

        foreach ($mahasiswaIds as $mahasiswaId) {
            AbsensiItem::query()->firstOrCreate(
                [
                    'absensi_id' => $absensi->id,
                    'mahasiswa_id' => $mahasiswaId,
                ],
                [
                    'status' => null,
                ]
            );
        }

        $absensi->load(['mataKuliah', 'items.mahasiswa']);

        return view('admin.absensi.entry', [
            'absensi' => $absensi,
            'routePrefix' => $routePrefix,
            'sidebarView' => $sidebarView,
        ]);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $routePrefix = $request->user()?->isDosen() ? 'dosen' : 'admin';

        if ($request->user()?->isDosen()) {
            $dosen = $request->user()?->dosen;
            if (! $dosen) {
                abort(403);
            }
            $absensi->loadMissing('mataKuliah');
            abort_unless(in_array((int) $dosen->id, [(int) ($absensi->mataKuliah?->dosen_id ?? 0), (int) ($absensi->mataKuliah?->dosen_id_2 ?? 0)], true), 403);
        }

        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'materi' => ['nullable', 'string'],
            'status' => ['required', 'array'],
            'status.*' => ['nullable', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:255'],
        ]);

        $absensi->update([
            'tanggal' => $validated['tanggal'] ?? null,
            'materi' => $validated['materi'] ?? null,
        ]);

        $items = $absensi->items()->get()->keyBy('id');
        foreach (($validated['status'] ?? []) as $itemId => $status) {
            $itemId = (int) $itemId;
            if (! isset($items[$itemId])) {
                continue;
            }

            $items[$itemId]->update([
                'status' => $status !== '' ? $status : null,
                'keterangan' => ($validated['keterangan'][$itemId] ?? null) ?: null,
            ]);
        }

        return redirect()->route($routePrefix.'.absensi.entry', [
            'jurusan' => $absensi->jurusan,
            'semester' => $absensi->semester,
            'mata_kuliah_id' => $absensi->mata_kuliah_id,
            'pertemuan' => $absensi->pertemuan,
        ])->with('success', 'Absensi berhasil disimpan.');
    }

    public function exportPdf(Request $request, Absensi $absensi)
    {
        $routePrefix = $request->user()?->isDosen() ? 'dosen' : 'admin';

        if ($request->user()?->isDosen()) {
            $dosen = $request->user()?->dosen;
            if (! $dosen) {
                abort(403);
            }
            $absensi->loadMissing('mataKuliah');
            abort_unless(in_array((int) $dosen->id, [(int) ($absensi->mataKuliah?->dosen_id ?? 0), (int) ($absensi->mataKuliah?->dosen_id_2 ?? 0)], true), 403);
        }

        $absensi->load(['mataKuliah.dosen', 'mataKuliah.dosen2', 'items.mahasiswa']);
        $items = $absensi->items->sortBy(fn ($i) => (string) ($i->mahasiswa?->npm ?? ''))->values();

        $kaprodiNama = $this->resolveKaprodiNama($absensi->jurusan);
        $dosenNama = $request->user()?->isDosen()
            ? ($request->user()?->dosen?->nama ?? null)
            : ($absensi->mataKuliah?->dosen?->nama ?? null);

        $html = view('admin.absensi.export-pdf', [
            'absensi' => $absensi,
            'items' => $items,
            'role' => $routePrefix,
            'kaprodiNama' => $kaprodiNama,
            'dosenNama' => $dosenNama,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $mk = $absensi->mataKuliah;
        $safeKode = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($mk?->kode ?? 'MK'));
        $filename = 'absensi-'.$safeKode.'-P'.$absensi->pertemuan.'.pdf';

        $disposition = $request->boolean('inline') ? 'inline' : 'attachment';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
        ]);
    }

    public function exportRekapPdf(Request $request)
    {
        $dosen = $request->user()?->dosen;
        abort_unless($request->user()?->isDosen() && $dosen, 403);

        $validated = $request->validate([
            'jurusan' => ['required', 'string'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'mata_kuliah_id' => [
                'required',
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $request->input('semester'))
                    ->where('jurusan', (string) $request->input('jurusan'))
                    ->where(function ($q) use ($dosen) {
                        $q->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
                    }),
            ],
        ]);

        $jurusan = (string) $validated['jurusan'];
        $semester = (int) $validated['semester'];
        $mataKuliahId = (int) $validated['mata_kuliah_id'];

        $mk = MataKuliah::query()
            ->with(['dosen', 'dosen2'])
            ->where('id', $mataKuliahId)
            ->firstOrFail();

        $mahasiswaIds = Mahasiswa::query()
            ->where('program_studi', $jurusan)
            ->whereHas('krs', function ($q) use ($semester, $mataKuliahId) {
                $q->where('semester', $semester)
                    ->where('status_approval', 'approved')
                    ->whereHas('items', function ($qi) use ($mataKuliahId) {
                        $qi->where('mata_kuliah_id', $mataKuliahId);
                    });
            })
            ->pluck('id')
            ->all();

        foreach (range(1, 16) as $pertemuan) {
            $absensi = Absensi::query()->firstOrCreate(
                [
                    'jurusan' => $jurusan,
                    'semester' => $semester,
                    'mata_kuliah_id' => $mataKuliahId,
                    'pertemuan' => $pertemuan,
                ],
                [
                    'created_by_user_id' => $request->user()?->id,
                ]
            );

            foreach ($mahasiswaIds as $mahasiswaId) {
                AbsensiItem::query()->firstOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'mahasiswa_id' => (int) $mahasiswaId,
                    ],
                    [
                        'status' => null,
                    ]
                );
            }
        }

        $mahasiswaList = Mahasiswa::query()
            ->whereIn('id', $mahasiswaIds)
            ->orderBy('npm')
            ->get()
            ->keyBy('id');

        $items = collect();
        foreach ($mahasiswaList as $mhs) {
            $items->push([
                'id' => (int) $mhs->id,
                'npm' => (string) ($mhs->npm ?? ''),
                'nama' => (string) ($mhs->nama_lengkap ?? ''),
                'pertemuan' => array_fill(1, 16, null),
                'totals' => ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0],
            ]);
        }

        $itemByMahasiswaId = $items->keyBy('id');

        $absensiItems = AbsensiItem::query()
            ->with('absensi')
            ->whereIn('mahasiswa_id', $mahasiswaIds)
            ->whereHas('absensi', function ($q) use ($jurusan, $semester, $mataKuliahId) {
                $q->where('jurusan', $jurusan)
                    ->where('semester', $semester)
                    ->where('mata_kuliah_id', $mataKuliahId);
            })
            ->get();

        foreach ($absensiItems as $ai) {
            $p = (int) ($ai->absensi?->pertemuan ?? 0);
            if ($p < 1 || $p > 16) {
                continue;
            }
            $entry = $itemByMahasiswaId->get((int) $ai->mahasiswa_id);
            if (! $entry) {
                continue;
            }
            $status = $ai->status ?: null;
            $entry['pertemuan'][$p] = $status;
            if ($status && isset($entry['totals'][$status])) {
                $entry['totals'][$status]++;
            }
            $itemByMahasiswaId->put((int) $ai->mahasiswa_id, $entry);
        }

        $finalItems = $itemByMahasiswaId->values();

        $kaprodiNama = $this->resolveKaprodiNama($jurusan);
        $dosenNama = $dosen->nama;

        $html = view('dosen.absensi.rekap-pdf', [
            'jurusan' => $jurusan,
            'semester' => $semester,
            'mk' => $mk,
            'items' => $finalItems,
            'kaprodiNama' => $kaprodiNama,
            'dosenNama' => $dosenNama,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $safeKode = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($mk?->kode ?? 'MK'));
        $filename = 'rekap-absensi-'.$safeKode.'-S'.$semester.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportExcel(Request $request, Absensi $absensi)
    {
        $routePrefix = $request->user()?->isDosen() ? 'dosen' : 'admin';

        if ($request->user()?->isDosen()) {
            $dosen = $request->user()?->dosen;
            if (! $dosen) {
                abort(403);
            }
            $absensi->loadMissing('mataKuliah');
            abort_unless(in_array((int) $dosen->id, [(int) ($absensi->mataKuliah?->dosen_id ?? 0), (int) ($absensi->mataKuliah?->dosen_id_2 ?? 0)], true), 403);
        }

        $absensi->load(['mataKuliah', 'items.mahasiswa']);
        $items = $absensi->items->sortBy(fn ($i) => (string) ($i->mahasiswa?->npm ?? ''))->values();

        $mk = $absensi->mataKuliah;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Absensi');

        $sheet->setCellValue('A1', 'ABSENSI');
        $sheet->setCellValue('A2', 'Jurusan');
        $sheet->setCellValue('B2', $absensi->jurusan);
        $sheet->setCellValue('A3', 'Semester');
        $sheet->setCellValue('B3', (int) $absensi->semester);
        $sheet->setCellValue('A4', 'Mata Kuliah');
        $sheet->setCellValue('B4', ($mk?->kode ?? '').' - '.($mk?->nama ?? ''));
        $sheet->setCellValue('A5', 'Pertemuan');
        $sheet->setCellValue('B5', (int) $absensi->pertemuan);
        $sheet->setCellValue('A6', 'Tanggal');
        $sheet->setCellValue('B6', $absensi->tanggal?->format('d/m/Y') ?? '');
        $sheet->setCellValue('A7', 'Materi');
        $sheet->setCellValue('B7', $absensi->materi ?? '');

        $headers = ['No', 'NPM', 'Nama', 'Status', 'Keterangan', 'Paraf'];
        $headerRow = 9;
        foreach ($headers as $col => $label) {
            $sheet->setCellValue([$col + 1, $headerRow], $label);
        }

        $rowIndex = $headerRow + 1;
        foreach ($items as $i => $item) {
            $sheet->setCellValue([1, $rowIndex], $i + 1);
            $sheet->setCellValue([2, $rowIndex], $item->mahasiswa?->npm ?? '');
            $sheet->setCellValue([3, $rowIndex], $item->mahasiswa?->nama_lengkap ?? '');
            $sheet->setCellValue([4, $rowIndex], '');
            $sheet->setCellValue([5, $rowIndex], '');
            $sheet->setCellValue([6, $rowIndex], '');
            $rowIndex++;
        }

        foreach ([1 => 6, 2 => 18, 3 => 32, 4 => 10, 5 => 22, 6 => 10] as $col => $width) {
            $sheet->getColumnDimensionByColumn($col)->setWidth($width);
        }

        $writer = new Xlsx($spreadsheet);

        $safeKode = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) ($mk?->kode ?? 'MK'));
        $filename = 'absensi-'.$safeKode.'-P'.$absensi->pertemuan.'.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
