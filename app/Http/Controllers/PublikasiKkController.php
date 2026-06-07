<?php

namespace App\Http\Controllers;

use App\Models\PublikasiKk;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PublikasiKkController extends Controller
{
    public function index(Request $request)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        $items = PublikasiKk::with('user')->orderByDesc('created_at')->paginate(10);

        return view('publikasi-kk.index', compact('items', 'routePrefix'));
    }

    public function create(Request $request)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        return view('publikasi-kk.create', compact('routePrefix'));
    }

    public function store(Request $request)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        $validated = $request->validate([
            'penulis' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kategori' => 'required|in:Penelitian,PKM,HAKI,Buku',
            'tahun_terbit' => 'required|numeric|digits:4',
            'reputasi' => 'required|in:Internasional,Nasional,tidakbersinta',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_path'] = $file->store('publikasi-kk', 'public');
        }

        $validated['user_id'] = Auth::id();
        PublikasiKk::create($validated);

        return redirect()->route($routePrefix . '.publikasi-kk.index')->with('success', 'Data publikasi berhasil ditambahkan.');
    }

    public function edit(Request $request, PublikasiKk $publikasiKk)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        return view('publikasi-kk.edit', compact('publikasiKk', 'routePrefix'));
    }

    public function update(Request $request, PublikasiKk $publikasiKk)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        $validated = $request->validate([
            'penulis' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kategori' => 'required|in:Penelitian,PKM,HAKI,Buku',
            'tahun_terbit' => 'required|numeric|digits:4',
            'reputasi' => 'required|in:Internasional,Nasional,tidakbersinta',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file')) {
            if ($publikasiKk->file_path) {
                Storage::disk('public')->delete($publikasiKk->file_path);
            }
            $file = $request->file('file');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_path'] = $file->store('publikasi-kk', 'public');
        }

        $publikasiKk->update($validated);

        return redirect()->route($routePrefix . '.publikasi-kk.index')->with('success', 'Data publikasi berhasil diperbarui.');
    }

    public function destroy(Request $request, PublikasiKk $publikasiKk)
    {
        $routePrefix = $request->is('admin/*') ? 'admin' : 'dosen';
        if ($publikasiKk->file_path) {
            Storage::disk('public')->delete($publikasiKk->file_path);
        }
        $publikasiKk->delete();

        return redirect()->route($routePrefix . '.publikasi-kk.index')->with('success', 'Data publikasi berhasil dihapus.');
    }

    public function download(PublikasiKk $publikasiKk)
    {
        return Storage::disk('public')->download($publikasiKk->file_path, $publikasiKk->file_name);
    }
}
