<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplBimbinganController extends Controller
{
    public function show(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender']);
        $ppl->update(['mahasiswa_last_read_at' => now()]);

        return view('mahasiswa.ppl.bimbingan', [
            'ppl' => $ppl,
        ]);
    }

    public function store(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless($ppl->dosen_pembimbing_id || $ppl->dosen_pembimbing_id_2, 403);

        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        $ppl->messages()->create([
            'sender_user_id' => $request->user()?->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan bimbingan terkirim.');
    }
}

