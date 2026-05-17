<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\SkripsiPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkripsiBimbinganController extends Controller
{
    public function index(Request $request): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $items = SkripsiPengajuan::query()
            ->with(['mahasiswa', 'latestMessage'])
            ->where(function ($q) use ($dosen) {
                $q->where('dosen_pembimbing_id', $dosen->id)
                    ->orWhere('dosen_pembimbing_id_2', $dosen->id);
            })
            ->orderByDesc('id')
            ->get();

        return view('dosen.skripsi.index', [
            'items' => $items,
        ]);
    }

    public function show(Request $request, SkripsiPengajuan $skripsi): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        $allowed = in_array((int) $dosen->id, [(int) $skripsi->dosen_pembimbing_id, (int) $skripsi->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender', 'files']);
        $skripsi->update(['dosen_last_read_at' => now()]);

        return view('dosen.skripsi.show', [
            'skripsi' => $skripsi,
        ]);
    }

    public function pdf(Request $request, SkripsiPengajuan $skripsi)
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        $allowed = in_array((int) $dosen->id, [(int) $skripsi->dosen_pembimbing_id, (int) $skripsi->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender']);

        $html = view('dosen.skripsi.bimbingan-pdf', [
            'skripsi' => $skripsi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'bimbingan-skripsi-'.$skripsi->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function store(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        $allowed = in_array((int) $dosen->id, [(int) $skripsi->dosen_pembimbing_id, (int) $skripsi->dosen_pembimbing_id_2], true);
        abort_unless($allowed, 404);

        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        $skripsi->messages()->create([
            'sender_user_id' => $request->user()?->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan bimbingan terkirim.');
    }
}
