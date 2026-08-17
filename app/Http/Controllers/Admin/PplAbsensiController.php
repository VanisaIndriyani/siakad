<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PplAbsensi;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplAbsensiController extends Controller
{
    private const PRODI_APPROVER_STATUS = ['Ketua Prodi', 'Sekretaris Prodi'];

    private function resolveContext(Request $request): array
    {
        $user = $request->user();
        if ($user?->isAdmin()) {
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
                'dosenId' => (int) ($dosen?->id ?? 0),
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

    private function authorizeAccess(Request $request, PplPengajuan $ppl, array $context): void
    {
        if ($context['programStudi'] ?? null) {
            $ppl->loadMissing('mahasiswa');
            abort_unless((string) ($ppl->mahasiswa?->program_studi ?? '') === $context['programStudi'], 403);
        }

        if (($context['dosenId'] ?? 0) > 0 && !($context['canAssign'] ?? false)) {
            $dosenId = $context['dosenId'];
            $allowed = in_array($dosenId, [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true);
            abort_unless($allowed, 403);
        }
    }

    public function index(Request $request, PplPengajuan $ppl): View
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);

        $ppl->load(['absensis', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        $total = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($ppl->absensis as $absen) {
            if (isset($total[$absen->status_kehadiran])) {
                $total[$absen->status_kehadiran]++;
            }
        }

        $canReview = ($context['canAssign'] ?? false)
            || (($context['dosenId'] ?? 0) > 0
                && in_array((int) $context['dosenId'], [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true));

        return view('admin.ppl.absensi.index', [
            'ppl' => $ppl,
            'absensis' => $ppl->absensis,
            'totals' => $total,
            'routePrefix' => $context['routePrefix'],
            'canReview' => $canReview,
        ]);
    }

    public function updateStatus(Request $request, PplPengajuan $ppl, PplAbsensi $absensi): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);
        abort_unless((int) $absensi->ppl_pengajuan_id === (int) $ppl->id, 404);

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

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);

        $ppl->load(['absensis', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);
        $kaprodi = $this->resolveKaprodi($ppl->mahasiswa?->program_studi);

        $html = view('ppl.absensi-pdf', [
            'ppl' => $ppl,
            'absensis' => $ppl->absensis,
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'daftar-hadir-ppl-'.$ppl->mahasiswa->npm.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
