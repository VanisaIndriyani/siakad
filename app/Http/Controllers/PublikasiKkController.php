<?php

namespace App\Http\Controllers;

use App\Models\PublikasiKk;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PublikasiKkController extends Controller
{
    private const KATEGORI = [
        'Penelitian',
        'PKM',
        'HAKI',
        'Buku',
        'Sertifikat',
        'Opini',
        'SK',
    ];

    private function resolveRoutePrefix(Request $request): string
    {
        return $request->is('admin/*') ? 'admin' : ($request->is('mahasiswa/*') ? 'mahasiswa' : 'dosen');
    }

    private function canManageAll(Request $request): bool
    {
        return (bool) $request->user()?->isStaffAkademik();
    }

    private function authorizePublikasiAccess(Request $request, PublikasiKk $publikasiKk): void
    {
        if ($this->canManageAll($request)) {
            return;
        }

        abort_unless((int) $publikasiKk->user_id === (int) $request->user()?->id, 403);
    }

    public function index(Request $request)
    {
        $routePrefix = $this->resolveRoutePrefix($request);
        
        $query = PublikasiKk::with('user')->orderByDesc('created_at');

        if (! $this->canManageAll($request)) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        if ($request->has('kategori') && $request->get('kategori') != '') {
            $query->where('kategori', $request->get('kategori'));
        }

        $items = $query->paginate(10)->withQueryString();

        $kategoriList = self::KATEGORI;

        return view('publikasi-kk.index', compact('items', 'routePrefix', 'kategoriList'));
    }

    public function create(Request $request)
    {
        $routePrefix = $this->resolveRoutePrefix($request);
        $kategoriList = self::KATEGORI;

        return view('publikasi-kk.create', compact('routePrefix', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $routePrefix = $this->resolveRoutePrefix($request);
        $validated = $request->validate([
            'penulis' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kategori' => 'required|in:' . implode(',', self::KATEGORI),
            'tahun_terbit' => 'required|numeric|digits:4',
            'reputasi' => 'required|in:Internasional,Regional,Nasional,tidakbersinta',
            'url_link' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_path'] = $file->store('publikasi-kk', 'public');
        }

        $validated['user_id'] = Auth::id();
        PublikasiKk::create($validated);

        return redirect()->route($routePrefix . '.publikasi.index')->with('success', 'Data publikasi berhasil ditambahkan.');
    }

    public function edit(Request $request, PublikasiKk $publikasiKk)
    {
        $this->authorizePublikasiAccess($request, $publikasiKk);

        $routePrefix = $this->resolveRoutePrefix($request);
        $kategoriList = self::KATEGORI;

        return view('publikasi-kk.edit', compact('publikasiKk', 'routePrefix', 'kategoriList'));
    }

    public function update(Request $request, PublikasiKk $publikasiKk)
    {
        $this->authorizePublikasiAccess($request, $publikasiKk);

        $routePrefix = $this->resolveRoutePrefix($request);
        $validated = $request->validate([
            'penulis' => 'required|string|max:255',
            'judul' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'kategori' => 'required|in:' . implode(',', self::KATEGORI),
            'tahun_terbit' => 'required|numeric|digits:4',
            'reputasi' => 'required|in:Internasional,Regional,Nasional,tidakbersinta',
            'url_link' => 'nullable|url',
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

        return redirect()->route($routePrefix . '.publikasi.index')->with('success', 'Data publikasi berhasil diperbarui.');
    }

    public function destroy(Request $request, PublikasiKk $publikasiKk)
    {
        $this->authorizePublikasiAccess($request, $publikasiKk);

        $routePrefix = $this->resolveRoutePrefix($request);
        if ($publikasiKk->file_path) {
            Storage::disk('public')->delete($publikasiKk->file_path);
        }
        $publikasiKk->delete();

        return redirect()->route($routePrefix . '.publikasi.index')->with('success', 'Data publikasi berhasil dihapus.');
    }

    public function download(Request $request, PublikasiKk $publikasiKk)
    {
        $this->authorizePublikasiAccess($request, $publikasiKk);

        return Storage::disk('public')->download($publikasiKk->file_path, $publikasiKk->file_name);
    }

    public function exportExcel(Request $request)
    {
        $query = PublikasiKk::query();

        if (! $this->canManageAll($request)) {
            $query->where('user_id', $request->user()->id);
        }

        if ($request->has('kategori') && $request->get('kategori') != '') {
            $query->where('kategori', $request->get('kategori'));
        }

        if ($request->has('search') && $request->get('search') != '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        $items = $query->orderByDesc('created_at')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Header
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Judul');
        $sheet->setCellValue('C1', 'Penulis');
        $sheet->setCellValue('D1', 'Penerbit');
        $sheet->setCellValue('E1', 'Kategori');
        $sheet->setCellValue('F1', 'Tahun Terbit');
        $sheet->setCellValue('G1', 'Reputasi');
        $sheet->setCellValue('H1', 'URL Link');

        $rowNum = 2;
        foreach ($items as $index => $item) {
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $item->judul);
            $sheet->setCellValue('C' . $rowNum, $item->penulis);
            $sheet->setCellValue('D' . $rowNum, $item->penerbit);
            $sheet->setCellValue('E' . $rowNum, $item->kategori);
            $sheet->setCellValue('F' . $rowNum, $item->tahun_terbit);
            $sheet->setCellValue('G' . $rowNum, $item->reputasi);
            $sheet->setCellValue('H' . $rowNum, $item->url_link);
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Publikasi_' . date('Y-m-d_H-i-s') . '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), 'excel');
        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
