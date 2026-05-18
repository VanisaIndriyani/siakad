<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\CutiPengajuan;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Dompdf\Dompdf;

class CutiController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $cuti = CutiPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa?->id)
            ->orderByDesc('id')
            ->paginate(10);

        return view('mahasiswa.cuti.index', [
            'cuti' => $cuti,
        ]);
    }

    public function create(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        if (! $user->mahasiswa) {
            abort(403, 'Profil mahasiswa belum tersedia.');
        }

        return view('mahasiswa.cuti.create');
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
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'alasan' => ['required', 'string'],
        ]);

        CutiPengajuan::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'semester' => $validated['semester'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'alasan' => $validated['alasan'],
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.cuti.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    public function show(CutiPengajuan $cuti): View
    {
        $this->authorizeAccess($cuti);
        $cuti->load(['mahasiswa.user', 'approvedByAdmin', 'approvedByProdi']);

        return view('mahasiswa.cuti.show', [
            'cuti' => $cuti,
        ]);
    }

    public function destroy(CutiPengajuan $cuti): RedirectResponse
    {
        $this->authorizeAccess($cuti);

        if ($cuti->status !== 'pending') {
            return back()->with('error', 'Pengajuan yang sudah diproses tidak dapat dihapus.');
        }

        $cuti->delete();

        return redirect()->route('mahasiswa.cuti.index')->with('success', 'Pengajuan cuti berhasil dihapus.');
    }

    public function downloadPdf(CutiPengajuan $cuti)
    {
        $this->authorizeAccess($cuti);
        
        if ($cuti->status !== 'approved') {
            return back()->with('error', 'Hanya pengajuan yang sudah disetujui yang dapat dicetak.');
        }

        $cuti->load(['mahasiswa.user', 'approvedByAdmin', 'approvedByProdi']);
        
        $prodi = $cuti->mahasiswa->program_studi ?? null;
        $kaprodi = null;
        $sekprodi = null;

        if ($prodi) {
            $kaprodi = \App\Models\Dosen::query()
                ->where('program_studi', $prodi)
                ->where('status_akademik', 'Ketua Prodi')
                ->orderByDesc('id')
                ->first();

            $sekprodi = \App\Models\Dosen::query()
                ->where('program_studi', $prodi)
                ->where('status_akademik', 'Sekretaris Prodi')
                ->orderByDesc('id')
                ->first();
        }

        $html = view('mahasiswa.cuti.pdf', [
            'cuti' => $cuti,
            'kaprodi' => $kaprodi,
            'sekprodi' => $sekprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Surat_Cuti_' . $cuti->mahasiswa->npm . '.pdf"',
        ]);
    }

    private function authorizeAccess(CutiPengajuan $cuti): void
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user->isAdmin()) return;
        
        if (! $user->mahasiswa || (int) $cuti->mahasiswa_id !== (int) $user->mahasiswa->id) {
            abort(403, 'Akses ditolak.');
        }
    }
}
