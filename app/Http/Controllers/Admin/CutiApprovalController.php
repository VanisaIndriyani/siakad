<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CutiPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CutiApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = CutiPengajuan::query()
            ->with(['mahasiswa.user'])
            ->orderByDesc('id');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $cuti = $query->paginate(10)->withQueryString();

        return view('admin.cuti.index', [
            'cuti' => $cuti,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(CutiPengajuan $cuti): View
    {
        $cuti->load(['mahasiswa.user', 'approvedByAdmin', 'approvedByProdi']);
        return view('admin.cuti.show', [
            'cuti' => $cuti,
        ]);
    }

    public function updateStatus(Request $request, CutiPengajuan $cuti): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $cuti->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'],
            'approved_by_admin_id' => auth()->id(),
        ]);

        // Jika disetujui, update status mahasiswa menjadi Cuti
        if ($validated['status'] === 'approved') {
            $cuti->mahasiswa->update(['status_mahasiswa' => 'Cuti']);
        }

        return redirect()->route('admin.cuti.index')->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:cuti_pengajuan,id'],
        ]);

        CutiPengajuan::query()->whereIn('id', $validated['ids'])->delete();

        return back()->with('success', 'Data pengajuan cuti terpilih berhasil dihapus.');
    }
}
