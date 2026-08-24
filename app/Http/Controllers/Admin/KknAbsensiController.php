<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KknAbsensi;
use App\Models\KknPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknAbsensiController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isStaffAkademik()) {
            return [
                'routePrefix' => 'admin',
                'canAssign' => true,
                'programStudi' => null,
            ];
        }

        if ($user?->isDosen()) {
            $dosen = $user->dosen;
            $programStudi = trim((string) ($dosen?->program_studi ?? ''));
            $statusAkademik = (string) ($dosen?->status_akademik ?? '');

            $canAssign = in_array($statusAkademik, self::PRODI_APPROVER_STATUS, true);

            return [
                'routePrefix' => 'dosen',
                'canAssign' => $canAssign,
                'programStudi' => $programStudi ?: '---',
            ];
        }

        abort(403);
    }

    private function resolveKaprodi(?string $programStudi): ?Dosen
    {
        $programStudi = trim((string) $programStudi);
        if ($programStudi === '') {
            return null;
        }

        return Dosen::query()
            ->where('program_studi', $programStudi)
            ->where('status_akademik', 'Ketua Prodi')
            ->orderByDesc('id')
            ->first();
    }

    private function authorizeAccess(Request $request, KknPengajuan $kkn, array $context): void
    {
        if ($context['programStudi'] ?? null) {
            $kkn->loadMissing('mahasiswa');
            abort_unless((string) ($kkn->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        if (($context['dosenId'] ?? 0) > 0 && !($context['canAssign'] ?? false)) {
            $dosenId = $context['dosenId'];
            $kkn->loadMissing('posko.pembimbingS');
            $pembimbingIds = $kkn->posko?->pembimbingS?->pluck('id')->map(fn ($v) => (int) $v)->toArray() ?? [];
            abort_unless(in_array($dosenId, $pembimbingIds, true), 403);
        }
    }

    public function index(Request $request, KknPengajuan $kkn): View
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);

        $kkn->load(['absensis', 'mahasiswa', 'posko.pembimbingS']);

        $total = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($kkn->absensis as $absen) {
            if (isset($total[$absen->status_kehadiran])) {
                $total[$absen->status_kehadiran]++;
            }
        }

        $pembimbingIds = $kkn->posko?->pembimbingS?->pluck('id')->map(fn ($v) => (int) $v)->toArray() ?? [];
        $canReview = ($context['canAssign'] ?? false)
            || (($context['dosenId'] ?? 0) > 0 && in_array((int) $context['dosenId'], $pembimbingIds, true));

        return view('admin.kkn.absensi.index', [
            'kkn' => $kkn,
            'absensis' => $kkn->absensis,
            'totals' => $total,
            'routePrefix' => $context['routePrefix'],
            'canReview' => $canReview,
        ]);
    }

    public function updateStatus(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'catatan_pembimbing' => ['nullable', 'string'],
        ]);

        $absensi->update([
            'status' => $validated['status'],
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ?: null,
        ]);

        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }

    public function edit(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): View
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $kkn->loadMissing(['mahasiswa', 'posko.pembimbingS']);

        return view('admin.kkn.absensi.edit', [
            'kkn' => $kkn,
            'absensi' => $absensi,
            'routePrefix' => $context['routePrefix'],
        ]);
    }

    public function update(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'status_kehadiran' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
            'catatan_pembimbing' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $absensi->update([
            'tanggal' => $validated['tanggal'],
            'jam_masuk' => $validated['jam_masuk'] ?: null,
            'jam_pulang' => $validated['jam_pulang'] ?: null,
            'status_kehadiran' => $validated['status_kehadiran'],
            'keterangan' => $validated['keterangan'] ?: null,
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ?: null,
            'status' => $validated['status'],
        ]);

        $indexRoute = $context['routePrefix'] === 'admin' ? 'admin.kkn.absensi.index' : 'dosen.kkn.absensi.index';

        return redirect()->route($indexRoute, $kkn)->with('success', 'Daftar hadir berhasil diperbarui.');
    }

    public function destroy(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $absensi->delete();

        $indexRoute = $context['routePrefix'] === 'admin' ? 'admin.kkn.absensi.index' : 'dosen.kkn.absensi.index';

        return redirect()->route($indexRoute, $kkn)->with('success', 'Daftar hadir berhasil dihapus.');
    }

    public function pdf(Request $request, KknPengajuan $kkn)
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);

        $kkn->load(['absensis', 'mahasiswa', 'posko.pembimbingS']);
        $kaprodi = $this->resolveKaprodi($kkn->mahasiswa?->program_studi);

        $html = view('kkn.absensi-pdf', [
            'kkn' => $kkn,
            'absensis' => $kkn->absensis,
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'daftar-hadir-kkn-'.$kkn->mahasiswa->npm.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
