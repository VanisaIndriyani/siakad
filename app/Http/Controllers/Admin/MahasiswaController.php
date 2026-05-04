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

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));

        $query = Mahasiswa::query()->with('user');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('program_studi', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        $mahasiswa = $query->orderByDesc('id')->paginate(10)->withQueryString();

        if ($request->boolean('partial')) {
            return view('admin.mahasiswa.partials.table', [
                'mahasiswa' => $mahasiswa,
            ]);
        }

        return view('admin.mahasiswa.index', [
            'mahasiswa' => $mahasiswa,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('admin.mahasiswa.create', [
            'jurusan' => self::JURUSAN,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nik' => ['nullable', 'string', 'max:50'],
            'npm' => ['required', 'string', 'max:50', 'unique:mahasiswa,npm'],
            'alamat' => ['nullable', 'string'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'angkatan' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'program_studi' => ['required', 'string', 'max:255', Rule::in(self::JURUSAN)],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'status_mahasiswa' => ['required', 'string', 'max:50'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        $emailBase = Str::lower(preg_replace('/\s+/', '', $validated['npm'])).'@kampus.ac.id';
        $email = $emailBase;
        $i = 1;
        while (User::query()->where('email', $email)->exists()) {
            $email = Str::before($emailBase, '@')."+{$i}@kampus.ac.id";
            $i++;
        }

        $user = User::query()->create([
            'name' => $validated['nama_lengkap'],
            'email' => $email,
            'role' => User::ROLE_MAHASISWA,
            'password' => Hash::make('password'),
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('photos/mahasiswa', 'public');
        }

        $mahasiswa = Mahasiswa::query()->create([
            'user_id' => $user->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'npm' => $validated['npm'],
            'alamat' => $validated['alamat'] ?? null,
            'nomor_telp' => $validated['nomor_telp'] ?? null,
            'angkatan' => $validated['angkatan'] ?? null,
            'program_studi' => $validated['program_studi'],
            'asal_sekolah' => $validated['asal_sekolah'] ?? null,
            'status_mahasiswa' => $validated['status_mahasiswa'],
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
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nik' => ['nullable', 'string', 'max:50'],
            'npm' => ['required', 'string', 'max:50', 'unique:mahasiswa,npm,'.$mahasiswa->id],
            'alamat' => ['nullable', 'string'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'angkatan' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'program_studi' => ['required', 'string', 'max:255', Rule::in(self::JURUSAN)],
            'asal_sekolah' => ['nullable', 'string', 'max:255'],
            'status_mahasiswa' => ['required', 'string', 'max:50'],
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
            'tempat_lahir' => $validated['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            'nik' => $validated['nik'] ?? null,
            'npm' => $validated['npm'],
            'alamat' => $validated['alamat'] ?? null,
            'nomor_telp' => $validated['nomor_telp'] ?? null,
            'angkatan' => $validated['angkatan'] ?? null,
            'program_studi' => $validated['program_studi'],
            'asal_sekolah' => $validated['asal_sekolah'] ?? null,
            'status_mahasiswa' => $validated['status_mahasiswa'],
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

    public function exportPdf(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $query = Mahasiswa::query()->with('user')->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('program_studi', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

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
        $q = trim((string) $request->get('q', ''));

        $query = Mahasiswa::query()->with('user')->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('program_studi', 'like', "%{$q}%")
                    ->orWhere('angkatan', 'like', "%{$q}%");
            });
        }

        $rows = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mahasiswa');

        $headers = ['Nama', 'NPM', 'NIK', 'Angkatan', 'Program Studi', 'Nomor Telp', 'Status'];
        foreach ($headers as $col => $label) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $label);
        }

        $rowIndex = 2;
        foreach ($rows as $row) {
            $sheet->setCellValueByColumnAndRow(1, $rowIndex, $row->nama_lengkap);
            $sheet->setCellValueByColumnAndRow(2, $rowIndex, $row->npm);
            $sheet->setCellValueByColumnAndRow(3, $rowIndex, $row->nik);
            $sheet->setCellValueByColumnAndRow(4, $rowIndex, $row->angkatan);
            $sheet->setCellValueByColumnAndRow(5, $rowIndex, $row->program_studi);
            $sheet->setCellValueByColumnAndRow(6, $rowIndex, $row->nomor_telp);
            $sheet->setCellValueByColumnAndRow(7, $rowIndex, $row->status_mahasiswa);
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
