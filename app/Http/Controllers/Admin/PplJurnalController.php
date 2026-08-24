<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PplJurnal;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplJurnalController extends Controller
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

        $ppl->load(['jurnals', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        return view('admin.ppl.jurnal.index', [
            'ppl' => $ppl,
            'jurnals' => $ppl->jurnals,
            'routePrefix' => $context['routePrefix'],
            'canReview' => ($context['canAssign'] ?? false)
                || (($context['dosenId'] ?? 0) > 0
                    && in_array((int) $context['dosenId'], [(int) $ppl->dosen_pembimbing_id, (int) $ppl->dosen_pembimbing_id_2], true)),
        ]);
    }

    public function updateStatus(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

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

    public function edit(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): View
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        $ppl->loadMissing(['mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        return view('admin.ppl.jurnal.edit', [
            'ppl' => $ppl,
            'jurnal' => $jurnal,
            'routePrefix' => $context['routePrefix'],
        ]);
    }

    public function update(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['required', 'string', 'max:255'],
            'pihak_terkait' => ['required', 'string', 'max:255'],
            'catatan_pembimbing' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $jurnal->update([
            'tanggal' => $validated['tanggal'],
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'lokasi' => $validated['lokasi'],
            'pihak_terkait' => $validated['pihak_terkait'],
            'catatan_pembimbing' => $validated['catatan_pembimbing'] ?: null,
            'status' => $validated['status'],
        ]);

        $indexRoute = $context['routePrefix'] === 'admin' ? 'admin.ppl.jurnal.index' : 'dosen.ppl.jurnal.index';

        return redirect()->route($indexRoute, $ppl)->with('success', 'Jurnal kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): RedirectResponse
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        $jurnal->delete();

        $indexRoute = $context['routePrefix'] === 'admin' ? 'admin.ppl.jurnal.index' : 'dosen.ppl.jurnal.index';

        return redirect()->route($indexRoute, $ppl)->with('success', 'Jurnal kegiatan berhasil dihapus.');
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $context = $this->resolveContext($request);
        $this->authorizeAccess($request, $ppl, $context);

        $ppl->load(['jurnals', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);
        $kaprodi = $this->resolveKaprodi($ppl->mahasiswa?->program_studi);

        $html = view('ppl.jurnal-pdf', [
            'ppl' => $ppl,
            'jurnals' => $ppl->jurnals,
            'printedBy' => $request->user()?->name,
            'kaprodi' => $kaprodi,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'jurnal-ppl-'.$ppl->mahasiswa->npm.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
