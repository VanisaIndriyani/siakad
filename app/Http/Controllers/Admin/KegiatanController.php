<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Mahasiswa;
use App\Models\PesertaKegiatan;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class KegiatanController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $jenis = trim((string) $request->get('jenis', ''));
        $status = trim((string) $request->get('status', ''));

        $query = Kegiatan::query()->withCount('peserta', 'pesertaHadir');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%")
                    ->orWhere('penyelenggara', 'like', "%{$q}%");
            });
        }

        if ($jenis !== '') {
            $query->where('jenis_kegiatan', $jenis);
        }

        if ($status === 'published') {
            $query->where('is_published', true);
        } elseif ($status === 'draft') {
            $query->where('is_published', false);
        }

        $items = $query->latest('tanggal_kegiatan')->latest('id')->paginate(10)->withQueryString();
        $jenisList = Kegiatan::query()
            ->reorder()
            ->whereNotNull('jenis_kegiatan')
            ->whereRaw('TRIM(jenis_kegiatan) != ?', [''])
            ->orderByDesc('tanggal_kegiatan')
            ->pluck('jenis_kegiatan')
            ->map(fn($j) => trim((string) $j))
            ->uniqueStrict()
            ->values();

        return view('admin.kegiatan.index', [
            'items' => $items,
            'q' => $q,
            'jenis' => $jenis,
            'status' => $status,
            'jenisList' => $jenisList,
        ]);
    }

    public function create(): View
    {
        return view('admin.kegiatan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeWaktuRequest($request);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'tanggal_kegiatan' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_kegiatan'],
            'waktu_mulai' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'waktu_selesai' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:waktu_mulai'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'narasumber' => ['nullable', 'string', 'max:255'],
            'ketua_panitia_nama' => ['nullable', 'string', 'max:255'],
            'ketua_panitia_nip' => ['nullable', 'string', 'max:50'],
            'narasumber_nip' => ['nullable', 'string', 'max:50'],
            'rektor_nama' => ['nullable', 'string', 'max:255'],
            'rektor_nip' => ['nullable', 'string', 'max:50'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'sertifikat_file_upload' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
            'sertifikat_aktif' => ['nullable', 'boolean'],
            'nomor_sertifikat_prefix' => ['nullable', 'string', 'max:50'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'waktu_mulai' => $this->truncateWaktu($validated['waktu_mulai'] ?? null),
            'waktu_selesai' => $this->truncateWaktu($validated['waktu_selesai'] ?? null),
            'penyelenggara' => $validated['penyelenggara'] ?? null,
            'narasumber' => $validated['narasumber'] ?? null,
            'ketua_panitia_nama' => $validated['ketua_panitia_nama'] ?? null,
            'ketua_panitia_nip' => $validated['ketua_panitia_nip'] ?? null,
            'narasumber_nip' => $validated['narasumber_nip'] ?? null,
            'rektor_nama' => $validated['rektor_nama'] ?? null,
            'rektor_nip' => $validated['rektor_nip'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? true),
            'sertifikat_aktif' => (bool) ($validated['sertifikat_aktif'] ?? true),
            'nomor_sertifikat_prefix' => $validated['nomor_sertifikat_prefix'] ?? null,
            'created_by' => $request->user()->id,
        ];

        if ($request->hasFile('gambar')) {
            $payload['gambar_path'] = $request->file('gambar')->store('kegiatan', 'public');
        }

        if ($request->hasFile('sertifikat_file_upload')) {
            $payload['sertifikat_upload_path'] = $request->file('sertifikat_file_upload')->store('kegiatan-sertifikat', 'public');
        }

        $kegiatan = Kegiatan::query()->create($payload);

        return redirect()->route('admin.kegiatan.show', $kegiatan)->with('success', 'Kegiatan berhasil dibuat.');
    }

    public function show(Kegiatan $kegiatan): View
    {
        $kegiatan->loadCount('peserta', 'pesertaHadir');
        $peserta = $kegiatan->peserta()->orderBy('nama_lengkap')->orderBy('id')->paginate(50);

        return view('admin.kegiatan.show', [
            'kegiatan' => $kegiatan,
            'peserta' => $peserta,
        ]);
    }

    public function edit(Kegiatan $kegiatan): View
    {
        return view('admin.kegiatan.edit', [
            'kegiatan' => $kegiatan,
        ]);
    }

    public function update(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $this->normalizeWaktuRequest($request);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jenis_kegiatan' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'tanggal_kegiatan' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_kegiatan'],
            'waktu_mulai' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'waktu_selesai' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/', 'after:waktu_mulai'],
            'penyelenggara' => ['nullable', 'string', 'max:255'],
            'narasumber' => ['nullable', 'string', 'max:255'],
            'ketua_panitia_nama' => ['nullable', 'string', 'max:255'],
            'ketua_panitia_nip' => ['nullable', 'string', 'max:50'],
            'narasumber_nip' => ['nullable', 'string', 'max:50'],
            'rektor_nama' => ['nullable', 'string', 'max:255'],
            'rektor_nip' => ['nullable', 'string', 'max:50'],
            'gambar' => ['nullable', 'image', 'max:4096'],
            'hapus_gambar' => ['nullable', 'boolean'],
            'sertifikat_file_upload' => ['nullable', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:10240'],
            'hapus_sertifikat_upload' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sertifikat_aktif' => ['nullable', 'boolean'],
            'nomor_sertifikat_prefix' => ['nullable', 'string', 'max:50'],
        ]);

        $payload = [
            'judul' => $validated['judul'],
            'jenis_kegiatan' => $validated['jenis_kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'tanggal_kegiatan' => $validated['tanggal_kegiatan'],
            'tanggal_selesai' => $validated['tanggal_selesai'] ?? null,
            'waktu_mulai' => $this->truncateWaktu($validated['waktu_mulai'] ?? null),
            'waktu_selesai' => $this->truncateWaktu($validated['waktu_selesai'] ?? null),
            'penyelenggara' => $validated['penyelenggara'] ?? null,
            'narasumber' => $validated['narasumber'] ?? null,
            'ketua_panitia_nama' => $validated['ketua_panitia_nama'] ?? null,
            'ketua_panitia_nip' => $validated['ketua_panitia_nip'] ?? null,
            'narasumber_nip' => $validated['narasumber_nip'] ?? null,
            'rektor_nama' => $validated['rektor_nama'] ?? null,
            'rektor_nip' => $validated['rektor_nip'] ?? null,
            'is_published' => (bool) ($validated['is_published'] ?? $kegiatan->is_published),
            'sertifikat_aktif' => (bool) ($validated['sertifikat_aktif'] ?? $kegiatan->sertifikat_aktif),
            'nomor_sertifikat_prefix' => $validated['nomor_sertifikat_prefix'] ?? null,
        ];

        if (!empty($validated['hapus_gambar']) && !empty($kegiatan->gambar_path)) {
            Storage::disk('public')->delete($kegiatan->gambar_path);
            $payload['gambar_path'] = null;
        }

        if ($request->hasFile('gambar')) {
            if (!empty($kegiatan->gambar_path)) {
                Storage::disk('public')->delete($kegiatan->gambar_path);
            }
            $payload['gambar_path'] = $request->file('gambar')->store('kegiatan', 'public');
        }

        if (!empty($validated['hapus_sertifikat_upload']) && !empty($kegiatan->sertifikat_upload_path)) {
            Storage::disk('public')->delete($kegiatan->sertifikat_upload_path);
            $payload['sertifikat_upload_path'] = null;
        }

        if ($request->hasFile('sertifikat_file_upload')) {
            if (!empty($kegiatan->sertifikat_upload_path)) {
                Storage::disk('public')->delete($kegiatan->sertifikat_upload_path);
            }
            $payload['sertifikat_upload_path'] = $request->file('sertifikat_file_upload')->store('kegiatan-sertifikat', 'public');
        }

        $kegiatan->update($payload);

        return back()->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan): RedirectResponse
    {
        if (!empty($kegiatan->gambar_path)) {
            Storage::disk('public')->delete($kegiatan->gambar_path);
        }
        if (!empty($kegiatan->sertifikat_upload_path)) {
            Storage::disk('public')->delete($kegiatan->sertifikat_upload_path);
        }

        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    public function togglePublish(Kegiatan $kegiatan): RedirectResponse
    {
        $kegiatan->update([
            'is_published' => !$kegiatan->is_published,
        ]);

        return back()->with('success', 'Status publish kegiatan berhasil diubah.');
    }

    public function importMahasiswa(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'angkatan' => ['nullable', 'string', 'max:10'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'fakultas' => ['nullable', 'string', 'max:100'],
            'status_mahasiswa' => ['nullable', 'string', 'max:50'],
            'semua_mahasiswa' => ['nullable', 'boolean'],
        ]);

        $mahasiswaQuery = Mahasiswa::query();

        if (empty($validated['semua_mahasiswa'])) {
            if (!empty($validated['angkatan'])) {
                $mahasiswaQuery->where('angkatan', $validated['angkatan']);
            }
            if (!empty($validated['program_studi'])) {
                $mahasiswaQuery->where('program_studi', 'like', "%{$validated['program_studi']}%");
            }
            if (!empty($validated['fakultas'])) {
                $mahasiswaQuery->where('fakultas', 'like', "%{$validated['fakultas']}%");
            }
            if (!empty($validated['status_mahasiswa'])) {
                $mahasiswaQuery->where('status_mahasiswa', $validated['status_mahasiswa']);
            }
        }

        $mahasiswas = $mahasiswaQuery->orderBy('nama_lengkap')->get();

        if ($mahasiswas->isEmpty()) {
            return back()->with('error', 'Tidak ada data mahasiswa yang sesuai kriteria.');
        }

        $addedCount = 0;
        foreach ($mahasiswas as $mhs) {
            $exists = PesertaKegiatan::query()
                ->where('kegiatan_id', $kegiatan->id)
                ->where('mahasiswa_id', $mhs->id)
                ->exists();

            if (!$exists) {
                PesertaKegiatan::query()->create([
                    'kegiatan_id' => $kegiatan->id,
                    'jenis_peserta' => 'mahasiswa',
                    'mahasiswa_id' => $mhs->id,
                    'nama_lengkap' => $mhs->nama_lengkap,
                    'npm' => $mhs->npm,
                    'program_studi' => $mhs->program_studi,
                    'fakultas' => $mhs->fakultas,
                    'nomor_telp' => $mhs->nomor_telp,
                    'email' => optional($mhs->user)->email,
                ]);
                $addedCount++;
            }
        }

        return back()->with('success', "Berhasil menambahkan {$addedCount} mahasiswa sebagai peserta.");
    }

    public function tambahPesertaManual(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'npm' => ['nullable', 'string', 'max:50'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'fakultas' => ['nullable', 'string', 'max:100'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        PesertaKegiatan::query()->create(array_merge($validated, [
            'kegiatan_id' => $kegiatan->id,
            'jenis_peserta' => 'mahasiswa',
        ]));

        return back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function tambahDosenPeserta(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'dosen_id' => ['nullable', 'exists:dosen,id'],
            'nama_lengkap' => ['nullable', 'required_without:dosen_id', 'string', 'max:255'],
            'nidn' => ['nullable', 'string', 'max:50'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'nomor_telp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $payload = [
            'kegiatan_id' => $kegiatan->id,
            'jenis_peserta' => 'dosen',
        ];

        if (!empty($validated['dosen_id'])) {
            $dosen = \App\Models\Dosen::find($validated['dosen_id']);
            if ($dosen) {
                $payload['dosen_id'] = $dosen->id;
                $payload['nama_lengkap'] = $dosen->nama ?? $validated['nama_lengkap'] ?? null;
                $payload['nidn'] = $dosen->nidn ?? $validated['nidn'] ?? null;
                $payload['program_studi'] = $dosen->program_studi ?? $validated['program_studi'] ?? null;
                $payload['nomor_telp'] = $dosen->nomor_hp ?? $validated['nomor_telp'] ?? null;
                $payload['email'] = $dosen->email ?? $validated['email'] ?? null;
            }
        } else {
            $payload['nama_lengkap'] = $validated['nama_lengkap'];
            $payload['nidn'] = $validated['nidn'] ?? null;
            $payload['program_studi'] = $validated['program_studi'] ?? null;
            $payload['nomor_telp'] = $validated['nomor_telp'] ?? null;
            $payload['email'] = $validated['email'] ?? null;
        }

        $existsCheck = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('jenis_peserta', 'dosen');
        if (!empty($payload['dosen_id'])) {
            $existsCheck = $existsCheck->where(function ($q) use ($payload) {
                $q->where('dosen_id', $payload['dosen_id'])->orWhere('nama_lengkap', $payload['nama_lengkap']);
            });
        } else {
            $existsCheck = $existsCheck->where('nama_lengkap', $payload['nama_lengkap']);
        }
        if ($existsCheck->exists()) {
            return back()->with('error', 'Dosen tersebut sudah terdaftar sebagai peserta kegiatan.');
        }

        PesertaKegiatan::query()->create($payload);

        return back()->with('success', 'Dosen berhasil ditambahkan sebagai peserta.');
    }

    public function hapusPeserta(Kegiatan $kegiatan, PesertaKegiatan $peserta): RedirectResponse
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) {
            abort(404);
        }
        if (!empty($peserta->sertifikat_peserta_path)) {
            try { Storage::disk('public')->delete($peserta->sertifikat_peserta_path); } catch (\Throwable $e) {}
        }
        $peserta->delete();

        return back()->with('success', 'Peserta berhasil dihapus.');
    }

    public function toggleHadir(Kegiatan $kegiatan, PesertaKegiatan $peserta): RedirectResponse
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) {
            abort(404);
        }

        $peserta->update([
            'status_hadir' => !$peserta->status_hadir,
            'waktu_hadir' => !$peserta->status_hadir ? now() : null,
        ]);

        return back()->with('success', 'Status kehadiran peserta berhasil diperbarui.');
    }

    public function tandaiSemuaHadir(Kegiatan $kegiatan): RedirectResponse
    {
        $now = now();
        DB::transaction(function () use ($kegiatan, $now) {
            $kegiatan->peserta()->where('status_hadir', false)->update([
                'status_hadir' => true,
                'waktu_hadir' => $now,
            ]);
        });

        return back()->with('success', 'Semua peserta berhasil ditandai hadir.');
    }

    public function uploadSertifikatPerPeserta(Request $request, Kegiatan $kegiatan): RedirectResponse
    {
        $validated = $request->validate([
            'peserta_id' => ['required', 'exists:peserta_kegiatan,id'],
            'file_sertifikat' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg,webp', 'max:10240'],
        ]);

        $peserta = PesertaKegiatan::query()->where('kegiatan_id', $kegiatan->id)->findOrFail($validated['peserta_id']);

        if (!empty($peserta->sertifikat_peserta_path)) {
            try { Storage::disk('public')->delete($peserta->sertifikat_peserta_path); } catch (\Throwable $e) {}
        }

        $path = $request->file('file_sertifikat')->store('kegiatan-sertifikat/peserta', 'public');
        $peserta->update([
            'sertifikat_peserta_path' => $path,
        ]);

        return back()->with('success', 'Sertifikat untuk ' . $peserta->nama_lengkap . ' berhasil diupload.');
    }

    public function hapusSertifikatPeserta(Kegiatan $kegiatan, PesertaKegiatan $peserta): RedirectResponse
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) { abort(404); }

        if (!empty($peserta->sertifikat_peserta_path)) {
            try { Storage::disk('public')->delete($peserta->sertifikat_peserta_path); } catch (\Throwable $e) {}
        }

        $peserta->update([
            'sertifikat_peserta_path' => null,
            'sertifikat_diunduh_at' => null,
        ]);

        return back()->with('success', 'Sertifikat peserta berhasil dihapus.');
    }

    public function downloadSertifikatMaster(Kegiatan $kegiatan)
    {
        if (empty($kegiatan->sertifikat_upload_path)) {
            abort(404, 'Belum ada file sertifikat master yang diupload.');
        }

        $disk = Storage::disk('public');
        if (!$disk->exists($kegiatan->sertifikat_upload_path)) {
            abort(404, 'File sertifikat tidak ditemukan di storage.');
        }

        $ext = strtolower(pathinfo($kegiatan->sertifikat_upload_path, PATHINFO_EXTENSION));
        if ($ext === '') $ext = 'pdf';
        $mimeMap = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];
        $mime = $mimeMap[$ext] ?? $disk->mimeType($kegiatan->sertifikat_upload_path) ?? 'application/octet-stream';
        $filename = 'Sertifikat-Master-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.' . $ext;
        return $disk->download($kegiatan->sertifikat_upload_path, $filename, [
            'Content-Type' => $mime,
        ]);
    }

    protected function resolveSertifikatPesertaPath(Kegiatan $kegiatan, PesertaKegiatan $peserta): ?string
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) { return null; }
        if (!$peserta->status_hadir) { return null; }
        if (!$kegiatan->sertifikat_aktif) { return null; }

        $perPath = (string)($peserta->sertifikat_peserta_path ?? '');
        if ($perPath !== '' && Storage::disk('public')->exists($perPath)) {
            return $perPath;
        }

        $masterPath = (string)($kegiatan->sertifikat_upload_path ?? '');
        if ($masterPath !== '' && Storage::disk('public')->exists($masterPath)) {
            return $masterPath;
        }

        return null;
    }

    public function lihatSertifikatPeserta(Kegiatan $kegiatan, PesertaKegiatan $peserta)
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) { abort(404); }
        if (!$peserta->status_hadir) { abort(403, 'Peserta belum hadir, sertifikat tidak tersedia.'); }
        if (!$kegiatan->sertifikat_aktif) { abort(403, 'Sertifikat untuk kegiatan ini tidak aktif.'); }

        $path = $this->resolveSertifikatPesertaPath($kegiatan, $peserta);
        if ($path === null) {
            abort(404, 'File sertifikat belum tersedia. Silakan hubungi admin untuk upload sertifikat.');
        }

        $disk = Storage::disk('public');
        if (empty($peserta->sertifikat_diunduh_at)) {
            try { $peserta->update(['sertifikat_diunduh_at' => now()]); } catch (\Throwable $e) {}
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === '') $ext = 'pdf';
        $mimeMap = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];
        $mime = $mimeMap[$ext] ?? $disk->mimeType($path) ?? 'application/octet-stream';
        $filename = 'Sertifikat-' . \Illuminate\Support\Str::slug($peserta->nama_lengkap) . '-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.' . $ext;
        return $disk->response($path, $filename, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    public function downloadSertifikatPeserta(Kegiatan $kegiatan, PesertaKegiatan $peserta)
    {
        if ($peserta->kegiatan_id !== $kegiatan->id) { abort(404); }
        if (!$peserta->status_hadir) { abort(403, 'Peserta belum hadir, sertifikat tidak tersedia.'); }
        if (!$kegiatan->sertifikat_aktif) { abort(403, 'Sertifikat untuk kegiatan ini tidak aktif.'); }

        $path = $this->resolveSertifikatPesertaPath($kegiatan, $peserta);
        if ($path === null) {
            abort(404, 'File sertifikat belum tersedia. Silakan hubungi admin untuk upload sertifikat.');
        }

        try { $peserta->update(['sertifikat_diunduh_at' => now()]); } catch (\Throwable $e) {}

        $disk = Storage::disk('public');
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === '') $ext = 'pdf';
        $mimeMap = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];
        $mime = $mimeMap[$ext] ?? $disk->mimeType($path) ?? 'application/octet-stream';
        $filename = 'Sertifikat-' . \Illuminate\Support\Str::slug($peserta->nama_lengkap) . '-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.' . $ext;
        return $disk->download($path, $filename, [
            'Content-Type' => $mime,
        ]);
    }

    public function daftarHadirPdf(Kegiatan $kegiatan): StreamedResponse
    {
        $peserta = $kegiatan->peserta()->orderBy('nama_lengkap')->orderBy('id')->get();

        $filename = 'Daftar-Hadir-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.pdf';

        return $this->generatePdfResponse(
            'admin.kegiatan.pdf-daftar-hadir',
            compact('kegiatan', 'peserta'),
            $filename,
            false,
            'a4',
            'portrait'
        );
    }

    public function downloadDaftarHadirPdf(Kegiatan $kegiatan): StreamedResponse
    {
        $peserta = $kegiatan->peserta()->orderBy('nama_lengkap')->orderBy('id')->get();

        $filename = 'Daftar-Hadir-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.pdf';

        return $this->generatePdfResponse(
            'admin.kegiatan.pdf-daftar-hadir',
            compact('kegiatan', 'peserta'),
            $filename,
            true,
            'a4',
            'portrait'
        );
    }

    protected function normalizeWaktuRequest(Request $request): void
    {
        $fields = ['waktu_mulai', 'waktu_selesai'];
        $merge = [];
        foreach ($fields as $f) {
            $v = $request->input($f);
            if ($v === null || is_string($v) && trim($v) === '') {
                continue;
            }
            $v = trim((string) $v);
            if (preg_match('/^\d{1,2}:\d{1,2}(:\d{1,2})?$/', $v)) {
                $parts = explode(':', $v);
                $h = str_pad((string) (int) ($parts[0] ?? 0), 2, '0', STR_PAD_LEFT);
                $i = str_pad((string) (int) ($parts[1] ?? 0), 2, '0', STR_PAD_LEFT);
                $merge[$f] = "{$h}:{$i}";
            }
        }
        if ($merge !== []) {
            $request->merge($merge);
        }
    }

    protected function truncateWaktu(?string $waktu): ?string
    {
        if ($waktu === null || trim($waktu) === '') return null;
        if (preg_match('/^(\d{2}:\d{2})(:\d{2})?$/', $waktu, $m)) {
            return $m[1];
        }
        return $waktu;
    }

    private function generatePdfResponse(string $viewName, array $data, string $filename, bool $forceDownload = false, string $paperSize = 'a4', string $orientation = 'portrait'): StreamedResponse
    {
        $prevDisplayErrors = ini_get('display_errors');
        $prevErrorReporting = error_reporting();
        ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

        try {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $paperMap = [
                'a4'        => [0.0, 0.0, 595.275591, 841.889764],
                'folio'     => [0.0, 0.0, 595.275591, 935.433071],
                'f4'        => [0.0, 0.0, 595.275591, 935.433071],
                'letter'    => [0.0, 0.0, 612.0, 792.0],
                'legal'     => [0.0, 0.0, 612.0, 1008.0],
            ];
            $paper = $paperMap[strtolower($paperSize)] ?? $paperMap['a4'];

            $html = view($viewName, $data)->render();

            $options = new Options([
                'tempDir'           => sys_get_temp_dir(),
                'fontDir'           => storage_path('fonts'),
                'fontCache'         => storage_path('fonts'),
                'chroot'            => base_path(),
                'isRemoteEnabled'   => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont'       => 'times',
                'dpi'               => 96,
                'isFontSubsettingEnabled' => true,
                'fontHeightRatio'   => 0.92,
            ]);

            $dompdf = new Dompdf($options);
            $dompdf->getOptions()->setIsRemoteEnabled(true);
            $dompdf->getOptions()->setDefaultFont('times');
            $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
            $dompdf->getOptions()->setFontHeightRatio(0.92);
            $dompdf->getOptions()->setDpi(96);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper($paper, strtolower($orientation) === 'landscape' ? 'landscape' : 'portrait');
            $dompdf->render();

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $outputPdf = $dompdf->output();

            $callback = function () use ($outputPdf) {
                echo $outputPdf;
            };

            $response = response()->streamDownload($callback, $filename, [
                'Content-Type' => $forceDownload ? 'application/octet-stream' : 'application/pdf',
                'Content-Transfer-Encoding' => 'binary',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0, private',
                'Pragma' => 'public',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'X-Content-Type-Options' => 'nosniff',
                'Content-Description' => 'File Transfer',
            ], $forceDownload ? 'attachment' : 'inline');

            ini_set('display_errors', $prevDisplayErrors);
            error_reporting($prevErrorReporting);

            return $response;
        } catch (\Throwable $e) {
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
            ini_set('display_errors', $prevDisplayErrors);
            error_reporting($prevErrorReporting);
            throw $e;
        }
    }
}
