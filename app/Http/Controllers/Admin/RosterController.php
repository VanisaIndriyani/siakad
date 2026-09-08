<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\RosterUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RosterController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $mataKuliahId = (int) $request->get('mata_kuliah_id', 0);
        $dosenId = (int) $request->get('dosen_id', 0);

        $query = RosterUpload::query()
            ->with(['mataKuliah', 'dosen'])
            ->when($semester >= 1 && $semester <= 8, fn ($s) => $s->where('semester', $semester))
            ->when($mataKuliahId > 0, fn ($s) => $s->where('mata_kuliah_id', $mataKuliahId))
            ->when($dosenId > 0, fn ($s) => $s->where('dosen_id', $dosenId))
            ->when($q !== '', function ($s) use ($q) {
                $s->where(function ($sub) use ($q) {
                    $sub->where('kelas', 'like', "%{$q}%")
                        ->orWhere('keterangan', 'like', "%{$q}%")
                        ->orWhereHas('mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"))
                        ->orWhereHas('dosen', fn ($d) => $d->where('nama', 'like', "%{$q}%"));
                });
            });

        $rows = $query->orderBy('mata_kuliah_id')->orderBy('semester')->orderByDesc('id')->paginate(15)->withQueryString();

        view()->share([
            'mataKuliahs' => MataKuliah::query()->orderBy('kode')->get(['id', 'kode', 'nama', 'jurusan']),
            'dosens' => Dosen::query()->orderBy('nama')->get(['id', 'nama']),
        ]);

        return view('admin.roster.index', compact('rows', 'q', 'semester', 'mataKuliahId', 'dosenId'));
    }

    public function create(Request $request): View
    {
        $this->shareFormLookups();
        $default = (object) [
            'mata_kuliah_id' => (int) $request->get('mata_kuliah_id'),
            'dosen_id' => (int) $request->get('dosen_id'),
            'semester' => (int) $request->get('semester', 1),
            'tahun_ajaran' => (string) $request->get('tahun_ajaran', ''),
            'kelas' => '',
            'keterangan' => '',
            'file_pdf' => null,
        ];
        return view('admin.roster.form', ['row' => $default, 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $validated = $this->prepareDefaults($validated);
        $validated['created_by'] = Auth::id();

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $validated['file_pdf'] = $this->storePdf($request, 'file_pdf_upload');
        }

        RosterUpload::create($validated);

        return to_route('admin.roster.index')
            ->with('success', 'Roster Kuliah berhasil ditambahkan.');
    }

    public function edit(RosterUpload $rosterUpload): View
    {
        $this->shareFormLookups();
        return view('admin.roster.form', ['row' => $rosterUpload, 'isEdit' => true]);
    }

    public function update(Request $request, RosterUpload $rosterUpload): RedirectResponse
    {
        $validated = $this->validateForm($request, $rosterUpload->id);
        $validated = $this->prepareDefaults($validated);

        if ($request->hasFile('file_pdf_upload') && $request->file('file_pdf_upload')->isValid()) {
            $oldPath = (string) $rosterUpload->file_pdf;
            $validated['file_pdf'] = $this->storePdf($request, 'file_pdf_upload');
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
        } elseif (filter_var($request->get('hapus_file_pdf'), FILTER_VALIDATE_BOOLEAN)) {
            $oldPath = (string) $rosterUpload->file_pdf;
            if ($oldPath !== '' && $oldPath !== '0') {
                $this->deletePdf($oldPath);
            }
            $validated['file_pdf'] = null;
        }

        $rosterUpload->update($validated);

        return to_route('admin.roster.index')
            ->with('success', 'Roster Kuliah berhasil diperbarui.');
    }

    public function destroy(RosterUpload $rosterUpload): RedirectResponse
    {
        $oldPath = (string) $rosterUpload->file_pdf;
        if ($oldPath !== '' && $oldPath !== '0') {
            $this->deletePdf($oldPath);
        }
        $rosterUpload->delete();

        return to_route('admin.roster.index')
            ->with('success', 'Roster Kuliah berhasil dihapus.');
    }

    private function prepareDefaults(array $payload): array
    {
        foreach (['tahun_ajaran', 'kelas', 'keterangan'] as $f) {
            if (!isset($payload[$f]) || $payload[$f] === null) {
                $payload[$f] = '';
            }
        }
        return $payload;
    }

    private function storePdf(Request $request, string $inputName): string
    {
        $file = $request->file($inputName);
        $orig = (string) $file->getClientOriginalName();
        $orig = preg_replace('/[^A-Za-z0-9._-]/', '_', $orig);
        $ts = date('Ymd_His');
        $filename = $ts.'_'.$orig;
        return $file->storeAs('dokumen-kuliah/roster', $filename, 'public');
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
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'semester' => 'required|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'kelas' => 'nullable|string|max:20',
            'keterangan' => 'nullable|string|max:255',
            'file_pdf_upload' => $id ? 'nullable|file|mimes:pdf|max:10240' : 'required|file|mimes:pdf|max:10240',
            'hapus_file_pdf' => 'nullable|boolean',
        ]);
    }

    private function shareFormLookups(): void
    {
        view()->share([
            'mataKuliahs' => MataKuliah::query()->orderBy('kode')->get(['id', 'kode', 'nama', 'jurusan', 'semester', 'sks']),
            'dosens' => Dosen::query()->orderBy('nama')->get(['id', 'nama', 'nidn', 'nuptk']),
        ]);
    }
}
