<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SkripsiFile;
use App\Models\SkripsiPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkripsiFileController extends Controller
{
    private function resolveSkripsi(Request $request): ?SkripsiPengajuan
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        return SkripsiPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('id')
            ->first();
    }

    public function index(Request $request): View
    {
        $skripsi = $this->resolveSkripsi($request);

        if ($skripsi) {
            $skripsi->load(['files' => fn ($q) => $q->orderByDesc('id')]);
        }

        return view('mahasiswa.skripsi.files', [
            'skripsi' => $skripsi,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $skripsi = $this->resolveSkripsi($request);
        abort_unless($skripsi, 404);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
            'keterangan' => ['required', 'string'],
        ]);

        $file = $validated['file'];
        $originalName = (string) $file->getClientOriginalName();
        $ext = (string) $file->getClientOriginalExtension();
        $filename = 'skripsi-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
        $path = $file->storeAs('skripsi/files/'.$skripsi->id, $filename, 'public');

        $skripsi->files()->create([
            'created_by_user_id' => $request->user()?->id,
            'file_path' => $path,
            'file_name' => $originalName,
            'keterangan' => $validated['keterangan'],
        ]);

        return back()->with('success', 'File skripsi berhasil diupload.');
    }

    private function authorizeFile(Request $request, SkripsiFile $file): void
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) ($file->skripsi?->mahasiswa_id ?? 0) === (int) $mahasiswa->id, 404);
    }

    public function download(Request $request, SkripsiFile $file): BinaryFileResponse
    {
        $this->authorizeFile($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $downloadName = $file->file_name ?: basename($file->file_path);
        return response()->download(storage_path('app/public/'.$file->file_path), $downloadName);
    }

    public function preview(Request $request, SkripsiFile $file): BinaryFileResponse
    {
        $this->authorizeFile($request, $file);
        abort_unless($file->file_path && Storage::disk('public')->exists($file->file_path), 404);

        $downloadName = $file->file_name ?: basename($file->file_path);
        return response()->file(storage_path('app/public/'.$file->file_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    public function destroy(Request $request, SkripsiFile $file): RedirectResponse
    {
        $this->authorizeFile($request, $file);

        if ($file->file_path) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        return back()->with('success', 'File skripsi berhasil dihapus.');
    }
}

