<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $query = Pembayaran::query()->with('mahasiswa');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }

        $pembayarans = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('keuangan.pembayaran.index', compact('pembayarans', 'q'));
    }

    public function create(): View
    {
        $mahasiswa = Mahasiswa::query()->orderBy('nama_lengkap')->get();
        return view('keuangan.pembayaran.create', compact('mahasiswa'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'exists:mahasiswa,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_ajaran' => ['required', 'string'],
            'total_biaya' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'jumlah_bayar' => ['nullable', 'numeric', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:2048'],
        ]);

        $pembayaran = Pembayaran::create([
            'mahasiswa_id' => $validated['mahasiswa_id'],
            'semester' => $validated['semester'],
            'tahun_ajaran' => $validated['tahun_ajaran'],
            'total_biaya' => $validated['total_biaya'],
            'catatan' => $validated['catatan'],
        ]);

        $jumlahBayar = $validated['jumlah_bayar'] ?? 0;
        
        if ($jumlahBayar > 0) {
            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');
            }

            $pembayaran->details()->create([
                'jumlah_bayar' => $jumlahBayar,
                'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
                'bukti_pembayaran' => $buktiPath,
                'keterangan' => 'Pembayaran awal',
            ]);
        }

        $pembayaran->updateStatus();

        return redirect()->route('keuangan.pembayaran.index')->with('success', 'Data pembayaran berhasil ditambahkan.');
    }

    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['mahasiswa', 'details' => function($q) {
            $q->orderByDesc('tanggal_bayar');
        }]);
        return view('keuangan.pembayaran.show', compact('pembayaran'));
    }

    public function addCicilan(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');
        }

        $pembayaran->details()->create([
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
            'bukti_pembayaran' => $buktiPath,
            'keterangan' => $validated['keterangan'],
        ]);

        $pembayaran->updateStatus();

        return redirect()->back()->with('success', 'Pembayaran cicilan berhasil ditambahkan.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        foreach ($pembayaran->details as $detail) {
            if ($detail->bukti_pembayaran) {
                Storage::disk('public')->delete($detail->bukti_pembayaran);
            }
        }
        $pembayaran->delete();
        return redirect()->route('keuangan.pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }
}
