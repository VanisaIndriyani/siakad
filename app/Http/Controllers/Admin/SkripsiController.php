<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\SkripsiPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkripsiController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return ['routePrefix' => 'admin', 'canAssign' => true];
        }

        if ($user?->isDosen()) {
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            abort_unless(in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true), 403);

            return ['routePrefix' => 'dosen', 'canAssign' => false];
        }

        abort(403);
    }

    public function index(Request $request): View
    {
        $context = $this->resolveContext($request);
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
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function show(Request $request, SkripsiPengajuan $skripsi): View
    {
        $context = $this->resolveContext($request);
        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'messages.sender', 'approvedBy']);

        $dosenList = $context['canAssign']
            ? Dosen::query()->orderBy('nama')->get()
            : collect();

        return view('admin.skripsi.show', [
            'skripsi' => $skripsi,
            'dosenList' => $dosenList,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function updateStatus(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $this->resolveContext($request);
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
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'dosen_pembimbing_id' => ['required', 'exists:dosen,id'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
            'sk_pembimbing_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf'],
        ]);

        $skPath = $skripsi->sk_pembimbing_path;
        $skName = $skripsi->sk_pembimbing_name;

        if (! empty($validated['sk_pembimbing_file'])) {
            $file = $validated['sk_pembimbing_file'];
            $originalName = (string) $file->getClientOriginalName();
            $ext = (string) $file->getClientOriginalExtension();
            $filename = 'sk-pembimbing-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
            $path = $file->storeAs('skripsi/sk/'.$skripsi->id, $filename, 'public');

            if ($skripsi->sk_pembimbing_path) {
                Storage::disk('public')->delete($skripsi->sk_pembimbing_path);
            }

            $skPath = $path;
            $skName = $originalName;
        }

        $skripsi->update([
            'status' => 'assigned',
            'dosen_pembimbing_id' => (int) $validated['dosen_pembimbing_id'],
            'nomor_sk' => $validated['nomor_sk'] ?: null,
            'tanggal_sk' => $validated['tanggal_sk'] ?: null,
            'sk_pembimbing_path' => $skPath,
            'sk_pembimbing_name' => $skName,
            'assigned_at' => now(),
            'approved_at' => $skripsi->approved_at ?: now(),
            'approved_by_user_id' => $skripsi->approved_by_user_id ?: $request->user()?->id,
        ]);

        return back()->with('success', 'Dosen pembimbing berhasil ditetapkan.');
    }

    public function downloadSkPembimbing(Request $request, SkripsiPengajuan $skripsi): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $skripsi->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isAdmin()) {
        } elseif ($user->isDosen()) {
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            abort_unless(in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true), 403);
        } else {
            abort(403);
        }

        abort_unless($skripsi->sk_pembimbing_path, 404);
        abort_unless(Storage::disk('public')->exists($skripsi->sk_pembimbing_path), 404);

        $downloadName = $skripsi->sk_pembimbing_name ?: basename($skripsi->sk_pembimbing_path);
        return response()->download(storage_path('app/public/'.$skripsi->sk_pembimbing_path), $downloadName);
    }

    public function previewSkPembimbing(Request $request, SkripsiPengajuan $skripsi): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $skripsi->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isAdmin()) {
        } elseif ($user->isDosen()) {
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            abort_unless(in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true), 403);
        } else {
            abort(403);
        }

        abort_unless($skripsi->sk_pembimbing_path, 404);
        abort_unless(Storage::disk('public')->exists($skripsi->sk_pembimbing_path), 404);

        $downloadName = $skripsi->sk_pembimbing_name ?: basename($skripsi->sk_pembimbing_path);
        return response()->file(storage_path('app/public/'.$skripsi->sk_pembimbing_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }
}
