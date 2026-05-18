<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplRevisiController extends Controller
{
    private function authorizePpl(Request $request, PplPengajuan $ppl): void
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
    }

    public function index(Request $request, PplPengajuan $ppl): View
    {
        $this->authorizePpl($request, $ppl);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        return view('mahasiswa.ppl.revisi', [
            'ppl' => $ppl,
        ]);
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $this->authorizePpl($request, $ppl);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);

        $html = view('ppl.revisi-pdf', [
            'ppl' => $ppl,
            'revisis' => $ppl->revisis->sortBy('id')->values(),
            'printedBy' => $request->user()?->name,
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
