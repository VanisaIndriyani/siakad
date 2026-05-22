<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\Mahasiswa;
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

class MahasiswaController extends Controller
{
    private const JURUSAN = [
        'Pendidikan Agama Islam',
        'Pendidikan Islam Anak Usia Dini',
        'Hukum Keluarga Islam',
        'Hukum Tata Negara',
        'Perbankan Syariah',
        'Ekonomi Syariah',
    ];

    private const FAKULTAS = [
        'Fakultas Tarbiyah & Keguruan',
        'Fakultas Syariah & Hukum',
        'Fakultas Ekonomi & Bisnis Islam',
    ];

    private function applyFilters($query, Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));
        $prodi = trim((string) $request->get('prodi', ''));
        $semester = trim((string) $request->get('semester', ''));

        if ($status !== '') {
            $query->where('status_mahasiswa', $status);
        }

        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }

        if ($prodi !== '') {
            $query->where('program_studi', $prodi);
        }

        if ($semester !== '') {
            $query->whereHas('krs', function ($sub) use ($semester) {
                $sub->where('semester', $semester);
            });
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('program_studi', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        return $query;
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));
        $prodi = trim((string) $request->get('prodi', ''));
        $semester = trim((string) $request->get('semester', ''));

        $query = Mahasiswa::query()->with('user');
        $query = $this->applyFilters($query, $request);

        $mahasiswa = $query->orderByDesc('id')->paginate(10)->withQueryString();

        if ($request->boolean('partial')) {
            return view('admin.mahasiswa.partials.table', [
                'mahasiswa' => $mahasiswa,
            ]);
        }

        $angkatanList = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->sortDesc()
            ->values()
            ->all();

        return view('admin.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'q' => $q,
            'status' => $status ?: null,
            'angkatan' => $angkatan,
            'prodi' => $prodi,
            'semester' => $semester,
            'angkatanList' => $angkatanList,
            'prodiList' => self::JURUSAN,
        ]);
    }

    public function create(): View
    {
        return view('admin.mahasiswa.create', [
            'jurusan' => self::JURUSAN,
            'fakultasList' => self::FAKULTAS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:50', 'unique:mahasiswa,npm'],
            'program_studi' => ['required', 'string', 'max:255', Rule::in(self::JURUSAN)],
            'fakultas' => ['nullable', 'string', Rule::in(self::FAKULTAS)],
            'status_mahasiswa' => ['required', 'string', 'max:50'],
            'angkatan' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $emailDomain = (string) config('app.email_domain');
        $emailBase = Str::lower(preg_replace('/\s+/', '', $validated['npm'])).'@'.$emailDomain;
        $email = $emailBase;
        $i = 1;
        while (User::query()->where('email', $email)->exists()) {
            $email = Str::before($emailBase, '@')."+{$i}@".$emailDomain;
            $i++;
        }

        $user = User::query()->create([
            'name' => $validated['nama_lengkap'],
            'email' => $email,
            'role' => User::ROLE_MAHASISWA,
            'password' => Hash::make('password'),
            'password_plain' => 'password',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('photos/mahasiswa', 'public');
        }

        $mahasiswa = Mahasiswa::query()->create([
            'user_id' => $user->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'npm' => $validated['npm'],
            'program_studi' => $validated['program_studi'],
            'fakultas' => $validated['fakultas'] ?? null,
            'status_mahasiswa' => $validated['status_mahasiswa'],
            'angkatan' => $validated['angkatan'] ?? null,
            'foto_path' => $fotoPath,
        ]);

        foreach (range(1, 8) as $semester) {
            Khs::query()->firstOrCreate([
                'mahasiswa_id' => $mahasiswa->id,
                'semester' => $semester,
            ]);
        }

        return redirect()->route('admin.mahasiswa.show', $mahasiswa)->with('success', 'Mahasiswa berhasil ditambahkan.');
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        $mahasiswa->load('user');

        return view('admin.mahasiswa.show', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        return view('admin.mahasiswa.edit', [
            'mahasiswa' => $mahasiswa,
            'jurusan' => self::JURUSAN,
            'fakultasList' => self::FAKULTAS,
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'npm' => ['required', 'string', 'max:50', 'unique:mahasiswa,npm,'.$mahasiswa->id],
            'program_studi' => ['required', 'string', 'max:255', Rule::in(self::JURUSAN)],
            'fakultas' => ['nullable', 'string', Rule::in(self::FAKULTAS)],
            'status_mahasiswa' => ['required', 'string', 'max:50'],
            'angkatan' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            if ($mahasiswa->foto_path) {
                Storage::disk('public')->delete($mahasiswa->foto_path);
            }
            $mahasiswa->foto_path = $request->file('foto')->store('photos/mahasiswa', 'public');
        }

        $mahasiswa->fill([
            'nama_lengkap' => $validated['nama_lengkap'],
            'npm' => $validated['npm'],
            'program_studi' => $validated['program_studi'],
            'fakultas' => $validated['fakultas'] ?? null,
            'status_mahasiswa' => $validated['status_mahasiswa'],
            'angkatan' => $validated['angkatan'] ?? null,
        ])->save();

        $mahasiswa->user?->update(['name' => $validated['nama_lengkap']]);

        return redirect()->route('admin.mahasiswa.show', $mahasiswa)->with('success', 'Mahasiswa berhasil diperbarui.');
    }

    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        $user = $mahasiswa->user;

        if ($mahasiswa->foto_path) {
            Storage::disk('public')->delete($mahasiswa->foto_path);
        }

        if ($user) {
            $user->delete();
        } else {
            $mahasiswa->delete();
        }

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', (array) $validated['ids'])));
        if (count($ids) === 0) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $rows = Mahasiswa::query()->with('user')->whereIn('id', $ids)->get();
        if ($rows->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang cocok untuk dihapus.');
        }

        foreach ($rows as $mahasiswa) {
            $user = $mahasiswa->user;

            if ($mahasiswa->foto_path) {
                Storage::disk('public')->delete($mahasiswa->foto_path);
            }

            if ($user) {
                $user->delete();
            } else {
                $mahasiswa->delete();
            }
        }

        return redirect()->route('admin.mahasiswa.index')->with('success', 'Mahasiswa terpilih berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $query = Mahasiswa::query()
            ->select([
                'id',
                'user_id',
                'nama_lengkap',
                'npm',
                'nik',
                'angkatan',
                'fakultas',
                'program_studi',
                'nomor_telp',
                'status_mahasiswa',
            ])
            ->with(['user:id,email'])
            ->orderByDesc('id');
        $query = $this->applyFilters($query, $request);

        $rows = $query->get();
        $html = view('admin.mahasiswa.export-pdf', ['rows' => $rows])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="mahasiswa.pdf"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $query = Mahasiswa::query()->with('user')->orderByDesc('id');
        $query = $this->applyFilters($query, $request);

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mahasiswa');

        $headers = ['Nama', 'NPM', 'NIK', 'Angkatan', 'Program Studi', 'Nomor Telp', 'Status'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValue([$col + 1, 1], $label);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue([1, $rowIndex], $row->nama_lengkap);
            $sheet->setCellValue([2, $rowIndex], $row->npm);
            $sheet->setCellValue([3, $rowIndex], $row->nik);
            $sheet->setCellValue([4, $rowIndex], $row->angkatan);
            $sheet->setCellValue([5, $rowIndex], $row->program_studi);
            $sheet->setCellValue([6, $rowIndex], $row->nomor_telp);
            $sheet->setCellValue([7, $rowIndex], $row->status_mahasiswa);
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'mahasiswa.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
