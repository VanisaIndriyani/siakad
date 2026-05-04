<?php

namespace App\Http\Controllers\Mahasiswa;

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

        return view('mahasiswa.profil', [
            'mahasiswa' => $user->mahasiswa,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa belum tersedia.');
        }

        $validated = $request->validate([
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto_path) {
                Storage::disk('public')->delete($mahasiswa->foto_path);
            }
            $mahasiswa->foto_path = $request->file('foto')->store('photos/mahasiswa', 'public');
        }

        $mahasiswa->alamat = $validated['alamat'] ?? $mahasiswa->alamat;
        $mahasiswa->save();

        return redirect()->route('mahasiswa.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
