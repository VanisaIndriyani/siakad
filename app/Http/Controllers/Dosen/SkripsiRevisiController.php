<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\SkripsiPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkripsiRevisiController extends Controller
{
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

    private function authorizeSkripsi(Request $request, SkripsiPengajuan $skripsi): void
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $allowed = in_array((int) $dosen->id, [(int) $skripsi->dosen_pembimbing_id, (int) $skripsi->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);
    }

    public function index(Request $request, SkripsiPengajuan $skripsi): View
    {
        $this->authorizeSkripsi($request, $skripsi);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        return view('dosen.skripsi.revisi', [
            'skripsi' => $skripsi,
        ]);
    }

    public function store(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $this->authorizeSkripsi($request, $skripsi);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'revisi' => ['required', 'string'],
        ]);

        $skripsi->revisis()->create([
            'created_by_user_id' => $request->user()?->id,
            'tanggal' => $validated['tanggal'],
            'revisi' => $validated['revisi'],
        ]);

        return back()->with('success', 'Revisi berhasil ditambahkan.');
    }

    public function pdf(Request $request, SkripsiPengajuan $skripsi)
    {
        $this->authorizeSkripsi($request, $skripsi);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);
        $kaprodi = $this->resolveKaprodi($skripsi->mahasiswa?->program_studi);

        $html = view('skripsi.revisi-pdf', [
            'skripsi' => $skripsi,
            'revisis' => $skripsi->revisis->sortBy('id')->values(),
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'revisi-skripsi-'.$skripsi->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
