<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

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
        $jurusan = trim((string) $request->get('jurusan', ''));
        $semester = (int) $request->get('semester', 0);
        $mataKuliahId = (int) $request->get('mata_kuliah_id', 0);

        $mataKuliahQuery = MataKuliah::query()->orderBy('semester')->orderBy('kode');
        if ($semester >= 1 && $semester <= 8) {
            $mataKuliahQuery->where('semester', $semester);
        }
        if ($jurusan !== '' && $semester >= 1 && $semester <= 8) {
            $mataKuliahQuery->where('jurusan', $jurusan);
            $mataKuliahQuery->whereHas('krsItems.krs', function ($q) use ($jurusan, $semester) {
                $q->where('semester', $semester)
                    ->where('status_approval', 'approved')
                    ->whereHas('mahasiswa', function ($qm) use ($jurusan) {
                        $qm->where('program_studi', $jurusan);
                    });
            });
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
        ]);
    }

    public function entry(Request $request): View
    {
        $validated = $request->validate([
            'jurusan' => ['required', 'string'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'mata_kuliah_id' => [
                'required',
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $request->input('semester'))
                    ->where('jurusan', (string) $request->input('jurusan')),
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
                $q->where('semester', $validated['semester'])
                    ->where('status_approval', 'approved')
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
        ]);
    }

    public function update(Request $request, Absensi $absensi): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['nullable', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['nullable', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'array'],
            'keterangan.*' => ['nullable', 'string', 'max:255'],
        ]);

        $absensi->update([
            'tanggal' => $validated['tanggal'] ?? null,
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

        return redirect()->route('admin.absensi.entry', [
            'jurusan' => $absensi->jurusan,
            'semester' => $absensi->semester,
            'mata_kuliah_id' => $absensi->mata_kuliah_id,
            'pertemuan' => $absensi->pertemuan,
        ])->with('success', 'Absensi berhasil disimpan.');
    }
}
