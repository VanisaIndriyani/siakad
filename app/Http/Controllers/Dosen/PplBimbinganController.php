<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplBimbinganController extends Controller
{
    public function index(Request $request): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $items = PplPengajuan::query()
            ->with(['mahasiswa', 'latestMessage'])
            ->where(function ($q) use ($dosen) {
                $q->where('dosen_pembimbing_id', $dosen->id)
                    ->orWhere('dosen_pembimbing_id_2', $dosen->id);
            })
            ->orderByDesc('id')
            ->get();

        return view('dosen.ppl.index', [
            'items' => $items,
        ]);
    }

    public function show(Request $request, PplPengajuan $ppl): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $allowed = in_array((int) $dosen->id, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender']);
        $ppl->update(['dosen_last_read_at' => now()]);

        return view('dosen.ppl.show', [
            'ppl' => $ppl,
        ]);
    }

    public function store(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $allowed = in_array((int) $dosen->id, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        $ppl->messages()->create([
            'sender_user_id' => $request->user()?->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan bimbingan terkirim.');
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $allowed = in_array((int) $dosen->id, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender']);

        $html = view('ppl.bimbingan-pdf', [
            'ppl' => $ppl,
            'messages' => $ppl->messages->sortBy('id')->values(),
            'printedBy' => $request->user()?->name,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'bimbingan-ppl-'.$ppl->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
