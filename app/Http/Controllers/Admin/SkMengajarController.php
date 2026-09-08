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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SkMengajarController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $dosenId = (int) $request->get('dosen_id', 0);

        $query = SkMengajar::query()
            ->with(['mataKuliah', 'dosen'])
            ->when($semester >= 1 && $semester <= 8, fn ($s) => $s->where('semester', $semester))
            ->when($dosenId > 0, fn ($s) => $s->where('dosen_id', $dosenId))
            ->when($q !== '', function ($s) use ($q) {
                $s->where(function ($sub) use ($q) {
                    $sub->where('nomor_sk', 'like', "%{$q}%")
                        ->orWhereHas('mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
                        ->orWhereHas('dosen', fn ($d) => $d->where('nama', 'like', "%{$q}%"));
                });
            });

        $rows = $query->orderByDesc('tanggal_sk')->orderByDesc('id')->paginate(12)->withQueryString();
        $dosens = Dosen::query()->orderBy('nama')->get(['id', 'nama', 'nidn', 'nuptk']);

        return view('admin.sk-mengajar.index', compact('rows', 'dosens', 'q', 'semester', 'dosenId'));
    }

    public function create(Request $request): View
    {
        $this->shareFormLookups();
        $default = (object) [
            'mata_kuliah_id' => (int) $request->get('mata_kuliah_id'),
            'dosen_id' => (int) $request->get('dosen_id'),
            'semester' => (int) $request->get('semester', 1),
            'tahun_ajaran' => $request->get('tahun_ajaran', ''),
            'nomor_sk' => '',
            'tanggal_sk' => date('Y-m-d'),
            'tanggal_mulai' => null,
            'tanggal_selesai' => null,
            'beban_sks' => null,
            'kelas' => '',
            'program_studi' => '',
            'jabatan_dosen' => '',
            'tugas_tambahan' => '',
            'catatan' => '',
            'file_pdf' => null,
        ];

        return view('admin.sk-mengajar.form', ['row' => $default, 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $validated = $this->applyAutoFillFromDosen($validated, $request);
        $validated = $this->prepareDefaults($validated, $request);
        $validated['created_by'] = Auth::id();

        if (!$this->validateHasMataKuliah($validated, $request)) {
            return redirect()->back()->withInput()->withErrors([
                'dosen_id' => 'Dosen ini BELUM mengampu mata kuliah manapun. Silakan atur dosen sebagai pengampu di menu Mata Kuliah terlebih dahulu (pada kolom Dosen 1 atau Dosen 2).',
            ]);
        }

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $validated['file_pdf'] = $this->storePdf($request, 'sk-mengajar', 'file_pdf_upload');
        }

        try {
            SkMengajar::create($validated);
        } catch (QueryException $e) {
            if (isset($validated['file_pdf'] ?? null)) {
                $this->deletePdf($validated['file_pdf']);
            }
            if ($e->getCode() === '23000' || (is_int($e->errorInfo[1] ?? null) && (int) $e->errorInfo[1] === 1062) {
                $mkCode = '';
                if (!empty($validated['mata_kuliah_id'])) {
                    $mk = MataKuliah::query()->find($validated['mata_kuliah_id'], ['kode']);
                    if ($mk) {
                        $mkCode = $mk->kode;
                    }
                }
                $msg = 'SK Mengajar untuk kombinasi ini SUDAH ADA sebelumnya (Mata Kuliah '
                    .($mkCode !== '' ? $mkCode.' ' : '')
                    .'• Dosen ID '.$validated['dosen_id']
                    .' • Semester '.$validated['semester']
                    .' • TA '.($validated['tahun_ajaran'] ?? '').').' Silakan <strong>EDIT</strong> data SK Mengajar yang sudah ada di halaman Index, jangan buat baru (CREATE) ulang!';
                return redirect()->back()->withInput()->withErrors([
                    'dosen_id' => $msg,
                ]);
            }
            throw $e;
        }

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar berhasil ditambahkan.');
    }

    public function edit(SkMengajar $skMengajar): View
    {
        $this->shareFormLookups();

        return view('admin.sk-mengajar.form', ['row' => $skMengajar, 'isEdit' => true]);
    }

    public function update(Request $request, SkMengajar $skMengajar): RedirectResponse
    {
        $validated = $this->validateForm($request, $skMengajar->id);
        $validated = $this->applyAutoFillFromDosen($validated, $request, $skMengajar);
        $validated = $this->prepareDefaults($validated, $request);

        if (!$this->validateHasMataKuliah($validated, $request)) {
            return redirect()->back()->withInput()->withErrors([
                'dosen_id' => 'Dosen ini BELUM mengampu mata kuliah manapun. Silakan atur dosen sebagai pengampu di menu Mata Kuliah terlebih dahulu (pada kolom Dosen 1 atau Dosen 2).',
            ]);
        }

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $oldPath = (string) $skMengajar->file_pdf;
            $validated['file_pdf'] = $this->storePdf($request, 'sk-mengajar', 'file_pdf_upload');
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
        } elseif (filter_var($request->get('hapus_file_pdf'), FILTER_VALIDATE_BOOLEAN)) {
            $oldPath = (string) $skMengajar->file_pdf;
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
            $validated['file_pdf'] = null;
        }

        try {
            $skMengajar->update($validated);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000' || (is_int($e->errorInfo[1] ?? null) && (int) $e->errorInfo[1] === 1062) {
                $mkCode = '';
                if (!empty($validated['mata_kuliah_id'])) {
                    $mk = MataKuliah::query()->find($validated['mata_kuliah_id'], ['kode']);
                    if ($mk) {
                        $mkCode = $mk->kode;
                    }
                }
                $msg = 'Gagal update: Kombinasi Mata Kuliah '
                    .($mkCode !== '' ? $mkCode.' ' : '')
                    .'• Dosen ID '.($validated['dosen_id'] ?? $skMengajar->dosen_id)
                    .' • Semester '.($validated['semester'] ?? $skMengajar->semester)
                    .' • TA '.($validated['tahun_ajaran'] ?? $skMengajar->tahun_ajaran ?? '')
                    .' SUDAH ADA di record lain. Silakan pilih dosen / edit data yang sudah ada.';
                return redirect()->back()->withInput()->withErrors([
                    'dosen_id' => $msg,
                ]);
            }
            throw $e;
        }

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar berhasil diperbarui.');
    }

    public function destroy(SkMengajar $skMengajar): RedirectResponse
    {
        $oldPath = (string) $skMengajar->file_pdf;
        if ($oldPath !== '' && $oldPath !== '0') {
            $this->deletePdf($oldPath);
        }
        $skMengajar->delete();

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar berhasil dihapus.');
    }

    private function applyAutoFillFromDosen(array $payload, Request $request, ?SkMengajar $existing = null): array
    {
        $dosenId = (int) ($payload['dosen_id'] ?? ($existing?->dosen_id ?? 0));
        if ($dosenId <= 0) {
            return $payload;
        }

        $auto = $this->resolveAutoFillFromDosen($dosenId);
        if ($auto === null) {
            return $payload;
        }

        $fieldShouldAuto = [
            'mata_kuliah_id',
            'semester',
            'tahun_ajaran',
            'nomor_sk',
            'tanggal_sk',
            'tanggal_mulai',
            'tanggal_selesai',
            'beban_sks',
            'kelas',
            'program_studi',
            'jabatan_dosen',
            'tugas_tambahan',
            'catatan',
        ];

        foreach ($fieldShouldAuto as $key) {
            $userProvided = array_key_exists($key, $payload)
                && $payload[$key] !== null
                && (is_array($payload[$key]) || trim((string) $payload[$key]) !== '');
            if (!$userProvided) {
                $payload[$key] = $auto[$key];
            }
        }

        return $payload;
    }

    private function resolveAutoFillFromDosen(int $dosenId): ?array
    {
        $dosen = Dosen::query()->find($dosenId, ['id', 'nama', 'nidn', 'nuptk', 'jabatan_struktural']);
        if (!$dosen) {
            return null;
        }

        $mk = MataKuliah::query()
            ->where(function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId)
                    ->orWhere('dosen_id_2', $dosenId);
            })
            ->orderBy('semester')
            ->orderBy('kode')
            ->first(['id', 'kode', 'nama', 'semester', 'sks', 'jurusan']);

        if (!$mk) {
            return null;
        }

        $y = (int) date('Y');
        $ta = $y.'/'.($y + 1);
        $tgl = date('Y-m-d');
        $tglSelesai = date('Y-m-d', strtotime('+4 months'));
        $nomor = 'IADD-SK/'.date('Y').'/MK-'.sprintf('%03d', $mk->id).'-D'.sprintf('%03d', $dosenId);

        return [
            'mata_kuliah_id' => (int) $mk->id,
            'semester' => (int) ($mk->semester ?? 1),
            'tahun_ajaran' => $ta,
            'nomor_sk' => $nomor,
            'tanggal_sk' => $tgl,
            'tanggal_mulai' => $tgl,
            'tanggal_selesai' => $tglSelesai,
            'beban_sks' => (float) ($mk->sks ?? 0),
            'kelas' => 'A',
            'program_studi' => (string) ($mk->jurusan ?? ''),
            'jabatan_dosen' => (string) ($dosen->jabatan_struktural ?? 'Dosen'),
            'tugas_tambahan' => '',
            'catatan' => '',
        ];
    }

    private function validateHasMataKuliah(array $payload, Request $request): bool
    {
        $mkId = (int) ($payload['mata_kuliah_id'] ?? 0);
        if ($mkId >= 1) {
            $exists = MataKuliah::query()->where('id', $mkId)->exists();
            if ($exists) {
                return true;
            }
        }

        $dosenId = (int) ($payload['dosen_id'] ?? 0);
        if ($dosenId >= 1) {
            $mk = MataKuliah::query()
                ->where(function ($q) use ($dosenId) {
                    $q->where('dosen_id', $dosenId)->orWhere('dosen_id_2', $dosenId);
                })
                ->first(['id']);
            if ($mk) {
                return true;
            }
        }
        return false;
    }

    private function prepareDefaults(array $payload, Request $request): array
    {
        $mkId = (int) ($payload['mata_kuliah_id'] ?? 0);
        $fallbackSks = 0;
        $fallbackProdi = '';
        if ($mkId > 0) {
            $mk = MataKuliah::query()->find($mkId, ['id', 'sks', 'jurusan']);
            if ($mk) {
                $fallbackSks = (float) ($mk->sks ?? 0);
                $fallbackProdi = (string) ($mk->jurusan ?? '');
            }
        }

        $payload['beban_sks'] = isset($payload['beban_sks']) && $payload['beban_sks'] !== null && trim((string) $payload['beban_sks']) !== ''
            ? (float) $payload['beban_sks']
            : $fallbackSks;

        if (!isset($payload['program_studi']) || trim((string) $payload['program_studi']) === '') {
            $payload['program_studi'] = $fallbackProdi;
        }
        foreach (['kelas', 'jabatan_dosen', 'tugas_tambahan', 'catatan', 'tahun_ajaran', 'nomor_sk'] as $f) {
            if (!isset($payload[$f]) || $payload[$f] === null) {
                $payload[$f] = '';
            }
        }
        if (empty($payload['semester']) || (int) $payload['semester'] < 1 || (int) $payload['semester'] > 8) {
            $payload['semester'] = 1;
        } else {
            $payload['semester'] = (int) $payload['semester'];
        }
        if (empty($payload['mata_kuliah_id']) || (int) $payload['mata_kuliah_id'] < 1) {
            $payload['mata_kuliah_id'] = max(0, $mkId);
        } else {
            $payload['mata_kuliah_id'] = (int) $payload['mata_kuliah_id'];
        }
        return $payload;
    }

    private function storePdf(Request $request, string $folder, string $inputName): string
    {
        $file = $request->file($inputName);
        $ext = strtolower((string) $file->getClientOriginalExtension());
        if ($ext === '') {
            $ext = 'pdf';
        }
        $orig = (string) $file->getClientOriginalName();
        $orig = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        $ts = date('Ymd_His');
        $filename = $ts.'_'.$orig;
        $path = $file->storeAs('dokumen-kuliah/'.$folder, $filename, 'public');
        return $path;
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

    private function validateForm(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'dosen_id' => 'required|exists:dosen,id',
            'mata_kuliah_id' => 'nullable|exists:mata_kuliah,id',
            'semester' => 'nullable|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'nomor_sk' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('sk_mengajars', 'nomor_sk')
                    ->where(fn ($q) => $q->when($id, fn ($sq) => $sq->where('id', '!=', $id))),
            ],
            'tanggal_sk' => 'nullable|date',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'beban_sks' => 'nullable|numeric|min:0|max:30',
            'kelas' => 'nullable|string|max:20',
            'program_studi' => 'nullable|string|max:120',
            'jabatan_dosen' => 'nullable|string|max:120',
            'tugas_tambahan' => 'nullable|string|max:500',
            'catatan' => 'nullable|string|max:500',
            'file_pdf_upload' => 'nullable|file|mimes:pdf|max:10240',
            'hapus_file_pdf' => 'nullable|boolean',
        ]);
    }

    private function shareFormLookups(): void
    {
        view()->share([
            'mataKuliahs' => MataKuliah::query()->orderBy('kode')->get(['id', 'kode', 'nama', 'jurusan', 'semester', 'sks']),
            'dosens' => Dosen::query()->orderBy('nama')->get(['id', 'nama', 'nidn', 'nuptk', 'jabatan_struktural']),
        ]);
    }
}
