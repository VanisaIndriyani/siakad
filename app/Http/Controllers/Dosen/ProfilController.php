<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        return view('dosen.profil', [
            'dosen' => $user->dosen,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        if (! $dosen) {
            return back()->with('error', 'Profil dosen belum tersedia.');
        }

        $validated = $request->validate([
            'nik' => ['nullable', 'string', 'max:50', 'unique:dosen,nik,'.$dosen->id],
            'nuptk' => ['nullable', 'string', 'max:50', 'unique:dosen,nuptk,'.$dosen->id],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'nomor_hp' => ['nullable', 'string', 'max:50'],
            'mata_kuliah' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($dosen->foto_path) {
                Storage::disk('public')->delete($dosen->foto_path);
            }
            $dosen->foto_path = $request->file('foto')->store('photos/dosen', 'public');
        }

        $dosen->fill($validated);
        $dosen->save();

        return redirect()->route('dosen.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
