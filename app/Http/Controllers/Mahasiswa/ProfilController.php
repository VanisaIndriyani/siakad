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
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'in:Laki-laki,Perempuan'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'agama' => ['nullable', 'string', 'max:50'],
            'kewarganegaraan' => ['nullable', 'string', 'max:100'],
            'nik' => ['nullable', 'string', 'max:50'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'jalan' => ['nullable', 'string', 'max:255'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:10'],
            'rw' => ['nullable', 'string', 'max:10'],
            'kelurahan' => ['nullable', 'string', 'max:100'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'jenis_tinggal' => ['nullable', 'string', 'max:100'],
            'alat_transportasi' => ['nullable', 'string', 'max:100'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'penerima_kps' => ['nullable', 'string', 'in:Ya,Tidak'],
            'no_kps' => ['nullable', 'string', 'max:100'],
            'ayah_nik' => ['nullable', 'string', 'max:50'],
            'ayah_nama' => ['nullable', 'string', 'max:255'],
            'ayah_tanggal_lahir' => ['nullable', 'date'],
            'ayah_pendidikan' => ['nullable', 'string', 'max:100'],
            'ayah_pekerjaan' => ['nullable', 'string', 'max:100'],
            'ayah_penghasilan' => ['nullable', 'string', 'max:100'],
            'ibu_nik' => ['nullable', 'string', 'max:50'],
            'ibu_nama' => ['nullable', 'string', 'max:255'],
            'ibu_tanggal_lahir' => ['nullable', 'date'],
            'ibu_pendidikan' => ['nullable', 'string', 'max:100'],
            'ibu_pekerjaan' => ['nullable', 'string', 'max:100'],
            'ibu_penghasilan' => ['nullable', 'string', 'max:100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto_path) {
                Storage::disk('public')->delete($mahasiswa->foto_path);
            }
            $mahasiswa->foto_path = $request->file('foto')->store('photos/mahasiswa', 'public');
        }

        $mahasiswa->fill($validated);
        $mahasiswa->save();

        return redirect()->route('mahasiswa.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
