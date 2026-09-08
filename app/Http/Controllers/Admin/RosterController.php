<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\MataKuliah;
use App\Models\Roster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RosterController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $mataKuliahId = (int) $request->get('mata_kuliah_id', 0);
        $dosenId = (int) $request->get('dosen_id', 0);

        $query = Roster::query()
            ->with(['mataKuliah', 'dosen'])
            ->when($semester >= 1 && $semester <= 8, fn ($s) => $s->where('semester', $semester))
            ->when($mataKuliahId > 0, fn ($s) => $s->where('mata_kuliah_id', $mataKuliahId))
            ->when($dosenId > 0, fn ($s) => $s->where('dosen_id', $dosenId))
            ->when($q !== '', function ($s) use ($q) {
                $s->where(function ($sub) use ($q) {
                    $sub->where('materi', 'like', "%{$q}%")
                        ->orWhere('keterangan', 'like', "%{$q}%")
                        ->orWhere('ruang', 'like', "%{$q}%")
                        ->orWhereHas('mataKuliah', fn ($m) => $m->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%"));
                });
            });

        $rows = $query->orderBy('mata_kuliah_id')->orderBy('semester')->orderBy('pertemuan_ke')->paginate(15)->withQueryString();

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
            'tahun_ajaran' => $request->get('tahun_ajaran', ''),
            'pertemuan_ke' => (int) $request->get('pertemuan_ke', 1),
            'hari' => '',
            'jam_mulai' => null,
            'jam_selesai' => null,
            'ruang' => '',
            'tanggal' => null,
            'materi' => '',
            'keterangan' => '',
            'metode_pembelajaran' => '',
        ];

        return view('admin.roster.form', ['row' => $default, 'isEdit' => false]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $validated['created_by'] = Auth::id();
        Roster::create($validated);

        return to_route('admin.roster.index')
            ->with('success', 'Roster pertemuan berhasil ditambahkan.');
    }

    public function edit(Roster $roster): View
    {
        $this->shareFormLookups();

        return view('admin.roster.form', ['row' => $roster, 'isEdit' => true]);
    }

    public function update(Request $request, Roster $roster): RedirectResponse
    {
        $validated = $this->validateForm($request);
        $roster->update($validated);

        return to_route('admin.roster.index')
            ->with('success', 'Roster pertemuan berhasil diperbarui.');
    }

    public function destroy(Roster $roster): RedirectResponse
    {
        $roster->delete();

        return to_route('admin.roster.index')
            ->with('success', 'Roster pertemuan berhasil dihapus.');
    }

    private function validateForm(Request $request): array
    {
        return $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliahs,id',
            'dosen_id' => 'nullable|exists:dosens,id',
            'semester' => 'required|integer|min:1|max:8',
            'tahun_ajaran' => 'nullable|string|max:20',
            'pertemuan_ke' => 'required|integer|min:1|max:30',
            'hari' => 'nullable|string|max:20',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'ruang' => 'nullable|string|max:60',
            'tanggal' => 'nullable|date',
            'materi' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string|max:500',
            'metode_pembelajaran' => 'nullable|string|max:60',
        ]);
    }

    private function shareFormLookups(): void
    {
        view()->share([
            'mataKuliahs' => MataKuliah::query()->orderBy('kode')->get(['id', 'kode', 'nama', 'jurusan', 'semester', 'sks']),
            'dosens' => Dosen::query()->orderBy('nama')->get(['id', 'nama', 'nidn', 'nuptk']),
            'listHari' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            'listMetode' => ['Tatap Muka', 'Daring (Online)', 'Hybrid', 'Praktek Lapangan', 'Mandiri'],
        ]);
    }
}
