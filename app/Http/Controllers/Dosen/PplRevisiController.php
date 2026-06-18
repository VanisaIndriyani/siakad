<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplRevisiController extends Controller
{
    private function authorizePpl(Request $request, PplPengajuan $ppl): void
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $allowed = in_array((int) $dosen->id, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);
    }

    private function resolveKaprodi(?string $programStudi): ?Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    public function index(Request $request, PplPengajuan $ppl): View
    {
        $this->authorizePpl($request, $ppl);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        return view('dosen.ppl.revisi', [
            'ppl' => $ppl,
        ]);
    }

    public function store(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        $this->authorizePpl($request, $ppl);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'revisi' => ['required', 'string'],
        ]);

        $ppl->revisis()->create([
            'created_by_user_id' => $request->user()?->id,
            'tanggal' => $validated['tanggal'],
            'revisi' => $validated['revisi'],
        ]);

        return back()->with('success', 'Catatan revisi PPL berhasil ditambahkan.');
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $this->authorizePpl($request, $ppl);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        $html = view('ppl.revisi-pdf', [
            'ppl' => $ppl,
            'revisis' => $ppl->revisis->sortBy('id')->values(),
            'printedBy' => $request->user()?->name,
            'kaprodi' => $this->resolveKaprodi($ppl->mahasiswa?->program_studi),
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'revisi-ppl-'.$ppl->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
