<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\Request;

class BiodataPdfController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa belum tersedia.');
        }

        $html = view('mahasiswa.biodata-pdf', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="biodata-'.$mahasiswa->npm.'.pdf"',
        ]);
    }
}
