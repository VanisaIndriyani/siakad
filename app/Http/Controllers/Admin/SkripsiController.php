<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\SkripsiFile;
use App\Models\SkripsiPengajuan;
use Dompdf\Dompdf;
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

    private function resolveKaprodi(?string $programStudi): ?Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
            return ['routePrefix' => 'admin', 'canAssign' => true, 'programStudi' => null];
        }

        if ($user?->isDosen()) {
            $dosen = $user->dosen;
            $programStudi = trim((string) ($dosen?->program_studi ?? ''));
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');

            $canAssign = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);

            return ['routePrefix' => 'dosen', 'canAssign' => $canAssign, 'programStudi' => $programStudi ?: '---'];
        }

        abort(403);
    }

    public function index(Request $request): View
    {
        $context = $this->resolveContext($request);
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = SkripsiPengajuan::query()->with(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->whereHas('mahasiswa', function ($sub) use ($programStudi) {
                $sub->where('program_studi', $programStudi);
            });
        }

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

        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'messages.sender', 'approvedBy', 'files']);

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

    public function downloadPdf(Request $request, SkripsiPengajuan $skripsi)
    {
        $context = $this->resolveContext($request);
        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $skripsi->load(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2', 'revisis.creator']);
        $kaprodi = $this->resolveKaprodi($skripsi->mahasiswa?->program_studi);

        $html = view('skripsi.revisi-pdf', [
            'skripsi' => $skripsi,
            'revisis' => $skripsi->revisis->sortBy('id')->values(),
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'skripsi-revisi-'.$skripsi->mahasiswa->npm.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function updateStatus(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

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
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        $validated = $request->validate([
            'dosen_pembimbing_id' => ['required', 'exists:dosen,id'],
            'dosen_pembimbing_id_2' => ['nullable', 'exists:dosen,id', 'different:dosen_pembimbing_id'],
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
            'dosen_pembimbing_id_2' => $validated['dosen_pembimbing_id_2'] ? (int) $validated['dosen_pembimbing_id_2'] : null,
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

    public function destroySkPembimbing(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        if ($skripsi->sk_pembimbing_path) {
            Storage::disk('public')->delete($skripsi->sk_pembimbing_path);
        }

        $skripsi->update([
            'sk_pembimbing_path' => null,
            'sk_pembimbing_name' => null,
        ]);

        return back()->with('success', 'File SK pembimbing berhasil dihapus.');
    }

    public function resetPembimbing(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            $skripsi->loadMissing('mahasiswa');
            abort_unless((string) ($skripsi->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        if ($skripsi->sk_pembimbing_path) {
            Storage::disk('public')->delete($skripsi->sk_pembimbing_path);
        }

        $skripsi->update([
            'status' => $skripsi->approved_at ? 'approved' : 'pending',
            'dosen_pembimbing_id' => null,
            'dosen_pembimbing_id_2' => null,
            'nomor_sk' => null,
            'tanggal_sk' => null,
            'sk_pembimbing_path' => null,
            'sk_pembimbing_name' => null,
            'assigned_at' => null,
        ]);

        return back()->with('success', 'Pembimbing berhasil dihapus/reset.');
    }

    public function destroy(Request $request, SkripsiPengajuan $skripsi): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($skripsi->sk_pembimbing_path) {
            Storage::disk('public')->delete($skripsi->sk_pembimbing_path);
        }

        $skripsi->delete();

        return redirect()->route('admin.skripsi.index')->with('success', 'Data skripsi berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));

        $items = SkripsiPengajuan::query()->whereIn('id', $ids)->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Tidak ada data skripsi yang ditemukan untuk dihapus.');
        }

        foreach ($items as $it) {
            if ($it->sk_pembimbing_path) {
                Storage::disk('public')->delete($it->sk_pembimbing_path);
            }
            $it->delete();
        }

        return back()->with('success', 'Data skripsi terpilih berhasil dihapus.');
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

    private function authorizeSkripsiFileAccess(Request $request, SkripsiFile $file): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        $skripsi = $file->skripsi;
        abort_unless($skripsi, 404);

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isMahasiswa()) {
            abort_unless((int) $skripsi->mahasiswa_id === (int) ($user->mahasiswa?->id ?? 0), 404);
            return;
        }

        if ($user->isDosen()) {
            $dosenId = (int) ($user->dosen?->id ?? 0);
            $statusAkademik = (string) ($user->dosen?->status_akademik ?? '');
            $allowed = in_array($dosenId, [(int) $skripsi->dosen_pembimbing_id, (int) $skripsi->dosen_pembimbing_id_2], true)
                || in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);
            abort_unless($allowed, 403);
            return;
        }

        abort(403);
    }

    public function previewFile(Request $request, SkripsiFile $file): BinaryFileResponse
    {
        $this->authorizeSkripsiFileAccess($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $name = $file->file_name ?: basename($file->file_path);
        return response()->file(storage_path('app/public/'.$file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$name.'"',
        ]);
    }

    public function downloadFile(Request $request, SkripsiFile $file): BinaryFileResponse
    {
        $this->authorizeSkripsiFileAccess($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $name = $file->file_name ?: basename($file->file_path);
        return response()->download(storage_path('app/public/'.$file->file_path), $name);
    }

    public function destroyFile(Request $request, SkripsiFile $file): RedirectResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'File skripsi berhasil dihapus.');
    }
}
