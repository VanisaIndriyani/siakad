<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CutiPengajuan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CutiApprovalController extends Controller
{
    private function resolveApprover(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        abort_unless($dosen, 403);

        $programStudi = trim((string) ($dosen->program_studi ?? ''));
        return [$dosen, $programStudi];
    }

    public function index(Request $request): View
    {
        [, $programStudi] = $this->resolveApprover($request);

        $q = trim((string) $request->get('q', ''));
        $statusParam = $request->get('status');
        $status = $statusParam === null ? 'pending' : trim((string) $statusParam);

        $query = CutiPengajuan::query()
            ->with(['mahasiswa.user'])
            ->orderByDesc('id')
            ->when($status !== '', fn ($q2) => $q2->where('status', $status));

        $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
            $sub->where('program_studi', $programStudi);
        });

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $cuti = $query->paginate(10)->withQueryString();

        return view('dosen.cuti.index', [
            'cuti' => $cuti,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(CutiPengajuan $cuti): View
    {
        [, $programStudi] = $this->resolveApprover(request());
        abort_unless((string) ($cuti->mahasiswa?->program_studi ?? '') === $programStudi, 403);

        $cuti->load(['mahasiswa.user', 'approvedByAdmin', 'approvedByProdi']);
        return view('dosen.cuti.show', [
            'cuti' => $cuti,
        ]);
    }

    public function updateStatus(Request $request, CutiPengajuan $cuti): RedirectResponse
    {
        [$dosen, $programStudi] = $this->resolveApprover($request);
        abort_unless((string) ($cuti->mahasiswa?->program_studi ?? '') === $programStudi, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'catatan_prodi' => ['nullable', 'string'],
        ]);

        $cuti->update([
            'status' => $validated['status'],
            'catatan_prodi' => $validated['catatan_prodi'],
            'approved_by_prodi_id' => auth()->id(),
        ]);

        return redirect()->route('dosen.cuti.index')->with('success', 'Status pengajuan cuti berhasil diperbarui.');
    }
}
