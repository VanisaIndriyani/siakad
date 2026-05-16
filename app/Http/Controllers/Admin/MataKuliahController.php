<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function index(Request $request): View
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

        $mataKuliah = $query->orderBy('kode')->paginate(10)->withQueryString();

        return view('admin.mata-kuliah.index', [
            'jurusanList' => self::JURUSAN,
            'mataKuliah' => $mataKuliah,
            'q' => $q,
            'jurusan' => $jurusan,
            'semester' => $semester ?: null,
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

    public function downloadRpsDosen(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        abort_unless($mataKuliah->rps_dosen_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_dosen_path), 404);

        $downloadName = $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path);
        return response()->download(storage_path('app/public/'.$mataKuliah->rps_dosen_path), $downloadName);
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
}
