<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenasehatAkademikController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        abort_unless($mahasiswa, 403);

        $mahasiswa->load(['user', 'dosenPenasehat', 'bimbinganAkademikMessages.sender']);

        // Update last read
        $mahasiswa->update(['mahasiswa_last_read_at' => now()]);

        return view('mahasiswa.penasehat-akademik.show', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless($mahasiswa->dosen_penasehat_id, 403, 'Anda belum memiliki dosen penasehat akademik.');

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:1000'],
        ]);

        $mahasiswa->bimbinganAkademikMessages()->create([
            'sender_user_id' => $user->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}
