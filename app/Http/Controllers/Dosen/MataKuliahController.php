<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MataKuliahController extends Controller
{
    private function authorizeMataKuliah(Request $request, MataKuliah $mataKuliah): void
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);
        abort_unless(in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true), 403);
    }

    public function index(Request $request): View
    {
        $dosen = $request->user()?->dosen;
        abort_unless($dosen, 403);

        $items = MataKuliah::query()
            ->where(function ($q) use ($dosen) {
                $q->where('dosen_id', $dosen->id)->orWhere('dosen_id_2', $dosen->id);
            })
            ->orderBy('semester')
            ->orderBy('kode')
            ->get();

        return view('dosen.mata-kuliah.index', [
            'items' => $items,
        ]);
    }

    public function uploadRps(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);

        $validated = $request->validate([
            'rps_dosen' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
        ]);

        $file = $validated['rps_dosen'];
        $originalName = (string) $file->getClientOriginalName();
        $ext = (string) $file->getClientOriginalExtension();
        $filename = 'rps-dosen-'.now()->format('YmdHis').'-'.Str::random(8).($ext !== '' ? '.'.$ext : '');
        $path = $file->storeAs('rps/dosen/'.$mataKuliah->id, $filename, 'public');

        if ($mataKuliah->rps_dosen_path) {
            Storage::disk('public')->delete($mataKuliah->rps_dosen_path);
        }

        $mataKuliah->update([
            'rps_dosen_path' => $path,
            'rps_dosen_name' => $originalName,
        ]);

        return back()->with('success', 'RPS dosen berhasil diupload.');
    }

    public function destroyRps(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);

        if ($mataKuliah->rps_dosen_path) {
            Storage::disk('public')->delete($mataKuliah->rps_dosen_path);
        }

        $mataKuliah->update([
            'rps_dosen_path' => null,
            'rps_dosen_name' => null,
        ]);

        return back()->with('success', 'RPS dosen berhasil dihapus.');
    }

    public function downloadRpsAdmin(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);
        abort_unless($mataKuliah->rps_admin_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_admin_path), 404);

        $downloadName = $mataKuliah->rps_admin_name ?: basename($mataKuliah->rps_admin_path);
        return response()->download(storage_path('app/public/'.$mataKuliah->rps_admin_path), $downloadName);
    }

    public function previewRpsAdmin(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);
        abort_unless($mataKuliah->rps_admin_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_admin_path), 404);

        $downloadName = $mataKuliah->rps_admin_name ?: basename($mataKuliah->rps_admin_path);
        return response()->file(storage_path('app/public/'.$mataKuliah->rps_admin_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }

    public function downloadRpsDosen(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);
        abort_unless($mataKuliah->rps_dosen_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_dosen_path), 404);

        $downloadName = $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path);
        return response()->download(storage_path('app/public/'.$mataKuliah->rps_dosen_path), $downloadName);
    }

    public function previewRpsDosen(Request $request, MataKuliah $mataKuliah): BinaryFileResponse
    {
        $this->authorizeMataKuliah($request, $mataKuliah);
        abort_unless($mataKuliah->rps_dosen_path, 404);
        abort_unless(Storage::disk('public')->exists($mataKuliah->rps_dosen_path), 404);

        $downloadName = $mataKuliah->rps_dosen_name ?: basename($mataKuliah->rps_dosen_path);
        return response()->file(storage_path('app/public/'.$mataKuliah->rps_dosen_path), [
            'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
        ]);
    }
}
