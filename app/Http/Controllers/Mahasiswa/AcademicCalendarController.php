<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicCalendarController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = AcademicEvent::query();
        if ($q !== '') {
            $query->where('judul', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%");
        }

        $events = $query->orderBy('tanggal_mulai')->get();

        return view('mahasiswa.kalender.index', [
            'events' => $events,
            'q' => $q,
        ]);
    }

    public function pdf(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = AcademicEvent::query();
        if ($q !== '') {
            $query->where('judul', 'like', "%{$q}%")
                ->orWhere('kategori', 'like', "%{$q}%");
        }

        $events = $query->orderBy('tanggal_mulai')->get();

        $html = view('kalender.pdf', [
            'events' => $events,
            'q' => $q,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = $q !== '' ? 'kalender-akademik-filtered.pdf' : 'kalender-akademik.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
