<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\SkMengajar;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SkMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $prodi = trim((string) $request->get('prodi', ''));
        $prodies = $this->listProdies();

        $sub = SkMengajar::query()
            ->selectRaw('MAX(id) as id')
            ->when($prodi !== '', fn ($s) => $s->where('program_studi', $prodi))
            ->groupBy('program_studi');

        $query = SkMengajar::query()
            ->with(['mataKuliah', 'dosen'])
            ->whereIn('id', $sub->pluck('id')->all())
            ->when($q !== '', function ($s) use ($q) {
                $s->where(function ($sub) use ($q) {
                    $sub->where('program_studi', 'like', "%{$q}%")
                        ->orWhere('nomor_sk', 'like', "%{$q}%")
                        ->orWhereHas('mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
                });
            });

        $rows = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('admin.sk-mengajar.index', compact('rows', 'prodies', 'q', 'prodi'));
    }

    public function create(Request $request): View
    {
        $prodies = $this->listProdies();
        $default = (object) [
            'id' => null,
            'program_studi' => (string) $request->get('prodi', ''),
            'mata_kuliah_id' => 0,
            'dosen_id' => 0,
            'semester' => 1,
            'tahun_ajaran' => '',
            'nomor_sk' => '',
            'tanggal_sk' => date('Y-m-d'),
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'beban_sks' => 0,
            'kelas' => '',
            'jabatan_dosen' => '',
            'tugas_tambahan' => '',
            'catatan' => '',
            'file_pdf' => null,
        ];
        view()->share(compact('prodies'));
        return view('admin.sk-mengajar.form', ['row' => $default, 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'prodi' => 'required|string|max:120',
            'file_pdf_upload' => 'required|file|mimes:pdf|max:10240',
        ]);
        $prodi = trim((string) $validated['prodi']);
        $prodiesAllowed = $this->listProdies();
        if (!in_array($prodi, $prodiesAllowed, true)) {
            return redirect()->back()->withInput()->withErrors(['prodi' => 'Prodi tidak valid. Pilih dari daftar yang tersedia.']);
        }

        $existing = SkMengajar::query()->where('program_studi', $prodi)->orderByDesc('id')->first();
        if ($existing) {
            return redirect()->back()->withInput()->withErrors([
                'prodi' => 'SK Mengajar untuk <strong>'.$prodi.'</strong> SUDAH ADA. Silakan <a class="underline text-emerald-300" href="'.route('admin.sk-mengajar.edit', $existing).'">EDIT DATA YANG SUDAH ADA</a> untuk mengganti file upload (jangan CREATE baru).',
            ]);
        }

        $auto = $this->autoFillForProdi($prodi);
        $auto['program_studi'] = $prodi;
        $auto['created_by'] = Auth::id();

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $auto['file_pdf'] = $this->storePdf($request, 'sk-mengajar', 'file_pdf_upload');
        }

        try {
            SkMengajar::create($auto);
        } catch (QueryException $e) {
            if (!empty($auto['file_pdf'])) {
                $this->deletePdf($auto['file_pdf']);
            }
            $duplicate = ((string) $e->getCode() === '23000');
            if (!$duplicate && isset($e->errorInfo[1])) {
                $duplicate = ((int) $e->errorInfo[1] === 1062);
            }
            if ($duplicate) {
                $existing2 = SkMengajar::query()->where('program_studi', $prodi)->orderByDesc('id')->first();
                $link = $existing2 ? route('admin.sk-mengajar.edit', $existing2) : route('admin.sk-mengajar.index');
                return redirect()->back()->withInput()->withErrors([
                    'prodi' => 'SK Mengajar untuk <strong>'.$prodi.'</strong> SUDAH ADA (unique index). Silakan <a class="underline text-emerald-300" href="'.$link.'">EDIT DATA YANG SUDAH ADA</a> untuk mengganti file upload.',
                ]);
            }
            throw $e;
        }

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar untuk <strong>'.$prodi.'</strong> berhasil ditambahkan. Semua dosen di prodi ini sekarang bisa mendownload file PDF tersebut.');
    }

    public function edit(SkMengajar $skMengajar): View
    {
        $prodies = $this->listProdies();
        view()->share(compact('prodies'));
        return view('admin.sk-mengajar.form', ['row' => $skMengajar, 'isEdit' => true]);
    }

    public function update(Request $request, SkMengajar $skMengajar): RedirectResponse
    {
        $validated = $request->validate([
            'prodi' => 'required|string|max:120',
            'file_pdf_upload' => 'nullable|file|mimes:pdf|max:10240',
            'hapus_file_pdf' => 'nullable|boolean',
        ]);
        $prodi = trim((string) $validated['prodi']);
        $prodiesAllowed = $this->listProdies();
        if (!in_array($prodi, $prodiesAllowed, true)) {
            return redirect()->back()->withInput()->withErrors(['prodi' => 'Prodi tidak valid. Pilih dari daftar yang tersedia.']);
        }

        if ($prodi !== (string) $skMengajar->program_studi) {
            $existing = SkMengajar::query()->where('program_studi', $prodi)->where('id', '!=', $skMengajar->id)->orderByDesc('id')->first();
            if ($existing) {
                return redirect()->back()->withInput()->withErrors([
                    'prodi' => 'SK Mengajar untuk <strong>'.$prodi.'</strong> SUDAH ADA di record lain. Silakan EDIT record yang sudah ada.',
                ]);
            }
        }

        $payload = ['program_studi' => $prodi];
        if ($prodi !== (string) $skMengajar->program_studi) {
            $payload = array_merge($payload, $this->autoFillForProdi($prodi));
        }

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $oldPath = (string) $skMengajar->file_pdf;
            $payload['file_pdf'] = $this->storePdf($request, 'sk-mengajar', 'file_pdf_upload');
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
        } elseif (filter_var($request->get('hapus_file_pdf'), FILTER_VALIDATE_BOOLEAN)) {
            $oldPath = (string) $skMengajar->file_pdf;
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
            $payload['file_pdf'] = null;
        }

        try {
            $skMengajar->update($payload);
        } catch (QueryException $e) {
            $duplicate = ((string) $e->getCode() === '23000');
            if (!$duplicate && isset($e->errorInfo[1])) {
                $duplicate = ((int) $e->errorInfo[1] === 1062);
            }
            if ($duplicate) {
                return redirect()->back()->withInput()->withErrors([
                    'prodi' => 'Gagal update: Kombinasi record untuk prodi <strong>'.$prodi.'</strong> SUDAH ADA di record lain. Silakan coba lagi.',
                ]);
            }
            throw $e;
        }

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar untuk <strong>'.$prodi.'</strong> berhasil diperbarui.');
    }

    public function destroy(SkMengajar $skMengajar): RedirectResponse
    {
        $prodi = (string) $skMengajar->program_studi;
        $oldPath = (string) $skMengajar->file_pdf;
        if ($oldPath !== '' && $oldPath !== '0') {
            $this->deletePdf($oldPath);
        }
        $skMengajar->delete();

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar untuk <strong>'.$prodi.'</strong> berhasil dihapus.');
    }

    private function listProdies(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        $rows = MataKuliah::query()->distinct()->orderBy('jurusan')->pluck('jurusan');
        $cache = array_values(array_filter(array_map('strval', $rows->all()), fn ($v) => trim($v) !== ''));
        return $cache;
    }

    private function autoFillForProdi(string $prodi): array
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
}
