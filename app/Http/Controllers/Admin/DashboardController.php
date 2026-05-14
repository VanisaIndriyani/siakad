<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalMahasiswa = Mahasiswa::query()->count();
        $totalDosen = Dosen::query()->count();
        $totalKrs = Krs::query()->count();
        $totalKhs = Khs::query()->count();
        $totalAdmin = User::query()->where('role', User::ROLE_ADMIN)->count();

        $totalBiaya = Pembayaran::query()->sum('total_biaya');
        $totalDibayar = Pembayaran::query()->sum('total_dibayar');

        $angkatan = Mahasiswa::query()
            ->selectRaw('angkatan, COUNT(*) as total')
            ->whereNotNull('angkatan')
            ->groupBy('angkatan')
            ->orderBy('angkatan')
            ->get();

        return view('admin.dashboard', [
            'totalMahasiswa' => $totalMahasiswa,
            'totalDosen' => $totalDosen,
            'totalKrs' => $totalKrs,
            'totalKhs' => $totalKhs,
            'totalBiaya' => $totalBiaya,
            'totalDibayar' => $totalDibayar,
            'chartLabels' => $angkatan->pluck('angkatan'),
            'chartValues' => $angkatan->pluck('total'),
            'roleLabels' => ['Admin', 'Dosen', 'Mahasiswa'],
            'roleValues' => [$totalAdmin, $totalDosen, $totalMahasiswa],
        ]);
    }
}
