<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplJurnal;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplJurnalController extends Controller
{
    public function index(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['jurnals', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        return view('mahasiswa.ppl.jurnal.index', [
            'ppl' => $ppl,
            'jurnals' => $ppl->jurnals,
        ]);
    }

    public function create(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        return view('mahasiswa.ppl.jurnal.create', [
            'ppl' => $ppl,
        ]);
    }

    public function store(Request $request, PplPengajuan $ppl): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'pihak_terkait' => ['nullable', 'string', 'max:255'],
        ]);

        $ppl->jurnals()->create([
            'tanggal' => $validated['tanggal'],
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'lokasi' => $validated['lokasi'] ?: null,
            'pihak_terkait' => $validated['pihak_terkait'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.ppl.jurnal.index', $ppl)
            ->with('success', 'Jurnal kegiatan berhasil ditambahkan.');
    }

    public function edit(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        return view('mahasiswa.ppl.jurnal.edit', [
            'ppl' => $ppl,
            'jurnal' => $jurnal,
        ]);
    }

    public function update(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'pihak_terkait' => ['nullable', 'string', 'max:255'],
        ]);

        $jurnal->update([
            'tanggal' => $validated['tanggal'],
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'lokasi' => $validated['lokasi'] ?: null,
            'pihak_terkait' => $validated['pihak_terkait'] ?: null,
        ]);

        return redirect()->route('mahasiswa.ppl.jurnal.index', $ppl)
            ->with('success', 'Jurnal kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, PplPengajuan $ppl, PplJurnal $jurnal): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->ppl_pengajuan_id === (int) $ppl->id, 404);

        $jurnal->delete();

        return back()->with('success', 'Jurnal kegiatan berhasil dihapus.');
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['jurnals', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        $html = view('ppl.jurnal-pdf', [
            'ppl' => $ppl,
            'jurnals' => $ppl->jurnals,
            'printedBy' => $request->user()?->name,
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
