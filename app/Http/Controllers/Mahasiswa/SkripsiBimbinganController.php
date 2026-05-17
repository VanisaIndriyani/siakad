<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SkripsiPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkripsiBimbinganController extends Controller
{
    public function show(Request $request, SkripsiPengajuan $skripsi): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender']);
        $skripsi->update(['mahasiswa_last_read_at' => now()]);

        return view('mahasiswa.skripsi.bimbingan', [
            'skripsi' => $skripsi,
        ]);
    }

    public function pdf(Request $request, SkripsiPengajuan $skripsi)
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);

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
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless($skripsi->dosen_pembimbing_id || $skripsi->dosen_pembimbing_id_2, 403);

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
