<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Krs;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $mahasiswaId = $mahasiswa?->id;

        $totalKrs = $mahasiswaId ? Krs::query()->where('mahasiswa_id', $mahasiswaId)->count() : 0;
        $totalKhs = $mahasiswaId ? Khs::query()->where('mahasiswa_id', $mahasiswaId)->count() : 0;

        $krsStatusCounts = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        if ($mahasiswaId) {
            $rows = Krs::query()
                ->selectRaw('status_approval, COUNT(*) as total')
                ->where('mahasiswa_id', $mahasiswaId)
                ->groupBy('status_approval')
                ->get();

            foreach ($rows as $row) {
                $krsStatusCounts[$row->status_approval] = (int) $row->total;
            }
        }

        $latestKhs = $mahasiswaId
            ? Khs::query()->where('mahasiswa_id', $mahasiswaId)->orderByDesc('semester')->first()
            : null;

        $ipsChart = $mahasiswaId
            ? Khs::query()
                ->select(['semester', 'ips'])
                ->where('mahasiswa_id', $mahasiswaId)
                ->whereNotNull('ips')
                ->orderBy('semester')
                ->get()
            : collect();

        return view('mahasiswa.dashboard', [
            'mahasiswa' => $mahasiswa,
            'totalKrs' => $totalKrs,
            'totalKhs' => $totalKhs,
            'krsStatusCounts' => $krsStatusCounts,
            'latestKhs' => $latestKhs,
            'chartLabels' => $ipsChart->pluck('semester')->map(fn ($s) => 'S'.$s),
            'chartValues' => $ipsChart->pluck('ips'),
        ]);
    }

    public function pembayaran(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        $pembayarans = Pembayaran::query()
            ->with(['details' => function($q) {
                $q->orderByDesc('tanggal_bayar');
            }])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('semester')
            ->get();

        return view('mahasiswa.pembayaran.index', compact('pembayarans'));
    }

    public function uploadPembayaran(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return back()->with('error', 'Profil mahasiswa belum tersedia.');
        }

        abort_unless((int) $pembayaran->mahasiswa_id === (int) $mahasiswa->id, 403);

        $validated = $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_pembayaran' => ['required', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');

        $pembayaran->details()->create([
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
            'bukti_pembayaran' => $buktiPath,
            'keterangan' => $validated['keterangan'] ?: 'Upload mahasiswa',
        ]);

        $pembayaran->updateStatus();

        return back()->with('success', 'Bukti pembayaran berhasil diupload.');
    }
}
