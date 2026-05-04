<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\KrsItem;
use App\Models\MataKuliah;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KrsController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $krs = Krs::query()
            ->withCount('items')
            ->where('mahasiswa_id', $mahasiswa?->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('mahasiswa.krs.index', [
            'krs' => $krs,
        ]);
    }

    public function create(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $semester = (int) ($request->get('semester', 1) ?: 1);
        $semester = max(1, min(8, $semester));

        $mataKuliah = MataKuliah::query()
            ->with('dosen')
            ->where('semester', $semester)
            ->when($mahasiswa?->program_studi, function ($q) use ($mahasiswa) {
                $q->where('jurusan', $mahasiswa->program_studi);
            })
            ->orderBy('kode')
            ->get();

        return view('mahasiswa.krs.create', [
            'semester' => $semester,
            'mataKuliah' => $mataKuliah,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa belum tersedia.');
        }

        $validated = $request->validate([
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'mata_kuliah_id' => ['required', 'array', 'min:1'],
            'mata_kuliah_id.*' => [
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $request->input('semester'))
                    ->where('jurusan', $mahasiswa->program_studi),
            ],
        ]);

        $krs = Krs::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'semester' => $validated['semester'],
            'tahun_ajaran' => $validated['tahun_ajaran'] ?? null,
            'status_approval' => 'pending',
        ]);

        foreach ($validated['mata_kuliah_id'] as $mkId) {
            KrsItem::query()->create([
                'krs_id' => $krs->id,
                'mata_kuliah_id' => $mkId,
            ]);
        }

        return redirect()->route('mahasiswa.krs.show', $krs)->with('success', 'KRS berhasil dibuat dan menunggu approval.');
    }

    public function show(Request $request, Krs $krs): View
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($user->mahasiswa && $krs->mahasiswa_id === $user->mahasiswa->id, 403);

        $krs->load(['items.mataKuliah']);

        return view('mahasiswa.krs.show', [
            'krs' => $krs,
        ]);
    }

    public function edit(Request $request, Krs $krs): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->mahasiswa && $krs->mahasiswa_id === $user->mahasiswa->id, 403);

        if ($krs->status_approval === 'approved') {
            return redirect()->route('mahasiswa.krs.show', $krs)->with('error', 'KRS sudah disetujui dan tidak dapat diubah.');
        }

        $semester = (int) $krs->semester;
        $mataKuliah = MataKuliah::query()
            ->where('semester', $semester)
            ->where('jurusan', $user->mahasiswa->program_studi)
            ->orderBy('kode')
            ->get();
        $selected = $krs->items()->pluck('mata_kuliah_id')->all();

        return view('mahasiswa.krs.edit', [
            'krs' => $krs,
            'mataKuliah' => $mataKuliah,
            'selected' => $selected,
        ]);
    }

    public function update(Request $request, Krs $krs): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->mahasiswa && $krs->mahasiswa_id === $user->mahasiswa->id, 403);

        if ($krs->status_approval === 'approved') {
            return redirect()->route('mahasiswa.krs.show', $krs)->with('error', 'KRS sudah disetujui dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'mata_kuliah_id' => ['required', 'array', 'min:1'],
            'mata_kuliah_id.*' => [
                'integer',
                Rule::exists('mata_kuliah', 'id')
                    ->where('semester', (int) $krs->semester)
                    ->where('jurusan', $user->mahasiswa->program_studi),
            ],
        ]);

        $krs->update([
            'tahun_ajaran' => $validated['tahun_ajaran'] ?? $krs->tahun_ajaran,
            'status_approval' => 'pending',
        ]);

        $existing = $krs->items()->pluck('mata_kuliah_id')->all();
        $incoming = $validated['mata_kuliah_id'];

        $toDelete = array_diff($existing, $incoming);
        if (count($toDelete) > 0) {
            KrsItem::query()->where('krs_id', $krs->id)->whereIn('mata_kuliah_id', $toDelete)->delete();
        }

        foreach ($incoming as $mkId) {
            KrsItem::query()->firstOrCreate([
                'krs_id' => $krs->id,
                'mata_kuliah_id' => $mkId,
            ]);
        }

        return redirect()->route('mahasiswa.krs.show', $krs)->with('success', 'KRS berhasil diperbarui dan menunggu approval.');
    }
}
