<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KknPengajuan;
use App\Models\KknPosko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $pengajuan = KknPengajuan::query()
            ->with(['posko.pembimbingS', 'posko.pengajuans.mahasiswa'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        return view('mahasiswa.kkn.index', [
            'pengajuan' => $pengajuan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $exists = KknPengajuan::query()->where('mahasiswa_id', $mahasiswa->id)->exists();
        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar untuk KKN.');
        }

        KknPengajuan::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.kkn.index')->with('success', 'Pendaftaran KKN berhasil dikirim.');
    }

    public function showPosko(Request $request, KknPosko $posko): View
    {
        $user = $request->user();
        $mahasiswa = $user?->mahasiswa;
        abort_unless($mahasiswa, 403);

        // Ensure student is part of this posko
        $isMember = KknPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('kkn_posko_id', $posko->id)
            ->exists();
        abort_unless($isMember, 404);

        $posko->load(['pembimbingS', 'pengajuans.mahasiswa', 'messages.sender', 'files.user']);

        return view('mahasiswa.kkn.posko', [
            'posko' => $posko,
        ]);
    }
}
