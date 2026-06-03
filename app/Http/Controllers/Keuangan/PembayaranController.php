<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use App\Models\PembayaranDetail;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    private const JENIS_TAGIHAN = [
        'SPP',
        'Herregistrasi',
        'Ujian Semester',
        'Pembangunan',
        'HAKI',
        'PPL',
        'PPL Nasional',
        'PPL Internasional',
        'KKN',
        'KKN Bakti',
        'KKN Nasional',
        'KKN Internasional',
        'Ujian Munaqasah',
        'Jurnal',
        'Wisuda',
        'Ujian Komprehensif',
        'Seminar Proposal',
        'Seminar Hasil',
        'Seminar Tutup',
    ];

    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $semester = (int) $request->get('semester', 0);
        $angkatan = (int) $request->get('angkatan', 0);
        $jenisTagihan = trim((string) $request->get('jenis_tagihan', ''));
        $jurusan = trim((string) $request->get('jurusan', ''));
        $perPageRaw = trim((string) $request->get('per_page', '10'));
        $allowedPerPage = ['10', '25', '50', '100', 'all'];
        if (! in_array($perPageRaw, $allowedPerPage, true)) {
            $perPageRaw = '10';
        }

        $query = Pembayaran::query()->with('mahasiswa');

        if ($q !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%");
            });
        }
        if ($semester > 0) {
            $query->where('semester', $semester);
        }
        if ($jenisTagihan !== '') {
            $query->where('jenis_tagihan', $jenisTagihan);
        }
        if ($angkatan > 0) {
            $query->whereHas('mahasiswa', function ($sub) use ($angkatan) {
                $sub->where('angkatan', $angkatan);
            });
        }
        if ($jurusan !== '') {
            $query->whereHas('mahasiswa', function ($sub) use ($jurusan) {
                $sub->where('program_studi', $jurusan);
            });
        }

        if ($perPageRaw === 'all') {
            $rows = $query->orderByDesc('id')->get();
            $perPage = max((int) $rows->count(), 1);
            $pembayarans = new LengthAwarePaginator(
                $rows,
                $rows->count(),
                $perPage,
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );
        } else {
            $pembayarans = $query->orderByDesc('id')->paginate((int) $perPageRaw)->withQueryString();
        }

        $jurusanList = Mahasiswa::query()
            ->selectRaw('program_studi')
            ->whereNotNull('program_studi')
            ->where('program_studi', '<>', '')
            ->distinct()
            ->orderBy('program_studi')
            ->pluck('program_studi')
            ->map(fn ($v) => (string) $v)
            ->values()
            ->all();

        $angkatanList = Mahasiswa::query()
            ->selectRaw('angkatan')
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderBy('angkatan')
            ->pluck('angkatan')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        return view('keuangan.pembayaran.index', [
            'pembayarans' => $pembayarans,
            'q' => $q,
            'semester' => $semester ?: null,
            'angkatan' => $angkatan ?: null,
            'jurusan' => $jurusan ?: null,
            'jenis_tagihan' => $jenisTagihan ?: null,
            'jenisTagihanList' => self::JENIS_TAGIHAN,
            'angkatanList' => $angkatanList,
            'jurusanList' => $jurusanList,
            'per_page' => $perPageRaw,
        ]);
    }

    public function create(): View
    {
        $mahasiswa = Mahasiswa::query()->orderBy('nama_lengkap')->get();
        $angkatanList = Mahasiswa::query()
            ->selectRaw('angkatan')
            ->whereNotNull('angkatan')
            ->distinct()
            ->orderBy('angkatan')
            ->pluck('angkatan')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        return view('keuangan.pembayaran.create', [
            'mahasiswa' => $mahasiswa,
            'jenisTagihanList' => self::JENIS_TAGIHAN,
            'angkatanList' => $angkatanList,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'string', Rule::in(['single', 'angkatan', 'all'])],
            'mahasiswa_id' => ['nullable', 'exists:mahasiswa,id'],
            'angkatan' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'tahun_ajaran' => ['required', 'string'],
            'jenis_tagihan' => ['required', 'string', 'max:100', Rule::in(self::JENIS_TAGIHAN)],
            'total_biaya' => ['required', 'numeric', 'min:0'],
            'catatan' => ['nullable', 'string'],
            'jumlah_bayar' => ['nullable', 'numeric', 'min:0'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:2048'],
        ]);

        $mode = $validated['mode'];
        $semester = (int) $validated['semester'];
        $tahunAjaran = (string) $validated['tahun_ajaran'];
        $jenisTagihan = (string) $validated['jenis_tagihan'];
        $totalBiaya = (float) $validated['total_biaya'];
        $catatan = $validated['catatan'] ?? null;

        $targetMahasiswaQuery = Mahasiswa::query();
        if ($mode === 'single') {
            if (empty($validated['mahasiswa_id'])) {
                return back()->with('error', 'Mahasiswa wajib dipilih untuk mode Single.');
            }
            $targetMahasiswaQuery->where('id', (int) $validated['mahasiswa_id']);
        } elseif ($mode === 'angkatan') {
            if (empty($validated['angkatan'])) {
                return back()->with('error', 'Angkatan wajib dipilih untuk mode Angkatan.');
            }
            $targetMahasiswaQuery->where('angkatan', (int) $validated['angkatan']);
        }

        $targetMahasiswa = $targetMahasiswaQuery->get(['id']);
        if ($targetMahasiswa->isEmpty()) {
            return back()->with('error', 'Tidak ada mahasiswa yang cocok dengan filter.');
        }

        $created = 0;
        $skipped = 0;

        $buktiPath = null;
        $jumlahBayar = (float) ($validated['jumlah_bayar'] ?? 0);
        if ($jumlahBayar > 0 && $request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');
        }

        foreach ($targetMahasiswa as $m) {
            $pembayaran = Pembayaran::query()->create([
                'mahasiswa_id' => (int) $m->id,
                'semester' => $semester,
                'tahun_ajaran' => $tahunAjaran,
                'jenis_tagihan' => $jenisTagihan,
                'total_biaya' => $totalBiaya,
                'catatan' => $catatan,
            ]);

            $created++;

            if ($jumlahBayar > 0) {
                $pembayaran->details()->create([
                    'jumlah_bayar' => $jumlahBayar,
                    'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
                    'bukti_pembayaran' => $buktiPath,
                    'keterangan' => 'Pembayaran awal',
                    'status_approval' => 'approved',
                    'approved_at' => now(),
                    'approved_by_user_id' => $request->user()?->id,
                ]);
            }

            $pembayaran->updateStatus();
        }

        $msg = $created > 0
            ? "Tagihan berhasil dibuat: {$created}. Duplikat dilewati: {$skipped}."
            : "Tidak ada tagihan baru dibuat. Duplikat dilewati: {$skipped}.";

        return redirect()->route('keuangan.pembayaran.index')->with('success', $msg);
    }

    public function show(Pembayaran $pembayaran): View
    {
        $pembayaran->load(['mahasiswa', 'details' => function($q) {
            $q->orderByDesc('tanggal_bayar');
        }]);
        return view('keuangan.pembayaran.show', compact('pembayaran'));
    }

    public function addCicilan(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $validated = $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['nullable', 'date'],
            'bukti_pembayaran' => ['nullable', 'image', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('pembayaran/bukti', 'public');
        }

        $pembayaran->details()->create([
            'jumlah_bayar' => $validated['jumlah_bayar'],
            'tanggal_bayar' => $validated['tanggal_bayar'] ?? now(),
            'bukti_pembayaran' => $buktiPath,
            'keterangan' => $validated['keterangan'],
            'status_approval' => 'approved',
            'approved_at' => now(),
            'approved_by_user_id' => $request->user()?->id,
        ]);

        $pembayaran->updateStatus();

        return redirect()->back()->with('success', 'Pembayaran cicilan berhasil ditambahkan.');
    }

    public function updateDetailStatus(Request $request, Pembayaran $pembayaran, PembayaranDetail $detail): RedirectResponse
    {
        abort_unless((int) $detail->pembayaran_id === (int) $pembayaran->id, 404);

        $validated = $request->validate([
            'status_approval' => ['required', Rule::in(['approved', 'rejected'])],
            'catatan_approval' => ['nullable', 'string', 'max:255'],
        ]);

        $detail->update([
            'status_approval' => $validated['status_approval'],
            'catatan_approval' => $validated['catatan_approval'] ?: null,
            'approved_at' => now(),
            'approved_by_user_id' => $request->user()?->id,
        ]);

        $pembayaran->updateStatus();

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function destroy(Pembayaran $pembayaran): RedirectResponse
    {
        foreach ($pembayaran->details as $detail) {
            if ($detail->bukti_pembayaran) {
                Storage::disk('public')->delete($detail->bukti_pembayaran);
            }
        }
        $pembayaran->delete();
        return redirect()->route('keuangan.pembayaran.index')->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_values(array_unique(array_map('intval', (array) $validated['ids'])));
        if (count($ids) === 0) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $rows = Pembayaran::query()
            ->with('details')
            ->whereIn('id', $ids)
            ->get();

        if ($rows->isEmpty()) {
            return back()->with('error', 'Tidak ada data yang cocok untuk dihapus.');
        }

        foreach ($rows as $pembayaran) {
            foreach ($pembayaran->details as $detail) {
                if ($detail->bukti_pembayaran) {
                    Storage::disk('public')->delete($detail->bukti_pembayaran);
                }
            }
            $pembayaran->delete();
        }

        return redirect()->route('keuangan.pembayaran.index')->with('success', 'Data pembayaran terpilih berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        // Debug: Aktifkan ini jika ingin mengecek apakah controller terpanggil
        // dd('Controller PDF terpanggil');

        try {
            @ini_set('memory_limit', '1024M');
            @set_time_limit(600);
            @ini_set('pcre.backtrack_limit', '5000000');

            $q = trim((string) $request->get('q', ''));
            $semester = (int) $request->get('semester', 0);
            $angkatan = (int) $request->get('angkatan', 0);
            $jenisTagihan = trim((string) $request->get('jenis_tagihan', ''));
            $jurusan = trim((string) $request->get('jurusan', ''));

            $query = Pembayaran::query()
                ->with(['mahasiswa' => function($q) {
                    $q->select('id', 'nama_lengkap', 'npm', 'angkatan', 'program_studi');
                }])
                ->orderByDesc('id');

            if ($q !== '') {
                $query->whereHas('mahasiswa', function ($sub) use ($q) {
                    $sub->where('nama_lengkap', 'like', "%{$q}%")
                        ->orWhere('npm', 'like', "%{$q}%");
                });
            }
            if ($semester > 0) {
                $query->where('semester', $semester);
            }
            if ($jenisTagihan !== '') {
                $query->where('jenis_tagihan', $jenisTagihan);
            }
            if ($angkatan > 0) {
                $query->whereHas('mahasiswa', function ($sub) use ($angkatan) {
                    $sub->where('angkatan', $angkatan);
                });
            }
            if ($jurusan !== '') {
                $query->whereHas('mahasiswa', function ($sub) use ($jurusan) {
                    $sub->where('program_studi', $jurusan);
                });
            }

            $rows = $query->get();
            if ($rows->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk diekspor.');
            }

            $html = view('keuangan.pembayaran.export-pdf', [
                'rows' => $rows,
                'q' => $q,
                'semester' => $semester ?: null,
                'angkatan' => $angkatan ?: null,
                'jurusan' => $jurusan ?: null,
                'jenis_tagihan' => $jenisTagihan ?: null,
            ])->render();

            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'sans-serif');
            $options->set('chroot', base_path());
            
            // Nonaktifkan font subsetting jika terjadi error di beberapa server
            $options->set('isFontSubsettingEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="pembayaran.pdf"',
            ]);
        } catch (\Exception $e) {
            // Jika error, tampilkan pesan error yang jelas
            return response('Error PDF: ' . $e->getMessage() . ' di file ' . $e->getFile() . ' baris ' . $e->getLine(), 500);
        } catch (\Throwable $e) {
            return response('Error Fatal: ' . $e->getMessage(), 500);
        }
    }

    public function downloadPdf(Pembayaran $pembayaran)
    {
        @ini_set('memory_limit', '256M');
        
        $pembayaran->load(['mahasiswa', 'details' => function ($q) {
            $q->orderByDesc('tanggal_bayar');
        }]);

        $html = view('keuangan.pembayaran.detail-pdf', [
            'pembayaran' => $pembayaran,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'pembayaran-'.$pembayaran->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
