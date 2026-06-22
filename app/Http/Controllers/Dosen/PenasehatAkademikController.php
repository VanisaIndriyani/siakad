<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenasehatAkademikController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $dosen = $user->dosen;
        abort_unless($dosen, 403);

        $q = trim((string) $request->get('q', ''));

        $query = Mahasiswa::query()->with(['user', 'dosenPenasehat'])
            ->where('dosen_penasehat_id', $dosen->id);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $items = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('dosen.penasehat-akademik.index', [
            'items' => $items,
            'q' => $q,
            'routePrefix' => 'dosen',
        ]);
    }

    public function show(Request $request, Mahasiswa $mahasiswa): View
    {
        $user = $request->user();
        $dosen = $user->dosen;
        abort_unless($dosen && (int) $mahasiswa->dosen_penasehat_id === (int) $dosen->id, 403);

        $mahasiswa->load(['user', 'dosenPenasehat', 'bimbinganAkademikMessages.sender']);

        // Update last read
        $mahasiswa->update(['dosen_last_read_at' => now()]);

        return view('dosen.penasehat-akademik.show', [
            'mahasiswa' => $mahasiswa,
            'routePrefix' => 'dosen',
            'canAssign' => false,
        ]);
    }

    public function sendMessage(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = $request->user();
        $dosen = $user->dosen;
        abort_unless($dosen && (int) $mahasiswa->dosen_penasehat_id === (int) $dosen->id, 403);

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
