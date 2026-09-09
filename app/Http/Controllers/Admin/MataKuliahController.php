<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\RosterUpload;
use App\Models\SkMengajar;
use Dompdf\Dompdf;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MataKuliahController extends Controller
{
    private const JURUSAN = [
        'Pendidikan Agama Islam',
        'Pendidikan Islam Anak Usia Dini',
        'Hukum Keluarga Islam',
        'Hukum Tata Negara',
        'Perbankan Syariah',
        'Ekonomi Syariah',
    ];

    private function buildMataKuliahQuery(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $jurusan = trim((string) $request->get('jurusan', ''));
        $semester = (int) $request->get('semester', 0);

        $query = MataKuliah::query()->with(['dosen', 'dosen2']);
        if ($jurusan !== '') {
            $query->where('jurusan', $jurusan);
        }
        if ($semester >= 1 && $semester <= 8) {
            $query->where('semester', $semester);
        }
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('kode', 'like', "%{$q}%")
                    ->orWhere('nama', 'like', "%{$q}%");
            });
        }

        return (object) [
            'query' => $query,
            'q' => $q,
            'jurusan' => $jurusan,
            'semester' => $semester,
        ];
    }

    public function index(Request $request): View
    {
        $ctx = $this->buildMataKuliahQuery($request);
        $mataKuliah = $ctx->query->orderBy('kode')->paginate(10)->withQueryString();

        $subSk = SkMengajar::query()
            ->selectRaw('MAX(id) as id')
            ->groupBy('program_studi');
        $skPerProdi = SkMengajar::query()
            ->whereIn('id', $subSk->pluck('id')->all())
            ->whereNotNull('file_pdf')
            ->where('file_pdf', '!=', '')
            ->get()
            ->keyBy('program_studi');

        $subRoster = RosterUpload::query()
            ->selectRaw('MAX(id) as id')
            ->groupBy('program_studi');
        $rosterPerProdi = RosterUpload::query()
            ->whereIn('id', $subRoster->pluck('id')->all())
            ->whereNotNull('file_pdf')
            ->where('file_pdf', '!=', '')
            ->get()
            ->keyBy('program_studi');

        $listMkPerProdi = [];
        foreach (self::JURUSAN as $j) {
            $listMkPerProdi[$j] = MataKuliah::query()
                ->where('jurusan', $j)
                ->orderBy('semester')
                ->orderBy('kode')
                ->get(['id', 'kode', 'nama', 'semester', 'sks']);
        }

        return view('admin.mata-kuliah.index', [
            'jurusanList' => self::JURUSAN,
            'mataKuliah' => $mataKuliah,
            'q' => $ctx->q,
            'jurusan' => $ctx->jurusan,
            'semester' => $ctx->semester ?: null,
            'skPerProdi' => $skPerProdi,
            'rosterPerProdi' => $rosterPerProdi,
            'listMkPerProdi' => $listMkPerProdi,
        ]);
    }

    public function uploadSkPdfProdi(Request $request, string $prodi): RedirectResponse
    {
        $prodi = trim(urldecode($prodi));
        if (!in_array($prodi, self::JURUSAN, true)) {
            return redirect()->back()->withErrors(['sk_pdf' => "Prodi {$prodi} tidak valid."]);
        }

        $validated = $request->validate([
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $existing = SkMengajar::query()->where('program_studi', $prodi)->orderByDesc('id')->first();
        $newPath = $this->storePdf($request, 'sk-mengajar', 'file_pdf');
        $oldPath = $existing && !empty($existing->file_pdf) ? (string) $existing->file_pdf : '';

        if ($existing) {
            try {
                $existing->update(['file_pdf' => $newPath]);
            } catch (QueryException $e) {
                $this->deletePdf($newPath);
                return redirect()->back()->withErrors(['sk_pdf' => 'Gagal update SK Mengajar: '.mb_substr($e->getMessage(), 0, 200)]);
            }
            if ($oldPath !== '') {
                $this->deletePdf($oldPath);
            }
            return redirect()->back()->with('success', "✅ File PDF SK Mengajar Prodi {$prodi} BERHASIL diupdate. Semua dosen di prodi ini sekarang mendapatkan file terbaru.");
        }

        $auto = $this->autoFillSkForProdi($prodi);
        $auto['program_studi'] = $prodi;
        $auto['created_by'] = Auth::id();
        $auto['file_pdf'] = $newPath;

        try {
            SkMengajar::create($auto);
        } catch (QueryException $e) {
            $this->deletePdf($newPath);
            $duplicate = ((string) $e->getCode() === '23000') || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
            if ($duplicate) {
                return redirect()->back()->withErrors(['sk_pdf' => "SK Mengajar untuk Prodi {$prodi} SUDAH ADA. Silakan refresh halaman lalu upload ulang untuk menimpa file lama."]);
            }
            throw $e;
        }

        return redirect()->back()->with('success', "✅ File PDF SK Mengajar Prodi {$prodi} BERHASIL diupload. Sekali upload — otomatis terbaca untuk SEMUA dosen di prodi ini ketika klik tombol PDF SK Mengajar.");
    }

    public function uploadRosterPdfProdi(Request $request, string $prodi): RedirectResponse
    {
        $prodi = trim(urldecode($prodi));
        if (!in_array($prodi, self::JURUSAN, true)) {
            return redirect()->back()->withErrors(['roster_pdf' => "Prodi {$prodi} tidak valid."]);
        }

        $validated = $request->validate([
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $existing = RosterUpload::query()->where('program_studi', $prodi)->orderByDesc('id')->first();
        $newPath = $this->storePdf($request, 'roster', 'file_pdf');
        $oldPath = $existing && !empty($existing->file_pdf) ? (string) $existing->file_pdf : '';

        if ($existing) {
            try {
                $existing->update(['file_pdf' => $newPath]);
            } catch (QueryException $e) {
                $this->deletePdf($newPath);
                return redirect()->back()->withErrors(['roster_pdf' => 'Gagal update Roster Kuliah: '.mb_substr($e->getMessage(), 0, 200)]);
            }
            if ($oldPath !== '') {
                $this->deletePdf($oldPath);
            }
            return redirect()->back()->with('success', "✅ File PDF Roster Kuliah Prodi {$prodi} BERHASIL diupdate. Semua dosen di prodi ini sekarang mendapatkan file terbaru.");
        }

        $auto = $this->autoFillRosterForProdi($prodi);
        $auto['program_studi'] = $prodi;
        $auto['created_by'] = Auth::id();
        $auto['file_pdf'] = $newPath;

        try {
            RosterUpload::create($auto);
        } catch (QueryException $e) {
            $this->deletePdf($newPath);
            $duplicate = ((string) $e->getCode() === '23000') || (isset($e->errorInfo[1]) && (int) $e->errorInfo[1] === 1062);
            if ($duplicate) {
                return redirect()->back()->withErrors(['roster_pdf' => "Roster Kuliah untuk Prodi {$prodi} SUDAH ADA. Silakan refresh halaman lalu upload ulang untuk menimpa file lama."]);
            }
            throw $e;
        }

        return redirect()->back()->with('success', "✅ File PDF Roster Kuliah Prodi {$prodi} BERHASIL diupload. Sekali upload — otomatis terbaca untuk SEMUA dosen di prodi ini ketika klik tombol PDF Roster.");
    }

    private function autoFillSkForProdi(string $prodi): array
    {
        $mk = MataKuliah::query()
            ->where('jurusan', $prodi)
            ->orderBy('semester')
            ->orderBy('kode')
            ->first(['id', 'kode', 'nama', 'semester', 'sks', 'jurusan', 'dosen_id', 'dosen_id_2']);

        $y = (int) date('Y');
        $ta = $y.'/'.($y + 1);
        $tgl = date('Y-m-d');
        $tglSelesai = date('Y-m-d', strtotime('+4 months'));

        if (!$mk) {
            return [
                'mata_kuliah_id' => 0,
                'dosen_id' => 0,
                'semester' => 1,
                'tahun_ajaran' => $ta,
                'nomor_sk' => 'IADD-SK/'.$y.'/'.preg_replace('/[^A-Za-z0-9]/', '_', $prodi),
                'tanggal_sk' => $tgl,
                'tanggal_mulai' => $tgl,
                'tanggal_selesai' => $tglSelesai,
                'beban_sks' => 0,
                'kelas' => 'A',
                'jabatan_dosen' => 'Dosen',
                'tugas_tambahan' => '',
                'catatan' => '',
            ];
        }

        $dosenId = (int) ($mk->dosen_id > 0 ? $mk->dosen_id : ($mk->dosen_id_2 > 0 ? $mk->dosen_id_2 : 0));
        $jabatan = 'Dosen';
        if ($dosenId > 0) {
            $d = Dosen::query()->find($dosenId, ['jabatan_struktural']);
            if ($d && !empty($d->jabatan_struktural)) {
                $jabatan = (string) $d->jabatan_struktural;
            }
        }

        $nomor = 'IADD-SK/'.$y.'/MK-'.sprintf('%03d', $mk->id).'-D'.sprintf('%03d', max(1, $dosenId));
        return [
            'mata_kuliah_id' => (int) $mk->id,
            'dosen_id' => $dosenId,
            'semester' => (int) ($mk->semester ?? 1),
            'tahun_ajaran' => $ta,
            'nomor_sk' => $nomor,
            'tanggal_sk' => $tgl,
            'tanggal_mulai' => $tgl,
            'tanggal_selesai' => $tglSelesai,
            'beban_sks' => (float) ($mk->sks ?? 0),
            'kelas' => 'A',
            'jabatan_dosen' => $jabatan,
            'tugas_tambahan' => '',
            'catatan' => '',
        ];
    }

    private function autoFillRosterForProdi(string $prodi): array
    {
        $mk = MataKuliah::query()
            ->where('jurusan', $prodi)
            ->orderBy('semester')
            ->orderBy('kode')
            ->first(['id', 'kode', 'nama', 'semester', 'sks', 'jurusan', 'dosen_id', 'dosen_id_2']);

        $y = (int) date('Y');
        $ta = $y.'/'.($y + 1);
        $smt = 1;
        $mkId = 0;
        $dosenId = 0;
        if ($mk) {
            $smt = (int) ($mk->semester ?? 1);
            $mkId = (int) $mk->id;
            $dosenId = (int) ($mk->dosen_id > 0 ? $mk->dosen_id : ($mk->dosen_id_2 > 0 ? $mk->dosen_id_2 : 0));
        }
        $ket = 'Roster Kuliah '.$prodi.' Semester '.$smt.' Tahun Ajaran '.$ta;

        return [
            'mata_kuliah_id' => $mkId,
            'dosen_id' => $dosenId,
            'semester' => $smt,
            'tahun_ajaran' => $ta,
            'kelas' => 'A',
            'keterangan' => $ket,
        ];
    }

    private function storePdf(Request $request, string $folder, string $inputName): string
    {
        $file = $request->file($inputName);
        $orig = (string) $file->getClientOriginalName();
        $orig = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        $ts = date('Ymd_His');
        $filename = $ts.'_'.$orig;
        return $file->storeAs('dokumen-kuliah/'.$folder, $filename, 'public');
    }

    private function deletePdf(string $storagePath): void
    {
        try {
            if (Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->delete($storagePath);
            }
        } catch (\Throwable $e) {
        }
    }

    public function exportPdf(Request $request)
    {
        $ctx = $this->buildMataKuliahQuery($request);
        $items = $ctx->query->orderBy('kode')->get();

        $html = view('admin.mata-kuliah.pdf', [
            'items' => $items,
            'q' => $ctx->q,
            'jurusan' => $ctx->jurusan,
            'semester' => $ctx->semester,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $namePart = $ctx->jurusan ? Str::slug($ctx->jurusan) : 'semua-jurusan';
        if ($ctx->semester >= 1 && $ctx->semester <= 8) {
            $namePart .= '-semester-'.$ctx->semester;
        }
        $filename = 'mata-kuliah-'.$namePart.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.mata-kuliah.create', [
            'dosen' => Dosen::query()->orderBy('nama')->get(),
            'jurusanList' => self::JURUSAN,
            'defaultJurusan' => trim((string) $request->get('jurusan', '')) ?: null,
            'defaultSemester' => (int) $request->get('semester', 0) ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', Rule::in(self::JURUSAN)],
            'sks' => ['required', 'integer', 'min:0', 'max:24'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'dosen_id_2' => ['nullable', 'exists:dosen,id', 'different:dosen_id'],
        ]);

        MataKuliah::query()->create($validated);

        return redirect()
            ->route('admin.mata-kuliah.index', ['jurusan' => $validated['jurusan'], 'semester' => $validated['semester']])
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function edit(MataKuliah $mataKuliah): View
    {
        return view('admin.mata-kuliah.edit', [
            'mataKuliah' => $mataKuliah,
            'dosen' => Dosen::query()->orderBy('nama')->get(),
            'jurusanList' => self::JURUSAN,
        ]);
    }

    public function uploadRpsAdmin(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $validated = $request->validate([
            'rps_admin' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
        ]);

        $file = $validated['rps_admin'];
        $originalName = (string) $file->getClientOriginalName();
        $ext = (string) $file->getClientOriginalExtension();
        $filename = 'rps-admin-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
        $path = $file->storeAs('rps/admin/'.$mataKuliah->id, $filename, 'public');

        if ($mataKuliah->rps_admin_path) {
            Storage::disk('public')->delete($mataKuliah->rps_admin_path);
        }

        $mataKuliah->update([
            'rps_admin_path' => $path,
            'rps_admin_name' => $originalName,
        ]);

        return back()->with('success', 'Contoh RPS (Admin) berhasil diupload.');
    }

    public function downloadRpsAdmin(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        abort_unless($mataKuliah->rps_admin_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_admin_path), 404);

        $downloadName = $mataKuliah->rps_admin_name ?: basename($mataKuliah->rps_admin_path);
        return response()->download(storage_path('app/public/'.$mataKuliah->rps_admin_path), $downloadName);
    }

    public function previewRpsAdmin(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        abort_unless($mataKuliah->rps_admin_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_admin_path), 404);

        $downloadName = $mataKuliah->rps_admin_name ?: basename($mataKuliah->rps_admin_path);
        return response()->file(storage_path('app/public/'.$mataKuliah->rps_admin_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    public function destroyRpsAdmin(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        if ($mataKuliah->rps_admin_path) {
            Storage::disk('public')->delete($mataKuliah->rps_admin_path);
        }

        $mataKuliah->update([
            'rps_admin_path' => null,
            'rps_admin_name' => null,
        ]);

        return back()->with('success', 'Contoh RPS (Admin) berhasil dihapus.');
    }

    public function downloadRpsDosen(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        abort_unless($mataKuliah->rps_dosen_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_dosen_path), 404);

        $downloadName = $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path);
        return response()->download(storage_path('app/public/'.$mataKuliah->rps_dosen_path), $downloadName);
    }

    public function previewRpsDosen(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        abort_unless($mataKuliah->rps_dosen_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_dosen_path), 404);

        $downloadName = $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path);
        return response()->file(storage_path('app/public/'.$mataKuliah->rps_dosen_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    public function destroyRpsDosen(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        if ($mataKuliah->rps_dosen_path) {
            Storage::disk('public')->delete($mataKuliah->rps_dosen_path);
        }

        $mataKuliah->update([
            'rps_dosen_path' => null,
            'rps_dosen_name' => null,
        ]);

        return back()->with('success', 'RPS dosen berhasil dihapus.');
    }

    public function update(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:50'],
            'nama' => ['required', 'string', 'max:255'],
            'jurusan' => ['required', 'string', Rule::in(self::JURUSAN)],
            'sks' => ['required', 'integer', 'min:0', 'max:24'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'dosen_id_2' => ['nullable', 'exists:dosen,id', 'different:dosen_id'],
        ]);

        $mataKuliah->update($validated);

        return redirect()
            ->route('admin.mata-kuliah.index', ['jurusan' => $validated['jurusan'], 'semester' => $validated['semester']])
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function destroy(MataKuliah $mataKuliah): RedirectResponse
    {
        $mataKuliah->delete();

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', (array) $validated['ids'])));
        if (count($ids) === 0) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        MataKuliah::query()->whereIn('id', $ids)->delete();

        return redirect()->route('admin.mata-kuliah.index')->with('success', 'Mata kuliah terpilih berhasil dihapus.');
    }
}
