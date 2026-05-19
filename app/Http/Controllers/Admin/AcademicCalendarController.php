<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicEvent;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
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

        $eventsAll = (clone $query)->orderBy('tanggal_mulai')->get();
        $events = $query->orderByDesc('tanggal_mulai')->paginate(10)->withQueryString();

        return view('admin.kalender.index', [
            'events' => $events,
            'eventsAll' => $eventsAll,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.kalender.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        AcademicEvent::query()->create([
            ...$validated,
            'created_by_user_id' => $request->user()?->id,
        ]);

        return redirect()->route('admin.kalender-akademik.index')->with('success', 'Kegiatan kalender akademik berhasil ditambahkan.');
    }

    public function edit(AcademicEvent $kalender_akademik): View
    {
        return view('admin.kalender.edit', [
            'event' => $kalender_akademik,
        ]);
    }

    public function update(Request $request, AcademicEvent $kalender_akademik): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $kalender_akademik->update($validated);

        return redirect()->route('admin.kalender-akademik.index')->with('success', 'Kegiatan kalender akademik berhasil diperbarui.');
    }

    public function destroy(AcademicEvent $kalender_akademik): RedirectResponse
    {
        $kalender_akademik->delete();

        return back()->with('success', 'Kegiatan kalender akademik berhasil dihapus.');
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
