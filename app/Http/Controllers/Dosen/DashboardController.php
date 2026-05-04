<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        $totalMahasiswa = Mahasiswa::query()->count();
        $krsUntukNilai = $dosen
            ? Krs::query()
                ->where('status_approval', 'approved')
                ->whereHas('items.mataKuliah', function ($q) use ($dosen) {
                    $q->where('dosen_id', $dosen->id);
                })
                ->count()
            : 0;

        $approvedPerSemester = $dosen
            ? Krs::query()
                ->selectRaw('krs.semester, COUNT(DISTINCT krs.id) as total')
                ->join('krs_items', 'krs_items.krs_id', '=', 'krs.id')
                ->join('mata_kuliah', 'mata_kuliah.id', '=', 'krs_items.mata_kuliah_id')
                ->where('krs.status_approval', 'approved')
                ->where('mata_kuliah.dosen_id', $dosen->id)
                ->groupBy('krs.semester')
                ->orderBy('krs.semester')
                ->get()
            : collect();

        return view('dosen.dashboard', [
            'dosen' => $dosen,
            'totalMahasiswa' => $totalMahasiswa,
            'krsUntukNilai' => $krsUntukNilai,
            'chartLabels' => $approvedPerSemester->pluck('semester')->map(fn ($s) => 'S'.$s),
            'chartValues' => $approvedPerSemester->pluck('total'),
        ]);
    }
}
