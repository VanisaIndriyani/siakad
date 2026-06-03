<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KknPosko;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknController extends Controller
{
    public function index(Request $request): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $poskos = KknPosko::query()
            ->with(['pengajuans.mahasiswa'])
            ->where('dosen_pembimbing_id', $dosen->id)
            ->get();

        return view('dosen.kkn.index', [
            'poskos' => $poskos,
        ]);
    }

    public function showPosko(Request $request, KknPosko $posko): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        abort_unless((int) $posko->dosen_pembimbing_id === (int) $dosen->id, 403);

        $posko->load(['pengajuans.mahasiswa', 'messages.sender', 'files.user']);

        return view('dosen.kkn.posko', [
            'posko' => $posko,
        ]);
    }
}
