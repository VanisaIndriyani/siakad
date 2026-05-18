<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KhsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = trim((string) $request->get('semester', ''));

        $query = Khs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($semester !== '') {
            $query->where('semester', $semester);
        }

        $khs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.khs.index', [
            'khs' => $khs,
            'q' => $q,
            'semester' => $semester,
        ]);
    }

    public function show(Khs $khs): View
    {
        $khs->load(['mahasiswa', 'mahasiswa.user', 'items.mataKuliah']);

        return view('admin.khs.show', [
            'khs' => $khs,
        ]);
    }

    public function create(): View
    {
        return view('admin.khs.create', [
            'mahasiswa' => Mahasiswa::query()->orderBy('nama_lengkap')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
        ]);

        $khs = Khs::query()->firstOrCreate(
            [
                'mahasiswa_id' => $validated['mahasiswa_id'],
                'semester' => $validated['semester'],
            ],
            [
                'tahun_ajaran' => $validated['tahun_ajaran'] ?? null,
            ]
        );

        return redirect()->route('admin.khs.edit', $khs)->with('success', 'KHS berhasil dibuat. Silakan input nilai.');
    }

    public function edit(Khs $khs): View
    {
        $khs->load(['mahasiswa', 'items.mataKuliah']);

        return view('admin.khs.edit', [
            'khs' => $khs,
            'mataKuliah' => MataKuliah::query()->orderBy('semester')->orderBy('kode')->get(),
        ]);
    }

    public function update(Request $request, Khs $khs): RedirectResponse
    {
        $validated = $request->validate([
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'ips' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'ipk' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'mata_kuliah_id' => ['nullable', 'array'],
            'mata_kuliah_id.*' => ['integer', 'exists:mata_kuliah,id'],
            'nilai_angka' => ['nullable', 'array'],
            'nilai_huruf' => ['nullable', 'array'],
        ]);

        $khs->update([
            'tahun_ajaran' => $validated['tahun_ajaran'] ?? $khs->tahun_ajaran,
            'ips' => $validated['ips'] ?? $khs->ips,
            'ipk' => $validated['ipk'] ?? $khs->ipk,
        ]);

        $mkIds = $validated['mata_kuliah_id'] ?? [];
        foreach ($mkIds as $mkId) {
            $angka = $validated['nilai_angka'][$mkId] ?? null;
            $huruf = $validated['nilai_huruf'][$mkId] ?? null;

            KhsItem::query()->updateOrCreate(
                [
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => $mkId,
                ],
                [
                    'nilai_angka' => $angka !== '' ? $angka : null,
                    'nilai_huruf' => $huruf !== '' ? $huruf : null,
                ]
            );
        }

        return redirect()->route('admin.khs.show', $khs)->with('success', 'KHS berhasil diperbarui.');
    }

    public function destroy(Khs $khs): RedirectResponse
    {
        $khs->delete();
        return back()->with('success', 'Data KHS berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:khs,id'],
        ]);

        Khs::query()->whereIn('id', $validated['ids'])->delete();

        return back()->with('success', 'Data KHS terpilih berhasil dihapus.');
    }
}
