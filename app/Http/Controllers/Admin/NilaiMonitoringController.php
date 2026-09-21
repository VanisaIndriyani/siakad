<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\NilaiArchive;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        $data = $this->buildIndexData($request, true);

        $archiveBatches = NilaiArchive::query()
            ->select('batch_code', 'semester', DB::raw('COUNT(*) as total_records'), DB::raw('MAX(reset_at) as reset_at'), 'reset_by')
            ->with(['resetBy:id,name'])
            ->groupBy('batch_code', 'semester', 'reset_by')
            ->orderByDesc('reset_at')
            ->limit(5)
            ->get();

        return view('admin.nilai-monitoring.index', array_merge($data, [
            'archiveBatches' => $archiveBatches,
        ]));
    }

    public function resetNilaiSemester(Request $request): RedirectResponse
    {
        $semester = (int) $request->input('semester', 0);
        if ($semester < 1 || $semester > 8) {
            return redirect()->back()->withErrors(['reset' => 'Semester tidak valid (pilih 1-8).']);
        }

        $confirm = strtoupper(trim((string) $request->input('confirm_reset', '')));
        if ($confirm !== 'RESET-SEMESTER-'.$semester) {
            return redirect()->back()->withErrors(['reset' => 'Konfirmasi reset tidak sesuai. Silakan ketik teks yang diminta dengan benar.']);
        }

        $khsQuery = Khs::query()->where('semester', $semester)->with('items');
        $totalKhs = (clone $khsQuery)->count();
        if ($totalKhs === 0) {
            return redirect()->back()->with('error', "Tidak ada data nilai untuk Semester {$semester}. Tidak perlu direset.");
        }

        $batchCode = 'RESET-SMT'.$semester.'-'.date('Ymd-His').'-'.Str::upper(Str::random(4));
        $resetBy = Auth::id();
        $resetAt = now();
        $tahunAjaranDefault = (int) date('Y').'/'.((int) date('Y') + 1);

        DB::beginTransaction();
        try {
            $khsList = (clone $khsQuery)->get();
            $archiveInsert = [];

            foreach ($khsList as $khs) {
                $tahunAjaran = $khs->tahun_ajaran ?: $tahunAjaranDefault;

                if ($khs->items && $khs->items->count() > 0) {
                    foreach ($khs->items as $item) {
                        $hasValue = $item->nilai_tm !== null
                            || $item->nilai_quis !== null
                            || $item->nilai_mid !== null
                            || $item->nilai_final !== null
                            || $item->nilai_angka !== null
                            || $item->nilai_huruf !== null;
                        if (! $hasValue) {
                            continue;
                        }
                        $archiveInsert[] = [
                            'batch_code' => $batchCode,
                            'semester' => $semester,
                            'tahun_ajaran' => $tahunAjaran,
                            'mahasiswa_id' => (int) $khs->mahasiswa_id,
                            'mata_kuliah_id' => (int) $item->mata_kuliah_id,
                            'khs_id' => (int) $khs->id,
                            'khs_item_id' => (int) $item->id,
                            'nilai_tm' => $item->nilai_tm,
                            'nilai_quis' => $item->nilai_quis,
                            'nilai_mid' => $item->nilai_mid,
                            'nilai_final' => $item->nilai_final,
                            'nilai_angka' => $item->nilai_angka,
                            'nilai_huruf' => $item->nilai_huruf,
                            'ips_saat_reset' => $khs->ips,
                            'ipk_saat_reset' => $khs->ipk,
                            'catatan' => 'Reset nilai otomatis semester '.$semester,
                            'reset_by' => $resetBy,
                            'reset_at' => $resetAt,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if (count($archiveInsert) > 0) {
                $chunks = array_chunk($archiveInsert, 500);
                foreach ($chunks as $chunk) {
                    NilaiArchive::insert($chunk);
                }
            }

            $totalArchived = count($archiveInsert);

            KhsItem::query()
                ->whereIn('khs_id', function ($sub) use ($semester) {
                    $sub->select('id')->from('khs')->where('semester', $semester);
                })
                ->update([
                    'nilai_tm' => null,
                    'nilai_quis' => null,
                    'nilai_mid' => null,
                    'nilai_final' => null,
                    'nilai_angka' => null,
                    'nilai_huruf' => null,
                ]);

            Khs::query()
                ->where('semester', $semester)
                ->update([
                    'ips' => null,
                ]);

            DB::commit();

            $msg = "✅ Reset nilai Semester {$semester} BERHASIL. ";
            $msg .= "Total KHS: {$totalKhs}, Total nilai yang di-archive: {$totalArchived}. ";
            if ($totalArchived > 0) {
                $msg .= "Batch: {$batchCode} — semua nilai lama AMAN tersimpan di tabel archive dan bisa dilihat/direstore kapan saja.";
            }

            return redirect()
                ->route('admin.nilai-monitoring.index', ['semester' => $semester])
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect()->back()->withErrors([
                'reset' => 'Gagal reset nilai: '.mb_substr($e->getMessage(), 0, 200),
            ]);
        }
    }

    public function arsipIndex(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $batchCode = trim((string) $request->get('batch', ''));

        $query = NilaiArchive::query()
            ->with([
                'mahasiswa:id,nama_lengkap,npm,program_studi',
                'mataKuliah:id,kode,nama,sks',
                'resetBy:id,name',
            ]);

        if ($semester >= 1 && $semester <= 8) {
            $query->where('semester', $semester);
        }
        if ($batchCode !== '') {
            $query->where('batch_code', 'like', '%'.$batchCode.'%');
        }
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('mahasiswa', function ($s) use ($q) {
                    $s->where('nama_lengkap', 'like', '%'.$q.'%')
                        ->orWhere('npm', 'like', '%'.$q.'%');
                })
                ->orWhereHas('mataKuliah', function ($s) use ($q) {
                    $s->where('kode', 'like', '%'.$q.'%')
                        ->orWhere('nama', 'like', '%'.$q.'%');
                })
                ->orWhere('batch_code', 'like', '%'.$q.'%');
            });
        }

        $query->orderByDesc('reset_at')->orderByDesc('id');
        $records = $query->paginate(25)->withQueryString();

        $batchGroups = NilaiArchive::query()
            ->select('batch_code', 'semester', DB::raw('COUNT(*) as total_records'), DB::raw('MAX(reset_at) as reset_at'), DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'), 'reset_by')
            ->with(['resetBy:id,name'])
            ->groupBy('batch_code', 'semester', 'reset_by')
            ->orderByDesc('reset_at')
            ->limit(10)
            ->get();

        $totalArsip = NilaiArchive::query()->count();
        $totalBatch = NilaiArchive::query()->distinct()->count('batch_code');

        return view('admin.nilai-monitoring.arsip', [
            'records' => $records,
            'batchGroups' => $batchGroups,
            'q' => $q,
            'semester' => $semester,
            'batchCode' => $batchCode,
            'totalArsip' => $totalArsip,
            'totalBatch' => $totalBatch,
            'semesterOptions' => range(1, 8),
        ]);
    }

    public function arsipDetail(Request $request, string $batch): View
    {
        $batch = strtoupper(trim($batch));
        $mhs_q = trim((string) $request->input('mhs_q', ''));

        $batchInfo = NilaiArchive::query()
            ->where('batch_code', $batch)
            ->select('batch_code', 'semester', 'tahun_ajaran', 'reset_by', DB::raw('COUNT(*) as total_records'), DB::raw('MAX(reset_at) as reset_at'), DB::raw('COUNT(DISTINCT mahasiswa_id) as total_mahasiswa'), DB::raw('COUNT(DISTINCT mata_kuliah_id) as total_mk'))
            ->with(['resetBy:id,name'])
            ->groupBy('batch_code', 'semester', 'tahun_ajaran', 'reset_by')
            ->firstOrFail();

        $query = NilaiArchive::query()
            ->where('batch_code', $batch)
            ->with([
                'mahasiswa:id,nama_lengkap,npm,program_studi',
                'mataKuliah:id,kode,nama,sks',
            ]);

        if ($mhs_q !== '') {
            $query->whereHas('mahasiswa', function ($s) use ($mhs_q) {
                $s->where('nama_lengkap', 'like', '%'.$mhs_q.'%')
                    ->orWhere('npm', 'like', '%'.$mhs_q.'%');
            });
        }

        $records = $query
            ->orderBy('mahasiswa_id')
            ->orderBy('mata_kuliah_id')
            ->get()
            ->groupBy(fn ($r) => (int) $r->mahasiswa_id);

        return view('admin.nilai-monitoring.arsip-detail', [
            'batchInfo' => $batchInfo,
            'recordsGrouped' => $records,
            'batchCode' => $batch,
        ]);
    }

    public function arsipRestoreBatch(Request $request, string $batch): RedirectResponse
    {
        $batch = strtoupper(trim($batch));
        $confirm = strtoupper(trim((string) $request->input('confirm_restore', '')));
        $expected = 'RESTORE-BATCH-'.$batch;
        if ($confirm !== $expected) {
            return redirect()->back()->withErrors([
                'restore' => "Konfirmasi restore salah. Harus ketik: {$expected}",
            ]);
        }

        $arsipRecords = NilaiArchive::query()
            ->where('batch_code', $batch)
            ->get();
        if ($arsipRecords->count() === 0) {
            return redirect()->back()->with('error', "Batch {$batch} tidak ditemukan.");
        }

        $updatedMhs = [];
        DB::beginTransaction();
        try {
            $restored = 0;
            $skipped = 0;
            $restoredViaFallback = 0;
            $skippedDetail = [];
            foreach ($arsipRecords as $ar) {
                $mkId = (int) $ar->mata_kuliah_id;
                $mhsId = (int) $ar->mahasiswa_id;
                $semester = (int) $ar->semester;
                $khsItemTarget = null;

                if (! empty($ar->khs_item_id)) {
                    $khsItem = KhsItem::query()->find((int) $ar->khs_item_id);
                    if ($khsItem) {
                        $khsItemTarget = $khsItem;
                    }
                }
                if (! $khsItemTarget && ! empty($ar->khs_id)) {
                    $khsItemTarget = KhsItem::query()
                        ->where('khs_id', (int) $ar->khs_id)
                        ->where('mata_kuliah_id', $mkId)
                        ->first();
                }
                if (! $khsItemTarget) {
                    $khsHead = Khs::query()
                        ->where('mahasiswa_id', $mhsId)
                        ->where('semester', $semester)
                        ->first();
                    if ($khsHead) {
                        $khsItemTarget = KhsItem::query()
                            ->where('khs_id', (int) $khsHead->id)
                            ->where('mata_kuliah_id', $mkId)
                            ->first();
                        if ($khsItemTarget) {
                            $restoredViaFallback++;
                        }
                    }
                }
                if (! $khsItemTarget) {
                    $skipped++;
                    $skippedDetail[] = [
                        'mahasiswa_id' => $mhsId,
                        'mata_kuliah_id' => $mkId,
                        'semester' => $semester,
                    ];
                    continue;
                }
                $khsItemTarget->nilai_tm = $ar->nilai_tm;
                $khsItemTarget->nilai_quis = $ar->nilai_quis;
                $khsItemTarget->nilai_mid = $ar->nilai_mid;
                $khsItemTarget->nilai_final = $ar->nilai_final;
                $khsItemTarget->nilai_angka = $ar->nilai_angka;
                $khsItemTarget->nilai_huruf = $ar->nilai_huruf;
                $khsItemTarget->save();
                $restored++;
                $updatedMhs[$mhsId] = true;
            }

            $nilaiController = new \App\Http\Controllers\Dosen\NilaiController();
            $maxSemester = 8;
            foreach (array_keys($updatedMhs) as $mhsId) {
                $nilaiController->callableRecalculate((int) $mhsId, $maxSemester);
            }
            DB::commit();

            $msg = "✅ Restore Batch {$batch} BERHASIL. ";
            $msg .= "Data nilai yang dikembalikan: {$restored} record. ";
            if ($restoredViaFallback > 0) {
                $msg .= "({$restoredViaFallback} record dipulihkan via pencarian Mahasiswa+Semester karena ID KHS lama tidak ditemukan — cocok untuk kasus KHS dibuat ulang setelah reset). ";
            }
            if ($skipped > 0) {
                $sample = array_slice($skippedDetail, 0, 5);
                $sampleStr = collect($sample)->map(fn ($s) => "Mhs#{$s['mahasiswa_id']}/MK#{$s['mata_kuliah_id']}/S{$s['semester']}")->implode(', ');
                $msg .= "Dilewati (KHS / KHS Item tidak ditemukan): {$skipped} record. Sampel: {$sampleStr}. Ini biasanya terjadi jika MK di KHS sekarang tidak sama dengan saat reset.";
            }
            $msg .= count($updatedMhs)." mahasiswa IPS/IPK dihitung ulang.";
            return redirect()->route('admin.nilai-monitoring.arsip-detail', ['batch' => $batch])
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withErrors([
                'restore' => 'Gagal restore batch: '.mb_substr($e->getMessage(), 0, 200),
            ]);
        }
    }

    public function arsipRestoreMahasiswa(Request $request, string $batch, Mahasiswa $mahasiswa)
    {
        $request->validate([
            'confirm' => ['required', 'string', function ($attr, $val, $fail) use ($batch, $mahasiswa) {
                $expected = "RESTORE-MHS-{$mahasiswa->npm}-BATCH-{$batch}";
                if (strtoupper(trim($val)) !== $expected) {
                    $fail("Teks konfirmasi SALAH. Harus ketik: {$expected}");
                }
            }],
        ]);

        DB::beginTransaction();
        try {
            $archives = NilaiArchive::query()
                ->where('batch_code', $batch)
                ->where('mahasiswa_id', $mahasiswa->id)
                ->get();

            if ($archives->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'restore' => "Tidak ada nilai backup untuk NPM {$mahasiswa->npm} di batch {$batch}",
                ]);
            }

            $semester = $archives->first()->semester;
            $khsLookupCache = [];
            $khsItemIdsByMkCache = [];
            $restored = 0;
            $restoredViaKhsItem = 0;
            $restoredViaKhsAndMk = 0;
            $restoredViaFallback = 0;
            $skipped = 0;
            $skippedDetail = [];

            foreach ($archives as $ar) {
                $updateData = [
                    'nilai_tm' => $ar->nilai_tm,
                    'nilai_quis' => $ar->nilai_quis,
                    'nilai_mid' => $ar->nilai_mid,
                    'nilai_final' => $ar->nilai_final,
                    'nilai_angka' => $ar->nilai_angka,
                    'nilai_huruf' => $ar->nilai_huruf,
                ];
                $updated = false;

                if ($ar->khs_item_id) {
                    $affected = KhsItem::query()->where('id', $ar->khs_item_id)->update($updateData);
                    if ($affected > 0) {
                        $restored++;
                        $restoredViaKhsItem++;
                        $updated = true;
                    }
                }

                if (! $updated && $ar->khs_id && $ar->mata_kuliah_id) {
                    $affected = KhsItem::query()
                        ->where('khs_id', $ar->khs_id)
                        ->where('mata_kuliah_id', $ar->mata_kuliah_id)
                        ->update($updateData);
                    if ($affected > 0) {
                        $restored++;
                        $restoredViaKhsAndMk++;
                        $updated = true;
                    }
                }

                if (! $updated) {
                    $cacheKey = "{$mahasiswa->id}:{$ar->semester}";
                    if (! isset($khsLookupCache[$cacheKey])) {
                        $khsLookupCache[$cacheKey] = Khs::query()
                            ->where('mahasiswa_id', $mahasiswa->id)
                            ->where('semester', $ar->semester)
                            ->first();
                    }
                    $currentKhs = $khsLookupCache[$cacheKey];
                    if ($currentKhs) {
                        $itemCacheKey = "{$currentKhs->id}:{$ar->mata_kuliah_id}";
                        if (! isset($khsItemIdsByMkCache[$itemCacheKey])) {
                            $khsItemIdsByMkCache[$itemCacheKey] = KhsItem::query()
                                ->where('khs_id', $currentKhs->id)
                                ->where('mata_kuliah_id', $ar->mata_kuliah_id)
                                ->first();
                        }
                        $item = $khsItemIdsByMkCache[$itemCacheKey];
                        if ($item) {
                            $item->update($updateData);
                            $restored++;
                            $restoredViaFallback++;
                            $updated = true;
                        }
                    }
                }

                if (! $updated) {
                    $skipped++;
                    $skippedDetail[] = [
                        'mahasiswa_id' => $ar->mahasiswa_id,
                        'mata_kuliah_id' => $ar->mata_kuliah_id,
                        'semester' => $ar->semester,
                    ];
                }
            }

            $updatedMhs = [];
            $updatedMhs[$mahasiswa->id] = true;
            try {
                $nilaiCtrl = new \App\Http\Controllers\Dosen\NilaiController();
                $nilaiCtrl->callableRecalculate([$mahasiswa->id]);
            } catch (\Throwable $e) {
                report($e);
            }

            DB::commit();
            $msg = "✅ Restore NPM {$mahasiswa->npm} BERHASIL! Batch: {$batch}. Semester {$semester}. Total data restore: {$restored} record. ";
            $layers = [];
            if ($restoredViaKhsItem > 0) $layers[] = "via Layer 1 (khs_item_id): {$restoredViaKhsItem}";
            if ($restoredViaKhsAndMk > 0) $layers[] = "via Layer 2 (khs_id+mk): {$restoredViaKhsAndMk}";
            if ($restoredViaFallback > 0) $layers[] = "via Layer 3 Fallback (Mahasiswa+Semester): {$restoredViaFallback}";
            if ($layers) $msg .= "Layer restore: ".implode(', ', $layers).". ";
            if ($skipped > 0) {
                $sample = array_slice($skippedDetail, 0, 5);
                $sampleStr = collect($sample)->map(fn ($s) => "MK#{$s['mata_kuliah_id']}/S{$s['semester']}")->implode(', ');
                $msg .= "Dilewati: {$skipped} record (MK tidak ada di KHS sekarang). Sampel: {$sampleStr}. ";
            }
            $msg .= "IPS/IPK NPM {$mahasiswa->npm} otomatis dihitung ulang. ✅";
            return redirect()->route('admin.nilai-monitoring.arsip-detail', ['batch' => $batch])
                ->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->withErrors([
                'restore' => "Gagal restore mahasiswa {$mahasiswa->npm}: ".mb_substr($e->getMessage(), 0, 200),
            ]);
        }
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

