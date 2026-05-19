<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\AbsensiItem;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\KrsItem;
use App\Models\MataKuliah;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class KrsController extends Controller
{
    private function resolveProdiSigners(?string $programStudi): array
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return ['kaprodi' => null, 'sekprodi' => null];
        }

        $kaprodi = Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();

        $sekprodi = Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Sekretaris Prodi')
            ->orderByDesc('id')
            ->first();

        return [
            'kaprodi' => $kaprodi?->nama,
            'sekprodi' => $sekprodi?->nama,
        ];
    }

    private function ensureAbsensiForMahasiswa(int $mahasiswaId, string $jurusan, int $semester, array $mataKuliahIds): void
    {
        $mataKuliahIds = array_values(array_unique(array_map('intval', $mataKuliahIds)));
        if (count($mataKuliahIds) === 0) {
            return;
        }

        foreach ($mataKuliahIds as $mkId) {
            foreach (range(1, 16) as $pertemuan) {
                $absensi = Absensi::query()->firstOrCreate(
                    [
                        'jurusan' => $jurusan,
                        'semester' => $semester,
                        'mata_kuliah_id' => $mkId,
                        'pertemuan' => $pertemuan,
                    ],
                    [
                        'created_by_user_id' => null,
                    ]
                );

                AbsensiItem::query()->firstOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'mahasiswa_id' => $mahasiswaId,
                    ],
                    [
                        'status' => null,
                    ]
                );
            }
        }
    }

    private function removeAbsensiForMahasiswa(int $mahasiswaId, string $jurusan, int $semester, array $mataKuliahIds): void
    {
        $mataKuliahIds = array_values(array_unique(array_map('intval', $mataKuliahIds)));
        if (count($mataKuliahIds) === 0) {
            return;
        }

        AbsensiItem::query()
            ->where('mahasiswa_id', $mahasiswaId)
            ->whereHas('absensi', function ($q) use ($jurusan, $semester, $mataKuliahIds) {
                $q->where('jurusan', $jurusan)
                    ->where('semester', $semester)
                    ->whereIn('mata_kuliah_id', $mataKuliahIds);
            })
            ->delete();
    }

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $krs = Krs::query()
            ->with(['items.mataKuliah'])
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

        $tahunAjaran = trim((string) ($validated['tahun_ajaran'] ?? ''));
        if ($tahunAjaran === '') {
            $tahunAjaran = null;
        }

        $krs = Krs::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'semester' => $validated['semester'],
            'tahun_ajaran' => $tahunAjaran,
            'status_approval' => 'pending',
        ]);

        foreach ($validated['mata_kuliah_id'] as $mkId) {
            KrsItem::query()->create([
                'krs_id' => $krs->id,
                'mata_kuliah_id' => $mkId,
            ]);
        }

        $this->ensureAbsensiForMahasiswa($mahasiswa->id, (string) $mahasiswa->program_studi, (int) $validated['semester'], (array) $validated['mata_kuliah_id']);

        return redirect()->route('mahasiswa.krs.show', $krs)->with('success', 'KRS berhasil dibuat dan menunggu approval.');
    }

    public function show(Request $request, Krs $krs): View
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $krs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Akses ditolak.');
        }

        $krs->load(['items.mataKuliah']);

        $signers = $this->resolveProdiSigners($user->mahasiswa->program_studi ?? null);

        return view('mahasiswa.krs.show', [
            'krs' => $krs,
            'kaprodiNama' => $signers['kaprodi'],
            'sekprodiNama' => $signers['sekprodi'],
        ]);
    }

    public function downloadPdf(Request $request, Krs $krs)
    {
        /** @var User $user */
        $user = $request->user();

        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $krs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Akses ditolak.');
        }

        $krs->load(['items.mataKuliah']);

        $signers = $this->resolveProdiSigners($user->mahasiswa->program_studi ?? null);

        $html = view('mahasiswa.krs.pdf', [
            'krs' => $krs,
            'kaprodiNama' => $signers['kaprodi'],
            'sekprodiNama' => $signers['sekprodi'],
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'krs-'.$krs->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function edit(Request $request, Krs $krs): View
    {
        /** @var User $user */
        $user = $request->user();
        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $krs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Akses ditolak.');
        }

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
        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $krs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.krs.index')->with('error', 'Akses ditolak.');
        }

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

        $tahunAjaran = trim((string) ($validated['tahun_ajaran'] ?? ''));
        if ($tahunAjaran === '') {
            $tahunAjaran = null;
        }

        $krs->update([
            'tahun_ajaran' => $tahunAjaran,
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

        $jurusan = (string) $user->mahasiswa->program_studi;
        if ($jurusan !== '') {
            $this->removeAbsensiForMahasiswa((int) $user->mahasiswa->id, $jurusan, (int) $krs->semester, (array) $toDelete);
            $this->ensureAbsensiForMahasiswa((int) $user->mahasiswa->id, $jurusan, (int) $krs->semester, (array) $incoming);
        }

        return redirect()->route('mahasiswa.krs.show', $krs)->with('success', 'KRS berhasil diperbarui dan menunggu approval.');
    }
}
