<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\SkripsiPengajuan;
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
            ->where('dosen_pembimbing_id', $dosen->id)
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
        abort_unless((int) $skripsi->dosen_pembimbing_id === (int) $dosen->id, 404);

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'messages.sender']);
        $skripsi->update(['dosen_last_read_at' => now()]);

        return view('dosen.skripsi.show', [
            'skripsi' => $skripsi,
        ]);
    }

    public function store(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        abort_unless((int) $skripsi->dosen_pembimbing_id === (int) $dosen->id, 404);

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
