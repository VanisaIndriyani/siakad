<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KhsController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return view('mahasiswa.khs.index', [
                'khs' => collect(),
            ]);
        }

        foreach (range(1, 8) as $semester) {
            Khs::query()->firstOrCreate([
                'mahasiswa_id' => $mahasiswa->id,
                'semester' => $semester,
            ]);
        }

        $khs = Khs::query()
            ->withCount('items')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('semester')
            ->get();

        return view('mahasiswa.khs.index', [
            'khs' => $khs,
        ]);
    }

    public function show(Request $request, Khs $khs): View
    {
        /** @var User $user */
        $user = $request->user();
        abort_unless($user->mahasiswa && $khs->mahasiswa_id === $user->mahasiswa->id, 403);

        $khs->load(['items.mataKuliah', 'mahasiswa']);

        return view('mahasiswa.khs.show', [
            'khs' => $khs,
        ]);
    }
}
