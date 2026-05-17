<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLaporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengajuanLaporanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', 'open'));

        $query = PengajuanLaporan::query()->with(['mahasiswa', 'latestMessage.sender']);

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhereHas('mahasiswa', function ($sub2) use ($q) {
                        $sub2->where('nama_lengkap', 'like', "%{$q}%")
                            ->orWhere('npm', 'like', "%{$q}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        $items = $query
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan.index', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function show(Request $request, PengajuanLaporan $laporan): View
    {
        $laporan->load(['mahasiswa', 'messages.sender']);
        $laporan->update(['staff_last_read_at' => now()]);

        return view('admin.laporan.show', [
            'laporan' => $laporan,
        ]);
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required'],
        ]);

        $raw = $validated['ids'];
        $ids = is_array($raw) ? $raw : preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY);
        
        PengajuanLaporan::whereIn('id', $ids)->delete();

        return back()->with('success', 'Laporan terpilih berhasil dihapus.');
    }

    public function storeMessage(Request $request, PengajuanLaporan $laporan): RedirectResponse
    {
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
            'staff_last_read_at' => now(),
            'mahasiswa_last_read_at' => null,
        ]);

        return back()->with('success', 'Balasan terkirim.');
    }
}
