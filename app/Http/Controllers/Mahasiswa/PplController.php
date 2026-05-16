<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplPengajuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $items = PplPengajuan::query()
            ->with(['dosenPembimbing', 'dosenPembimbing2', 'latestMessage'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('id')
            ->get();

        return view('mahasiswa.ppl.index', [
            'items' => $items,
        ]);
    }

    public function create(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        return view('mahasiswa.ppl.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $validated = $request->validate([
            'instansi_nama' => ['required', 'string', 'max:255'],
            'instansi_alamat' => ['nullable', 'string'],
            'keterangan' => ['nullable', 'string'],
        ]);

        PplPengajuan::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'instansi_nama' => $validated['instansi_nama'],
            'instansi_alamat' => $validated['instansi_alamat'] ?: null,
            'keterangan' => $validated['keterangan'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.ppl.index')->with('success', 'Pengajuan instansi/sekolah PPL berhasil dikirim ke Admin/Prodi.');
    }

    public function show(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['dosenPembimbing', 'dosenPembimbing2', 'messages.sender', 'mahasiswa']);

        return view('mahasiswa.ppl.show', [
            'ppl' => $ppl,
        ]);
    }
}

