<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\PplAbsensi;
use App\Models\PplPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PplAbsensiController extends Controller
{
    public function index(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['absensis', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        $total = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($ppl->absensis as $absen) {
            if (isset($total[$absen->status_kehadiran])) {
                $total[$absen->status_kehadiran]++;
            }
        }

        return view('mahasiswa.ppl.absensi.index', [
            'ppl' => $ppl,
            'absensis' => $ppl->absensis,
            'totals' => $total,
        ]);
    }

    public function create(Request $request, PplPengajuan $ppl): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        return view('mahasiswa.ppl.absensi.create', [
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
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'status_kehadiran' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $exists = PplAbsensi::query()
            ->where('ppl_pengajuan_id', $ppl->id)
            ->where('tanggal', $validated['tanggal'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Absensi untuk tanggal ini sudah ada.'])->withInput();
        }

        $ppl->absensis()->create([
            'tanggal' => $validated['tanggal'],
            'jam_masuk' => $validated['jam_masuk'] ?: null,
            'jam_pulang' => $validated['jam_pulang'] ?: null,
            'status_kehadiran' => $validated['status_kehadiran'],
            'keterangan' => $validated['keterangan'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.ppl.absensi.index', $ppl)
            ->with('success', 'Daftar hadir berhasil ditambahkan.');
    }

    public function edit(Request $request, PplPengajuan $ppl, PplAbsensi $absensi): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->ppl_pengajuan_id === (int) $ppl->id, 404);

        return view('mahasiswa.ppl.absensi.edit', [
            'ppl' => $ppl,
            'absensi' => $absensi,
        ]);
    }

    public function update(Request $request, PplPengajuan $ppl, PplAbsensi $absensi): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->ppl_pengajuan_id === (int) $ppl->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'status_kehadiran' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($absensi->tanggal->format('Y-m-d') !== $validated['tanggal']) {
            $exists = PplAbsensi::query()
                ->where('ppl_pengajuan_id', $ppl->id)
                ->where('tanggal', $validated['tanggal'])
                ->where('id', '!=', $absensi->id)
                ->exists();
            if ($exists) {
                return back()->withErrors(['tanggal' => 'Absensi untuk tanggal ini sudah ada.'])->withInput();
            }
        }

        $absensi->update([
            'tanggal' => $validated['tanggal'],
            'jam_masuk' => $validated['jam_masuk'] ?: null,
            'jam_pulang' => $validated['jam_pulang'] ?: null,
            'status_kehadiran' => $validated['status_kehadiran'],
            'keterangan' => $validated['keterangan'] ?: null,
        ]);

        return redirect()->route('mahasiswa.ppl.absensi.index', $ppl)
            ->with('success', 'Daftar hadir berhasil diperbarui.');
    }

    public function destroy(Request $request, PplPengajuan $ppl, PplAbsensi $absensi): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->ppl_pengajuan_id === (int) $ppl->id, 404);

        $absensi->delete();

        return back()->with('success', 'Daftar hadir berhasil dihapus.');
    }

    public function pdf(Request $request, PplPengajuan $ppl)
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $ppl->mahasiswa_id === (int) $mahasiswa->id, 404);

        $ppl->load(['absensis', 'mahasiswa', 'dosenPembimbing', 'dosenPembimbing2']);

        $kaprodi = null;
        if ($ppl->mahasiswa?->program_studi) {
            $kaprodi = \App\Models\Dosen::query()
                ->where('program_studi', $ppl->mahasiswa->program_studi)
                ->where('status_akademik', 'Ketua Prodi')
                ->orderByDesc('id')
                ->first();
        }

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
