<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KrsApprovalController extends Controller
{
    private function resolveApprover(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        abort_unless($dosen, 403);

        $programStudi = trim((string) ($dosen->program_studi ?? ''));
        abort_unless($programStudi !== '', 403, 'Anda belum memiliki Program Studi yang terdaftar.');

        return [$dosen, $programStudi];
    }

    public function index(Request $request): View
    {
        [, $programStudi] = $this->resolveApprover($request);

        $q = trim((string) $request->get('q', ''));
        $statusParam = $request->get('status');
        $status = $statusParam === null ? 'pending' : trim((string) $statusParam);

        $query = Krs::query()
            ->with(['mahasiswa', 'mahasiswa.user'])
            ->withCount('items')
            ->when($status !== '', fn ($q2) => $q2->where('status_approval', $status));

        $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
            $sub->where('program_studi', $programStudi);
        });

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $krs = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('dosen.krs.approval', [
            'krs' => $krs,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(Request $request, Krs $krs): View
    {
        [, $programStudi] = $this->resolveApprover($request);

        $krs->load(['mahasiswa', 'items.mataKuliah', 'mahasiswa.user']);
        abort_unless((string) ($krs->mahasiswa?->program_studi ?? '') === $programStudi, 403);

        return view('dosen.krs.show', [
            'krs' => $krs,
        ]);
    }

    public function updateStatus(Request $request, Krs $krs): RedirectResponse
    {
        [$dosen, $programStudi] = $this->resolveApprover($request);

        $krs->loadMissing('mahasiswa');
        abort_unless((string) ($krs->mahasiswa?->program_studi ?? '') === $programStudi, 403);

        $validated = $request->validate([
            'status_approval' => ['required', 'in:pending,approved,rejected'],
            'catatan_approval' => ['nullable', 'string'],
        ]);

        $approvedByDosenId = $krs->approved_by_dosen_id;
        if ($validated['status_approval'] === 'pending') {
            $approvedByDosenId = null;
        } else {
            $approvedByDosenId = $dosen->id;
        }

        $krs->update([
            'status_approval' => $validated['status_approval'],
            'catatan_approval' => $validated['catatan_approval'] ?: null,
            'approved_by_dosen_id' => $approvedByDosenId,
        ]);

        return redirect()->route('dosen.krs.approval', ['status' => $validated['status_approval']])->with('success', 'Status KRS berhasil diperbarui.');
    }
}
