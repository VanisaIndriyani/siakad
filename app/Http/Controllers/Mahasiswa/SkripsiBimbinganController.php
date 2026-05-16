<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SkripsiPengajuan;
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

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'messages.sender']);
        $skripsi->update(['mahasiswa_last_read_at' => now()]);

        return view('mahasiswa.skripsi.bimbingan', [
            'skripsi' => $skripsi,
        ]);
    }

    public function store(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless($skripsi->dosen_pembimbing_id, 403);

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
