<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\PesertaKegiatan;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function downloadSertifikat(Request $request, Kegiatan $kegiatan): StreamedResponse
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

        if (empty($peserta->nomor_sertifikat)) {
            $peserta->update([
                'nomor_sertifikat' => $kegiatan->generateNomorSertifikat($peserta),
            ]);
            $peserta->refresh();
        }

        if (empty($peserta->sertifikat_diunduh_at)) {
            $peserta->update([
                'sertifikat_diunduh_at' => now(),
            ]);
        }

        $filename = 'Sertifikat-' . \Illuminate\Support\Str::slug($peserta->nama_lengkap) . '-' . \Illuminate\Support\Str::slug($kegiatan->judul) . '.pdf';

        return $this->generatePdfResponse(
            'admin.kegiatan.pdf-sertifikat',
            compact('kegiatan', 'peserta'),
            $filename,
            true,
            'a4',
            'landscape'
        );
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
