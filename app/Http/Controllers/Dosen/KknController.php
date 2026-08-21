<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\KknPengajuan;
use App\Models\KknPosko;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknController extends Controller
{
    public function index(Request $request): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $poskos = $dosen->kknBimbinganS()
            ->with(['pengajuans.mahasiswa'])
            ->get();

        $items = KknPengajuan::query()
            ->with(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2'])
            ->where(function ($q) use ($dosen) {
                $q->where('dosen_pembimbing_id', $dosen->id)
                    ->orWhere('dosen_pembimbing_id_2', $dosen->id);
            })
            ->orderByDesc('id')
            ->get();

        return view('dosen.kkn.index', [
            'poskos' => $poskos,
            'items' => $items,
        ]);
    }

    public function showPosko(Request $request, KknPosko $posko): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        
        $isAssigned = $posko->pembimbingS()->where('dosen_id', $dosen->id)->exists();
        abort_unless($isAssigned, 403);

        $posko->load(['pembimbingS', 'pengajuans.mahasiswa', 'messages.sender', 'files.user']);

        return view('dosen.kkn.posko', [
            'posko' => $posko,
        ]);
    }
}
