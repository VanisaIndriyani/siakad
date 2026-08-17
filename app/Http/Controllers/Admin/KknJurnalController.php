<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KknJurnal;
use App\Models\KknPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknJurnalController extends Controller
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

        $kkn->load(['jurnals', 'mahasiswa', 'posko.pembimbingS']);

        $pembimbingIds = $kkn->posko?->pembimbingS?->pluck('id')->map(fn ($v) => (int) $v)->toArray() ?? [];
        $canReview = ($context['canAssign'] ?? false)
            || (($context['dosenId'] ?? 0) > 0 && in_array((int) $context['dosenId'], $pembimbingIds, true));

        return view('admin.kkn.jurnal.index', [
            'kkn' => $kkn,
            'jurnals' => $kkn->jurnals,
            'routePrefix' => $context['routePrefix'],
            'canReview' => $canReview,
        ]);
    }

    public function updateStatus(Request $request, KknPengajuan $kkn, KknJurnal $jurnal): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);
        abort_unless((int) $jurnal->kkn_pengajuan_id === (int) $kkn->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'catatan_pembimbing' => ['nullable', 'string'],
        ]);

        $jurnal->update([
            'status' => $validated['status'],
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ?: null,
        ]);

        return back()->with('success', 'Status jurnal berhasil diperbarui.');
    }

    public function pdf(Request $request, KknPengajuan $kkn)
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $kkn, $context);

        $kkn->load(['jurnals', 'mahasiswa', 'posko.pembimbingS']);
        $kaprodi = $this->resolveKaprodi($kkn->mahasiswa?->program_studi);

        $html = view('kkn.jurnal-pdf', [
            'kkn' => $kkn,
            'jurnals' => $kkn->jurnals,
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'jurnal-kkn-'.$kkn->mahasiswa->npm.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
