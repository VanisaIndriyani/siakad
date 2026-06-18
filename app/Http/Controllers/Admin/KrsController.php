<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiItem;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KrsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status_approval', $status);
        }

        $krs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.krs.index', [
            'krs' => $krs,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(Krs $krs): View
    {
        $krs->load(['mahasiswa', 'mahasiswa.user', 'items.mataKuliah']);

        return view('admin.krs.show', [
            'krs' => $krs,
        ]);
    }

    private function resolveProdiSigners(?string $programStudi): array
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return ['kaprodi' => null, 'sekprodi' => null];
        }

        $kaprodi = \App\Models\Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();

        $sekprodi = \App\Models\Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Sekretaris Prodi')
            ->orderByDesc('id')
            ->first();

        return [
            'kaprodi' => $kaprodi,
            'sekprodi' => $sekprodi,
        ];
    }

    public function downloadPdf(Krs $krs)
    {
        $krs->load(['mahasiswa', 'items.mataKuliah']);
        
        $signers = $this->resolveProdiSigners($krs->mahasiswa->program_studi ?? null);

        $html = view('mahasiswa.krs.pdf', [
            'krs' => $krs,
            'kaprodi' => $signers['kaprodi'],
            'sekprodi' => $signers['sekprodi'],
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'krs-'.$krs->mahasiswa->npm.'-'.$krs->semester.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function updateStatus(Request $request, Krs $krs): RedirectResponse
    {
        $validated = $request->validate([
            'status_approval' => ['required', 'in:pending,approved,rejected'],
            'catatan_approval' => ['nullable', 'string'],
        ]);

        $krs->update($validated);
        cache()->forget('admin_pending_krs_count');

        if ($validated['status_approval'] === 'approved') {
            $krs->loadMissing(['items', 'mahasiswa']);

            $khs = Khs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $krs->mahasiswa_id,
                    'semester' => $krs->semester,
                ],
                [
                    'tahun_ajaran' => $krs->tahun_ajaran,
                ]
            );

            foreach ($krs->items as $item) {
                KhsItem::query()->firstOrCreate([
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => $item->mata_kuliah_id,
                ]);
            }

            $jurusan = (string) ($krs->mahasiswa?->program_studi ?? '');
            if ($jurusan !== '') {
                $mkIds = $krs->items->pluck('mata_kuliah_id')->map(fn ($v) => (int) $v)->all();
                foreach ($mkIds as $mkId) {
                    foreach (range(1, 16) as $pertemuan) {
                        $absensi = Absensi::query()->firstOrCreate(
                            [
                                'jurusan' => $jurusan,
                                'semester' => (int) $krs->semester,
                                'mata_kuliah_id' => $mkId,
                                'pertemuan' => $pertemuan,
                            ],
                            [
                                'created_by_user_id' => null,
                            ]
                        );

                        AbsensiItem::query()->firstOrCreate(
                            [
                                'absensi_id' => $absensi->id,
                                'mahasiswa_id' => (int) $krs->mahasiswa_id,
                            ],
                            [
                                'status' => null,
                            ]
                        );
                    }
                }
            }
        }

        $redirect = (string) $request->input('redirect', '');
        if ($redirect !== '') {
            $host = parse_url($redirect, PHP_URL_HOST);
            if ($host === null || $host === $request->getHost()) {
                return redirect()->to($redirect)->with('success', 'Status KRS berhasil diperbarui.');
            }
        }

        return redirect()->route('admin.krs.show', $krs)->with('success', 'Status KRS berhasil diperbarui.');
    }

    private function cleanupDerivedRecordsForKrs(Krs $krs): void
    {
        $krs->loadMissing(['items', 'mahasiswa']);

        $semester = (int) $krs->semester;
        $jurusan = trim((string) ($krs->mahasiswa?->program_studi ?? ''));
        $mkIds = $krs->items
            ->pluck('mata_kuliah_id')
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        if ($jurusan !== '' && count($mkIds) > 0) {
            AbsensiItem::query()
                ->where('mahasiswa_id', (int) $krs->mahasiswa_id)
                ->whereHas('absensi', function ($q) use ($jurusan, $semester, $mkIds) {
                    $q->where('jurusan', $jurusan)
                        ->where('semester', $semester)
                        ->whereIn('mata_kuliah_id', $mkIds);
                })
                ->delete();
        }

        if (count($mkIds) > 0) {
            $khs = Khs::query()
                ->where('mahasiswa_id', (int) $krs->mahasiswa_id)
                ->where('semester', $semester)
                ->first();

            if ($khs) {
                KhsItem::query()
                    ->where('khs_id', (int) $khs->id)
                    ->whereIn('mata_kuliah_id', $mkIds)
                    ->whereNull('nilai_angka')
                    ->whereNull('nilai_huruf')
                    ->delete();
            }
        }
    }

    public function destroy(Krs $krs): RedirectResponse
    {
        $this->cleanupDerivedRecordsForKrs($krs);
        $krs->delete();
        cache()->forget('admin_pending_krs_count');

        return back()->with('success', 'Data KRS berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:krs,id'],
        ]);

        $ids = array_values(array_unique(array_map('intval', (array) $validated['ids'])));
        $rows = Krs::query()->with(['items', 'mahasiswa'])->whereIn('id', $ids)->get();
        foreach ($rows as $krs) {
            $this->cleanupDerivedRecordsForKrs($krs);
        }

        Krs::query()->whereIn('id', $ids)->delete();
        cache()->forget('admin_pending_krs_count');

        return back()->with('success', 'Data KRS terpilih berhasil dihapus.');
    }
}
