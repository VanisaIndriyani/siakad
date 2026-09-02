<?php

namespace App\Http\Controllers\Dosen;

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
        $dosen = $request->user()->dosen;
        abort_if(!$dosen, 404, 'Data dosen tidak ditemukan.');

        $q = trim((string) $request->get('q', ''));
        $jenis = trim((string) $request->get('jenis', ''));

        $query = Kegiatan::query()->published()->withCount('peserta')
            ->where(function ($sub) {
                $sub->where('tampilkan_ke_dosen', true)->orWhereNull('tampilkan_ke_dosen');
            });

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
            ->where('jenis_peserta', 'dosen')
            ->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id);
                if (!empty($dosen->nidn)) {
                    $sub->orWhere(function ($ss) use ($dosen) {
                        $ss->whereNull('dosen_id')->where('nidn', $dosen->nidn);
                    });
                }
                $sub->orWhere(function ($ss) use ($dosen) {
                    $ss->whereNull('dosen_id')->whereNull('nidn')->where('nama_lengkap', $dosen->nama);
                });
            })
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

        return view('dosen.kegiatan.index', [
            'items' => $items,
            'q' => $q,
            'jenis' => $jenis,
            'jenisList' => $jenisList,
            'pesertaSaya' => $pesertaSaya,
            'kegiatanSayaIds' => $kegiatanSayaIds,
            'dosen' => $dosen,
        ]);
    }

    public function show(Request $request, Kegiatan $kegiatan): View
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');

        $dosen = $request->user()->dosen;
        abort_if(!$dosen, 404, 'Data dosen tidak ditemukan.');

        $peserta = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('jenis_peserta', 'dosen')
            ->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id);
                if (!empty($dosen->nidn)) {
                    $sub->orWhere(function ($ss) use ($dosen) {
                        $ss->whereNull('dosen_id')->where('nidn', $dosen->nidn);
                    });
                }
                $sub->orWhere(function ($ss) use ($dosen) {
                    $ss->whereNull('dosen_id')->whereNull('nidn')->where('nama_lengkap', $dosen->nama);
                });
            })
            ->first();

        return view('dosen.kegiatan.show', [
            'kegiatan' => $kegiatan,
            'peserta' => $peserta,
            'dosen' => $dosen,
        ]);
    }

    public function daftar(Request $request, Kegiatan $kegiatan): \Illuminate\Http\RedirectResponse
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');

        $dosen = $request->user()->dosen;
        abort_if(!$dosen, 404, 'Data dosen tidak ditemukan.');

        $existsQuery = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('jenis_peserta', 'dosen')
            ->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id);
                if (!empty($dosen->nidn)) {
                    $sub->orWhere('nidn', $dosen->nidn);
                }
                $sub->orWhere('nama_lengkap', $dosen->nama);
            });

        if (!$existsQuery->exists()) {
            PesertaKegiatan::query()->create([
                'kegiatan_id' => $kegiatan->id,
                'jenis_peserta' => 'dosen',
                'dosen_id' => $dosen->id,
                'nama_lengkap' => $dosen->nama,
                'nidn' => $dosen->nidn ?? null,
                'program_studi' => $dosen->program_studi ?? null,
                'nomor_telp' => $dosen->nomor_hp ?? null,
                'email' => $request->user()->email ?? null,
            ]);
        }

        return back()->with('success', 'Anda berhasil terdaftar sebagai peserta kegiatan.');
    }

    public function downloadSertifikat(Request $request, Kegiatan $kegiatan): BinaryFileResponse
    {
        abort_if(!$kegiatan->is_published, 404, 'Kegiatan tidak tersedia.');
        abort_if(!$kegiatan->sertifikat_aktif, 403, 'Sertifikat untuk kegiatan ini tidak tersedia.');

        $dosen = $request->user()->dosen;
        abort_if(!$dosen, 404, 'Data dosen tidak ditemukan.');

        $peserta = PesertaKegiatan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->where('jenis_peserta', 'dosen')
            ->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id);
                if (!empty($dosen->nidn)) {
                    $sub->orWhere(function ($ss) use ($dosen) {
                        $ss->whereNull('dosen_id')->where('nidn', $dosen->nidn);
                    });
                }
                $sub->orWhere(function ($ss) use ($dosen) {
                    $ss->whereNull('dosen_id')->whereNull('nidn')->where('nama_lengkap', $dosen->nama);
                });
            })
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
        $mimeMap = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
        ];
        $mime = $mimeMap[$ext] ?? $disk->mimeType($sertifikatPath) ?? 'application/octet-stream';
        $filename = 'Sertifikat-' . \Illuminate\Support\Str::slug($peserta->nama_lengkap) . '-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.' . $ext;

        return $disk->download($sertifikatPath, $filename, [
            'Content-Type' => $mime,
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0, private',
            'Pragma' => 'public',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function sertifikatSaya(Request $request): View
    {
        $dosen = $request->user()->dosen;
        abort_if(!$dosen, 404, 'Data dosen tidak ditemukan.');

        $pesertas = PesertaKegiatan::query()
            ->with('kegiatan')
            ->where('jenis_peserta', 'dosen')
            ->where(function ($sub) use ($dosen) {
                $sub->where('dosen_id', $dosen->id);
                if (!empty($dosen->nidn)) {
                    $sub->orWhere(function ($ss) use ($dosen) {
                        $ss->whereNull('dosen_id')->where('nidn', $dosen->nidn);
                    });
                }
                $sub->orWhere(function ($ss) use ($dosen) {
                    $ss->whereNull('dosen_id')->whereNull('nidn')->where('nama_lengkap', $dosen->nama);
                });
            })
            ->where('status_hadir', true)
            ->latest('id')
            ->paginate(15);

        return view('dosen.kegiatan.sertifikat', [
            'pesertas' => $pesertas,
            'dosen' => $dosen,
        ]);
    }
}
