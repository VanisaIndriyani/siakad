<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PesertaKegiatan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class KegiatanController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if(!$mahasiswa, 404, 'Data mahasiswa tidak ditemukan.');

        $q = trim((string) $request->get('q', ''));
        $jenis = trim((string) $request->get('jenis', ''));

        $query = Kegiatan::query()->published()->withCount('peserta');

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

        $items = $query->paginate(10)->withQueryString();

        $pesertaSaya = PesertaKegiatan::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->pluck('status_hadir', 'kegiatan_id')
            ->toArray();

        $kegiatanSayaIds = array_keys($pesertaSaya);

        $jenisList = Kegiatan::query()
            ->published()
            ->reorder()
            ->whereNotNull('jenis_kegiatan')
            ->whereRaw('TRIM(jenis_kegiatan) != ?', [''])
            ->orderByDesc('tanggal_kegiatan')
            ->pluck('jenis_kegiatan')
            ->map(fn($j) => trim((string) $j))
            ->uniqueStrict()
            ->values();

        return view('mahasiswa.kegiatan.index', [
            'items' => $items,
            'q' => $q,
            'jenis' => $jenis,
            'jenisList' => $jenisList,
            'pesertaSaya' => $pesertaSaya,
            'kegiatanSayaIds' => $kegiatanSayaIds,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function show(Request $request, Kegiatan $kegiatan): View
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');

        $mahasiswa = $request->user()->mahasiswa;
        abort_if(!$mahasiswa, 404, 'Data mahasiswa tidak ditemukan.');

        $peserta = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        return view('mahasiswa.kegiatan.show', [
            'kegiatan' => $kegiatan,
            'peserta' => $peserta,
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function daftar(Request $request, Kegiatan $kegiatan): \Illuminate\Http\RedirectResponse
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');

        $mahasiswa = $request->user()->mahasiswa;
        abort_if(!$mahasiswa, 404, 'Data mahasiswa tidak ditemukan.');

        $exists = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->exists();

        if (!$exists) {
            PesertaKegiatan::query()->create([
                'kegiatan_id' => $kegiatan->id,
                'mahasiswa_id' => $mahasiswa->id,
                'nama_lengkap' => $mahasiswa->nama_lengkap,
                'npm' => $mahasiswa->npm,
                'program_studi' => $mahasiswa->program_studi,
                'fakultas' => $mahasiswa->fakultas,
                'nomor_telp' => $mahasiswa->nomor_telp,
                'email' => $request->user()->email,
            ]);
        }

        return back()->with('success', 'Anda berhasil terdaftar sebagai peserta kegiatan.');
    }

    public function downloadSertifikat(Request $request, Kegiatan $kegiatan): BinaryFileResponse
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');
        abort_if(!$kegiatan->sertifikat_aktif, 403, 'Sertifikat untuk kegiatan ini tidak tersedia.');

        $mahasiswa = $request->user()->mahasiswa;
        abort_if(!$mahasiswa, 404, 'Data mahasiswa tidak ditemukan.');

        $peserta = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        abort_if(!$peserta->status_hadir, 403, 'Anda belum tercatat hadir pada kegiatan ini. Sertifikat hanya tersedia untuk peserta yang hadir.');

        $disk = Storage::disk('public');
        $sertifikatPath = null;

        $pesertaPath = trim((string) ($peserta->sertifikat_peserta_path ?? ''));
        if ($pesertaPath !== '' && $disk->exists($pesertaPath)) {
            $sertifikatPath = $pesertaPath;
        }

        if ($sertifikatPath === null) {
            $masterPath = trim((string) ($kegiatan->sertifikat_upload_path ?? ''));
            if ($masterPath !== '' && $disk->exists($masterPath)) {
                $sertifikatPath = $masterPath;
            }
        }

        abort_if($sertifikatPath === null, 404, 'Sertifikat belum diupload oleh admin. Silakan hubungi panitia kegiatan.');

        if (empty($peserta->sertifikat_diunduh_at)) {
            $peserta->update([
                'sertifikat_diunduh_at' => now(),
            ]);
        }

        $ext = strtolower(pathinfo($sertifikatPath, PATHINFO_EXTENSION));
        if ($ext === '') $ext = 'pdf';

        $filename = 'Sertifikat-' . \Illuminate\Support\Str::slug($peserta->nama_lengkap) . '-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.' . $ext;

        return $disk->download($sertifikatPath, $filename, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0, private',
            'Pragma' => 'public',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function sertifikatSaya(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;
        abort_if(!$mahasiswa, 404, 'Data mahasiswa tidak ditemukan.');

        $pesertas = PesertaKegiatan::query()
            ->with('kegiatan')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status_hadir', true)
            ->latest('id')
            ->paginate(15);

        return view('mahasiswa.kegiatan.sertifikat', [
            'pesertas' => $pesertas,
            'mahasiswa' => $mahasiswa,
        ]);
    }
}
