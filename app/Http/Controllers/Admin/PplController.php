<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PplFile;
use App\Models\PplPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PplController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return [
                'routePrefix' => 'admin',
                'canAssign' => true,
                'programStudi' => null,
            ];
        }

        if ($user?->isDosen()) {
            $dosen = $user->dosen;
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');
            abort_unless(in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true), 403);

            $programStudi = trim((string) ($dosen?->program_studi ?? ''));
            abort_unless($programStudi !== '', 403);

            return [
                'routePrefix' => 'dosen',
                'canAssign' => false,
                'programStudi' => $programStudi,
            ];
        }

        abort(403);
    }

    public function index(Request $request): View
    {
        $context = $this->resolveContext($request);

        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = PplPengajuan::query()->with(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
                $sub->where('program_studi', $programStudi);
            });
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('instansi_nama', 'like', "%{$q}%")
                    ->orWhereHas('mahasiswa', function ($m) use ($q) {
                        $m->where('nama_lengkap', 'like', "%{$q}%")
                            ->orWhere('npm', 'like', "%{$q}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $items = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.ppl.index', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function show(Request $request, PplPengajuan $ppl): View
    {
        $context = $this->resolveContext($request);

        if ($context['programStudi']) {
            $ppl->loadMissing('mahasiswa');
            abort_unless((string) ($ppl->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $ppl->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender', 'approvedBy', 'files']);

        $dosenList = $context['canAssign']
            ? Dosen::query()->orderBy('nama')->get()
            : collect();

        return view('admin.ppl.show', [
            'ppl' => $ppl,
            'dosenList' => $dosenList,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function updateStatus(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        $context = $this->resolveContext($request);

        if ($context['programStudi']) {
            $ppl->loadMissing('mahasiswa');
            abort_unless((string) ($ppl->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $ppl->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?: null,
            'approved_at' => now(),
            'approved_by_user_id' => $request->user()?->id,
        ]);

        $msg = $validated['status'] === 'approved'
            ? 'Pengajuan PPL disetujui.'
            : 'Pengajuan PPL ditolak.';

        return back()->with('success', $msg);
    }

    public function assign(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'dosen_pembimbing_id' => ['required', 'exists:dosen,id'],
            'dosen_pembimbing_id_2' => ['nullable', 'exists:dosen,id', 'different:dosen_pembimbing_id'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'tanggal_sk' => ['nullable', 'date'],
            'sk_pembimbing_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf'],
        ]);

        $skPath = $ppl->sk_pembimbing_path;
        $skName = $ppl->sk_pembimbing_name;

        if (! empty($validated['sk_pembimbing_file'])) {
            $file = $validated['sk_pembimbing_file'];
            $originalName = (string) $file->getClientOriginalName();
            $ext = (string) $file->getClientOriginalExtension();
            $filename = 'sk-ppl-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
            $path = $file->storeAs('ppl/sk/'.$ppl->id, $filename, 'public');

            if ($ppl->sk_pembimbing_path) {
                Storage::disk('public')->delete($ppl->sk_pembimbing_path);
            }

            $skPath = $path;
            $skName = $originalName;
        }

        $ppl->update([
            'status' => 'assigned',
            'dosen_pembimbing_id' => (int) $validated['dosen_pembimbing_id'],
            'dosen_pembimbing_id_2' => $validated['dosen_pembimbing_id_2'] ? (int) $validated['dosen_pembimbing_id_2'] : null,
            'nomor_sk' => $validated['nomor_sk'] ?: null,
            'tanggal_sk' => $validated['tanggal_sk'] ?: null,
            'sk_pembimbing_path' => $skPath,
            'sk_pembimbing_name' => $skName,
            'assigned_at' => now(),
            'approved_at' => $ppl->approved_at ?: now(),
            'approved_by_user_id' => $ppl->approved_by_user_id ?: $request->user()?->id,
        ]);

        return back()->with('success', 'Pembimbing PPL berhasil ditetapkan.');
    }

    public function downloadSkPembimbing(Request $request, PplPengajuan $ppl): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $ppl->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isAdmin()) {
        } elseif ($user->isDosen()) {
            $dosenId = (int) ($user->dosen?->id ?? 0);
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            $allowed = in_array($dosenId, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true)
                || in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);
            abort_unless($allowed, 403);
        } else {
            abort(403);
        }

        abort_unless($ppl->sk_pembimbing_path, 404);
        abort_unless(Storage::disk('public')->exists($ppl->sk_pembimbing_path), 404);

        $downloadName = $ppl->sk_pembimbing_name ?: basename($ppl->sk_pembimbing_path);
        return response()->download(storage_path('app/public/'.$ppl->sk_pembimbing_path), $downloadName);
    }

    public function previewSkPembimbing(Request $request, PplPengajuan $ppl): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $ppl->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isAdmin()) {
        } elseif ($user->isDosen()) {
            $dosenId = (int) ($user->dosen?->id ?? 0);
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            $allowed = in_array($dosenId, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true)
                || in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);
            abort_unless($allowed, 403);
        } else {
            abort(403);
        }

        abort_unless($ppl->sk_pembimbing_path, 404);
        abort_unless(Storage::disk('public')->exists($ppl->sk_pembimbing_path), 404);

        $downloadName = $ppl->sk_pembimbing_name ?: basename($ppl->sk_pembimbing_path);
        return response()->file(storage_path('app/public/'.$ppl->sk_pembimbing_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    private function authorizePplFileAccess(Request $request, PplFile $file): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        $ppl = $file->ppl;
        abort_unless($ppl, 404);

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isMahasiswa()) {
            abort_unless((int) $ppl->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
            return;
        }

        if ($user->isDosen()) {
            $dosenId = (int) ($user->dosen?->id ?? 0);
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            $allowed = in_array($dosenId, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true)
                || in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);
            abort_unless($allowed, 403);
            return;
        }

        abort(403);
    }

    public function previewFile(Request $request, PplFile $file): BinaryFileResponse
    {
        $this->authorizePplFileAccess($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $name = $file->file_name ?: basename($file->file_path);
        return response()->file(storage_path('app/public/'.$file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    public function downloadFile(Request $request, PplFile $file): BinaryFileResponse
    {
        $this->authorizePplFileAccess($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $name = $file->file_name ?: basename($file->file_path);
        return response()->download(storage_path('app/public/'.$file->file_path), $name);
    }

    public function destroyFile(Request $request, PplFile $file): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'Laporan PPL berhasil dihapus.');
    }

    public function destroy(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($ppl->sk_pembimbing_path) {
            Storage::disk('public')->delete($ppl->sk_pembimbing_path);
        }

        $ppl->delete();

        return redirect()->route('admin.ppl.index')->with('success', 'Data PPL berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'ids' => ['required'],
        ]);

        $raw = $validated['ids'];
        $ids = is_array($raw)
            ? $raw
            : preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);

        $ids = collect($ids)
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        if (count($ids) === 0) {
            return back()->with('error', 'Pilih minimal 1 data PPL.');
        }

        $items = PplPengajuan::query()->whereIn('id', $ids)->get();
        foreach ($items as $it) {
            if ($it->sk_pembimbing_path) {
                Storage::disk('public')->delete($it->sk_pembimbing_path);
            }
            $it->delete();
        }

        return back()->with('success', 'Data PPL terpilih berhasil dihapus.');
    }
}
