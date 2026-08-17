<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\KknAbsensi;
use App\Models\KknPengajuan;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KknAbsensiController extends Controller
{
    public function index(Request $request, KknPengajuan $kkn): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        $kkn->load(['absensis', 'mahasiswa', 'posko.pembimbingS']);

        $total = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        foreach ($kkn->absensis as $absen) {
            if (isset($total[$absen->status_kehadiran])) {
                $total[$absen->status_kehadiran]++;
            }
        }

        return view('mahasiswa.kkn.absensi.index', [
            'kkn' => $kkn,
            'absensis' => $kkn->absensis,
            'totals' => $total,
        ]);
    }

    public function create(Request $request, KknPengajuan $kkn): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        return view('mahasiswa.kkn.absensi.create', [
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
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'status_kehadiran' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $exists = KknAbsensi::query()
            ->where('kkn_pengajuan_id', $kkn->id)
            ->where('tanggal', $validated['tanggal'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['tanggal' => 'Absensi untuk tanggal ini sudah ada.'])->withInput();
        }

        $kkn->absensis()->create([
            'tanggal' => $validated['tanggal'],
            'jam_masuk' => $validated['jam_masuk'] ?: null,
            'jam_pulang' => $validated['jam_pulang'] ?: null,
            'status_kehadiran' => $validated['status_kehadiran'],
            'keterangan' => $validated['keterangan'] ?: null,
            'status' => 'pending',
        ]);

        return redirect()->route('mahasiswa.kkn.absensi.index', $kkn)
            ->with('success', 'Daftar hadir berhasil ditambahkan.');
    }

    public function edit(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): View
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        return view('mahasiswa.kkn.absensi.edit', [
            'kkn' => $kkn,
            'absensi' => $absensi,
        ]);
    }

    public function update(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_pulang' => ['nullable', 'date_format:H:i'],
            'status_kehadiran' => ['required', 'in:hadir,izin,sakit,alpha'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($absensi->tanggal->format('Y-m-d') !== $validated['tanggal']) {
            $exists = KknAbsensi::query()
                ->where('kkn_pengajuan_id', $kkn->id)
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

        return redirect()->route('mahasiswa.kkn.absensi.index', $kkn)
            ->with('success', 'Daftar hadir berhasil diperbarui.');
    }

    public function destroy(Request $request, KknPengajuan $kkn, KknAbsensi $absensi): RedirectResponse
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);
        abort_unless((int) $absensi->kkn_pengajuan_id === (int) $kkn->id, 404);

        $absensi->delete();

        return back()->with('success', 'Daftar hadir berhasil dihapus.');
    }

    public function pdf(Request $request, KknPengajuan $kkn)
    {
        $mahasiswa = $request->user()?->mahasiswa;
        abort_unless($mahasiswa, 403);
        abort_unless((int) $kkn->mahasiswa_id === (int) $mahasiswa->id, 404);

        $kkn->load(['absensis', 'mahasiswa', 'posko.pembimbingS']);

        $kaprodi = null;
        if ($kkn->mahasiswa?->program_studi) {
            $kaprodi = \App\Models\Dosen::query()
                ->where('program_studi', $kkn->mahasiswa->program_studi)
                ->where('status_akademik', 'Ketua Prodi')
                ->orderByDesc('id')
                ->first();
        }

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
