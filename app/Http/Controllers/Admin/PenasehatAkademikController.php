<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PenasehatAkademikController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isStaffAkademik()) {
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

        $query = Mahasiswa::query()->with(['user', 'dosenPenasehat']);

        if ($context['programStudi']) {
            $programStudi = $context['programStudi'];
            $query->where('program_studi', $programStudi);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $items = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('admin.penasehat-akademik.index', [
            'items' => $items,
            'q' => $q,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function show(Request $request, Mahasiswa $mahasiswa): View
    {
        $context = $this->resolveContext($request);

        if ($context['programStudi']) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        $mahasiswa->load(['user', 'dosenPenasehat', 'bimbinganAkademikMessages.sender']);

        $dosenList = $context['canAssign']
            ? Dosen::query()->when($context['programStudi'], function ($q) use ($context) {
                $q->where('program_studi', $context['programStudi']);
            })->orderBy('nama')->get()
            : collect();

        return view('admin.penasehat-akademik.show', [
            'mahasiswa' => $mahasiswa,
            'dosenList' => $dosenList,
            'routePrefix' => $context['routePrefix'],
            'canAssign' => $context['canAssign'],
        ]);
    }

    public function assign(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        $validated = $request->validate([
            'dosen_penasehat_id' => ['required', 'exists:dosen,id'],
            'nomor_sk_penasehat' => ['nullable', 'string', 'max:255'],
            'tanggal_sk_penasehat' => ['nullable', 'date'],
            'sk_penasehat_file' => ['nullable', 'file', 'max:10240', 'mimes:pdf'],
        ]);

        $skPath = $mahasiswa->sk_penasehat_path;
        $skName = $mahasiswa->sk_penasehat_name;

        if (! empty($validated['sk_penasehat_file'])) {
            $file = $validated['sk_penasehat_file'];
            $originalName = (string) $file->getClientOriginalName();
            $ext = (string) $file->getClientOriginalExtension();
            $filename = 'sk-penasehat-' . now()->format('YmdHis') . '-' . Str::random(8) . ($ext !== '' ? '.' . $ext : '');
            $path = $file->storeAs('penasehat-akademik/sk/' . $mahasiswa->id, $filename, 'public');

            if ($mahasiswa->sk_penasehat_path) {
                Storage::disk('public')->delete($mahasiswa->sk_penasehat_path);
            }

            $skPath = $path;
            $skName = $originalName;
        }

        $mahasiswa->update([
            'dosen_penasehat_id' => (int) $validated['dosen_penasehat_id'],
            'nomor_sk_penasehat' => $validated['nomor_sk_penasehat'] ?? null,
            'tanggal_sk_penasehat' => $validated['tanggal_sk_penasehat'] ?? null,
            'sk_penasehat_path' => $skPath,
            'sk_penasehat_name' => $skName,
        ]);

        return back()->with('success', 'Dosen penasehat akademik berhasil ditetapkan.');
    }

    public function destroySkPenasehat(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        if ($mahasiswa->sk_penasehat_path) {
            Storage::disk('public')->delete($mahasiswa->sk_penasehat_path);
        }

        $mahasiswa->update([
            'sk_penasehat_path' => null,
            'sk_penasehat_name' => null,
        ]);

        return back()->with('success', 'File SK penasehat berhasil dihapus.');
    }

    public function resetPenasehat(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $context = $this->resolveContext($request);
        abort_unless($context['canAssign'], 403);

        if ($context['programStudi']) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        if ($mahasiswa->sk_penasehat_path) {
            Storage::disk('public')->delete($mahasiswa->sk_penasehat_path);
        }

        $mahasiswa->update([
            'dosen_penasehat_id' => null,
            'nomor_sk_penasehat' => null,
            'tanggal_sk_penasehat' => null,
            'sk_penasehat_path' => null,
            'sk_penasehat_name' => null,
        ]);

        return back()->with('success', 'Penasehat berhasil dihapus/reset.');
    }

    public function sendMessage(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $context = $this->resolveContext($request);

        if ($context['programStudi']) {
            abort_unless((string) ($mahasiswa->program_studi ?? '') === $context['programStudi'], 403);
        }

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:1000'],
        ]);

        $mahasiswa->bimbinganAkademikMessages()->create([
            'sender_user_id' => $request->user()->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function downloadSkPenasehat(Request $request, Mahasiswa $mahasiswa): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $mahasiswa->id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isDosen()) {
            $dosen = $user->dosen;
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');
            $isPenasehat = (int) ($mahasiswa->dosen_penasehat_id ?? 0) === (int) ($dosen?->id ?? 0);
            $isProdiApprover = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true) &&
                (string) ($mahasiswa->program_studi ?? '') === (string) ($dosen?->program_studi ?? '');
            abort_unless($isPenasehat || $isProdiApprover, 403);
        } elseif (! $user->isStaffAkademik()) {
            abort(403);
        }

        abort_unless($mahasiswa->sk_penasehat_path, 404);
        abort_unless(Storage::disk('public')->exists($mahasiswa->sk_penasehat_path), 404);

        $downloadName = $mahasiswa->sk_penasehat_name ?? basename($mahasiswa->sk_penasehat_path);
        return response()->download(storage_path('app/public/' . $mahasiswa->sk_penasehat_path), $downloadName);
    }

    public function previewSkPenasehat(Request $request, Mahasiswa $mahasiswa): BinaryFileResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->isMahasiswa()) {
            abort_unless((int) $mahasiswa->id === (int) ($user->mahasiswa?->id ?? 0), 404);
        } elseif ($user->isDosen()) {
            $dosen = $user->dosen;
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');
            $isPenasehat = (int) ($mahasiswa->dosen_penasehat_id ?? 0) === (int) ($dosen?->id ?? 0);
            $isProdiApprover = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true) &&
                (string) ($mahasiswa->program_studi ?? '') === (string) ($dosen?->program_studi ?? '');
            abort_unless($isPenasehat || $isProdiApprover, 403);
        } elseif (! $user->isStaffAkademik()) {
            abort(403);
        }

        abort_unless($mahasiswa->sk_penasehat_path, 404);
        abort_unless(Storage::disk('public')->exists($mahasiswa->sk_penasehat_path), 404);

        $downloadName = $mahasiswa->sk_penasehat_name ?? basename($mahasiswa->sk_penasehat_path);
        return response()->file(storage_path('app/public/' . $mahasiswa->sk_penasehat_path), [
            'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
        ]);
    }
}
