<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class TranskripNilaiController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $angkatan = trim((string) $request->get('angkatan', ''));
        $prodi = trim((string) $request->get('prodi', ''));

        $query = Mahasiswa::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('npm', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%");
            });
        }

        if ($angkatan !== '') {
            $query->where('angkatan', $angkatan);
        }

        if ($prodi !== '') {
            $query->where('program_studi', $prodi);
        }

        $mahasiswa = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $angkatanList = Mahasiswa::query()
            ->whereNotNull('angkatan')
            ->distinct()
            ->pluck('angkatan')
            ->sortDesc()
            ->values()
            ->all();

        $prodiList = [
            'Pendidikan Agama Islam',
            'Pendidikan Islam Anak Usia Dini',
            'Hukum Keluarga Islam',
            'Hukum Tata Negara',
            'Perbankan Syariah',
            'Ekonomi Syariah',
        ];

        return view('admin.transkrip-nilai.index', [
            'mahasiswa' => $mahasiswa,
            'q' => $q,
            'angkatan' => $angkatan,
            'prodi' => $prodi,
            'angkatanList' => $angkatanList,
            'prodiList' => $prodiList,
        ]);
    }

    public function show(Mahasiswa $mahasiswa): View
    {
        return view('admin.transkrip-nilai.show', $this->buildTranskripData($mahasiswa));
    }

    public function edit(Mahasiswa $mahasiswa): View
    {
        $ujian = $mahasiswa->ujian_kompre;
        if (!is_array($ujian)) {
            $ujian = [];
        }

        while (count($ujian) < 7) {
            $ujian[] = '';
        }

        return view('admin.transkrip-nilai.edit', [
            'mahasiswa' => $mahasiswa,
            'ujian' => array_slice($ujian, 0, 7),
        ]);
    }

    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'nomor_transkrip' => ['nullable', 'string', 'max:100'],
            'tanggal_lulus' => ['nullable', 'date'],
            'nomor_sk_banpt' => ['nullable', 'string', 'max:100'],
            'judul_skripsi' => ['nullable', 'string', 'max:500'],
            'ujian' => ['nullable', 'array'],
            'ujian.*' => ['nullable', 'string', 'max:255'],
        ]);

        $ujian = $validated['ujian'] ?? [];
        $ujian = array_values(array_filter(array_map(fn($v) => trim((string) $v), $ujian), fn($v) => $v !== ''));

        $mahasiswa->update([
            'nomor_transkrip' => $validated['nomor_transkrip'] !== '' ? $validated['nomor_transkrip'] : null,
            'tanggal_lulus' => $validated['tanggal_lulus'] ?? null,
            'nomor_sk_banpt' => $validated['nomor_sk_banpt'] !== '' ? $validated['nomor_sk_banpt'] : null,
            'judul_skripsi' => $validated['judul_skripsi'] !== '' ? $validated['judul_skripsi'] : null,
            'ujian_kompre' => count($ujian) > 0 ? $ujian : null,
        ]);

        return redirect()->route('admin.transkrip-nilai.show', $mahasiswa)
            ->with('success', 'Data transkrip berhasil disimpan.');
    }

    public function pdf(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $this->buildTranskripData($mahasiswa);

        $html = view('admin.transkrip-nilai.pdf', $data)->render();

        $dompdf = new Dompdf([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'times',
            'fontHeightRatio' => 0.75,
            'dpi' => 150,
            'isJavascriptEnabled' => false,
            'isFontSubsettingEnabled' => true,
            'debugPng' => false,
            'debugKeepTemp' => false,
            'debugCss' => false,
            'debugLayout' => false,
            'debugLayoutLines' => false,
            'debugLayoutBlocks' => false,
            'debugLayoutInline' => false,
            'debugLayoutPaddingBox' => false,
        ]);
        $dompdf->getOptions()->setIsRemoteEnabled(true);
        $dompdf->getOptions()->setDefaultFont('times');
        $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
        $dompdf->getOptions()->setFontHeightRatio(0.75);
        $dompdf->getOptions()->setDpi(150);

        $dompdf->loadHtml($html, 'UTF-8');

        // FOLIO / F4 INDONESIA: 210mm x 330mm = 595.28pt x 935.43pt
        // Paper size diset EXACT + margin 0 di sini, sisanya atur via @page CSS & .print-area padding
        $dompdf->setPaper(array(0.0, 0.0, 595.275591, 935.433071), 'portrait');
        $dompdf->render();

        $namafile = 'Transkrip-' . ($mahasiswa->npm ?: $mahasiswa->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string) $mahasiswa->nama_lengkap) . '.pdf';

        $forceDownload = (string) $request->query('download', '') !== ''
            || (string) $request->query('dl', '') !== ''
            || (string) $request->query('fd', '') !== ''
            || strtolower((string) $request->query('disposition', '')) === 'attachment';

        $disposition = $forceDownload ? 'attachment' : 'inline';
        $contentType = $forceDownload ? 'application/octet-stream' : 'application/pdf';

        $output = $dompdf->output();
        $contentLength = function_exists('mb_strlen') ? mb_strlen($output, '8bit') : strlen($output);
        $namafileRaw = rawurlencode($namafile);

        $headers = [
            'Content-Type' => $contentType,
            'Content-Disposition' => $disposition . '; filename="' . $namafile . '"; filename*=UTF-8\'\'' . $namafileRaw,
            'Content-Length' => $contentLength,
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0, private',
            'Pragma' => 'public',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            'X-Content-Type-Options' => 'nosniff',
            'Accept-Ranges' => 'bytes',
            'Content-Description' => 'File Transfer',
        ];

        return response($output, 200, $headers);
    }

    public function excel(Request $request, Mahasiswa $mahasiswa)
    {
        $data = $this->buildTranskripData($mahasiswa);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Transkrip Nilai');

        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setBottom(0.4)->setLeft(0.4)->setRight(0.4);

        $boldFont = ['font' => ['bold' => true]];
        $centerAlign = ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]];
        $wrapText = ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]];
        $thinBorder = ['borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']],
        ]];
        $headerFill = ['fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'FFD9EAD3'],
        ]];

        // ===== LOGO INSTITUSI (DI TENGAH ATAS) — FALLBACK CHAIN LAYERED AGAR MUNCUL DI HOSTING =====
        $logoInserted = false;
        $logoCandidates = [];
        try {
            try { $logoCandidates[] = rtrim(public_path(), '\\/') . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'lo.jpeg'; } catch (\Throwable $e) {}
            try {
                $bp = rtrim(str_replace('\\', '/', base_path()), '/');
                if ($bp !== '') {
                    $logoCandidates[] = $bp . '/public/img/lo.jpeg';
                    $logoCandidates[] = $bp . '/public_html/img/lo.jpeg';
                }
            } catch (\Throwable $e) {}
            try {
                $docRoot = rtrim(str_replace('\\', '/', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
                if ($docRoot !== '') {
                    $logoCandidates[] = $docRoot . '/img/lo.jpeg';
                    $logoCandidates[] = $docRoot . '/public/img/lo.jpeg';
                }
            } catch (\Throwable $e) {}
        } catch (\Throwable $e) {}
        $logoPath = null;
        foreach ($logoCandidates as $lc) {
            $lc = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, (string)$lc);
            if (@is_file($lc) && @is_readable($lc)) { $logoPath = $lc; break; }
        }
        if ($logoPath) {
            // ===== LAYER 1: Drawing (file-based, TIDAK BUTUH GD FUNCTION — PALING AMAN DI HOSTING) =====
            try {
                $logoDrawing = new Drawing();
                $logoDrawing->setName('Logo IAI DDI Sidrap');
                $logoDrawing->setDescription('Logo IAI DDI Sidrap');
                $logoDrawing->setPath($logoPath, false);
                $logoDrawing->setHeight(95);
                $logoDrawing->setWidth(95);
                $logoDrawing->setOffsetX(445);
                $logoDrawing->setOffsetY(2);
                $logoDrawing->setCoordinates('A1');
                $logoDrawing->setWorksheet($sheet);
                $logoInserted = true;
            } catch (\Throwable $e) {
                // ===== LAYER 2: MemoryDrawing (GD-based) — fallback jika setPath tidak diizinkan hosting =====
                try {
                    $imgInfo = @getimagesize($logoPath);
                    $mime = $imgInfo ? ($imgInfo['mime'] ?? '') : '';
                    $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
                    $gd = null;
                    if ($ext === 'png' || $mime === 'image/png') {
                        $gd = function_exists('imagecreatefrompng') ? @imagecreatefrompng($logoPath) : false;
                    } elseif ($ext === 'gif' || $mime === 'image/gif') {
                        $gd = function_exists('imagecreatefromgif') ? @imagecreatefromgif($logoPath) : false;
                    } else {
                        $gd = function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($logoPath) : false;
                    }
                    if ($gd !== false && $gd !== null) {
                        $logoDrawing2 = new MemoryDrawing();
                        $logoDrawing2->setName('Logo IAI DDI Sidrap');
                        $logoDrawing2->setDescription('Logo IAI DDI Sidrap');
                        $logoDrawing2->setImageResource($gd);
                        if ($ext === 'png' || $mime === 'image/png') {
                            $logoDrawing2->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                            $logoDrawing2->setMimeType(MemoryDrawing::MIMETYPE_PNG);
                        } elseif ($ext === 'gif' || $mime === 'image/gif') {
                            $logoDrawing2->setRenderingFunction(MemoryDrawing::RENDERING_GIF);
                            $logoDrawing2->setMimeType(MemoryDrawing::MIMETYPE_GIF);
                        } else {
                            $logoDrawing2->setRenderingFunction(MemoryDrawing::RENDERING_JPEG);
                            $logoDrawing2->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
                        }
                        $logoDrawing2->setHeight(95);
                        $logoDrawing2->setWidth(95);
                        $logoDrawing2->setOffsetX(445);
                        $logoDrawing2->setOffsetY(2);
                        $logoDrawing2->setCoordinates('A1');
                        $logoDrawing2->setWorksheet($sheet);
                        $logoInserted = true;
                    }
                } catch (\Throwable $e2) { $logoInserted = false; }
            }
        }
        // ===== AREA LOGO SELALU ADA (MERGE ROW 1-4) + TEXT FALLBACK KALAU GAMBAR GAGAL INSERT =====
        $sheet->mergeCells('A1:J4');
        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->getRowDimension(3)->setRowHeight(24);
        $sheet->getRowDimension(4)->setRowHeight(24);
        if (!$logoInserted) {
            $sheet->setCellValue('A1', 'LOGO IAI DDI SIDRAP');
            $sheet->getStyle('A1')->applyFromArray(array_merge(
                $boldFont, $centerAlign,
                ['font' => ['size' => 14, 'bold' => true, 'color' => ['argb' => 'FF1B6B3B']]]
            ));
        }

        $sheet->setCellValue('A6', 'INSTITUT AGAMA ISLAM DARUD DA\'WAH WAL IRSYAD');
        $sheet->mergeCells('A6:J6');
        $sheet->getStyle('A6')->applyFromArray(array_merge($boldFont, $centerAlign, ['font' => ['bold' => true, 'size' => 18]]));

        $sheet->setCellValue('A7', 'SIDENRENG RAPPANG');
        $sheet->mergeCells('A7:J7');
        $sheet->getStyle('A7')->applyFromArray(array_merge($boldFont, $centerAlign, ['font' => ['bold' => true, 'size' => 17]]));

        $sheet->setCellValue('A8', 'TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026');
        $sheet->mergeCells('A8:J8');
        $sheet->getStyle('A8')->applyFromArray(array_merge($centerAlign, ['font' => ['size' => 10]]));

        $sheet->setCellValue('A9', 'Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang');
        $sheet->mergeCells('A9:J9');
        $sheet->getStyle('A9')->applyFromArray(array_merge($centerAlign, ['font' => ['size' => 10]]));

        $sheet->setCellValue('A10', 'E-mail : iaiddisidrap@gmail.com   Website : www.yppddisrapp.ac.id');
        $sheet->mergeCells('A10:J10');
        $sheet->getStyle('A10')->applyFromArray(array_merge($centerAlign, ['font' => ['size' => 10]]));

        $row = 12;
        $sheet->setCellValue("A{$row}", 'TRANSKRIP AKADEMIK');
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, $centerAlign, ['font' => ['bold' => true, 'size' => 15]]));
        $row++;
        $sheet->setCellValue("A{$row}", 'Nomor : ' . $data['nomorTranskrip']);
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($centerAlign, ['font' => ['size' => 9]]));
        $row++;

        $biodata = [
            ['Nama', $mahasiswa->nama_lengkap, 'Program Pendidikan', 'Strata Satu (S1)'],
            ['No. Pokok Mahasiswa', $mahasiswa->npm ?? '-', 'Fakultas', $mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan'],
            ['No. Ijazah', $mahasiswa->nik ?? '-', 'Program Studi', $mahasiswa->program_studi ?? '-'],
            ['Tempat / Tanggal Lahir', $data['tempatTgl'], 'No. SK BAN-PT', $data['skBanpt']],
            ['Tanggal, Bulan dan Tahun Lulus', $data['tanggalLulus'], '', ''],
        ];
        foreach ($biodata as $bio) {
            $labelKiri = rtrim((string)$bio[0]);
            if ($labelKiri !== '' && !str_ends_with($labelKiri, ':')) $labelKiri .= ': ';
            $sheet->setCellValue("A{$row}", $labelKiri);
            $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]));
            $sheet->setCellValue("B{$row}", $bio[1]);
            $sheet->mergeCells("B{$row}:E{$row}");
            $sheet->getStyle("B{$row}:E{$row}")->applyFromArray(['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]);
            $labelKanan = trim((string)$bio[2]);
            if ($labelKanan !== '') {
                if (!str_ends_with($labelKanan, ':')) $labelKanan .= ': ';
                $sheet->setCellValue("F{$row}", $labelKanan);
                $sheet->getStyle("F{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]));
            }
            $sheet->setCellValue("G{$row}", $bio[3]);
            $sheet->mergeCells("G{$row}:J{$row}");
            $sheet->getStyle("G{$row}:J{$row}")->applyFromArray(['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]);
            $row++;
        }
        $row++;

        $sheet->setCellValue("A{$row}", 'DAFTAR MATA KULIAH DAN NILAI');
        $sheet->mergeCells("A{$row}:J{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, $headerFill, ['font' => ['bold' => true, 'size' => 12]]));
        $row++;

        $headerCol = ['NO', 'MATA KULIAH', 'SKS', 'NILAI', 'M'];
        $colStart = ['A', 'F'];
        foreach ($colStart as $cs) {
            $col = $cs;
            foreach ($headerCol as $h) {
                $sheet->setCellValue("{$col}{$row}", $h);
                $sheet->getStyle("{$col}{$row}")->applyFromArray(array_merge($boldFont, $centerAlign, $thinBorder, $headerFill));
                $col++;
            }
        }
        $sheet->getRowDimension($row)->setRowHeight(22);
        $row++;

        $daftarMK = $data['daftarMataKuliah'];
        $ujianKompre = $data['ujianKompre'];
        $ujianAda = array_values(array_filter(array_map(fn($v) => trim((string)$v), $ujianKompre), fn($v) => $v !== ''));
        $ujianCount = count($ujianAda);
        $totalMK = count($daftarMK);
        $barisBawah = 3 + $ujianCount;
        $sisa = max(0, $totalMK - $barisBawah);
        $mkAtas = array_slice($daftarMK, 0, $sisa);
        $mkBawahKiri = array_slice($daftarMK, $sisa);
        while (count($mkBawahKiri) < $barisBawah) { $mkBawahKiri[] = null; }
        $halfAtas = (int) ceil(count($mkAtas) / 2);
        $kiriAtas = array_slice($mkAtas, 0, $halfAtas);
        $kananAtas = array_slice($mkAtas, $halfAtas);
        $maxAtas = max(count($kiriAtas), count($kananAtas));
        $noAwalKanan = count($kiriAtas);

        for ($i = 0; $i < $maxAtas; $i++) {
            $L = $kiriAtas[$i] ?? null;
            $R = $kananAtas[$i] ?? null;
            $noL = $L ? ($i + 1) : '';
            $noR = $R ? ($noAwalKanan + $i + 1) : '';
            $namaL = $L ? $L->nama_mata_kuliah : '';
            $sksL = $L ? ($L->sks == 0 ? '0' : $L->sks) : '';
            $nhL = $L ? ($L->nilai_huruf !== '' ? $L->nilai_huruf : '') : '';
            $mutuL = $L ? ($L->nilai_m > 0 ? rtrim(rtrim(number_format($L->nilai_m, 2, '.', ''), '0'), '.') : ($L->nilai_huruf !== '' ? '0' : '')) : '';
            $namaR = $R ? $R->nama_mata_kuliah : '';
            $sksR = $R ? ($R->sks == 0 ? '0' : $R->sks) : '';
            $nhR = $R ? ($R->nilai_huruf !== '' ? $R->nilai_huruf : '') : '';
            $mutuR = $R ? ($R->nilai_m > 0 ? rtrim(rtrim(number_format($R->nilai_m, 2, '.', ''), '0'), '.') : ($R->nilai_huruf !== '' ? '0' : '')) : '';

            if ($L) {
                $sheet->setCellValue("A{$row}", $noL);
                $sheet->getStyle("A{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("B{$row}", $namaL);
                $sheet->getStyle("B{$row}")->applyFromArray(array_merge($wrapText, $thinBorder));
                $sheet->setCellValue("C{$row}", $sksL);
                $sheet->getStyle("C{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("D{$row}", $nhL);
                $sheet->getStyle("D{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("E{$row}", $mutuL);
                $sheet->getStyle("E{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
            } else {
                foreach (range('A', 'E') as $c) {
                    $sheet->getStyle("{$c}{$row}")->applyFromArray($thinBorder);
                }
            }

            if ($R) {
                $sheet->setCellValue("F{$row}", $noR);
                $sheet->getStyle("F{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("G{$row}", $namaR);
                $sheet->getStyle("G{$row}")->applyFromArray(array_merge($wrapText, $thinBorder));
                $sheet->setCellValue("H{$row}", $sksR);
                $sheet->getStyle("H{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("I{$row}", $nhR);
                $sheet->getStyle("I{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
                $sheet->setCellValue("J{$row}", $mutuR);
                $sheet->getStyle("J{$row}")->applyFromArray(array_merge($centerAlign, $thinBorder));
            } else {
                foreach (range('F', 'J') as $c) {
                    $sheet->getStyle("{$c}{$row}")->applyFromArray($thinBorder);
                }
            }
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        for ($bi = 0; $bi < $barisBawah; $bi++) {
            $LL = $mkBawahKiri[$bi] ?? null;
            $namaLL = $LL ? $LL->nama_mata_kuliah : '';
            $sksLL = $LL ? ($LL->sks == 0 ? '0' : $LL->sks) : '';
            $nhLL = $LL ? ($LL->nilai_huruf !== '' ? $LL->nilai_huruf : '') : '';
            $mutuLL = $LL ? ($LL->nilai_m > 0 ? rtrim(rtrim(number_format($LL->nilai_m, 2, '.', ''), '0'), '.') : ($LL->nilai_huruf !== '' ? '0' : '')) : '';
            $noLanjutTampil = $LL ? ($noAwalKanan + count($kananAtas) + $bi + 1) : '';
            if ($bi === 0) {
                $jenisBaris = 'jumlah';
            } elseif ($bi === 1) {
                $jenisBaris = 'spacer';
            } elseif ($bi === 2) {
                $jenisBaris = 'ujian-head';
            } else {
                $jenisBaris = 'ujian-row';
                $uIdx = $bi - 3;
                $uNama = $ujianKompre[$uIdx] ?? '';
                $uNo = $uIdx + 1;
            }

            foreach (range('A', 'E') as $c) {
                $sheet->getStyle("{$c}{$row}")->applyFromArray($thinBorder);
            }
            foreach (range('F', 'J') as $c) {
                $sheet->getStyle("{$c}{$row}")->applyFromArray($thinBorder);
            }

            if ($jenisBaris === 'jumlah') {
                $sheet->setCellValue("A{$row}", $noLanjutTampil);
                $sheet->setCellValue("B{$row}", $namaLL);
                $sheet->setCellValue("C{$row}", $sksLL);
                $sheet->setCellValue("D{$row}", $nhLL);
                $sheet->setCellValue("E{$row}", $mutuLL);
                $sheet->setCellValue("G{$row}", 'Jumlah');
                $sheet->setCellValue("H{$row}", $data['totalSks']);
                $sheet->setCellValue("J{$row}", rtrim(rtrim(number_format($data['totalMutu'], 2, '.', ''), '0'), '.'));
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray(array_merge($boldFont, $centerAlign));
                $sheet->getStyle("B{$row}")->applyFromArray(array_merge($wrapText, $boldFont));
                $sheet->getStyle("G{$row}")->applyFromArray(array_merge($boldFont, $centerAlign));
                $sheet->getStyle("H{$row}")->applyFromArray(array_merge($boldFont, $centerAlign));
                $sheet->getStyle("J{$row}")->applyFromArray(array_merge($boldFont, $centerAlign));
            } elseif ($jenisBaris === 'spacer') {
                $sheet->setCellValue("A{$row}", $noLanjutTampil);
                $sheet->setCellValue("B{$row}", $namaLL);
                $sheet->setCellValue("C{$row}", $sksLL);
                $sheet->setCellValue("D{$row}", $nhLL);
                $sheet->setCellValue("E{$row}", $mutuLL);
                $sheet->getRowDimension($row)->setRowHeight(18);
            } elseif ($jenisBaris === 'ujian-head') {
                $sheet->setCellValue("A{$row}", $noLanjutTampil);
                $sheet->setCellValue("B{$row}", $namaLL);
                $sheet->setCellValue("C{$row}", $sksLL);
                $sheet->setCellValue("D{$row}", $nhLL);
                $sheet->setCellValue("E{$row}", $mutuLL);
                $sheet->mergeCells("G{$row}:J{$row}");
                $sheet->setCellValue("G{$row}", 'Ujian Kompetensi');
                $sheet->getStyle("G{$row}")->applyFromArray(array_merge($boldFont, $wrapText));
                $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($boldFont);
            } else {
                $sheet->setCellValue("A{$row}", $noLanjutTampil);
                $sheet->setCellValue("B{$row}", $namaLL);
                $sheet->setCellValue("C{$row}", $sksLL);
                $sheet->setCellValue("D{$row}", $nhLL);
                $sheet->setCellValue("E{$row}", $mutuLL);
                $sheet->setCellValue("F{$row}", $uNo);
                $sheet->setCellValue("G{$row}", $uNama);
                $sheet->setCellValue("H{$row}", '0');
                $sheet->setCellValue("I{$row}", 'A');
                $sheet->setCellValue("J{$row}", '0');
                $sheet->getStyle("F{$row}")->applyFromArray($centerAlign);
                $sheet->getStyle("G{$row}")->applyFromArray($wrapText);
                $sheet->getStyle("H{$row}")->applyFromArray($centerAlign);
                $sheet->getStyle("I{$row}")->applyFromArray(array_merge($centerAlign, $boldFont));
                $sheet->getStyle("J{$row}")->applyFromArray($centerAlign);
            }
            $row++;
        }
        $row++;

        $sheet->setCellValue("A{$row}", 'INDEKS PRESTASI KUMULATIF (IPK)');
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]));
        $sheet->setCellValue("B{$row}", ': ' . str_replace('.', ',', number_format($data['ipk'], 2)));
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}:E{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]]));
        $row++;
        $sheet->setCellValue("A{$row}", 'PREDIKAT KELULUSAN');
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_CENTER]]));
        $sheet->setCellValue("B{$row}", ': ' . $data['predikat']);
        $sheet->mergeCells("B{$row}:E{$row}");
        $sheet->getStyle("B{$row}:E{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['vertical' => Alignment::VERTICAL_CENTER]]));
        $row++;
        $sheet->setCellValue("A{$row}", 'JUDUL SKRIPSI');
        $sheet->getStyle("A{$row}")->applyFromArray(array_merge($boldFont, ['alignment' => ['vertical' => Alignment::VERTICAL_TOP]]));
        $sheet->setCellValue("B{$row}", ': ' . $data['judulSkripsi']);
        $sheet->mergeCells("B{$row}:J{$row}");
        $sheet->getStyle("B{$row}:J{$row}")->applyFromArray($wrapText);
        $sheet->getRowDimension($row)->setRowHeight(-1);
        $row++;
        $row++;

        // ===== KOTAK PLACEHOLDER FOTO (TIDAK DITAMPILKAN OTOMATIS, UNTUK DITENDEL MANUAL) =====
        $fotoInserted = true;
        // Baris untuk area foto (6 baris x 22px = 132px cukup untuk 3x4)
        for ($fi = 0; $fi < 6; $fi++) { $sheet->getRowDimension($row + $fi)->setRowHeight(22); }
        // Merge 6 baris kolom F → jadi 1 kotak besar 3x4
        $fotoRange = "F{$row}:F" . ($row + 5);
        $sheet->mergeCells($fotoRange);
        $sheet->setCellValue("F{$row}", "Foto\n3 × 4");
        $sheet->getStyle("F{$row}")->applyFromArray(array_merge(
            $centerAlign,
            $wrapText,
            $thinBorder,
            ['font' => ['size' => 10, 'bold' => true, 'color' => ['argb' => 'FF333333']]]
        ));

        $ttdCol = $fotoInserted ? 'G' : 'F';
        $ttdMergeEnd = 'J';

        $sheet->setCellValue("{$ttdCol}{$row}", $data['tanggalTtd']);
        $sheet->mergeCells("{$ttdCol}{$row}:{$ttdMergeEnd}{$row}");
        $sheet->getStyle("{$ttdCol}{$row}")->applyFromArray($centerAlign);
        $row++;
        $sheet->setCellValue("{$ttdCol}{$row}", $data['ttdJabatan']);
        $sheet->mergeCells("{$ttdCol}{$row}:{$ttdMergeEnd}{$row}");
        $sheet->getStyle("{$ttdCol}{$row}")->applyFromArray(array_merge($centerAlign, $boldFont));
        $row += 5;
        $sheet->setCellValue("{$ttdCol}{$row}", $data['ttdNama']);
        $sheet->mergeCells("{$ttdCol}{$row}:{$ttdMergeEnd}{$row}");
        $sheet->getStyle("{$ttdCol}{$row}")->applyFromArray(array_merge($centerAlign, $boldFont, ['font' => ['underline' => true]]));
        $row++;
        $sheet->setCellValue("{$ttdCol}{$row}", $data['ttdNomorLabel'] . '. ' . $data['ttdNomor']);
        $sheet->mergeCells("{$ttdCol}{$row}:{$ttdMergeEnd}{$row}");
        $sheet->getStyle("{$ttdCol}{$row}")->applyFromArray($centerAlign);

        // ===== LEBAR KOLOM DIPERBAIKI SUPAYA LABEL BIODATA TIDAK OVERFLOW =====
        $sheet->getColumnDimension('A')->setWidth(24);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(6);
        $sheet->getColumnDimension('D')->setWidth(6);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(22);
        $sheet->getColumnDimension('G')->setWidth(26);
        $sheet->getColumnDimension('H')->setWidth(6);
        $sheet->getColumnDimension('I')->setWidth(6);
        $sheet->getColumnDimension('J')->setWidth(8);

        $namafile = 'Transkrip-' . ($mahasiswa->npm ?: $mahasiswa->id) . '-' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', (string) $mahasiswa->nama_lengkap) . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $output = ob_get_clean();

        $namafileRaw = rawurlencode($namafile);
        $contentLength = function_exists('mb_strlen') ? mb_strlen($output, '8bit') : strlen($output);

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $namafile . '"; filename*=UTF-8\'\'' . $namafileRaw,
            'Content-Length' => $contentLength,
            'Content-Transfer-Encoding' => 'binary',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0, private',
            'Pragma' => 'public',
            'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
            'X-Content-Type-Options' => 'nosniff',
            'Accept-Ranges' => 'bytes',
            'Content-Description' => 'File Transfer',
        ];

        return response($output, 200, $headers);
    }

    private function nilaiMutuHuruf(string $huruf): float
    {
        return match ($huruf) {
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            default => 0.00,
        };
    }

    private function predikat(float $ipk): string
    {
        if ($ipk >= 3.75) return 'Dengan Pujian';
        if ($ipk >= 3.50) return 'Sangat Memuaskan';
        if ($ipk >= 3.00) return 'Memuaskan';
        if ($ipk >= 2.00) return 'Cukup';
        return 'Kurang';
    }

    private function formatTanggalID($tanggal, string $fallback = '-'): string
    {
        try {
            if ($tanggal === null || $tanggal === '') {
                return $fallback;
            }
            $c = $tanggal instanceof \Illuminate\Support\Carbon || $tanggal instanceof \DateTimeInterface
                ? \Illuminate\Support\Carbon::instance($tanggal)
                : \Illuminate\Support\Carbon::parse($tanggal);
            if (!$c) return $fallback;

            $bulanID = [
                1  => 'Januari',
                2  => 'Februari',
                3  => 'Maret',
                4  => 'April',
                5  => 'Mei',
                6  => 'Juni',
                7  => 'Juli',
                8  => 'Agustus',
                9  => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember',
            ];

            $hari   = (int) $c->format('j');
            $bulan  = (int) $c->format('n');
            $tahun  = (int) $c->format('Y');
            $namaBulan = $bulanID[$bulan] ?? strtr((string)$c->format('F'), [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
            ]);

            return sprintf('%02d %s %04d', $hari, $namaBulan, $tahun);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function defaultUjianKompre(): array
    {
        return [
            'Ujian Komprehensif',
            'Al-Qur\'an, Agama, dan Bahasa (Lisan)',
            'Ilmu Pendidikan Islam',
            'Pengembangan Kurikulum Anak Usia Dini',
            'Metodik Khusus Pendidikan Anak Usia Dini',
            'Pengembangan Moral dan Agama',
            'Psikologi Perkembangan Anak',
        ];
    }

    private function buildTranskripData(Mahasiswa $mahasiswa): array
    {
        $mahasiswa->load([
            'khs.items.mataKuliah',
            'dosenPenasehat',
            'dekanFakultas',
        ]);

        $items = [];

        foreach ($mahasiswa->khs as $khs) {
            foreach ($khs->items as $khsItem) {
                if ($khsItem->mataKuliah) {
                    $items[] = $khsItem;
                }
            }
        }

        usort($items, function ($a, $b) {
            $sa = (int) ($a->mataKuliah->semester ?? 0);
            $sb = (int) ($b->mataKuliah->semester ?? 0);
            if ($sa !== $sb) return $sa <=> $sb;
            return strnatcasecmp((string) ($a->mataKuliah->kode ?? ''), (string) ($b->mataKuliah->kode ?? ''));
        });

        $totalSks = 0;
        $totalMutu = 0;
        $sksLulus = 0;
        $mutuLulus = 0;

        $daftarMataKuliah = [];

        foreach ($items as $it) {
            $mk = $it->mataKuliah;
            $sks = (int) ($mk->sks ?? 0);
            $huruf = (string) ($it->nilai_huruf ?? '');
            $nilaiM = $this->nilaiMutuHuruf($huruf);
            $m = $sks * $nilaiM;

            $namaMk = (string) ($mk->nama ?? '');
            if ($namaMk !== '') {
                $daftarMataKuliah[] = (object) [
                    'nama_mata_kuliah' => $namaMk,
                    'sks' => $sks,
                    'nilai_huruf' => $huruf,
                    'nilai_m' => $m,
                ];
            }

            $it->_sks = $sks;
            $it->_nilai_huruf = $huruf;
            $it->_nilai_m = $m;
            $it->_semester = $mk->semester ?? 0;

            $totalSks += $sks;
            $totalMutu += $m;

            if ($huruf !== '' && $huruf !== 'E') {
                $sksLulus += $sks;
                $mutuLulus += $m;
            }
        }

        $ujianKompre = is_array($mahasiswa->ujian_kompre) ? $mahasiswa->ujian_kompre : [];
        $ujianKompre = array_values(array_filter(array_map(fn($v) => trim((string) $v), $ujianKompre), fn($v) => $v !== ''));
        if (count($ujianKompre) === 0) {
            $ujianKompre = $this->defaultUjianKompre();
        }

        $totalItem = count($daftarMataKuliah);
        $jumlahKiri = (int) ceil($totalItem / 2);
        $bagianKiri = array_slice($daftarMataKuliah, 0, $jumlahKiri);
        $bagianKanan = array_slice($daftarMataKuliah, $jumlahKiri);

        $ipk = 0;
        if ($sksLulus > 0) {
            $ipk = round($mutuLulus / $sksLulus, 2);
        }

        $nomorTranskrip = $mahasiswa->nomor_transkrip
            ? $mahasiswa->nomor_transkrip
            : ('TR' . ($mahasiswa->npm ? $mahasiswa->npm : '0000' . $mahasiswa->id) . now()->format('Ym'));

        $judulSkripsi = (string) ($mahasiswa->judul_skripsi ?? '-');
        if ($judulSkripsi === '') $judulSkripsi = '-';

        $tempatLahir = (string) ($mahasiswa->tempat_lahir ?? '-');
        if ($tempatLahir === '') $tempatLahir = '-';
        $tglLahir = $this->formatTanggalID($mahasiswa->tanggal_lahir, '-');
        if ($tempatLahir !== '-' && $tglLahir !== '-') {
            $tempatTgl = "{$tempatLahir}, {$tglLahir}";
        } else {
            $gabung = ($tempatLahir !== '-' ? $tempatLahir : '') . ($tglLahir !== '-' ? $tglLahir : '');
            $tempatTgl = $gabung !== '' ? $gabung : '-';
        }

        $tanggalLulus = $this->formatTanggalID($mahasiswa->tanggal_lulus, '-');

        $tglTtd = $mahasiswa->tanggal_lulus
            ? \Illuminate\Support\Carbon::parse($mahasiswa->tanggal_lulus)
            : now();
        $tglTtdStr = $this->formatTanggalID($tglTtd, '');
        if ($tglTtdStr === '' || $tglTtdStr === '-') {
            try {
                $bulanIDFallback = [
                    1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
                    7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
                ];
                $nowFallback = $tglTtd instanceof \Illuminate\Support\Carbon ? $tglTtd : now();
                $tglTtdStr = sprintf('%02d %s %04d', (int)$nowFallback->day, $bulanIDFallback[(int)$nowFallback->month] ?? 'Agustus', (int)$nowFallback->year);
            } catch (\Throwable $e) { $tglTtdStr = date('d ') . 'Agustus ' . date('Y'); }
        }
        $tanggalTtd = 'Pangkajene, ' . $tglTtdStr;

        $skBanpt = (string) ($mahasiswa->nomor_sk_banpt ?? '');
        if ($skBanpt === '') {
            $skBanpt = '337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026';
        }

        $ujianAda = $ujianKompre;
        $ujianCount = count($ujianAda);

        $dekan = $mahasiswa->dekanFakultas;
        if (!$dekan) {
            $fakultasMhs = (string) ($mahasiswa->fakultas ?? '');
            if ($fakultasMhs !== '') {
                $dekan = \App\Models\Dosen::query()
                    ->where(function ($s) {
                        $s->whereNotNull('jabatan_struktural')
                            ->whereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%fakultas%'])
                            ->orWhereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%']);
                    })
                    ->where('fakultas', $fakultasMhs)
                    ->first();
            }
            if (!$dekan) {
                $dekan = \App\Models\Dosen::query()
                    ->where(function ($s) {
                        $s->whereNotNull('jabatan_struktural')
                            ->whereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%fakultas%'])
                            ->orWhereRaw('LOWER(jabatan_struktural) LIKE ?', ['%dekan%']);
                    })
                    ->first();
            }
        }

        if ($dekan) {
            $ttdJabatan = trim((string) ($dekan->jabatan_struktural ?? ''));
            if ($ttdJabatan === '') {
                $fakultasTtd = (string) ($dekan->fakultas ?: ($mahasiswa->fakultas ?? ''));
                if ($fakultasTtd !== '') {
                    // Hindari "DEKAN FAKULTAS FAKULTAS EKONOMI..." dobel kata FAKULTAS
                    $fakultasClean = $fakultasTtd;
                    if (preg_match('/^fakultas\s+/i', $fakultasClean)) {
                        $fakultasClean = preg_replace('/^fakultas\s+/i', '', $fakultasClean, 1);
                    }
                    $ttdJabatan = "DEKAN FAKULTAS " . strtoupper(trim((string)$fakultasClean));
                } else {
                    $ttdJabatan = "DEKAN FAKULTAS";
                }
            } else {
                // Normalisasi: jika jabatan struktural "DEKAN FAKULTAS EKONOMI" tapi masih ada dobel karena db, trim dobel FAKULTAS juga
                $jab = $ttdJabatan;
                if (preg_match('/^dekan\s+fakultas\s+fakultas\s+/i', $jab)) {
                    $ttdJabatan = preg_replace('/^dekan\s+fakultas\s+/i', 'DEKAN FAKULTAS ', $jab, 1);
                }
                $ttdJabatan = strtoupper(trim((string)$ttdJabatan));
            }
            $ttdNama = (string) ($dekan->nama ?? '');
            $nomorInduk = (string) ($dekan->nidn ?? '');
            $labelInduk = 'NIDN';
            if ($nomorInduk === '' && !empty($dekan->nidk)) {
                $nomorInduk = (string) $dekan->nidk;
                $labelInduk = 'NIDK';
            }
            if ($nomorInduk === '' && !empty($dekan->nip)) {
                $nomorInduk = (string) $dekan->nip;
                $labelInduk = 'NIP';
            }
            if ($nomorInduk === '' && !empty($dekan->nuptk)) {
                $nomorInduk = (string) $dekan->nuptk;
                $labelInduk = 'NUPTK';
            }
            if ($ttdNama === '') {
                $ttdNama = 'Dr. H. UMAR YAHYA, M.Ag.';
                $nomorInduk = '8932610021';
                $labelInduk = 'NIDK';
            }
        } else {
            $fakultasTtd = (string) ($mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan');
            $ttdJabatan = "DEKAN FAKULTAS " . strtoupper($fakultasTtd);
            $ttdNama = 'Dr. H. UMAR YAHYA, M.Ag.';
            $nomorInduk = '8932610021';
            $labelInduk = 'NIDK';
        }
        $ttdNomor = $nomorInduk;
        $ttdNomorLabel = $labelInduk;

        $fotoMahasiswa = null;
        if (!empty($mahasiswa->foto_path)) {
            try {
                $relPath = trim(str_replace(['/', '\\'], '/', (string) $mahasiswa->foto_path), '/');
                $absPath = public_path('storage/' . $relPath);
                $absPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $absPath);

                if (\Illuminate\Support\Facades\File::exists($absPath) && \Illuminate\Support\Facades\File::isFile($absPath)) {
                    $size = (int) \Illuminate\Support\Facades\File::size($absPath);
                    if ($size > 0 && $size < 10_000_000) {
                        $ext = strtolower((string) \Illuminate\Support\Facades\File::extension($absPath));
                        $mimeMap = [
                            'jpg'  => 'image/jpeg',
                            'jpeg' => 'image/jpeg',
                            'png'  => 'image/png',
                            'gif'  => 'image/gif',
                        ];
                        if (isset($mimeMap[$ext])) {
                            $content = \Illuminate\Support\Facades\File::get($absPath);
                            if ($content !== false && $content !== '') {
                                $fotoMahasiswa = 'data:' . $mimeMap[$ext] . ';base64,' . base64_encode($content);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                $fotoMahasiswa = null;
            }
        }

        return [
            'mahasiswa' => $mahasiswa,
            'items' => $items,
            'daftarMataKuliah' => $daftarMataKuliah,
            'bagianKiri' => $bagianKiri,
            'bagianKanan' => $bagianKanan,
            'left' => $bagianKiri,
            'right' => $bagianKanan,
            'half' => count($bagianKiri),
            'maxRows' => $maxRows ?? max(count($bagianKiri), count($bagianKanan)),
            'totalSks' => $totalSks,
            'totalMutu' => round($totalMutu, 2),
            'sksLulus' => $sksLulus,
            'mutuLulus' => round($mutuLulus, 2),
            'ipk' => $ipk,
            'predikat' => $this->predikat($ipk),
            'nomorTranskrip' => $nomorTranskrip,
            'ujianKompre' => $ujianKompre,
            'ujianAda' => $ujianAda,
            'ujianCount' => $ujianCount,
            'judulSkripsi' => $judulSkripsi,
            'tempatTgl' => $tempatTgl,
            'tanggalLulus' => $tanggalLulus,
            'tanggalTtd' => $tanggalTtd,
            'skBanpt' => $skBanpt,
            'fotoMahasiswa' => $fotoMahasiswa,
            'ttdJabatan' => $ttdJabatan,
            'ttdNama' => $ttdNama,
            'ttdNomor' => $ttdNomor,
            'ttdNomorLabel' => $ttdNomorLabel,
        ];
    }
}
