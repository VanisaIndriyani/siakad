<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\SkripsiPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SkripsiController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $items = SkripsiPengajuan::query()
            ->with(['dosenPembimbing', 'latestMessage'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('id')
            ->get();

        return view('mahasiswa.skripsi.index', [
            'items' => $items,
        ]);
    }

    public function create(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        return view('mahasiswa.skripsi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        SkripsiPengajuan::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.skripsi.index')->with('success', 'Pengajuan judul skripsi berhasil dikirim ke Admin/Prodi.');
    }

    public function show(Request $request, SkripsiPengajuan $skripsi): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $skripsi->mahasiswa_id === (int) $mahasiswa->id, 404);

        $skripsi->load(['dosenPembimbing', 'messages.sender', 'mahasiswa']);

        return view('mahasiswa.skripsi.show', [
            'skripsi' => $skripsi,
        ]);
    }
}
