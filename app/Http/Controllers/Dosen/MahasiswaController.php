<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = Mahasiswa::query()->with('user');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('program_studi', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        $mahasiswa = $query->orderBy('angkatan')->orderBy('nama_lengkap')->paginate(10)->withQueryString();

        return view('dosen.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'q' => $q,
        ]);
    }
}
