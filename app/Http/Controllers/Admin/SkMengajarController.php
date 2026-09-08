<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\SkMengajar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ];

        return view('admin.sk-mengajar.form', ['row' => $default, 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $validated['created_by'] = Auth::id();
        SkMengajar::create($validated);

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
        $skMengajar->update($validated);

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar berhasil diperbarui.');
    }

    public function destroy(SkMengajar $skMengajar): RedirectResponse
    {
        $skMengajar->delete();

        return to_route('admin.sk-mengajar.index')
            ->with('success', 'SK Mengajar berhasil dihapus.');
    }

    private function validateForm(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'nullable|exists:dosen,id',
            'semester' => 'required|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'nomor_sk' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('sk_mengajars', 'nomor_sk')
                    ->where(fn ($q) => $q->where('mata_kuliah_id', (int) $request->get('mata_kuliah_id'))
                        ->where('semester', (int) $request->get('semester'))
                        ->when($id, fn ($sq) => $sq->where('id', '!=', $id))
                    ),
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
