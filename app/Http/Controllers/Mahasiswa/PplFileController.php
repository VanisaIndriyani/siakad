<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplFile;
use App\Models\PplPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PplFileController extends Controller
{
    private function resolvePpl(Request $request): ?PplPengajuan
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        return PplPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('id')
            ->first();
    }

    public function index(Request $request): View
    {
        $ppl = $this->resolvePpl($request);

        if ($ppl) {
            $ppl->load(['files' => fn ($q) => $q->with('creator')->orderByDesc('id')]);
        }

        return view('mahasiswa.ppl.files', [
            'ppl' => $ppl,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ppl = $this->resolvePpl($request);
        abort_unless($ppl, 404);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
            'keterangan' => ['required', 'string'],
        ]);

        $file = $validated['file'];
        $originalName = (string) $file->getClientOriginalName();
        $ext = (string) $file->getClientOriginalExtension();
        $filename = 'laporan-ppl-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
        $path = $file->storeAs('ppl/laporan/'.$ppl->id, $filename, 'public');

        $ppl->files()->create([
            'created_by_user_id' => $request->user()?->id,
            'file_path' => $path,
            'file_name' => $originalName,
            'keterangan' => $validated['keterangan'],
        ]);

        return back()->with('success', 'Laporan PPL berhasil diupload.');
    }

    private function authorizeFile(Request $request, PplFile $file): void
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) ($file->ppl?->mahasiswa_id ?? 0) === (int) $mahasiswa->id, 404);
    }

    public function download(Request $request, PplFile $file): BinaryFileResponse
    {
        $this->authorizeFile($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $downloadName = $file->file_name ?: basename($file->file_path);
        return response()->download(storage_path('app/public/'.$file->file_path), $downloadName);
    }

    public function preview(Request $request, PplFile $file): BinaryFileResponse
    {
        $this->authorizeFile($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $downloadName = $file->file_name ?: basename($file->file_path);
        return response()->file(storage_path('app/public/'.$file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    public function destroy(Request $request, PplFile $file): RedirectResponse
    {
        $this->authorizeFile($request, $file);

        // Mahasiswa hanya bisa hapus file yang mereka upload sendiri
        if ((int) $file->created_by_user_id !== (int) $request->user()?->id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus file ini.');
        }

        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'Laporan PPL berhasil dihapus.');
    }
}

