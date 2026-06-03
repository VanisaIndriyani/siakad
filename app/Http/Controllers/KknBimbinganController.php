<?php

namespace App\Http\Controllers;

use App\Models\KknBimbinganMessage;
use App\Models\KknFile;
use App\Models\KknPosko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KknBimbinganController extends Controller
{
    private function checkAccess(Request $request, KknPosko $posko): void
    {
        $user = $request->user();
        if ($user->isAdmin()) return;

        if ($user->isDosen()) {
            $isAssigned = $posko->pembimbingS()->where('dosen_id', $user->dosen?->id)->exists();
            abort_unless($isAssigned, 403);
            return;
        }

        if ($user->isMahasiswa()) {
            $isMember = $posko->pengajuans()->where('mahasiswa_id', $user->mahasiswa?->id)->exists();
            abort_unless($isMember, 403);
            return;
        }

        abort(403);
    }

    public function sendMessage(Request $request, KknPosko $posko): RedirectResponse
    {
        $this->checkAccess($request, $posko);

        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        KknBimbinganMessage::query()->create([
            'kkn_posko_id' => $posko->id,
            'sender_user_id' => $request->user()->id,
            'pesan' => $validated['pesan'],
        ]);

        return back()->with('success', 'Pesan terkirim.');
    }

    public function uploadFile(Request $request, KknPosko $posko): RedirectResponse
    {
        $this->checkAccess($request, $posko);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480'], // 20MB
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('kkn/files/'.$posko->id, 'public');

        KknFile::query()->create([
            'kkn_posko_id' => $posko->id,
            'user_id' => $request->user()->id,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'keterangan' => $validated['keterangan'],
        ]);

        return back()->with('success', 'File berhasil diunggah.');
    }

    public function deleteFile(Request $request, KknFile $file): RedirectResponse
    {
        $user = $request->user();
        // Only uploader or admin can delete
        abort_unless($user->isAdmin() || (int) $file->user_id === (int) $user->id, 403);

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }

    public function previewFile(Request $request, KknFile $file)
    {
        $this->checkAccess($request, $file->posko);
        return response()->file(storage_path('app/public/'.$file->file_path));
    }

    public function downloadFile(Request $request, KknFile $file)
    {
        $this->checkAccess($request, $file->posko);
        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}
