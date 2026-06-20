<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\SkripsiPengajuan;
use Dompdf\Dompdf;
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
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);
    }

    public function index(Request $request, SkripsiPengajuan $skripsi): View
    {
        $this->authorizeSkripsi($request, $skripsi);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        return view('mahasiswa.skripsi.revisi', [
            'skripsi' => $skripsi,
        ]);
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
