<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KrsApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items')
            ->where('status_approval', 'pending');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $krs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('dosen.krs.approval', [
            'krs' => $krs,
            'q' => $q,
        ]);
    }

    public function show(Request $request, Krs $krs): View
    {
        abort_unless($krs->status_approval === 'pending', 404);

        $krs->load(['mahasiswa', 'items.mataKuliah', 'mahasiswa.user']);

        return view('dosen.krs.show', [
            'krs' => $krs,
        ]);
    }

    public function updateStatus(Request $request, Krs $krs): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        if (! $dosen) {
            return back()->with('error', 'Profil dosen belum tersedia.');
        }

        $validated = $request->validate([
            'status_approval' => ['required', 'in:approved,rejected'],
        ]);

        $krs->update([
            'status_approval' => $validated['status_approval'],
            'approved_by_dosen_id' => $dosen->id,
        ]);

        return redirect()->route('dosen.krs.approval')->with('success', 'Status KRS berhasil diperbarui.');
    }
}
