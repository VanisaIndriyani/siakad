<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Krs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KrsController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status_approval', $status);
        }

        $krs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.krs.index', [
            'krs' => $krs,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(Krs $krs): View
    {
        $krs->load(['mahasiswa', 'mahasiswa.user', 'items.mataKuliah']);

        return view('admin.krs.show', [
            'krs' => $krs,
        ]);
    }

    public function updateStatus(Request $request, Krs $krs): RedirectResponse
    {
        $validated = $request->validate([
            'status_approval' => ['required', 'in:pending,approved,rejected'],
        ]);

        $krs->update($validated);
        cache()->forget('admin_pending_krs_count');

        if ($validated['status_approval'] === 'approved') {
            $krs->loadMissing(['items']);

            $khs = Khs::query()->firstOrCreate(
                [
                    'mahasiswa_id' => $krs->mahasiswa_id,
                    'semester' => $krs->semester,
                ],
                [
                    'tahun_ajaran' => $krs->tahun_ajaran,
                ]
            );

            foreach ($krs->items as $item) {
                KhsItem::query()->firstOrCreate([
                    'khs_id' => $khs->id,
                    'mata_kuliah_id' => $item->mata_kuliah_id,
                ]);
            }
        }

        $redirect = (string) $request->input('redirect', '');
        if ($redirect !== '') {
            $host = parse_url($redirect, PHP_URL_HOST);
            if ($host === null || $host === $request->getHost()) {
                return redirect()->to($redirect)->with('success', 'Status KRS berhasil diperbarui.');
            }
        }

        return redirect()->route('admin.krs.show', $krs)->with('success', 'Status KRS berhasil diperbarui.');
    }
}
