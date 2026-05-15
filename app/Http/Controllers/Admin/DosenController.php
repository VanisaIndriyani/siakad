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
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DosenController extends Controller
{
    private const STATUS_AKADEMIK = [
        'Dosen',
        'Ketua Prodi',
        'Sekretaris Prodi',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = Dosen::query()->with('user');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%")
                    ->orWhere('nomor_sk', 'like', "%{$q}%")
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
            'nidn' => ['nullable', 'string', 'max:50', 'unique:dosen,nidn'],
            'nik' => ['required', 'string', 'max:50', 'unique:dosen,nik'],
            'nuptk' => ['nullable', 'string', 'max:50', 'unique:dosen,nuptk'],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'status_akademik' => ['nullable', 'string', Rule::in(self::STATUS_AKADEMIK)],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $identifier = (string) ($validated['nidn'] ?? $validated['nik']);
        $emailBase = Str::lower(preg_replace('/\s+/', '', $identifier)).'@kampus.ac.id';
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
            'nik' => $validated['nik'] ?? null,
            'nidn' => $validated['nidn'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'nomor_sk' => $validated['nomor_sk'] ?? null,
            'program_studi' => $validated['program_studi'] ?? null,
            'status_akademik' => $validated['status_akademik'] ?? 'Dosen',
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
            'nidn' => ['nullable', 'string', 'max:50', 'unique:dosen,nidn,'.$dosen->id],
            'nik' => ['required', 'string', 'max:50', 'unique:dosen,nik,'.$dosen->id],
            'nuptk' => ['nullable', 'string', 'max:50', 'unique:dosen,nuptk,'.$dosen->id],
            'nomor_sk' => ['nullable', 'string', 'max:255'],
            'program_studi' => ['nullable', 'string', 'max:255'],
            'status_akademik' => ['nullable', 'string', Rule::in(self::STATUS_AKADEMIK)],
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
            'nik' => $validated['nik'],
            'nidn' => $validated['nidn'] ?? $dosen->nidn,
            'nuptk' => $validated['nuptk'] ?? $dosen->nuptk,
            'nomor_sk' => $validated['nomor_sk'] ?? $dosen->nomor_sk,
            'program_studi' => $validated['program_studi'] ?? $dosen->program_studi,
            'status_akademik' => $validated['status_akademik'] ?? ($dosen->status_akademik ?: 'Dosen'),
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
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%")
                    ->orWhere('nomor_sk', 'like', "%{$q}%")
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
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('nidn', 'like', "%{$q}%")
                    ->orWhere('nuptk', 'like', "%{$q}%")
                    ->orWhere('nomor_sk', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah', 'like', "%{$q}%");
            });
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dosen');

        $headers = ['Nama', 'NIK', 'NIDN', 'NUPTK', 'Nomor SK', 'Nomor HP', 'Mata Kuliah'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue([$col + 1, 1], $label);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue([1, $rowIndex], $row->nama);
            $sheet->setCellValue([2, $rowIndex], $row->nik);
            $sheet->setCellValue([3, $rowIndex], $row->nidn);
            $sheet->setCellValue([4, $rowIndex], $row->nuptk);
            $sheet->setCellValue([5, $rowIndex], $row->nomor_sk);
            $sheet->setCellValue([6, $rowIndex], $row->nomor_hp);
            $sheet->setCellValue([7, $rowIndex], $row->mata_kuliah);
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
