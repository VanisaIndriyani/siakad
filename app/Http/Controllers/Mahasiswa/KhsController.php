<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Khs;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KhsController extends Controller
{
    private function resolveKaprodiName(?string $programStudi): ?string
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->value('nama');
    }

    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return view('mahasiswa.khs.index', [
                'khs' => collect(),
            ]);
        }

        foreach (range(1, 8) as $semester) {
            Khs::query()->firstOrCreate([
                'mahasiswa_id' => $mahasiswa->id,
                'semester' => $semester,
            ]);
        }

        $khs = Khs::query()
            ->with(['items.mataKuliah.dosen'])
            ->withCount('items')
            ->withCount(['items as nilai_count' => function ($q) {
                $q->whereNotNull('nilai_huruf')->orWhereNotNull('nilai_angka');
            }])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->whereHas('items', function ($q) {
                $q->whereNotNull('nilai_huruf')->orWhereNotNull('nilai_angka');
            })
            ->orderBy('semester')
            ->get();

        return view('mahasiswa.khs.index', [
            'khs' => $khs,
        ]);
    }

    public function show(Request $request, Khs $khs): View
    {
        /** @var User $user */
        $user = $request->user();
        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $khs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Akses ditolak.');
        }

        $khs->load(['items.mataKuliah.dosen', 'mahasiswa']);

        $kaprodiNama = $this->resolveKaprodiName($user->mahasiswa->program_studi ?? null);

        return view('mahasiswa.khs.show', [
            'khs' => $khs,
            'kaprodiNama' => $kaprodiNama,
        ]);
    }

    public function pdf(Request $request, Khs $khs)
    {
        /** @var User $user */
        $user = $request->user();
        if (! $user->mahasiswa) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }
        if ((int) $khs->mahasiswa_id !== (int) $user->mahasiswa->id) {
            return redirect()->route('mahasiswa.khs.index')->with('error', 'Akses ditolak.');
        }

        $khs->load(['items.mataKuliah.dosen', 'mahasiswa']);

        $kaprodiNama = $this->resolveKaprodiName($user->mahasiswa->program_studi ?? null);

        $html = view('mahasiswa.khs.pdf', [
            'khs' => $khs,
            'kaprodiNama' => $kaprodiNama,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'khs-'.$khs->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
