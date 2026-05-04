<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\User;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DosenController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = Dosen::query()->with('user');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah', 'like', "%{$q}%");
            });
        }

        $dosen = $query->orderByDesc('id')->paginate(10)->withQueryString();

        if ($request->boolean('partial')) {
            return view('admin.dosen.partials.table', [
                'dosen' => $dosen,
            ]);
        }

        return view('admin.dosen.index', [
            'dosen' => $dosen,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.dosen.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nidn' => ['required', 'string', 'max:50', 'unique:dosen,nidn'],
            'alamat' => ['nullable', 'string'],
            'nomor_hp' => ['nullable', 'string', 'max:50'],
            'mata_kuliah' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $emailBase = Str::lower(preg_replace('/\s+/', '', $validated['nidn'])).'@kampus.ac.id';
        $email = $emailBase;
        $i = 1;
        while (User::query()->where('email', $email)->exists()) {
            $email = Str::before($emailBase, '@')."+{$i}@kampus.ac.id";
            $i++;
        }

        $user = User::query()->create([
            'name' => $validated['nama'],
            'email' => $email,
            'role' => User::ROLE_DOSEN,
            'password' => Hash::make('password'),
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('photos/dosen', 'public');
        }

        $dosen = Dosen::query()->create([
            'user_id' => $user->id,
            'nama' => $validated['nama'],
            'nidn' => $validated['nidn'],
            'alamat' => $validated['alamat'] ?? null,
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'mata_kuliah' => $validated['mata_kuliah'] ?? null,
            'foto_path' => $fotoPath,
        ]);

        return redirect()->route('admin.dosen.show', $dosen)->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function show(Dosen $dosen): View
    {
        $dosen->load('user');

        return view('admin.dosen.show', [
            'dosen' => $dosen,
        ]);
    }

    public function edit(Dosen $dosen): View
    {
        return view('admin.dosen.edit', [
            'dosen' => $dosen,
        ]);
    }

    public function update(Request $request, Dosen $dosen): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nidn' => ['required', 'string', 'max:50', 'unique:dosen,nidn,'.$dosen->id],
            'alamat' => ['nullable', 'string'],
            'nomor_hp' => ['nullable', 'string', 'max:50'],
            'mata_kuliah' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($dosen->foto_path) {
                Storage::disk('public')->delete($dosen->foto_path);
            }
            $dosen->foto_path = $request->file('foto')->store('photos/dosen', 'public');
        }

        $dosen->fill([
            'nama' => $validated['nama'],
            'nidn' => $validated['nidn'],
            'alamat' => $validated['alamat'] ?? null,
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'mata_kuliah' => $validated['mata_kuliah'] ?? null,
        ])->save();

        $dosen->user?->update(['name' => $validated['nama']]);

        return redirect()->route('admin.dosen.show', $dosen)->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(Dosen $dosen): RedirectResponse
    {
        $user = $dosen->user;

        if ($dosen->foto_path) {
            Storage::disk('public')->delete($dosen->foto_path);
        }

        if ($user) {
            $user->delete();
        } else {
            $dosen->delete();
        }

        return redirect()->route('admin.dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = Dosen::query()->with('user')->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah', 'like', "%{$q}%");
            });
        }

        $rows = $query->get();
        $html = view('admin.dosen.export-pdf', ['rows' => $rows])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="dosen.pdf"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = Dosen::query()->with('user')->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah', 'like', "%{$q}%");
            });
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dosen');

        $headers = ['Nama', 'NIDN', 'Nomor HP', 'Mata Kuliah'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $label);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $rowIndex, $row->nama);
            $sheet->setCellValueByColumnAndRow(2, $rowIndex, $row->nidn);
            $sheet->setCellValueByColumnAndRow(3, $rowIndex, $row->nomor_hp);
            $sheet->setCellValueByColumnAndRow(4, $rowIndex, $row->mata_kuliah);
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'dosen.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
