<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLaporan;
use App\Models\PplPengajuan;
use App\Models\SkripsiPengajuan;
use App\Models\Krs;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengajuanLaporanController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $items = PengajuanLaporan::query()
            ->with(['latestMessage.sender'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('mahasiswa.laporan.index', [
            'items' => $items,
        ]);
    }

    public function create(Request $request): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $pendingSkripsi = SkripsiPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $pendingPpl = PplPengajuan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'pending')
            ->orderByDesc('id')
            ->get();

        $pendingKrs = Krs::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status_approval', 'pending')
            ->orderByDesc('id')
            ->get();

        $pendingKhs = Khs::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderByDesc('id')
            ->get();

        return view('mahasiswa.laporan.create', [
            'pendingSkripsi' => $pendingSkripsi,
            'pendingPpl' => $pendingPpl,
            'pendingKrs' => $pendingKrs,
            'pendingKhs' => $pendingKhs,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);

        $validated = $request->validate([
            'jenis' => ['required', 'string', Rule::in(['skripsi', 'ppl', 'krs', 'khs'])],
            'pengajuan_id' => ['required', 'integer', 'min:1'],
            'judul' => ['required', 'string', 'max:255'],
            'pesan' => ['required', 'string'],
        ]);

        if ($validated['jenis'] === 'skripsi') {
            SkripsiPengajuan::query()
                ->where('id', (int) $validated['pengajuan_id'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'pending')
                ->firstOrFail();
        } elseif ($validated['jenis'] === 'ppl') {
            PplPengajuan::query()
                ->where('id', (int) $validated['pengajuan_id'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status', 'pending')
                ->firstOrFail();
        } elseif ($validated['jenis'] === 'krs') {
            Krs::query()
                ->where('id', (int) $validated['pengajuan_id'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->where('status_approval', 'pending')
                ->firstOrFail();
        } else {
            Khs::query()
                ->where('id', (int) $validated['pengajuan_id'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->firstOrFail();
        }

        $laporan = PengajuanLaporan::query()->create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis' => $validated['jenis'],
            'pengajuan_id' => (int) $validated['pengajuan_id'],
            'judul' => $validated['judul'],
            'status' => 'open',
            'last_message_at' => now(),
            'mahasiswa_last_read_at' => now(),
            'staff_last_read_at' => null,
        ]);

        $laporan->messages()->create([
            'sender_user_id' => $request->user()?->id,
            'pesan' => $validated['pesan'],
        ]);

        return redirect()
            ->route('mahasiswa.laporan.show', $laporan)
            ->with('success', 'Laporan berhasil dibuat. Silakan tunggu balasan Admin/Prodi.');
    }

    public function show(Request $request, PengajuanLaporan $laporan): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $laporan->mahasiswa_id === (int) $mahasiswa->id, 404);

        $laporan->load(['mahasiswa', 'messages.sender']);
        $laporan->update(['mahasiswa_last_read_at' => now()]);

        return view('mahasiswa.laporan.show', [
            'laporan' => $laporan,
        ]);
    }

    public function storeMessage(Request $request, PengajuanLaporan $laporan): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $laporan->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless($laporan->status === 'open', 403);

        $validated = $request->validate([
            'pesan' => ['required', 'string'],
        ]);

        $laporan->messages()->create([
            'sender_user_id' => $request->user()?->id,
            'pesan' => $validated['pesan'],
        ]);

        $laporan->update([
            'last_message_at' => now(),
            'mahasiswa_last_read_at' => now(),
            'staff_last_read_at' => null,
        ]);

        return back()->with('success', 'Pesan laporan terkirim.');
    }
}

