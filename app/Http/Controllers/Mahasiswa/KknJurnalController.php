<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KknJurnal;
use App\Models\KknPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknJurnalController extends Controller
{
    public function index(Request $request, KknPengajuan $kkn): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        $kkn->load(['jurnals', 'mahasiswa', 'posko.pembimbingS']);

        return view('mahasiswa.kkn.jurnal.index', [
            'kkn' => $kkn,
            'jurnals' => $kkn->jurnals,
        ]);
    }

    public function create(Request $request, KknPengajuan $kkn): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        return view('mahasiswa.kkn.jurnal.create', [
            'kkn' => $kkn,
        ]);
    }

    public function store(Request $request, KknPengajuan $kkn): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'kegiatan' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:255'],
            'pihak_terkait' => ['nullable', 'string', 'max:255'],
        ]);

        $kkn->jurnals()->create([
            'tanggal' => $validated['tanggal'],
            'kegiatan' => $validated['kegiatan'],
            'deskripsi' => $validated['deskripsi'] ?: null,
            'lokasi' => $validated['lokasi'] ?: null,
            'pihak_terkait' => $validated['pihak_terkait'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.kkn.jurnal.index', $kkn)
            ->with('success', 'Jurnal kegiatan berhasil ditambahkan.');
    }

    public function edit(Request $request, KknPengajuan $kkn, KknJurnal $jurnal): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->kkn_pengajuan_id === (int) $kkn->id, 404);

        return view('mahasiswa.kkn.jurnal.edit', [
            'kkn' => $kkn,
            'jurnal' => $jurnal,
        ]);
    }

    public function update(Request $request, KknPengajuan $kkn, KknJurnal $jurnal): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->kkn_pengajuan_id === (int) $kkn->id, 404);

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

        return redirect()->route('mahasiswa.kkn.jurnal.index', $kkn)
            ->with('success', 'Jurnal kegiatan berhasil diperbarui.');
    }

    public function destroy(Request $request, KknPengajuan $kkn, KknJurnal $jurnal): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $jurnal->kkn_pengajuan_id === (int) $kkn->id, 404);

        $jurnal->delete();

        return back()->with('success', 'Jurnal kegiatan berhasil dihapus.');
    }

    public function pdf(Request $request, KknPengajuan $kkn)
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        $kkn->load(['jurnals', 'mahasiswa', 'posko.pembimbingS']);

        $html = view('kkn.jurnal-pdf', [
            'kkn' => $kkn,
            'jurnals' => $kkn->jurnals,
            'printedBy' => $request->user()?->name,
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
