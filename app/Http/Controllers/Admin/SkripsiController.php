<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\SkripsiPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SkripsiController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = SkripsiPengajuan::query()->with(['mahasiswa', 'dosenPembimbing']);

        if ($q !== '') {
            $query->where('judul', 'like', "%{$q}%")
                ->orWhereHas('mahasiswa', function ($sub) use ($q) {
                    $sub->where('nama_lengkap', 'like', "%{$q}%")
                        ->orWhere('npm', 'like', "%{$q}%");
                });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.skripsi.index', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(SkripsiPengajuan $skripsi): View
    {
        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'messages.sender', 'approvedBy']);

        $dosenList = Dosen::query()->orderBy('nama')->get();

        return view('admin.skripsi.show', [
            'skripsi' => $skripsi,
            'dosenList' => $dosenList,
        ]);
    }

    public function updateStatus(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $skripsi->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?: null,
            'approved_at' => now(),
            'approved_by_user_id' => $request->user()?->id,
        ]);

        $msg = $validated['status'] === 'approved'
            ? 'Pengajuan skripsi disetujui.'
            : 'Pengajuan skripsi ditolak.';

        return back()->with('success', $msg);
    }

    public function assign(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $validated = $request->validate([
            'dosen_pembimbing_id' => ['required', 'exists:dosen,id'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
        ]);

        $skripsi->update([
            'status' => 'assigned',
            'dosen_pembimbing_id' => (int) $validated['dosen_pembimbing_id'],
            'nomor_sk' => $validated['nomor_sk'] ?: null,
            'tanggal_sk' => $validated['tanggal_sk'] ?: null,
            'assigned_at' => now(),
            'approved_at' => $skripsi->approved_at ?: now(),
            'approved_by_user_id' => $skripsi->approved_by_user_id ?: $request->user()?->id,
        ]);

        return back()->with('success', 'Dosen pembimbing berhasil ditetapkan.');
    }
}
