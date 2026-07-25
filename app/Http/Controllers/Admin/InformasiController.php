<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Informasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InformasiController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $query = Informasi::query()->with('createdBy');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($status === 'aktif') {
            $query->where('is_aktif', true);
        } elseif ($status === 'nonaktif') {
            $query->where('is_aktif', false);
        }

        $items = $query->latest('id')->paginate(10)->withQueryString();

        return view('admin.informasi.index', [
            'items' => $items,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('admin.informasi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'is_aktif' => ['nullable', 'boolean'],
            'tanggal_aktif' => ['nullable', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after:tanggal_aktif'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_aktif' => (bool) ($validated['is_aktif'] ?? true),
            'tanggal_aktif' => $validated['tanggal_aktif'] ?? null,
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('gambar')) {
            $payload['gambar_path'] = $request->file('gambar')->store('informasi', 'public');
        }

        $informasi = Informasi::query()->create($payload);

        return redirect()->route('admin.informasi.edit', $informasi)->with('success', 'Informasi berhasil dibuat.');
    }

    public function edit(Informasi $informasi): View
    {
        return view('admin.informasi.edit', [
            'informasi' => $informasi,
        ]);
    }

    public function update(Request $request, Informasi $informasi): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'hapus_gambar' => ['nullable', 'boolean'],
            'is_aktif' => ['nullable', 'boolean'],
            'tanggal_aktif' => ['nullable', 'date'],
            'tanggal_kadaluarsa' => ['nullable', 'date', 'after:tanggal_aktif'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'is_aktif' => (bool) ($validated['is_aktif'] ?? $informasi->is_aktif),
            'tanggal_aktif' => $validated['tanggal_aktif'] ?? null,
            'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'] ?? null,
        ];

        if (!empty($validated['hapus_gambar']) && !empty($informasi->gambar_path)) {
            Storage::disk('public')->delete($informasi->gambar_path);
            $payload['gambar_path'] = null;
        }

        if ($request->hasFile('gambar')) {
            if (!empty($informasi->gambar_path)) {
                Storage::disk('public')->delete($informasi->gambar_path);
            }
            $payload['gambar_path'] = $request->file('gambar')->store('informasi', 'public');
        }

        $informasi->update($payload);

        return back()->with('success', 'Informasi berhasil diperbarui.');
    }

    public function destroy(Informasi $informasi): RedirectResponse
    {
        if (!empty($informasi->gambar_path)) {
            Storage::disk('public')->delete($informasi->gambar_path);
        }

        $informasi->delete();

        return back()->with('success', 'Informasi berhasil dihapus.');
    }

    public function toggleAktif(Informasi $informasi): RedirectResponse
    {
        $informasi->update([
            'is_aktif' => !$informasi->is_aktif,
        ]);

        return back()->with('success', 'Status informasi berhasil diubah.');
    }
}
