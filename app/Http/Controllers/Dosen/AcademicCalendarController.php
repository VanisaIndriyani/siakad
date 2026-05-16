<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
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

        return view('dosen.kalender.index', [
            'events' => $events,
            'q' => $q,
        ]);
    }
}

