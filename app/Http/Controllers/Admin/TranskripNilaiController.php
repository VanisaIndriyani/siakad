<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use ZipArchive;

class TranskripNilaiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

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

        $mahasiswa = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

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


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(Mahasiswa $mahasiswa): View
    {
        return view(
            'admin.transkrip-nilai.show',
            $this->buildTranskripData($mahasiswa)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Mahasiswa $mahasiswa
    ): RedirectResponse {
        $validated = $request->validate([
            'nomor_transkrip' => ['nullable', 'string', 'max:100'],
            'tanggal_lulus' => ['nullable', 'date'],
            'nomor_sk_banpt' => ['nullable', 'string', 'max:100'],
            'no_ijazah' => ['nullable', 'string', 'max:100'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'judul_skripsi' => ['nullable', 'string', 'max:500'],

            'ujian' => ['nullable', 'array'],
            'ujian.*' => ['nullable', 'string', 'max:255'],
        ]);

        $ujian = $validated['ujian'] ?? [];

        $ujian = array_values(
            array_filter(
                array_map(
                    fn ($v) => trim((string) $v),
                    $ujian
                ),
                fn ($v) => $v !== ''
            )
        );

        $mahasiswa->update([
            'nomor_transkrip' =>
                !empty($validated['nomor_transkrip'])
                    ? $validated['nomor_transkrip']
                    : null,

            'tanggal_lulus' =>
                $validated['tanggal_lulus'] ?? null,

            'nomor_sk_banpt' =>
                !empty($validated['nomor_sk_banpt'])
                    ? $validated['nomor_sk_banpt']
                    : null,

            'no_ijazah' =>
                !empty($validated['no_ijazah'])
                    ? $validated['no_ijazah']
                    : null,

            'tempat_lahir' =>
                !empty($validated['tempat_lahir'])
                    ? $validated['tempat_lahir']
                    : null,

            'tanggal_lahir' =>
                $validated['tanggal_lahir'] ?? null,

            'judul_skripsi' =>
                !empty($validated['judul_skripsi'])
                    ? $validated['judul_skripsi']
                    : null,

            'ujian_kompre' =>
                count($ujian) > 0
                    ? $ujian
                    : null,
        ]);

        return redirect()
            ->route('admin.transkrip-nilai.show', $mahasiswa)
            ->with('success', 'Data transkrip berhasil disimpan.');
    }


    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

    public function pdf(Request $request, Mahasiswa $mahasiswa)
    {
        $prevDisplayErrors = ini_get('display_errors');
        $prevErrorReporting = error_reporting();

        ini_set('display_errors', '0');

        error_reporting(
            $prevErrorReporting
            & ~E_NOTICE
            & ~E_WARNING
            & ~E_DEPRECATED
            & ~E_STRICT
        );

        while (ob_get_level() > 0) {
            if (!@ob_end_clean()) {
                break;
            }
        }

        ob_start();

        try {
            $data = $this->buildTranskripData($mahasiswa);

            /* =========================================================
               LOGO: load & convert ke BASE64 (DomPDF tidak bisa baca asset URL)
               ========================================================= */
            $logoB64 = null;
            $logoCandidates = [
                public_path('img/lo.jpeg'),
                public_path('img/lo.jpg'),
                public_path('img/lo.png'),
                public_path('img/logo.jpeg'),
                public_path('img/logo.jpg'),
                public_path('img/logo.png'),
            ];
            foreach ($logoCandidates as $lp) {
                if ($logoB64 !== null) {
                    break;
                }
                try {
                    if (!$lp || !is_file($lp) || !is_readable($lp)) {
                        continue;
                    }
                    $sizeRaw = @getimagesize($lp);
                    $mimeRaw = is_array($sizeRaw) && !empty($sizeRaw['mime']) ? $sizeRaw['mime'] : '';
                    $extRaw = strtolower(pathinfo($lp, PATHINFO_EXTENSION));
                    $mime = '';
                    if ($mimeRaw) {
                        $mime = $mimeRaw;
                    } else {
                        if ($extRaw === 'png') {
                            $mime = 'image/png';
                        } elseif ($extRaw === 'gif') {
                            $mime = 'image/gif';
                        } else {
                            $mime = 'image/jpeg';
                        }
                    }
                    $contents = @file_get_contents($lp);
                    if ($contents === false || $contents === '') {
                        continue;
                    }
                    $logoB64 = 'data:' . $mime . ';base64,' . base64_encode($contents);
                } catch (\Throwable $e) {
                    $logoB64 = null;
                }
            }

            /* =========================================================
               FOTO: convert path/URL ke BASE64
               ========================================================= */
            $fotoB64 = null;
            $fotoMahasiswaRaw = $data['fotoMahasiswa'] ?? null;
            if (!empty($fotoMahasiswaRaw) && is_string($fotoMahasiswaRaw)) {
                try {
                    $fotoCandidates = [];
                    $trim = ltrim($fotoMahasiswaRaw, '/');
                    if (strpos($fotoMahasiswaRaw, '://') !== false) {
                        $fotoCandidates[] = $fotoMahasiswaRaw;
                    }
                    $fotoCandidates[] = public_path($trim);
                    $fotoCandidates[] = public_path($fotoMahasiswaRaw);
                    foreach ($fotoCandidates as $fc) {
                        if ($fotoB64 !== null) {
                            break;
                        }
                        try {
                            $contents = null;
                            $mime = null;
                            if (strpos($fc, '://') !== false) {
                                $ch = @curl_init($fc);
                                if ($ch) {
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
                                    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
                                    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 SIAKAD-DOMPDF');
                                    $buf = curl_exec($ch);
                                    $httpCode = 0;
                                    $ct = '';
                                    try {
                                        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                    } catch (\Throwable $e) {
                                    }
                                    try {
                                        $ct = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                                    } catch (\Throwable $e) {
                                    }
                                    try {
                                        curl_close($ch);
                                    } catch (\Throwable $e) {
                                    }
                                    if ($buf !== false && $buf !== '' && $httpCode >= 200 && $httpCode < 300) {
                                        $contents = $buf;
                                        if ($ct && strpos($ct, 'image/') === 0) {
                                            $mime = trim(explode(';', $ct, 2)[0]);
                                        }
                                    }
                                }
                            } else {
                                if (is_file($fc) && is_readable($fc)) {
                                    $si = @getimagesize($fc);
                                    if ($si && !empty($si['mime']) && strpos($si['mime'], 'image/') === 0) {
                                        $buf = @file_get_contents($fc);
                                        if ($buf !== false && $buf !== '') {
                                            $contents = $buf;
                                            $mime = $si['mime'];
                                        }
                                    }
                                }
                            }
                            if ($contents === null || $contents === '') {
                                continue;
                            }
                            if ($mime === null || $mime === '') {
                                try {
                                    $off = @getimagesizefromstring($contents);
                                } catch (\Throwable $e) {
                                    $off = null;
                                }
                                if (is_array($off) && !empty($off['mime']) && strpos($off['mime'], 'image/') === 0) {
                                    $mime = $off['mime'];
                                } else {
                                    $mime = 'image/jpeg';
                                }
                            }
                            $fotoB64 = 'data:' . $mime . ';base64,' . base64_encode($contents);
                        } catch (\Throwable $e) {
                            $fotoB64 = null;
                        }
                    }
                } catch (\Throwable $e) {
                    $fotoB64 = null;
                }
            }

            /* =========================================================
               RENDER BLADE KE HTML STRING
               ========================================================= */
            $html = view('admin.transkrip-nilai.pdf', [
                'data' => $data,
                'logoSrc' => $logoB64,
                'fotoSrc' => $fotoB64,
            ])->render();

            /* =========================================================
               HAPUS JUNK / BOM / NEWLINE SEBELUM <!DOCTYPE>
               (blade @php baris 1 menambahkan newline prefix yang
                membuat Dompdf hasilkan binary %PDF tidak di offset 0)
               ========================================================= */
            if ($html !== '' && is_string($html)) {
                $firstTag = strpos($html, '<');
                if ($firstTag !== false && $firstTag > 0) {
                    $html = substr($html, $firstTag);
                }
                $html = preg_replace('/^[\x{FEFF}\x{200B}\s]+/u', '', $html);
            }

            /* =========================================================
               BOOT DOMPDF DENGAN CONFIG STABIL
               ========================================================= */
            $dompdf = new Dompdf([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'times',
                'fontHeightRatio' => 0.92,
                'dpi' => 96,
                'isJavascriptEnabled' => false,
                'isFontSubsettingEnabled' => true,
            ]);

            $dompdf->getOptions()->setIsRemoteEnabled(true);
            $dompdf->getOptions()->setDefaultFont('times');
            $dompdf->getOptions()->setIsFontSubsettingEnabled(true);
            $dompdf->getOptions()->setFontHeightRatio(0.95);
            $dompdf->getOptions()->setDpi(120);

            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('a4', 'portrait');
            $dompdf->render();

            /* =========================================================
               NAMA FILE AMAN
               Transkrip-{NPM}-{Nama_Mahasiswa}.pdf
               ========================================================= */
            $namafile =
                'Transkrip-' .
                ($mahasiswa->npm ?: $mahasiswa->id) .
                '-' .
                preg_replace(
                    '/[^a-zA-Z0-9_\-]/',
                    '_',
                    (string) $mahasiswa->nama_lengkap
                ) .
                '.pdf';

            $forceDownload =
                (string) $request->query('download', '') !== ''
                || (string) $request->query('dl', '') !== ''
                || (string) $request->query('fd', '') !== ''
                || strtolower(
                    (string) $request->query('disposition', '')
                ) === 'attachment';

            /* =========================================================
               BERSIHKAN OUTPUT BUFFER SEBELUM DOMPDF OUTPUT
               (Pastikan tidak ada echo / warning / BOM / whitespace
                yang ikut tercampur ke binary PDF)
               ========================================================= */
            while (ob_get_level() > 0) {
                if (!@ob_end_clean()) {
                    break;
                }
            }

            $outputPdf = $dompdf->output();
            if ($outputPdf === '' || $outputPdf === false) {
                $outputPdf = '';
            }

            /* =========================================================
               VALIDASI SIGNATURE PDF: 5 byte PERTAMA HARUS %PDF-
               Jika tidak valid = Dompdf gagal render / ada error
               inject. Dump ke storage dan throw Exception jelas.
               ========================================================= */
            $pdfSig = substr($outputPdf, 0, 5);
            if ($pdfSig !== '%PDF-') {
                $debugDir = rtrim(storage_path(), '\\/') . DIRECTORY_SEPARATOR . 'debug_transkrip';
                if (!is_dir($debugDir)) {
                    @mkdir($debugDir, 0755, true);
                }
                @file_put_contents($debugDir . '/pdf_bad_sig.bin', $outputPdf);
                @file_put_contents($debugDir . '/pdf_bad_html.html', $html);
                @file_put_contents($debugDir . '/pdf_bad_sig_info.txt',
                    "Signature TIDAK VALID\n" .
                    "Time: " . date('Y-m-d H:i:s') . "\n" .
                    "5 byte pertama HEX: " . bin2hex($pdfSig) . "\n" .
                    "Teks: " . $pdfSig . "\n" .
                    "Panjang outputPdf: " . strlen($outputPdf) . "\n"
                );

                ini_set('display_errors', $prevDisplayErrors);
                error_reporting($prevErrorReporting);

                throw new \RuntimeException(
                    'Generate PDF gagal: signature binary tidak valid (bukan diawali %PDF-). ' .
                    'Lihat debug file di storage/debug_transkrip/pdf_bad_*. ' .
                    'Info: sig=' . $pdfSig . ', len=' . strlen($outputPdf)
                );
            }

            /* =========================================================
               KIRIM PDF DENGAN LARAVEL RESPONSE PROPER.
               JANGAN PAKAI: echo, flush(), fastcgi_finish_request,
                             header() manual, exit.
               Semua dikelola oleh Response Laravel.
               ========================================================= */
            $size = strlen($outputPdf);
            $disposition = $forceDownload ? 'attachment' : 'inline';
            $filenameForHeader = addcslashes($namafile, '"\\');

            ini_set('display_errors', $prevDisplayErrors);
            error_reporting($prevErrorReporting);

            return response($outputPdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length' => $size,
                'Content-Disposition' =>
                    $disposition .
                    '; filename="' . $filenameForHeader . '"; ' .
                    'filename*=UTF-8\'\'' . rawurlencode($namafile),
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0, private',
                'Pragma' => 'public',
                'Expires' => 'Sat, 26 Jul 1997 05:00:00 GMT',
                'X-Content-Type-Options' => 'nosniff',
                'Accept-Ranges' => 'bytes',
                'Content-Description' => 'File Transfer',
            ]);

        } catch (\Throwable $e) {

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $debugDir = rtrim(storage_path(), '\\/') . DIRECTORY_SEPARATOR . 'debug_transkrip';
            if (!is_dir($debugDir)) {
                @mkdir($debugDir, 0755, true);
            }
            @file_put_contents($debugDir . '/pdf_exception.txt',
                "EXCEPTION di pdf()\n" .
                "Time: " . date('Y-m-d H:i:s') . "\n" .
                "Class: " . get_class($e) . "\n" .
                "Message: " . $e->getMessage() . "\n" .
                "File: " . $e->getFile() . "\n" .
                "Line: " . $e->getLine() . "\n\n" .
                "Trace:\n" . $e->getTraceAsString() . "\n"
            );
            if (isset($html)) {
                @file_put_contents($debugDir . '/pdf_exception_html.html', $html);
            }
            if (isset($outputPdf)) {
                @file_put_contents($debugDir . '/pdf_exception_pdf.bin', $outputPdf);
            }

            ini_set('display_errors', $prevDisplayErrors);
            error_reporting($prevErrorReporting);

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EXCEL
    |--------------------------------------------------------------------------
    */

    public function excel(Request $request, Mahasiswa $mahasiswa)
    {
        $tempFile = null;

        try {
            $data = $this->buildTranskripData($mahasiswa);

            $spreadsheet = new Spreadsheet();

            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setTitle('Transkrip Nilai');

            $sheet->getPageSetup()
                ->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                )
                ->setPaperSize(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
                )
                ->setFitToPage(false);

            $sheet->getPageMargins()
                ->setTop(0.3)
                ->setBottom(0.3)
                ->setLeft(0.3)
                ->setRight(0.3)
                ->setHeader(0.0)
                ->setFooter(0.0);

            $sheet->getHeaderFooter()
                ->setOddHeader('')
                ->setEvenHeader('')
                ->setOddFooter('')
                ->setEvenFooter('');

            $sheet->setShowGridLines(true);
            $sheet->setPrintGridLines(true);
            $sheet->setShowRowColHeaders(true);

            $boldFont = [
                'font' => [
                    'bold' => true,
                ],
            ];

            $centerAlign = [
                'alignment' => [
                    'horizontal' =>
                        Alignment::HORIZONTAL_CENTER,

                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],
            ];

            $wrapText = [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' =>
                        Alignment::VERTICAL_CENTER,
                ],
            ];

            $thinBorder = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' =>
                            Border::BORDER_THIN,

                        'color' => [
                            'argb' => 'FF000000',
                        ],
                    ],
                ],
            ];

            $headerFill = [
                'fill' => [
                    'fillType' =>
                        Fill::FILL_SOLID,

                    'color' => [
                        'argb' => 'FFD9EAD3',
                    ],
                ],
            ];

            $row = 1;

            $sheet->setCellValue(
                "A{$row}",
                'TRANSKRIP AKADEMIK'
            );

            $sheet->mergeCells(
                "A{$row}:J{$row}"
            );

            $sheet->getStyle(
                "A{$row}"
            )->applyFromArray(
                array_merge(
                    $boldFont,
                    $centerAlign,
                    [
                        'font' => [
                            'bold' => true,
                            'size' => 15,
                        ],
                    ]
                )
            );

            $row++;

            $sheet->setCellValue(
                "A{$row}",
                'Nomor : ' .
                $data['nomorTranskrip']
            );

            $sheet->mergeCells(
                "A{$row}:J{$row}"
            );

            $sheet->getStyle(
                "A{$row}:J{$row}"
            )->applyFromArray(
                array_merge(
                    $centerAlign,
                    [
                        'font' => [
                            'size' => 9,
                        ],
                    ]
                )
            );

            $row += 2;

            $biodata = [
                [
                    'Nama',
                    $mahasiswa->nama_lengkap,
                    'Program Pendidikan',
                    'Strata Satu (S1)',
                ],

                [
                    'No. Pokok Mahasiswa',
                    $mahasiswa->npm ?? '-',
                    'Fakultas',
                    $mahasiswa->fakultas
                        ?? 'Fakultas Tarbiyah & Keguruan',
                ],

                [
                    'No. Ijazah',
                    $data['noIjazah']
                        ?? ($mahasiswa->nik ?? '-'),
                    'Program Studi',
                    $mahasiswa->program_studi ?? '-',
                ],

                [
                    'Tempat / Tanggal Lahir',
                    $data['tempatTgl'],
                    'No. SK BAN-PT',
                    $data['skBanpt'],
                ],

                [
                    'Tanggal, Bulan dan Tahun Lulus',
                    $data['tanggalLulus'],
                    '',
                    '',
                ],
            ];

            foreach ($biodata as $bio) {

                $labelKiri = rtrim(
                    (string) $bio[0]
                );

                if (
                    $labelKiri !== ''
                    && !str_ends_with(
                        $labelKiri,
                        ':'
                    )
                ) {
                    $labelKiri .= ': ';
                }

                $sheet->setCellValue(
                    "A{$row}",
                    $labelKiri
                );

                $sheet->getStyle(
                    "A{$row}"
                )->applyFromArray(
                    array_merge(
                        $boldFont,
                        [
                            'alignment' => [
                                'wrapText' => true,
                                'vertical' =>
                                    Alignment::VERTICAL_CENTER,
                            ],
                        ]
                    )
                );

                $sheet->setCellValue(
                    "B{$row}",
                    $bio[1]
                );

                $sheet->mergeCells(
                    "B{$row}:E{$row}"
                );

                $sheet->getStyle(
                    "B{$row}:E{$row}"
                )->applyFromArray(
                    $wrapText
                );

                $labelKanan = trim(
                    (string) $bio[2]
                );

                if ($labelKanan !== '') {

                    if (
                        !str_ends_with(
                            $labelKanan,
                            ':'
                        )
                    ) {
                        $labelKanan .= ': ';
                    }

                    $sheet->setCellValue(
                        "F{$row}",
                        $labelKanan
                    );

                    $sheet->getStyle(
                        "F{$row}"
                    )->applyFromArray(
                        array_merge(
                            $boldFont,
                            $wrapText
                        )
                    );
                }

                $sheet->setCellValue(
                    "G{$row}",
                    $bio[3]
                );

                $sheet->mergeCells(
                    "G{$row}:J{$row}"
                );

                $sheet->getStyle(
                    "G{$row}:J{$row}"
                )->applyFromArray(
                    $wrapText
                );

                $row++;
            }

            $row++;

            $sheet->setCellValue(
                "A{$row}",
                'DAFTAR MATA KULIAH DAN NILAI'
            );

            $sheet->mergeCells(
                "A{$row}:J{$row}"
            );

            $sheet->getStyle(
                "A{$row}"
            )->applyFromArray(
                array_merge(
                    $boldFont,
                    $headerFill,
                    [
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                        ],
                    ]
                )
            );

            $row++;

            $headerCol = [
                'NO',
                'MATA KULIAH',
                'SKS',
                'NILAI',
                'M',
            ];

            foreach (['A', 'F'] as $startCol) {

                $col = $startCol;

                foreach ($headerCol as $h) {

                    $sheet->setCellValue(
                        "{$col}{$row}",
                        $h
                    );

                    $sheet->getStyle(
                        "{$col}{$row}"
                    )->applyFromArray(
                        array_merge(
                            $boldFont,
                            $centerAlign,
                            $thinBorder,
                            $headerFill
                        )
                    );

                    $col++;
                }
            }

            $sheet->getRowDimension(
                $row
            )->setRowHeight(22);

            $row++;

            $daftarMK =
                $data['daftarMataKuliah'];

            $ujianKompre =
                $data['ujianKompre'];

            $ujianAda = array_values(
                array_filter(
                    array_map(
                        fn ($v) =>
                            trim((string) $v),
                        $ujianKompre
                    ),
                    fn ($v) =>
                        $v !== ''
                )
            );

            $ujianCount =
                count($ujianAda);

            $totalMK =
                count($daftarMK);

            $barisBawah =
                3 + $ujianCount;

            $sisa =
                max(
                    0,
                    $totalMK - $barisBawah
                );

            $mkAtas =
                array_slice(
                    $daftarMK,
                    0,
                    $sisa
                );

            $mkBawahKiri =
                array_slice(
                    $daftarMK,
                    $sisa
                );

            while (
                count($mkBawahKiri)
                < $barisBawah
            ) {
                $mkBawahKiri[] = null;
            }

            $halfAtas =
                (int) ceil(
                    count($mkAtas) / 2
                );

            $kiriAtas =
                array_slice(
                    $mkAtas,
                    0,
                    $halfAtas
                );

            $kananAtas =
                array_slice(
                    $mkAtas,
                    $halfAtas
                );

            $maxAtas = max(
                count($kiriAtas),
                count($kananAtas)
            );

            $noAwalKanan =
                count($kiriAtas);

            for (
                $i = 0;
                $i < $maxAtas;
                $i++
            ) {

                $L =
                    $kiriAtas[$i] ?? null;

                $R =
                    $kananAtas[$i] ?? null;

                if ($L) {

                    $sheet->setCellValue(
                        "A{$row}",
                        $i + 1
                    );

                    $sheet->setCellValue(
                        "B{$row}",
                        $L->nama_mata_kuliah
                    );

                    $sheet->setCellValue(
                        "C{$row}",
                        $L->sks
                    );

                    $sheet->setCellValue(
                        "D{$row}",
                        $L->nilai_huruf
                    );

                    $sheet->setCellValue(
                        "E{$row}",
                        $this->formatNilaiM(
                            $L->nilai_m
                        )
                    );
                }

                if ($R) {

                    $sheet->setCellValue(
                        "F{$row}",
                        $noAwalKanan + $i + 1
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        $R->nama_mata_kuliah
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        $R->sks
                    );

                    $sheet->setCellValue(
                        "I{$row}",
                        $R->nilai_huruf
                    );

                    $sheet->setCellValue(
                        "J{$row}",
                        $this->formatNilaiM(
                            $R->nilai_m
                        )
                    );
                }

                foreach (
                    range('A', 'J')
                    as $c
                ) {
                    $sheet->getStyle(
                        "{$c}{$row}"
                    )->applyFromArray(
                        $thinBorder
                    );
                }

                $sheet->getStyle(
                    "A{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "C{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "D{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "E{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "F{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "H{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "I{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "J{$row}"
                )->applyFromArray(
                    $centerAlign
                );

                $sheet->getStyle(
                    "B{$row}"
                )->applyFromArray(
                    $wrapText
                );

                $sheet->getStyle(
                    "G{$row}"
                )->applyFromArray(
                    $wrapText
                );

                $sheet->getRowDimension(
                    $row
                )->setRowHeight(20);

                $row++;
            }

            for (
                $bi = 0;
                $bi < $barisBawah;
                $bi++
            ) {

                $LL =
                    $mkBawahKiri[$bi]
                    ?? null;

                foreach (
                    range('A', 'J')
                    as $c
                ) {
                    $sheet->getStyle(
                        "{$c}{$row}"
                    )->applyFromArray(
                        $thinBorder
                    );
                }

                if ($bi === 0) {

                    if ($LL) {

                        $sheet->setCellValue(
                            "A{$row}",
                            $noAwalKanan
                            + count($kananAtas)
                            + 1
                        );

                        $sheet->setCellValue(
                            "B{$row}",
                            $LL->nama_mata_kuliah
                        );

                        $sheet->setCellValue(
                            "C{$row}",
                            $LL->sks
                        );

                        $sheet->setCellValue(
                            "D{$row}",
                            $LL->nilai_huruf
                        );

                        $sheet->setCellValue(
                            "E{$row}",
                            $this->formatNilaiM(
                                $LL->nilai_m
                            )
                        );
                    }

                    $sheet->setCellValue(
                        "G{$row}",
                        'Jumlah'
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        $data['totalSks']
                    );

                    $sheet->setCellValue(
                        "J{$row}",
                        $this->formatNilaiM(
                            $data['totalMutu']
                        )
                    );

                    $sheet->getStyle(
                        "G{$row}:J{$row}"
                    )->applyFromArray(
                        $boldFont
                    );
                }

                elseif ($bi === 1) {

                    if ($LL) {

                        $sheet->setCellValue(
                            "A{$row}",
                            $noAwalKanan
                            + count($kananAtas)
                            + $bi + 1
                        );

                        $sheet->setCellValue(
                            "B{$row}",
                            $LL->nama_mata_kuliah
                        );

                        $sheet->setCellValue(
                            "C{$row}",
                            $LL->sks
                        );

                        $sheet->setCellValue(
                            "D{$row}",
                            $LL->nilai_huruf
                        );

                        $sheet->setCellValue(
                            "E{$row}",
                            $this->formatNilaiM(
                                $LL->nilai_m
                            )
                        );
                    }
                }

                elseif ($bi === 2) {

                    if ($LL) {

                        $sheet->setCellValue(
                            "A{$row}",
                            $noAwalKanan
                            + count($kananAtas)
                            + $bi + 1
                        );

                        $sheet->setCellValue(
                            "B{$row}",
                            $LL->nama_mata_kuliah
                        );

                        $sheet->setCellValue(
                            "C{$row}",
                            $LL->sks
                        );

                        $sheet->setCellValue(
                            "D{$row}",
                            $LL->nilai_huruf
                        );

                        $sheet->setCellValue(
                            "E{$row}",
                            $this->formatNilaiM(
                                $LL->nilai_m
                            )
                        );
                    }

                    $sheet->mergeCells(
                        "G{$row}:J{$row}"
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        'Ujian Kompetensi'
                    );

                    $sheet->getStyle(
                        "G{$row}"
                    )->applyFromArray(
                        $boldFont
                    );
                }

                else {

                    if ($LL) {

                        $sheet->setCellValue(
                            "A{$row}",
                            $noAwalKanan
                            + count($kananAtas)
                            + $bi + 1
                        );

                        $sheet->setCellValue(
                            "B{$row}",
                            $LL->nama_mata_kuliah
                        );

                        $sheet->setCellValue(
                            "C{$row}",
                            $LL->sks
                        );

                        $sheet->setCellValue(
                            "D{$row}",
                            $LL->nilai_huruf
                        );

                        $sheet->setCellValue(
                            "E{$row}",
                            $this->formatNilaiM(
                                $LL->nilai_m
                            )
                        );
                    }

                    $uIdx =
                        $bi - 3;

                    $uNama =
                        $ujianAda[$uIdx]
                        ?? '';

                    $sheet->setCellValue(
                        "F{$row}",
                        $uIdx + 1
                    );

                    $sheet->setCellValue(
                        "G{$row}",
                        $uNama
                    );

                    $sheet->setCellValue(
                        "H{$row}",
                        0
                    );

                    $sheet->setCellValue(
                        "I{$row}",
                        'A'
                    );

                    $sheet->setCellValue(
                        "J{$row}",
                        0
                    );
                }

                $row++;
            }

            $row++;

            $sheet->setCellValue(
                "A{$row}",
                'INDEKS PRESTASI KUMULATIF (IPK)'
            );

            $sheet->getStyle(
                "A{$row}"
            )->applyFromArray(
                $boldFont
            );

            $sheet->setCellValue(
                "B{$row}",
                ': ' .
                str_replace(
                    '.',
                    ',',
                    number_format(
                        $data['ipk'],
                        2
                    )
                )
            );

            $sheet->mergeCells(
                "B{$row}:E{$row}"
            );

            $row++;

            $sheet->setCellValue(
                "A{$row}",
                'PREDIKAT KELULUSAN'
            );

            $sheet->getStyle(
                "A{$row}"
            )->applyFromArray(
                $boldFont
            );

            $sheet->setCellValue(
                "B{$row}",
                ': ' .
                $data['predikat']
            );

            $sheet->mergeCells(
                "B{$row}:E{$row}"
            );

            $row++;

            $sheet->setCellValue(
                "A{$row}",
                'JUDUL SKRIPSI'
            );

            $sheet->getStyle(
                "A{$row}"
            )->applyFromArray(
                $boldFont
            );

            $sheet->setCellValue(
                "B{$row}",
                ': ' .
                $data['judulSkripsi']
            );

            $sheet->mergeCells(
                "B{$row}:J{$row}"
            );

            $sheet->getStyle(
                "B{$row}:J{$row}"
            )->applyFromArray(
                $wrapText
            );

            $row += 2;

            $sheet->setCellValue(
                "F{$row}",
                'Foto 3 × 4'
            );

            $sheet->getStyle(
                "F{$row}"
            )->applyFromArray(
                [
                    'font' => [
                        'size' => 10,
                        'bold' => true,
                    ],
                    'alignment' => [
                        'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                        'vertical' =>
                            Alignment::VERTICAL_CENTER,
                    ],
                ]
            );

            $row++;

            $sheet->setCellValue(
                "G{$row}",
                $data['tanggalTtd']
            );

            $sheet->mergeCells(
                "G{$row}:J{$row}"
            );

            $sheet->getStyle(
                "G{$row}"
            )->applyFromArray(
                $centerAlign
            );

            $row++;

            $sheet->setCellValue(
                "G{$row}",
                $data['ttdJabatan']
            );

            $sheet->mergeCells(
                "G{$row}:J{$row}"
            );

            $sheet->getStyle(
                "G{$row}"
            )->applyFromArray(
                array_merge(
                    $centerAlign,
                    $boldFont
                )
            );

            $row += 5;

            $sheet->setCellValue(
                "G{$row}",
                $data['ttdNama']
            );

            $sheet->mergeCells(
                "G{$row}:J{$row}"
            );

            $sheet->getStyle(
                "G{$row}"
            )->applyFromArray(
                [
                    'font' => [
                        'bold' => true,
                        'underline' => true,
                    ],
                    'alignment' =>
                        $centerAlign['alignment'],
                ]
            );

            $row++;

            $sheet->setCellValue(
                "G{$row}",
                $data['ttdNomorLabel']
                . '. '
                . $data['ttdNomor']
            );

            $sheet->mergeCells(
                "G{$row}:J{$row}"
            );

            $sheet->getStyle(
                "G{$row}"
            )->applyFromArray(
                $centerAlign
            );

            $sheet->getColumnDimension('A')
                ->setWidth(24);

            $sheet->getColumnDimension('B')
                ->setWidth(30);

            $sheet->getColumnDimension('C')
                ->setWidth(6);

            $sheet->getColumnDimension('D')
                ->setWidth(6);

            $sheet->getColumnDimension('E')
                ->setWidth(8);

            $sheet->getColumnDimension('F')
                ->setWidth(22);

            $sheet->getColumnDimension('G')
                ->setWidth(26);

            $sheet->getColumnDimension('H')
                ->setWidth(6);

            $sheet->getColumnDimension('I')
                ->setWidth(6);

            $sheet->getColumnDimension('J')
                ->setWidth(8);

            $namafile =
                'Transkrip-' .
                ($mahasiswa->npm ?: $mahasiswa->id) .
                '-' .
                preg_replace(
                    '/[^a-zA-Z0-9_\-]/',
                    '_',
                    (string) $mahasiswa->nama_lengkap
                ) .
                '.xlsx';

            $writer =
                new Xlsx($spreadsheet);

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            $tempDir =
                storage_path('app/temp');

            if (!is_dir($tempDir)) {
                mkdir(
                    $tempDir,
                    0755,
                    true
                );
            }

            $tempFile =
                $tempDir .
                DIRECTORY_SEPARATOR .
                'transkrip-' .
                $mahasiswa->id .
                '-' .
                substr(
                    md5(
                        uniqid(
                            (string) mt_rand(),
                            true
                        )
                    ),
                    0,
                    16
                ) .
                '.xlsx';

            $writer->save($tempFile);

            clearstatcache(
                true,
                $tempFile
            );

            if (
                !is_file($tempFile)
                || filesize($tempFile) < 1024
            ) {
                throw new \RuntimeException(
                    'File Excel gagal dibuat.'
                );
            }

            $zip =
                new ZipArchive();

            if (
                $zip->open($tempFile)
                !== true
            ) {
                throw new \RuntimeException(
                    'File XLSX tidak valid.'
                );
            }

            if (
                $zip->locateName(
                    '[Content_Types].xml'
                ) === false
            ) {
                $zip->close();

                throw new \RuntimeException(
                    'File XLSX rusak.'
                );
            }

            $zip->close();

            try {
                $loaded =
                    IOFactory::load(
                        $tempFile
                    );

                if (
                    !($loaded instanceof Spreadsheet)
                ) {
                    throw new \RuntimeException(
                        'Workbook tidak dapat dibaca kembali.'
                    );
                }
            } catch (\Throwable $e) {
                throw new \RuntimeException(
                    'Validasi Excel gagal: '
                    . $e->getMessage()
                );
            }

            return response()
                ->download(
                    $tempFile,
                    $namafile,
                    [
                        'Content-Type' =>
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                        'Cache-Control' =>
                            'no-cache, no-store, must-revalidate',

                        'Pragma' =>
                            'no-cache',

                        'Expires' =>
                            '0',
                    ]
                )
                ->deleteFileAfterSend(true);

        } catch (\Throwable $e) {

            while (ob_get_level() > 0) {
                @ob_end_clean();
            }

            if (
                $tempFile
                && is_file($tempFile)
            ) {
                @unlink($tempFile);
            }

            return redirect()
                ->back()
                ->withErrors([
                    'excel' =>
                        'Terjadi kesalahan saat generate Excel: '
                        . $e->getMessage(),
                ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | WORD
    |--------------------------------------------------------------------------
    | Word TIDAK lagi memakai:
    | admin.transkrip-nilai.word
    |
    | HTML langsung dibuat di controller.
    |--------------------------------------------------------------------------
    */

    public function word(
        Request $request,
        Mahasiswa $mahasiswa
    ) {
        $data =
            $this->buildTranskripData(
                $mahasiswa
            );

        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logoSrc =
            $this->getImageDataUri(
                $this->findLogoPath()
            );

        /*
        |--------------------------------------------------------------------------
        | FOTO MAHASISWA
        |--------------------------------------------------------------------------
        */

        $fotoSrc =
            $data['fotoMahasiswa']
            ?? null;

        if (
            empty($fotoSrc)
            && !empty($mahasiswa->foto_path)
        ) {
            $fotoSrc =
                $this->getImageDataUri(
                    $this->findFotoPath(
                        $mahasiswa
                    )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | BANGUN HTML WORD
        |--------------------------------------------------------------------------
        */

        $html = view('admin.transkrip-nilai.word', [
            'data' => $data,
            'logoSrc' => $logoSrc,
            'fotoSrc' => $fotoSrc,
        ])->render();

        /*
        |--------------------------------------------------------------------------
        | NAMA FILE
        |--------------------------------------------------------------------------
        */

        $namafile =
            'Transkrip-' .
            ($mahasiswa->npm ?: $mahasiswa->id) .
            '-' .
            preg_replace(
                '/[^a-zA-Z0-9_\-]/',
                '_',
                (string) $mahasiswa->nama_lengkap
            ) .
            '.doc';

        /*
        |--------------------------------------------------------------------------
        | DOWNLOAD
        |--------------------------------------------------------------------------
        */

        $forceDownload =
            (string) $request->query('download', '') !== ''
            || (string) $request->query('dl', '') !== ''
            || (string) $request->query('fd', '') !== ''
            || strtolower(
                (string) $request->query('disposition', '')
            ) === 'attachment';

        while (ob_get_level() > 0) {
            @ob_end_clean();
        }

        return response()->streamDownload(
            function () use ($html) {
                echo $html;
            },
            $namafile,
            [
                'Content-Type' =>
                    'application/msword; charset=UTF-8',

                'Content-Transfer-Encoding' =>
                    'binary',

                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0, private',

                'Pragma' =>
                    'public',

                'Expires' =>
                    'Sat, 26 Jul 1997 05:00:00 GMT',

                'X-Content-Type-Options' =>
                    'nosniff',

                'Content-Description' =>
                    'File Transfer',
            ],
            $forceDownload
                ? 'attachment'
                : 'inline'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HTML WORD
    |--------------------------------------------------------------------------
    */

    private function buildWordHtml(
        array $data,
        ?string $logoSrc,
        ?string $fotoSrc
    ): string {

        $mahasiswa = $data['mahasiswa'];

        $esc = function ($value): string {
            return htmlspecialchars(
                (string) ($value ?? ''),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        };

        $nama = $esc($mahasiswa->nama_lengkap);
        $npm = $esc($mahasiswa->npm ?? '-');
        $noIjazah = $esc($data['noIjazah'] ?? '-');
        $tempatTgl = $esc($data['tempatTgl'] ?? '-');
        $tanggalLulus = $esc($data['tanggalLulus'] ?? '-');
        $fakultas = $esc($mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan');
        $prodi = $esc($mahasiswa->program_studi ?? '-');
        $skBanpt = $esc($data['skBanpt'] ?? '-');
        $nomorTranskrip = $esc($data['nomorTranskrip'] ?? '-');
        $ipkRaw = (float) ($data['ipk'] ?? 0);
        $ipk = str_replace('.', ',', number_format($ipkRaw, 2));
        $predikat = $esc($data['predikat'] ?? '-');
        $judulSkripsi = $esc($data['judulSkripsi'] ?? '-');
        $tanggalTtd = $esc($data['tanggalTtd'] ?? '-');
        $ttdJabatan = $esc($data['ttdJabatan'] ?? 'DEKAN FAKULTAS');
        $ttdNama = $esc($data['ttdNama'] ?? '-');
        $ttdNomorLabel = $esc($data['ttdNomorLabel'] ?? 'NIDN');
        $ttdNomor = $esc($data['ttdNomor'] ?? '-');
        $totalSks = $esc($data['totalSks'] ?? 0);
        $totalMutuRaw = (float) ($data['totalMutu'] ?? 0);
        $totalMutu = $esc($this->formatNilaiM($totalMutuRaw));

        $logoImg = '';
        if (!empty($logoSrc)) {
            $logoImg = '<img src="' . $esc($logoSrc) . '" alt="Logo IAI DDI Sidrap" width="110" height="110" style="width:110px;height:110px;object-fit:contain;display:inline-block;">';
        }

        if (!empty($fotoSrc)) {
            $fotoInner = '<img src="' . $esc($fotoSrc) . '" alt="Foto ' . $nama . '" style="width:100%;height:100%;object-fit:cover;display:block;">';
        } else {
            $fotoInner = '<div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#888;font-size:10.5px;font-weight:400;line-height:1.25;text-align:center;background:#ffffff;">Foto<br>3 × 4</div>';
        }

        $semuaMK = $data['daftarMataKuliah'] ?? [];
        if (!is_array($semuaMK)) {
            $semuaMK = [];
        }

        $ujianKompre = $data['ujianKompre'] ?? [];
        if (!is_array($ujianKompre)) {
            $ujianKompre = [];
        }
        $ujianKompre = array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), $ujianKompre),
            fn ($v) => $v !== ''
        ));
        $ujianCount = count($ujianKompre);
        $totalMK = count($semuaMK);

        $totalMK = count($semuaMK);
        $halfAtas = (int) ceil($totalMK / 2);
        $kiriAtas = array_slice($semuaMK, 0, $halfAtas);
        $kananAtas = array_slice($semuaMK, $halfAtas);
        $maxAtas = max(count($kiriAtas), count($kananAtas));
        $noAwalKanan = count($kiriAtas);
        $barisBawah = 3 + $ujianCount;

        $formatMut = function ($nilaiM, $nilaiHuruf): string {
            $nm = (float) ($nilaiM ?? 0);
            if ($nm > 0) {
                $s = number_format($nm, 2, '.', '');
                $s = rtrim($s, '0');
                $s = rtrim($s, '.');
                return $s;
            }
            if ((string) $nilaiHuruf !== '') {
                return '0';
            }
            return '';
        };

        $tableRows = '';

        for ($i = 0; $i < $maxAtas; $i++) {
            $L = $kiriAtas[$i] ?? null;
            $R = $kananAtas[$i] ?? null;

            $noL = $L ? ($i + 1) : '';
            $noR = $R ? ($noAwalKanan + $i + 1) : '';
            $namaL = $L ? $esc($L->nama_mata_kuliah ?? '') : '';
            $sksL = $L ? (($L->sks ?? 0) == 0 ? '0' : $esc($L->sks)) : '';
            $nhL = $L ? ($esc($L->nilai_huruf ?? '')) : '';
            $mutuL = $L ? $formatMut($L->nilai_m ?? 0, $L->nilai_huruf ?? '') : '';

            $namaR = $R ? $esc($R->nama_mata_kuliah ?? '') : '';
            $sksR = $R ? (($R->sks ?? 0) == 0 ? '0' : $esc($R->sks)) : '';
            $nhR = $R ? ($esc($R->nilai_huruf ?? '')) : '';
            $mutuR = $R ? $formatMut($R->nilai_m ?? 0, $R->nilai_huruf ?? '') : '';

            $tableRows .=
                '<tr>' .
                '<td class="num">' . $noL . '</td>' .
                '<td class="mk">' . $namaL . '</td>' .
                '<td class="sks">' . $sksL . '</td>' .
                '<td class="nilaih">' . $nhL . '</td>' .
                '<td class="m">' . $mutuL . '</td>' .
                '<td class="num">' . $noR . '</td>' .
                '<td class="mk">' . $namaR . '</td>' .
                '<td class="sks">' . $sksR . '</td>' .
                '<td class="nilaih">' . $nhR . '</td>' .
                '<td class="m">' . $mutuR . '</td>' .
                '</tr>';
        }

        for ($bi = 0; $bi < $barisBawah; $bi++) {
            if ($bi === 0) {
                $tableRows .=
                    '<tr class="jumlah">' .
                    '<td class="num left-col"></td>' .
                    '<td class="mk left-col"></td>' .
                    '<td class="sks left-col"></td>' .
                    '<td class="nilaih left-col"></td>' .
                    '<td class="m left-col"></td>' .
                    '<td class="num jumlah-dashed"></td>' .
                    '<td class="mk">Jumlah</td>' .
                    '<td class="sks">' . $totalSks . '</td>' .
                    '<td class="nilaih"></td>' .
                    '<td class="m">' . $totalMutu . '</td>' .
                    '</tr>';
                continue;
            }

            if ($bi === 1) {
                $tableRows .=
                    '<tr class="spacer-row">' .
                    '<td class="num left-col"></td>' .
                    '<td class="mk left-col"></td>' .
                    '<td class="sks left-col"></td>' .
                    '<td class="nilaih left-col"></td>' .
                    '<td class="m left-col"></td>' .
                    '<td class="num"></td>' .
                    '<td class="mk"></td>' .
                    '<td class="sks"></td>' .
                    '<td class="nilaih"></td>' .
                    '<td class="m"></td>' .
                    '</tr>';
                continue;
            }

            if ($bi === 2) {
                $tableRows .=
                    '<tr class="ujian-head">' .
                    '<td class="num left-col"></td>' .
                    '<td class="mk left-col"></td>' .
                    '<td class="sks left-col"></td>' .
                    '<td class="nilaih left-col"></td>' .
                    '<td class="m left-col"></td>' .
                    '<td class="num ujian-right-spacer"></td>' .
                    '<td class="mk ujian-left-title" colspan="4">Ujian Kompetensi</td>' .
                    '</tr>';
                continue;
            }

            $uIdx = $bi - 3;
            $uNama = $ujianKompre[$uIdx] ?? '';
            $uNo = $uIdx + 1;
            $tableRows .=
                '<tr class="ujian-row">' .
                '<td class="num left-col"></td>' .
                '<td class="mk left-col"></td>' .
                '<td class="sks left-col"></td>' .
                '<td class="nilaih left-col"></td>' .
                '<td class="m left-col"></td>' .
                '<td class="num">' . $uNo . '</td>' .
                '<td class="mk">' . $esc($uNama) . '</td>' .
                '<td class="sks">0</td>' .
                '<td class="nilaih">A</td>' .
                '<td class="m">0</td>' .
                '</tr>';
        }

        $css = '
*, *:before, *:after { box-sizing: border-box !important; }
table, table th, table td { box-sizing: border-box !important; mso-cellspacing: 0; }
* { word-wrap: break-word !important; overflow-wrap: anywhere !important; white-space: normal !important; overflow: visible !important; }

@page WordSection1 {
    size: 210mm 297mm;
    margin: 0;
    margin-top: 0;
    margin-right: 0;
    margin-bottom: 0;
    margin-left: 0;
    mso-page-width: 595.28pt;
    mso-page-height: 841.89pt;
    mso-page-orientation: portrait;
}
div.WordSection1 { page: WordSection1; }

html, body {
    margin: 0 !important;
    padding: 0 !important;
    width: auto !important;
    height: auto !important;
    min-height: 0 !important;
    background: #ffffff !important;
    color: #000000 !important;
    font-family: "Times New Roman", Times, serif;
    mso-default-props: yes;
    mso-ascii-font-family: "Times New Roman";
    mso-hansi-font-family: "Times New Roman";
    mso-bidi-font-family: "Times New Roman";
    mso-font-kerning: 1.0pt;
    mso-text-indent: 0;
    mso-list: none;
    overflow: visible !important;
}

body {
    mso-title: 0;
    mso-body-margin-top: 0;
    mso-body-margin-bottom: 0;
    mso-body-margin-left: 0;
    mso-body-margin-right: 0;
    mso-header-margin: 0;
    mso-footer-margin: 0;
}

p.MsoNormal, li.MsoNormal, div.MsoNormal {
    margin: 0pt;
    margin-top: 0pt;
    margin-bottom: 0pt;
    margin-left: 0pt;
    margin-right: 0pt;
    text-indent: 0pt;
    mso-para-margin: 0pt;
    mso-para-margin-top: 0pt;
    mso-para-margin-bottom: 0pt;
    mso-para-margin-left: 0pt;
    mso-para-margin-right: 0pt;
    mso-para-indent: 0pt;
    line-height: 1.25;
}

div.WordSection1 {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
    overflow: visible !important;
}

.transcript-paper {
    display: block !important;
    width: 100% !important;
    min-height: 0 !important;
    height: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff;
    color: #000000;
    box-sizing: border-box !important;
    font-family: "Times New Roman", Times, serif;
    overflow: visible !important;
    line-height: 1.2;
}
.wrap { width: 100%; display: block; page-break-inside: auto !important; }

.kop-wrap { width: 100%; text-align: center; color: #000000; page-break-inside: avoid !important; }
.kop-logo-center { width: 100%; text-align: center; margin-bottom: 4px; display: block; }
.kop-logo-center img { width: 72px; height: 72px; object-fit: contain; display: inline-block; }
.kop-title-a { font-size: 16px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.16; margin: 1px 0 0; padding: 0; color: #000000; text-align: center; display: block; }
.kop-title-a2 { margin-top: 0.5px; }
.kop-title-b { font-size: 15px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.16; margin-top: 1px; padding: 0; color: #000000; text-align: center; display: block; }
.kop-terakreditasi { font-size: 8.8px; margin-top: 3px; line-height: 1.2; color: #000000; text-align: center; letter-spacing: 0.05px; display: block; }
.kop-alamat-line { font-size: 8.5px; margin-top: 2px; line-height: 1.2; color: #000000; text-align: center; display: block; }
.kop-email-web { margin-top: 1px; }
.kop-line-double { width: 100%; margin-top: 3px; display: block; }
.kop-line-double .kop-line-top { width: 100%; height: 2px; background: #000000; display: block; }
.kop-line-double .kop-line-bottom { width: 100%; height: 1px; background: #000000; margin-top: 2px; display: block; }

.judul-box { width: 100%; text-align: center; margin-top: 6px; margin-bottom: 2px; display: block; page-break-inside: avoid !important; }
.judul-text { font-size: 13px; font-weight: 700; letter-spacing: 1.3px; text-transform: uppercase; color: #000000; text-align: center; display: block; }
.judul-nomor { font-size: 8.2px; margin-top: 0.5px; color: #000000; text-align: center; display: block; }

.biodata {
    width: 100%; margin-top: 6px; border-collapse: collapse;
    table-layout: fixed; font-size: 9.2px; color: #000000; mso-cellspacing: 0;
    page-break-inside: avoid !important;
}
.biodata td { vertical-align: top; padding: 1.3px 0; line-height: 1.22; }
.biodata td.bio-label {
    width: 25%; padding-right: 10px; text-align: left; font-weight: 400;
    color: #000000; position: relative;
}
.biodata td.bio-label.right-label { width: 20%; }
.biodata td.bio-label:after {
    content: ":"; position: absolute; right: 0; top: 1.3px; color: #000000;
}
.biodata td.bio-value { width: 25%; padding-left: 6px; color: #000000; }
.biodata td.bio-value.right-val { width: 30%; }
.bio-val { display: inline !important; font-weight: 700; color: #000000; }

table.nilai {
    width: 100%; margin-top: 6px; border-collapse: collapse;
    table-layout: fixed; font-size: 8.5px; color: #000000; mso-cellspacing: 0;
    page-break-inside: auto !important;
}
table.nilai th {
    border: 1px solid #000000; background: #e6e6e6; font-weight: 700;
    letter-spacing: 0.1px; padding: 3.5px 3px; vertical-align: middle;
    line-height: 1.15; text-align: center;
}
table.nilai th.num { width: 4.5%; padding: 3.5px 3px; }
table.nilai th.mk { width: 29%; text-align: left; padding: 3.5px 5px; }
table.nilai th.sks { width: 5.5%; padding: 3.5px 3px; }
table.nilai th.nilaih { width: 5.5%; padding: 3.5px 3px; }
table.nilai th.m { width: 5.5%; padding: 3.5px 3px; }
table.nilai td {
    border: 1px solid #000000; padding: 3px 3px; vertical-align: middle;
    line-height: 1.15; text-align: center; color: #000000;
}
table.nilai td.num { width: 4.5%; padding: 3px 3px; }
table.nilai td.mk { width: 29%; text-align: left; padding: 3px 5px; }
table.nilai td.sks { width: 5.5%; padding: 3px 3px; }
table.nilai td.nilaih { width: 5.5%; padding: 3px 3px; font-weight: 700; }
table.nilai td.m { width: 5.5%; padding: 3px 3px; }

table.nilai tr { page-break-inside: avoid !important; break-inside: avoid !important; }
table.nilai thead tr { display: table-header-group !important; page-break-after: avoid !important; }

table.nilai tr.jumlah td {
    background: #ffffff !important; font-weight: 700; padding: 3px 5px; line-height: 1.15;
}
table.nilai tr.jumlah td.mk { text-align: center; }
table.nilai tr.jumlah td.jumlah-dashed {
    background: #ffffff !important;
    border-top: 1px dashed #000000 !important;
    border-bottom: 1px solid #000000 !important;
}
table.nilai tr.ujian-head td {
    background: #ffffff !important; font-weight: 700; letter-spacing: 0.1px;
    padding: 3px 5px; line-height: 1.15; font-size: 8.5px;
}
table.nilai td.ujian-left-title { text-align: left; padding-left: 6px !important; }
table.nilai tr.ujian-row td { font-size: 8.5px; padding: 3px 3px; line-height: 1.15; }
table.nilai tr.spacer-row td {
    background: #ffffff !important; border: 1px solid #000000;
    height: 10px; padding: 0;
}
table.nilai tr.jumlah td.left-col,
table.nilai tr.spacer-row td.left-col,
table.nilai tr.ujian-head td.left-col,
table.nilai tr.ujian-row td.left-col {
    background: #ffffff !important; font-weight: 400 !important;
    padding: 3px 3px !important; text-align: center !important; letter-spacing: 0 !important;
}
table.nilai tr.jumlah td.mk.left-col,
table.nilai tr.spacer-row td.mk.left-col,
table.nilai tr.ujian-head td.mk.left-col,
table.nilai tr.ujian-row td.mk.left-col {
    text-align: left !important; padding: 3px 5px !important;
}

.ringkasan {
    width: 100%; margin-top: 6px; border-collapse: collapse;
    table-layout: auto; font-size: 9.2px; color: #000000; mso-cellspacing: 0;
    page-break-inside: avoid !important;
}
.ringkasan td { vertical-align: top; padding: 1.3px 0; line-height: 1.22; }
.ringkasan td.label {
    width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 12px;
}
.ringkasan td.label-top {
    width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 12px; padding-top: 1.3px;
}
.ringkasan td.sep { width: auto; text-align: left; padding-right: 8px; }
.ringkasan td.sep-top { width: auto; text-align: left; padding-right: 8px; padding-top: 1.3px; }
.ringkasan td.val {
    font-weight: 800; color: #000000; font-size: 9.5px; white-space: nowrap;
}
.ringkasan td.val-judul {
    text-align: left; color: #000000; line-height: 1.22; padding: 1.3px 0;
    vertical-align: top;
}

.ttd-area {
    width: 100%; margin-top: 6px; padding-left: 86mm !important; page-break-inside: avoid !important; break-inside: avoid !important; display: block;
}
.ttd-foto-wrapper {
    width: 100%; margin: 0 !important; padding: 0 !important;
    border-collapse: collapse; mso-cellspacing: 0;
}
.ttd-foto-wrapper td { vertical-align: top; padding: 0; }
.ttd-foto-col { width: 28mm; padding-right: 2mm !important; }
.ttd-foto-box {
    width: 24mm; height: 32mm; margin: 0; padding: 0;
    border: 1px solid #333 !important; background: #fdfdfd !important;
    overflow: hidden; position: relative; box-sizing: border-box;
}
.ttd-col-wrapper { width: auto; }
.ttd-box {
    width: 100%; margin: 0; border-collapse: collapse;
    font-size: 9px; color: #000000; mso-cellspacing: 0;
}
.ttd-box td { vertical-align: top; }
.ttd-spacer-l { width: 0%; }
.ttd-spacer-r { width: 0%; }
.ttd-col {
    width: 100%; text-align: left; line-height: 1.25; color: #000000;
    padding-left: 0; font-size: 9px;
}
.ttd-jabatan { margin-top: 2px; font-weight: 800; letter-spacing: 0.15px; }
.ttd-nama { margin-top: 36px; font-weight: 800; text-decoration: underline; font-size: 9px; }
.ttd-nidk { margin-top: 0.5px; font-size: 8px; letter-spacing: 0.05px; }
';

        return '<html xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns:m="http://schemas.microsoft.com/office/2004/12/omml"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>Transkrip Akademik - ' . $nama . '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="ProgId" content="Word.Document">
    <meta name="Generator" content="Microsoft Word 15">
    <meta name="Originator" content="Microsoft Word 15">
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
            <w:Compatibility>
                <w:UseFELayout/>
                <w:SpaceForUL/>
                <w:BalanceSingleByteDoubleByteWidth/>
                <w:DoNotLeaveBackslashAlone/>
                <w:ULTrailSpace/>
                <w:DoNotExpandShiftReturn/>
                <w:AdjustLineHeightInTable/>
            </w:Compatibility>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>' . $css . '</style>
</head>
<body style="margin:0;padding:0;">
<div class="WordSection1" style="page: WordSection1; width:100%; margin:0; padding:0;">
<div class="transcript-paper">
<div class="wrap">

<div class="kop-wrap">
    <div class="kop-logo-center">' . $logoImg . '</div>
    <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
    <div class="kop-title-a kop-title-a2">DARUD DA\'WAH WAL IRSYAD</div>
    <div class="kop-title-b">SIDENRENG RAPPANG</div>
    <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
    <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
    <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
    <div class="kop-line-double">
        <div class="kop-line-top"></div>
        <div class="kop-line-bottom"></div>
    </div>
</div>

<div class="judul-box">
    <div class="judul-text">TRANSKRIP AKADEMIK</div>
    <div class="judul-nomor">Nomor : ' . $nomorTranskrip . '</div>
</div>

<table class="biodata" cellpadding="0" cellspacing="0">
    <tr>
        <td class="bio-label">Nama:</td>
        <td class="bio-value"><span class="bio-val">' . $nama . '</span></td>
        <td class="bio-label right-label">Program Pendidikan:</td>
        <td class="bio-value right-val"><span class="bio-val">Strata Satu (S1)</span></td>
    </tr>
    <tr>
        <td class="bio-label">No. Pokok Mahasiswa</td>
        <td class="bio-value"><span class="bio-val">' . $npm . '</span></td>
        <td class="bio-label right-label">Fakultas:</td>
        <td class="bio-value right-val"><span class="bio-val">' . $fakultas . '</span></td>
    </tr>
    <tr>
        <td class="bio-label">No. Ijazah</td>
        <td class="bio-value"><span class="bio-val">' . $noIjazah . '</span></td>
        <td class="bio-label right-label">Program Studi</td>
        <td class="bio-value right-val"><span class="bio-val">' . $prodi . '</span></td>
    </tr>
    <tr>
        <td class="bio-label">Tempat / Tanggal Lahir</td>
        <td class="bio-value"><span class="bio-val">' . $tempatTgl . '</span></td>
        <td class="bio-label right-label">No. SK BAN-PT</td>
        <td class="bio-value right-val"><span class="bio-val">' . $skBanpt . '</span></td>
    </tr>
    <tr>
        <td class="bio-label">Tanggal, Bulan dan Tahun Lulus:</td>
        <td class="bio-value"><span class="bio-val">' . $tanggalLulus . '</span></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table class="nilai" cellpadding="0" cellspacing="0">
<thead>
<tr>
    <th class="num">NO</th>
    <th class="mk">MATA KULIAH</th>
    <th class="sks">SKS</th>
    <th class="nilaih">NILAI</th>
    <th class="m">M</th>
    <th class="num">NO</th>
    <th class="mk">MATA KULIAH</th>
    <th class="sks">SKS</th>
    <th class="nilaih">NILAI</th>
    <th class="m">M</th>
</tr>
</thead>
<tbody>
' . $tableRows . '
</tbody>
</table>

<table class="ringkasan" cellpadding="0" cellspacing="0">
    <colgroup>
        <col style="width:290px;">
        <col style="width:20px;">
        <col style="width:auto;">
    </colgroup>
    <tr>
        <td class="label">INDEKS PRESTASI KUMULATIF (IPK)</td>
        <td class="sep">:</td>
        <td class="val">' . $ipk . '</td>
    </tr>
    <tr>
        <td class="label">PREDIKAT KELULUSAN</td>
        <td class="sep">:</td>
        <td class="val">' . $predikat . '</td>
    </tr>
    <tr>
        <td class="label-top">JUDUL SKRIPSI</td>
        <td class="sep-top">:</td>
        <td class="val-judul">' . $judulSkripsi . '</td>
    </tr>
</table>

<div class="ttd-area">
<table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0">
<tr>
    <td class="ttd-foto-col" style="vertical-align:top; padding-top:0;">
        <div class="ttd-foto-box">' . $fotoInner . '</div>
    </td>
    <td class="ttd-col-wrapper">
        <table class="ttd-box" cellpadding="0" cellspacing="0">
            <tr>
                <td class="ttd-spacer-l"></td>
                <td class="ttd-col">
                    <div>' . $tanggalTtd . '</div>
                    <div class="ttd-jabatan">' . $ttdJabatan . '</div>
                    <div class="ttd-nama">' . $ttdNama . '</div>
                    <div class="ttd-nidk">' . $ttdNomorLabel . '. ' . $ttdNomor . '</div>
                </td>
                <td class="ttd-spacer-r"></td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>
</div>
</div>
</div>
<!--[if gte mso 9]>
<xml>
  <w:sectPr>
    <w:pgSz w:w="11906" w:h="16838" w:orient="portrait"/>
    <w:pgMar w:top="720" w:right="850" w:bottom="680" w:left="850" w:header="567" w:footer="567" w:gutter="0"/>
    <w:cols w:space="360"/>
    <w:docGrid w:line-pitch="210"/>
  </w:sectPr>
</xml>
<![endif]-->
</body>
</html>';
    }


    /*
    |--------------------------------------------------------------------------
    | CARI LOGO
    |--------------------------------------------------------------------------
    */

    private function findLogoPath(): ?string
    {
        $candidates = [];

        try {
            $candidates[] =
                public_path('img/lo.jpeg');

            $candidates[] =
                public_path('img/lo.jpg');

            $candidates[] =
                public_path('img/lo.png');

            $candidates[] =
                public_path('img/logo.jpeg');

            $candidates[] =
                public_path('img/logo.jpg');

            $candidates[] =
                public_path('img/logo.png');

        } catch (\Throwable $e) {
        }


        try {

            $base =
                rtrim(
                    str_replace(
                        '\\',
                        '/',
                        base_path()
                    ),
                    '/'
                );

            $candidates[] =
                $base .
                '/public/img/lo.jpeg';

            $candidates[] =
                $base .
                '/public/img/lo.jpg';

            $candidates[] =
                $base .
                '/public/img/lo.png';

            $candidates[] =
                $base .
                '/public/img/logo.jpeg';

            $candidates[] =
                $base .
                '/public/img/logo.jpg';

            $candidates[] =
                $base .
                '/public/img/logo.png';

            $candidates[] =
                $base .
                '/public_html/img/lo.jpeg';

        } catch (\Throwable $e) {
        }


        try {

            $docRoot =
                rtrim(
                    str_replace(
                        '\\',
                        '/',
                        (string) (
                            $_SERVER['DOCUMENT_ROOT']
                            ?? ''
                        )
                    ),
                    '/'
                );

            if ($docRoot !== '') {

                $candidates[] =
                    $docRoot .
                    '/img/lo.jpeg';

                $candidates[] =
                    $docRoot .
                    '/img/lo.jpg';

                $candidates[] =
                    $docRoot .
                    '/img/lo.png';

                $candidates[] =
                    $docRoot .
                    '/img/logo.jpeg';

                $candidates[] =
                    $docRoot .
                    '/img/logo.jpg';

                $candidates[] =
                    $docRoot .
                    '/img/logo.png';

            }

        } catch (\Throwable $e) {
        }


        foreach ($candidates as $path) {

            if (
                is_string($path)
                && @is_file($path)
                && @is_readable($path)
            ) {
                return $path;
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | CARI FOTO MAHASISWA
    |--------------------------------------------------------------------------
    */

    private function findFotoPath(
        Mahasiswa $mahasiswa
    ): ?string {

        $relPath =
            trim(
                str_replace(
                    ['/', '\\'],
                    '/',
                    (string) $mahasiswa->foto_path
                ),
                '/'
            );

        if ($relPath === '') {
            return null;
        }

        $candidates = [];

        try {
            $candidates[] =
                public_path(
                    'storage/' . $relPath
                );
        } catch (\Throwable $e) {
        }


        try {

            $docRoot =
                rtrim(
                    str_replace(
                        '\\',
                        '/',
                        (string) (
                            $_SERVER['DOCUMENT_ROOT']
                            ?? ''
                        )
                    ),
                    '/'
                );

            if ($docRoot !== '') {

                $candidates[] =
                    $docRoot .
                    '/storage/' .
                    $relPath;

                $candidates[] =
                    $docRoot .
                    '/' .
                    $relPath;
            }

        } catch (\Throwable $e) {
        }


        foreach ($candidates as $path) {

            $path =
                str_replace(
                    ['/', '\\'],
                    DIRECTORY_SEPARATOR,
                    (string) $path
                );

            if (
                @is_file($path)
                && @is_readable($path)
                && @filesize($path) > 200
            ) {
                return $path;
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE → DATA URI
    |--------------------------------------------------------------------------
    */

    private function getImageDataUri(
        ?string $path
    ): ?string {

        if (
            empty($path)
            || !@is_file($path)
            || !@is_readable($path)
        ) {
            return null;
        }

        try {

            $content =
                @file_get_contents($path);

            if (
                $content === false
                || $content === ''
            ) {
                return null;
            }

            $ext =
                strtolower(
                    pathinfo(
                        $path,
                        PATHINFO_EXTENSION
                    )
                );

            $mimeMap = [
                'jpg' =>
                    'image/jpeg',

                'jpeg' =>
                    'image/jpeg',

                'png' =>
                    'image/png',

                'gif' =>
                    'image/gif',

                'webp' =>
                    'image/webp',
            ];

            $mime =
                $mimeMap[$ext]
                ?? null;

            if (!$mime) {
                return null;
            }

            return
                'data:' .
                $mime .
                ';base64,' .
                base64_encode($content);

        } catch (\Throwable $e) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | NILAI MUTU
    |--------------------------------------------------------------------------
    */

    private function nilaiMutuHuruf(
        string $huruf
    ): float {

        return match (
            strtoupper(trim($huruf))
        ) {

            'A' =>
                4.00,

            'A-' =>
                3.70,

            'B+' =>
                3.30,

            'B' =>
                3.00,

            'B-' =>
                2.70,

            'C+' =>
                2.30,

            'C' =>
                2.00,

            'C-' =>
                1.70,

            'D' =>
                1.00,

            default =>
                0.00,
        };
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT NILAI M
    |--------------------------------------------------------------------------
    */

    private function buildPdfHtml(
        array $data,
        ?string $logoBase64Src,
        ?string $fotoBase64Src
    ): string {

        $mahasiswa = $data['mahasiswa'];

        $esc = function ($value): string {
            return htmlspecialchars(
                (string) ($value ?? ''),
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        };

        $nama = $esc($mahasiswa->nama_lengkap);
        $npm = $esc($mahasiswa->npm ?? '-');
        $noIjazah = $esc($data['noIjazah'] ?? '-');
        $tempatTgl = $esc($data['tempatTgl'] ?? '-');
        $tanggalLulus = $esc($data['tanggalLulus'] ?? '-');
        $fakultas = $esc($mahasiswa->fakultas ?? 'Fakultas Tarbiyah & Keguruan');
        $prodi = $esc($mahasiswa->program_studi ?? '-');
        $skBanpt = $esc($data['skBanpt'] ?? '-');
        $nomorTranskrip = $esc($data['nomorTranskrip'] ?? '-');
        $ipkRaw = (float) ($data['ipk'] ?? 0);
        $ipk = str_replace('.', ',', number_format($ipkRaw, 2));
        $predikat = $esc($data['predikat'] ?? '-');
        $judulSkripsi = $esc($data['judulSkripsi'] ?? '-');
        $tanggalTtd = $esc($data['tanggalTtd'] ?? '-');
        $ttdJabatan = $esc($data['ttdJabatan'] ?? 'DEKAN FAKULTAS');
        $ttdNama = $esc($data['ttdNama'] ?? '-');
        $ttdNomorLabel = $esc($data['ttdNomorLabel'] ?? 'NIDN');
        $ttdNomor = $esc($data['ttdNomor'] ?? '-');
        $totalSks = $esc($data['totalSks'] ?? 0);
        $totalMutuRaw = (float) ($data['totalMutu'] ?? 0);
        $totalMutuStr = rtrim(rtrim(number_format($totalMutuRaw, 2, '.', ''), '0'), '.');

        $logoImg = '';
        if (!empty($logoBase64Src)) {
            $logoImg = '<img src="' . $esc($logoBase64Src) . '" alt="Logo IAI DDI Sidrap" width="110" height="110">';
        }

        if (!empty($fotoBase64Src)) {
            $fotoInner = '<img src="' . $esc($fotoBase64Src) . '" alt="Foto ' . $nama . '">';
        } else {
            $fotoInner = '<div class="ttd-foto-empty">Foto<br>3 × 4</div>';
        }

        $semuaMK = $data['daftarMataKuliah'] ?? [];
        if (!is_array($semuaMK)) {
            $semuaMK = [];
        }

        $ujianKompre = $data['ujianKompre'] ?? [];
        if (!is_array($ujianKompre)) {
            $ujianKompre = [];
        }
        $ujianKompre = array_values(array_filter(
            array_map(fn ($v) => trim((string) $v), $ujianKompre),
            fn ($v) => $v !== ''
        ));
        $ujianCount = count($ujianKompre);
        $totalMK = count($semuaMK);

        $totalMK = count($semuaMK);
        $halfAtas = (int) ceil($totalMK / 2);
        $kiriAtas = array_slice($semuaMK, 0, $halfAtas);
        $kananAtas = array_slice($semuaMK, $halfAtas);
        $maxAtas = max(count($kiriAtas), count($kananAtas));
        $noAwalKanan = count($kiriAtas);
        $barisBawah = 3 + $ujianCount;

        $formatMut = function ($nilaiM, $nilaiHuruf): string {
            $nm = (float) ($nilaiM ?? 0);
            if ($nm > 0) {
                return rtrim(rtrim(number_format($nm, 2, '.', ''), '0'), '.');
            }
            if ((string) $nilaiHuruf !== '') {
                return '0';
            }
            return '';
        };

        $tableRows = '';

        for ($i = 0; $i < $maxAtas; $i++) {
            $L = $kiriAtas[$i] ?? null;
            $R = $kananAtas[$i] ?? null;

            $noL = $L ? ($i + 1) : '';
            $noR = $R ? ($noAwalKanan + $i + 1) : '';
            $namaL = $L ? $esc($L->nama_mata_kuliah ?? '') : '';
            $sksValL = (int) ($L->sks ?? 0);
            $sksL = $L ? (($sksValL === 0) ? '0' : $esc($L->sks)) : '';
            $nhL = $L ? $esc($L->nilai_huruf ?? '') : '';
            $mutuL = $L ? $formatMut($L->nilai_m ?? 0, $L->nilai_huruf ?? '') : '';

            $namaR = $R ? $esc($R->nama_mata_kuliah ?? '') : '';
            $sksValR = (int) ($R->sks ?? 0);
            $sksR = $R ? (($sksValR === 0) ? '0' : $esc($R->sks)) : '';
            $nhR = $R ? $esc($R->nilai_huruf ?? '') : '';
            $mutuR = $R ? $formatMut($R->nilai_m ?? 0, $R->nilai_huruf ?? '') : '';

            $tableRows .=
                '<tr>' .
                '<td class="num">' . $noL . '</td>' .
                '<td class="mk">' . $namaL . '</td>' .
                '<td class="sks">' . $sksL . '</td>' .
                '<td class="nilaih">' . $nhL . '</td>' .
                '<td class="m">' . $mutuL . '</td>' .
                '<td class="num">' . $noR . '</td>' .
                '<td class="mk">' . $namaR . '</td>' .
                '<td class="sks">' . $sksR . '</td>' .
                '<td class="nilaih">' . $nhR . '</td>' .
                '<td class="m">' . $mutuR . '</td>' .
                '</tr>';
        }

        for ($bi = 0; $bi < $barisBawah; $bi++) {
            $LL = $mkBawahKiri[$bi] ?? null;
            $namaLL = $LL ? $esc($LL->nama_mata_kuliah ?? '') : '';
            $sksValLL = (int) ($LL->sks ?? 0);
            $sksLL = $LL ? (($sksValLL === 0) ? '0' : $esc($LL->sks)) : '';
            $nhLL = $LL ? $esc($LL->nilai_huruf ?? '') : '';
            $mutuLL = $LL ? $formatMut($LL->nilai_m ?? 0, $LL->nilai_huruf ?? '') : '';
            $noLanjutTampil = $LL ? ($noAwalKanan + count($kananAtas) + $bi + 1) : '';

            if ($bi === 0) {
                $tableRows .=
                    '<tr class="jumlah">' .
                    '<td class="num left-col">' . $noLanjutTampil . '</td>' .
                    '<td class="mk left-col">' . $namaLL . '</td>' .
                    '<td class="sks left-col">' . $sksLL . '</td>' .
                    '<td class="nilaih left-col">' . $nhLL . '</td>' .
                    '<td class="m left-col">' . $mutuLL . '</td>' .
                    '<td class="num jumlah-dashed"></td>' .
                    '<td class="mk">Jumlah</td>' .
                    '<td class="sks">' . $totalSks . '</td>' .
                    '<td class="nilaih"></td>' .
                    '<td class="m">' . $totalMutuStr . '</td>' .
                    '</tr>';
                continue;
            }

            if ($bi === 1) {
                $tableRows .=
                    '<tr class="spacer-row">' .
                    '<td class="num left-col">' . $noLanjutTampil . '</td>' .
                    '<td class="mk left-col">' . $namaLL . '</td>' .
                    '<td class="sks left-col">' . $sksLL . '</td>' .
                    '<td class="nilaih left-col">' . $nhLL . '</td>' .
                    '<td class="m left-col">' . $mutuLL . '</td>' .
                    '<td class="num"></td>' .
                    '<td class="mk"></td>' .
                    '<td class="sks"></td>' .
                    '<td class="nilaih"></td>' .
                    '<td class="m"></td>' .
                    '</tr>';
                continue;
            }

            if ($bi === 2) {
                $tableRows .=
                    '<tr class="ujian-head">' .
                    '<td class="num left-col">' . $noLanjutTampil . '</td>' .
                    '<td class="mk left-col">' . $namaLL . '</td>' .
                    '<td class="sks left-col">' . $sksLL . '</td>' .
                    '<td class="nilaih left-col">' . $nhLL . '</td>' .
                    '<td class="m left-col">' . $mutuLL . '</td>' .
                    '<td class="num ujian-right-spacer"></td>' .
                    '<td class="mk ujian-left-title" colspan="4">Ujian Kompetensi</td>' .
                    '</tr>';
                continue;
            }

            $uIdx = $bi - 3;
            $uNama = $ujianKompre[$uIdx] ?? '';
            $uNo = $uIdx + 1;
            $tableRows .=
                '<tr class="ujian-row">' .
                '<td class="num left-col">' . $noLanjutTampil . '</td>' .
                '<td class="mk left-col">' . $namaLL . '</td>' .
                '<td class="sks left-col">' . $sksLL . '</td>' .
                '<td class="nilaih left-col">' . $nhLL . '</td>' .
                '<td class="m left-col">' . $mutuLL . '</td>' .
                '<td class="num">' . $uNo . '</td>' .
                '<td class="mk">' . $esc($uNama) . '</td>' .
                '<td class="sks">0</td>' .
                '<td class="nilaih">A</td>' .
                '<td class="m">0</td>' .
                '</tr>';
        }

        $css = '
@page {
    size: 210mm 297mm portrait;
    margin: 0 !important;
    padding: 0 !important;
}
*, *:before, *:after { box-sizing: border-box !important; }
table, table th, table td { box-sizing: border-box !important; }
* {
    word-wrap: break-word !important;
    overflow-wrap: anywhere !important;
    white-space: normal !important;
    text-overflow: clip !important;
    overflow: visible !important;
}
html, body {
    margin: 0 !important;
    padding: 0 !important;
    width: 210mm !important;
    height: auto !important;
    min-height: 0 !important;
    background: #fff !important;
    color: #000 !important;
    font-family: \'Times New Roman\', Times, serif;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    overflow: visible !important;
}
thead { display: table-header-group; }
tfoot { display: table-footer-group; }
tr { page-break-inside: avoid !important; break-inside: avoid !important; }
th, td { page-break-inside: avoid !important; break-inside: avoid !important; }
body::after, .wrap::after, .transcript-paper::after {
    content: \'\' !important;
    display: none !important;
    clear: both;
}
/* ===== A4 PROFESSIONAL TRANSKRIP (COMPACT 12/15mm MARGIN) ===== */
.transcript-paper {
    width: 210mm !important;
    height: auto !important;
    max-height: none !important;
    margin: 0 !important;
    padding: 12mm 15mm 12mm 15mm !important;
    background: #ffffff;
    color: #000000;
    box-shadow: none !important;
    border-radius: 0 !important;
    overflow: visible !important;
    box-sizing: border-box !important;
    font-family: \'Times New Roman\', Times, serif;
    page-break-after: avoid !important;
    page-break-inside: auto !important;
    line-height: 1.2;
}
.wrap { width: 100%; }

.kop-wrap { width: 100%; text-align: center; color: #000000; page-break-inside: avoid !important; break-inside: avoid !important; }
.kop-logo-center { width: 100%; text-align: center; margin-bottom: 4px; }
.kop-logo-center img { width: 72px; height: 72px; object-fit: contain; display: inline-block; }
.kop-title-a {
    font-size: 16px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.15; margin: 2px 0 0; padding: 0; color: #000000;
}
.kop-title-a2 { margin-top: 1px; }
.kop-title-b {
    font-size: 15px; font-weight: 800; letter-spacing: 0.6px; line-height: 1.15; margin: 2px 0 0; padding: 0; color: #000000;
}
.kop-terakreditasi {
    font-size: 8.8px; margin-top: 4px; color: #000000; text-align: center; letter-spacing: 0.1px;
}
.kop-alamat-line {
    font-size: 8.5px; margin-top: 3px; line-height: 1.2; color: #000000; text-align: center;
}
.kop-email-web { margin-top: 1px; }
.kop-line-double {
    margin-top: 4px;
    width: 100%;
    display: block;
}
.kop-line-double .kop-line-top {
    width: 100%; height: 2px; background: #000000;
}
.kop-line-double .kop-line-bottom {
    width: 100%; height: 1px; background: #000000; margin-top: 2px;
}

.judul-box { text-align: center; margin-top: 6px; page-break-inside: avoid !important; break-inside: avoid !important; }
.judul-text {
    font-size: 13px; font-weight: 700; letter-spacing: 1.3px; text-transform: uppercase;
    text-decoration: none; color: #000000;
}
.judul-nomor { font-size: 8.5px; margin-top: 1px; color: #000000; }

.biodata {
    width: 100%; margin-top: 6px; border-collapse: collapse;
    font-size: 9.2px; color: #000000; table-layout: fixed;
    page-break-inside: avoid !important; break-inside: avoid !important;
}
.biodata td { vertical-align: top; padding: 1.3px 0; line-height: 1.25; }
.biodata td.bio-label {
    width: 25%;
    padding: 1.3px 12px 1.3px 0;
    text-align: left;
    font-weight: 400;
    color: #000000;
    position: relative;
}
.biodata td.bio-label.right-label {
    width: 20%;
}
.biodata td.bio-value {
    width: 25%;
    padding: 1.3px 0 1.3px 7px;
    color: #000000;
}
.biodata td.bio-value.right-val {
    width: 30%;
}
.bio-val { font-weight: 700; color: #000000; display: inline !important; }

table.nilai {
    width: 100%; border-collapse: collapse; margin-top: 6px;
    font-size: 8.5px; color: #000000; table-layout: fixed;
    page-break-inside: auto !important;
}
table.nilai tr { page-break-inside: avoid !important; break-inside: avoid !important; }
table.nilai thead tr { display: table-header-group !important; page-break-after: avoid !important; }
table.nilai th {
    border: 1px solid #000; background: #e0f2ea; font-weight: 700; letter-spacing: 0.15px;
    padding: 3.5px 3px; vertical-align: middle; line-height: 1.18; text-align: center;
}
table.nilai th.mk { text-align: left; padding: 3.5px 5px; width: 29%; }
table.nilai th.num { width: 4.5%; padding: 3.5px 3px; }
table.nilai th.sks { width: 5.5%; padding: 3.5px 3px; }
table.nilai th.nilaih { width: 5.5%; padding: 3.5px 3px; }
table.nilai th.m { width: 5.5%; padding: 3.5px 3px; }
table.nilai td {
    border: 1px solid #000; padding: 3px 3px; vertical-align: middle;
    line-height: 1.18; text-align: center; color: #000000;
}
table.nilai td.mk { text-align: left; padding: 3px 5px; width: 29%; }
table.nilai td.num { width: 4.5%; padding: 3px 3px; }
table.nilai td.sks { width: 5.5%; padding: 3px 3px; }
table.nilai td.nilaih { width: 5.5%; font-weight: 700; padding: 3px 3px; }
table.nilai td.m { width: 5.5%; padding: 3px 3px; }
table.nilai tr.jumlah td {
    background: #ffffff !important; font-weight: 700; padding: 3px 5px;
    letter-spacing: 0.2px; line-height: 1.18; font-size: 8.5px;
}
table.nilai tr.jumlah td.mk { text-align: center; }
table.nilai tr.jumlah td.jumlah-dashed {
    background: #ffffff !important;
    border-top: 1px dashed #000000 !important;
    border-bottom: 1px solid #000000 !important;
}
table.nilai tr.ujian-head td {
    background: #ffffff !important; font-weight: 700; letter-spacing: 0.15px;
    padding: 3px 5px; line-height: 1.18; font-size: 8.5px;
}
table.nilai td.ujian-left-title { text-align: left; padding-left: 6px !important; }
table.nilai tr.spacer-row td {
    background: #ffffff !important; border: 1px solid #000000;
    height: 10px; padding: 0;
}
table.nilai tr.ujian-row td { font-size: 8.5px; padding: 3px 5px; line-height: 1.18; }
table.nilai tr.jumlah td.left-col,
table.nilai tr.spacer-row td.left-col,
table.nilai tr.ujian-head td.left-col,
table.nilai tr.ujian-row td.left-col {
    background: #ffffff !important;
    font-weight: 400 !important;
    padding: 3px 5px !important;
    text-align: center !important;
    letter-spacing: 0 !important;
}
table.nilai tr.jumlah td.mk.left-col,
table.nilai tr.spacer-row td.mk.left-col,
table.nilai tr.ujian-head td.mk.left-col,
table.nilai tr.ujian-row td.mk.left-col {
    text-align: left !important;
    padding: 3px 5px !important;
}

.ringkasan {
    width: 100%; margin-top: 6px; border-collapse: collapse;
    font-size: 9.2px; color: #000000; table-layout: auto;
    page-break-inside: avoid !important; break-inside: avoid !important;
}
.ringkasan td { vertical-align: top; padding: 1.3px 0; line-height: 1.25; }
.ringkasan td.label {
    width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding-right: 14px;
}
.ringkasan td.label-top {
    width: auto; white-space: nowrap; font-weight: 700; color: #000000; padding: 1.3px 14px 0 0;
}
.ringkasan td.sep   { width: auto; text-align: left; padding-right: 10px; }
.ringkasan td.sep-top { width: auto; text-align: left; padding: 1.3px 10px 0 0; }
.ringkasan td.val   { font-weight: 800; color: #000000; font-size: 9.5px; width: auto; white-space: nowrap; }
.ringkasan td.val-judul {
    text-align: left; color: #000000; line-height: 1.25; padding: 1.3px 0;
    vertical-align: top; width: auto;
}

.ttd-foto-wrapper {
    width: 100%; margin-top: 6px !important; border-collapse: collapse;
    padding-left: 0 !important;
    page-break-inside: avoid !important; break-inside: avoid !important;
}
.ttd-foto-wrapper td { vertical-align: top; padding: 0; }
.ttd-foto-col {
    width: 28mm; padding-right: 2mm;
}
.ttd-foto-box {
    width: 24mm; height: 32mm;
    border: 1px solid #333; background: #fdfdfd;
    overflow: hidden; box-sizing: border-box;
    position: relative;
    margin: 0;
}
.ttd-foto-box img {
    width: 100%; height: 100%; object-fit: cover; display: block;
}
.ttd-foto-empty {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    flex-direction: column;
    color: #888; font-size: 9px; font-weight: 400;
    line-height: 1.2; text-align: center;
    background: #ffffff;
}
.ttd-col-wrapper { width: auto; }
.ttd-box {
    width: 100%; margin-top: 0; border-collapse: collapse;
    font-size: 9px; color: #000000;
}
.ttd-box td { vertical-align: top; }
.ttd-spacer-l { width: 0%; }
.ttd-spacer-r { width: 0%; }
.ttd-col { width: 100%; text-align: left; line-height: 1.28; color: #000000; padding-left: 0; font-size: 9px; }
.ttd-jabatan { margin-top: 3px; font-weight: 800; letter-spacing: 0.2px; }
.ttd-nama    { margin-top: 36px; font-weight: 800; text-decoration: underline; font-size: 9px; }
.ttd-nidk    { margin-top: 1px; font-size: 8.2px; letter-spacing: 0.1px; }
';

        return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Transkrip Akademik - ' . $nama . '</title>
    <style>' . $css . '</style>
</head>
<body>
<div class="transcript-paper">
    <div class="wrap">
        <div class="kop-wrap">
            <div class="kop-logo-center">
                ' . $logoImg . '
            </div>
            <div class="kop-title-a">INSTITUT AGAMA ISLAM</div>
            <div class="kop-title-a kop-title-a2">DARUD DA\'WAH WAL IRSYAD</div>
            <div class="kop-title-b">SIDENRENG RAPPANG</div>
            <div class="kop-terakreditasi">TERAKREDITASI INSTITUSI • SK : 337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026</div>
            <div class="kop-alamat-line">Alamat : Jl. Tugu Tani Kel. Majelling Watang Sidenreng Rappang</div>
            <div class="kop-alamat-line kop-email-web">E-mail : iaiddisidrap@gmail.com &nbsp;&nbsp; Website : www.yppddisrapp.ac.id</div>
            <div class="kop-line-double">
                <div class="kop-line-top"></div>
                <div class="kop-line-bottom"></div>
            </div>
        </div>

        <div class="judul-box">
            <div class="judul-text">Transkrip Akademik</div>
            <div class="judul-nomor">Nomor : ' . $nomorTranskrip . '</div>
        </div>

        <table class="biodata" cellpadding="0" cellspacing="0">
            <tr>
                <td class="bio-label">Nama:</td>
                <td class="bio-value"><span class="bio-val">' . $nama . '</span></td>
                <td class="bio-label right-label">Program Pendidikan:</td>
                <td class="bio-value right-val"><span class="bio-val">Strata Satu (S1)</span></td>
            </tr>
            <tr>
                <td class="bio-label">No. Pokok Mahasiswa</td>
                <td class="bio-value"><span class="bio-val">' . $npm . '</span></td>
                <td class="bio-label right-label">Fakultas:</td>
                <td class="bio-value right-val"><span class="bio-val">' . $fakultas . '</span></td>
            </tr>
            <tr>
                <td class="bio-label">No. Ijazah</td>
                <td class="bio-value"><span class="bio-val">' . $noIjazah . '</span></td>
                <td class="bio-label right-label">Program Studi</td>
                <td class="bio-value right-val"><span class="bio-val">' . $prodi . '</span></td>
            </tr>
            <tr>
                <td class="bio-label">Tempat / Tanggal Lahir</td>
                <td class="bio-value"><span class="bio-val">' . $tempatTgl . '</span></td>
                <td class="bio-label right-label">No. SK BAN-PT</td>
                <td class="bio-value right-val"><span class="bio-val">' . $skBanpt . '</span></td>
            </tr>
            <tr>
                <td class="bio-label">Tanggal, Bulan dan Tahun Lulus:</td>
                <td class="bio-value"><span class="bio-val">' . $tanggalLulus . '</span></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <table class="nilai" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th class="num">NO</th>
                    <th class="mk">MATA KULIAH</th>
                    <th class="sks">SKS</th>
                    <th class="nilaih">NILAI</th>
                    <th class="m">M</th>
                    <th class="num">NO</th>
                    <th class="mk">MATA KULIAH</th>
                    <th class="sks">SKS</th>
                    <th class="nilaih">NILAI</th>
                    <th class="m">M</th>
                </tr>
            </thead>
            <tbody>
                ' . $tableRows . '
            </tbody>
        </table>

        <table class="ringkasan" cellpadding="0" cellspacing="0">
            <colgroup>
                <col style="width:290px;">
                <col style="width:22px;">
                <col style="width:auto;">
            </colgroup>
            <tr>
                <td class="label">INDEKS PRESTASI KUMULATIF (IPK)</td>
                <td class="sep">:</td>
                <td class="val">' . $ipk . '</td>
            </tr>
            <tr>
                <td class="label">PREDIKAT KELULUSAN</td>
                <td class="sep">:</td>
                <td class="val">' . $predikat . '</td>
            </tr>
            <tr>
                <td class="label-top">JUDUL SKRIPSI</td>
                <td class="sep-top">:</td>
                <td class="val-judul">' . $judulSkripsi . '</td>
            </tr>
        </table>

        <div style="page-break-inside: avoid; padding-left:90mm !important; margin-top:10px !important;">
            <table class="ttd-foto-wrapper" cellpadding="0" cellspacing="0" style="padding-left:0 !important; margin:0 !important; border-collapse: collapse;">
                <tr>
                    <td class="ttd-foto-col" style="vertical-align: top; padding-top: 0;">
                        <div class="ttd-foto-box" style="margin-top: 0;">
                            ' . $fotoInner . '
                        </div>
                    </td>
                    <td class="ttd-col-wrapper">
                        <table class="ttd-box" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="ttd-spacer-l"></td>
                                <td class="ttd-col">
                                    <div>' . $tanggalTtd . '</div>
                                    <div class="ttd-jabatan">' . $ttdJabatan . '</div>
                                    <div class="ttd-nama">' . $ttdNama . '</div>
                                    <div class="ttd-nidk">' . $ttdNomorLabel . '. ' . $ttdNomor . '</div>
                                </td>
                                <td class="ttd-spacer-r"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
</body>
</html>';
    }
    private function formatNilaiM(
        $nilai
    ): string {

        $nilai =
            (float) $nilai;

        if ($nilai <= 0) {
            return '0';
        }

        return rtrim(
            rtrim(
                number_format(
                    $nilai,
                    2,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PREDIKAT
    |--------------------------------------------------------------------------
    */

    private function predikat(
        float $ipk
    ): string {

        if ($ipk >= 3.75) {
            return 'Dengan Pujian';
        }

        if ($ipk >= 3.50) {
            return 'Sangat Memuaskan';
        }

        if ($ipk >= 3.00) {
            return 'Memuaskan';
        }

        if ($ipk >= 2.00) {
            return 'Cukup';
        }

        return 'Kurang';
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT TANGGAL INDONESIA
    |--------------------------------------------------------------------------
    */

    private function formatTanggalID(
        $tanggal,
        string $fallback = '-'
    ): string {

        try {

            if (
                $tanggal === null
                || $tanggal === ''
            ) {
                return $fallback;
            }

            $c =
                $tanggal instanceof \Illuminate\Support\Carbon
                || $tanggal instanceof \DateTimeInterface

                    ? \Illuminate\Support\Carbon::instance(
                        $tanggal
                    )

                    : \Illuminate\Support\Carbon::parse(
                        $tanggal
                    );

            $bulanID = [
                1 =>
                    'Januari',

                2 =>
                    'Februari',

                3 =>
                    'Maret',

                4 =>
                    'April',

                5 =>
                    'Mei',

                6 =>
                    'Juni',

                7 =>
                    'Juli',

                8 =>
                    'Agustus',

                9 =>
                    'September',

                10 =>
                    'Oktober',

                11 =>
                    'November',

                12 =>
                    'Desember',
            ];

            $hari =
                (int) $c->format('j');

            $bulan =
                (int) $c->format('n');

            $tahun =
                (int) $c->format('Y');

            return sprintf(
                '%02d %s %04d',
                $hari,
                $bulanID[$bulan]
                    ?? $c->format('F'),
                $tahun
            );

        } catch (\Throwable $e) {

            return $fallback;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEFAULT UJIAN
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | DATA TRANSKRIP
    |--------------------------------------------------------------------------
    */

    private function buildTranskripData(
        Mahasiswa $mahasiswa
    ): array {

        $mahasiswa->load([
            'khs.items.mataKuliah',
            'dosenPenasehat',
            'dekanFakultas',
        ]);

        $items = [];

        foreach (
            $mahasiswa->khs
            as $khs
        ) {

            foreach (
                $khs->items
                as $khsItem
            ) {

                if (
                    $khsItem->mataKuliah
                ) {
                    $items[] =
                        $khsItem;
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | SORT MATA KULIAH
        |--------------------------------------------------------------------------
        */

        usort(
            $items,
            function ($a, $b) {

                $sa =
                    (int) (
                        $a->mataKuliah->semester
                        ?? 0
                    );

                $sb =
                    (int) (
                        $b->mataKuliah->semester
                        ?? 0
                    );

                if ($sa !== $sb) {
                    return $sa <=> $sb;
                }

                return strnatcasecmp(
                    (string) (
                        $a->mataKuliah->kode
                        ?? ''
                    ),
                    (string) (
                        $b->mataKuliah->kode
                        ?? ''
                    )
                );
            }
        );


        $totalSks = 0;
        $totalMutu = 0;

        $sksLulus = 0;
        $mutuLulus = 0;

        $daftarMataKuliah = [];


        /*
        |--------------------------------------------------------------------------
        | HITUNG NILAI
        |--------------------------------------------------------------------------
        */

        foreach (
            $items as $it
        ) {

            $mk =
                $it->mataKuliah;

            $sks =
                (int) (
                    $mk->sks
                    ?? 0
                );

            $huruf =
                (string) (
                    $it->nilai_huruf
                    ?? ''
                );

            $nilaiM =
                $this->nilaiMutuHuruf(
                    $huruf
                );

            $m =
                $sks * $nilaiM;

            $namaMk =
                (string) (
                    $mk->nama
                    ?? ''
                );

            if ($namaMk !== '') {

                $daftarMataKuliah[] =
                    (object) [
                        'nama_mata_kuliah' =>
                            $namaMk,

                        'sks' =>
                            $sks,

                        'nilai_huruf' =>
                            $huruf,

                        'nilai_m' =>
                            $m,
                    ];
            }

            $it->_sks =
                $sks;

            $it->_nilai_huruf =
                $huruf;

            $it->_nilai_m =
                $m;

            $it->_semester =
                $mk->semester
                ?? 0;

            $totalSks +=
                $sks;

            $totalMutu +=
                $m;


            if (
                $huruf !== ''
                && strtoupper($huruf) !== 'E'
            ) {

                $sksLulus +=
                    $sks;

                $mutuLulus +=
                    $m;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | UJIAN
        |--------------------------------------------------------------------------
        */

        $ujianKompre =
            is_array(
                $mahasiswa->ujian_kompre
            )
                ? $mahasiswa->ujian_kompre
                : [];

        $ujianKompre =
            array_values(
                array_filter(
                    array_map(
                        fn ($v) =>
                            trim((string) $v),
                        $ujianKompre
                    ),
                    fn ($v) =>
                        $v !== ''
                )
            );

        if (
            count($ujianKompre) === 0
        ) {
            $ujianKompre =
                $this->defaultUjianKompre();
        }


        $ujianAda =
            $ujianKompre;

        $ujianCount =
            count($ujianAda);


        /*
        |--------------------------------------------------------------------------
        | BAGIAN KIRI / KANAN
        |--------------------------------------------------------------------------
        */

        $totalItem =
            count($daftarMataKuliah);

        $jumlahKiri =
            (int) ceil(
                $totalItem / 2
            );

        $bagianKiri =
            array_slice(
                $daftarMataKuliah,
                0,
                $jumlahKiri
            );

        $bagianKanan =
            array_slice(
                $daftarMataKuliah,
                $jumlahKiri
            );


        /*
        |--------------------------------------------------------------------------
        | IPK
        |--------------------------------------------------------------------------
        */

        $ipk = 0;

        if (
            $sksLulus > 0
        ) {

            $ipk =
                round(
                    $mutuLulus
                    / $sksLulus,
                    2
                );
        }


        /*
        |--------------------------------------------------------------------------
        | NOMOR TRANSKRIP
        |--------------------------------------------------------------------------
        */

        $nomorTranskrip =
            $mahasiswa->nomor_transkrip
            ?: (
                'TR' .
                (
                    $mahasiswa->npm
                    ?: '0000' .
                    $mahasiswa->id
                ) .
                now()->format('Ym')
            );


        /*
        |--------------------------------------------------------------------------
        | JUDUL SKRIPSI
        |--------------------------------------------------------------------------
        */

        $judulSkripsi =
            (string) (
                $mahasiswa->judul_skripsi
                ?? '-'
            );

        if (
            $judulSkripsi === ''
        ) {
            $judulSkripsi =
                '-';
        }


        /*
        |--------------------------------------------------------------------------
        | TEMPAT TANGGAL LAHIR
        |--------------------------------------------------------------------------
        */

        $tempatLahir =
            (string) (
                $mahasiswa->tempat_lahir
                ?? '-'
            );

        if (
            $tempatLahir === ''
        ) {
            $tempatLahir =
                '-';
        }

        $tglLahir =
            $this->formatTanggalID(
                $mahasiswa->tanggal_lahir,
                '-'
            );

        if (
            $tempatLahir !== '-'
            && $tglLahir !== '-'
        ) {

            $tempatTgl =
                $tempatLahir .
                ', ' .
                $tglLahir;

        } else {

            $gabung =
                (
                    $tempatLahir !== '-'
                        ? $tempatLahir
                        : ''
                )
                .
                (
                    $tglLahir !== '-'
                        ? $tglLahir
                        : ''
                );

            $tempatTgl =
                $gabung !== ''
                    ? $gabung
                    : '-';
        }


        /*
        |--------------------------------------------------------------------------
        | TANGGAL LULUS
        |--------------------------------------------------------------------------
        */

        $tanggalLulus =
            $this->formatTanggalID(
                $mahasiswa->tanggal_lulus,
                '-'
            );


        /*
        |--------------------------------------------------------------------------
        | TANGGAL TTD
        |--------------------------------------------------------------------------
        */

        $tglTtd =
            $mahasiswa->tanggal_lulus
                ? \Illuminate\Support\Carbon::parse(
                    $mahasiswa->tanggal_lulus
                )
                : now();

        $tglTtdStr =
            $this->formatTanggalID(
                $tglTtd,
                ''
            );

        if (
            $tglTtdStr === ''
            || $tglTtdStr === '-'
        ) {
            $tglTtdStr =
                $this->formatTanggalID(
                    now(),
                    '-'
                );
        }

        $tanggalTtd =
            'Pangkajene, ' .
            $tglTtdStr;


        /*
        |--------------------------------------------------------------------------
        | SK BAN-PT
        |--------------------------------------------------------------------------
        */

        $skBanpt =
            (string) (
                $mahasiswa->nomor_sk_banpt
                ?? ''
            );

        if (
            $skBanpt === ''
        ) {
            $skBanpt =
                '337/SK/BAN-PT/Ak-S/2.0/PT/VI/2026';
        }


        /*
        |--------------------------------------------------------------------------
        | NO IJAZAH
        |--------------------------------------------------------------------------
        */

        $noIjazah =
            (string) (
                $mahasiswa->no_ijazah
                ?? ''
            );

        if (
            $noIjazah === ''
        ) {

            $noIjazah =
                (string) (
                    $mahasiswa->nik
                    ?? '-'
                );
        }

        if (
            $noIjazah === ''
        ) {
            $noIjazah =
                '-';
        }


        /*
        |--------------------------------------------------------------------------
        | DEKAN
        |--------------------------------------------------------------------------
        */

        $dekan =
            $mahasiswa->dekanFakultas;

        if (!$dekan) {

            $fakultasMhs =
                (string) (
                    $mahasiswa->fakultas
                    ?? ''
                );

            if (
                $fakultasMhs !== ''
            ) {

                $dekan =
                    \App\Models\Dosen::query()
                        ->where(
                            function ($s) {

                                $s->whereNotNull(
                                    'jabatan_struktural'
                                )
                                ->whereRaw(
                                    'LOWER(jabatan_struktural) LIKE ?',
                                    ['%dekan%fakultas%']
                                )
                                ->orWhereRaw(
                                    'LOWER(jabatan_struktural) LIKE ?',
                                    ['%dekan%']
                                );
                            }
                        )
                        ->where(
                            'fakultas',
                            $fakultasMhs
                        )
                        ->first();
            }


            if (!$dekan) {

                $dekan =
                    \App\Models\Dosen::query()
                        ->where(
                            function ($s) {

                                $s->whereNotNull(
                                    'jabatan_struktural'
                                )
                                ->whereRaw(
                                    'LOWER(jabatan_struktural) LIKE ?',
                                    ['%dekan%fakultas%']
                                )
                                ->orWhereRaw(
                                    'LOWER(jabatan_struktural) LIKE ?',
                                    ['%dekan%']
                                );
                            }
                        )
                        ->first();
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DATA TTD
        |--------------------------------------------------------------------------
        */

        if ($dekan) {

            $ttdJabatan =
                trim(
                    (string) (
                        $dekan->jabatan_struktural
                        ?? ''
                    )
                );

            if (
                $ttdJabatan === ''
            ) {

                $fakultasTtd =
                    (string) (
                        $dekan->fakultas
                        ?: (
                            $mahasiswa->fakultas
                            ?? ''
                        )
                    );

                if (
                    $fakultasTtd !== ''
                ) {

                    $fakultasClean =
                        $fakultasTtd;

                    if (
                        preg_match(
                            '/^fakultas\s+/i',
                            $fakultasClean
                        )
                    ) {

                        $fakultasClean =
                            preg_replace(
                                '/^fakultas\s+/i',
                                '',
                                $fakultasClean,
                                1
                            );
                    }

                    $ttdJabatan =
                        'DEKAN FAKULTAS ' .
                        strtoupper(
                            trim(
                                $fakultasClean
                            )
                        );

                } else {

                    $ttdJabatan =
                        'DEKAN FAKULTAS';
                }

            } else {

                $jab =
                    $ttdJabatan;

                if (
                    preg_match(
                        '/^dekan\s+fakultas\s+fakultas\s+/i',
                        $jab
                    )
                ) {

                    $ttdJabatan =
                        preg_replace(
                            '/^dekan\s+fakultas\s+/i',
                            'DEKAN FAKULTAS ',
                            $jab,
                            1
                        );
                }

                $ttdJabatan =
                    strtoupper(
                        trim(
                            (string)
                                $ttdJabatan
                        )
                    );
            }


            $ttdNama =
                (string) (
                    $dekan->nama
                    ?? ''
                );

            $nomorInduk =
                (string) (
                    $dekan->nidn
                    ?? ''
                );

            $labelInduk =
                'NIDN';


            if (
                $nomorInduk === ''
                && !empty($dekan->nidk)
            ) {

                $nomorInduk =
                    (string) $dekan->nidk;

                $labelInduk =
                    'NIDK';
            }


            if (
                $nomorInduk === ''
                && !empty($dekan->nip)
            ) {

                $nomorInduk =
                    (string) $dekan->nip;

                $labelInduk =
                    'NIP';
            }


            if (
                $nomorInduk === ''
                && !empty($dekan->nuptk)
            ) {

                $nomorInduk =
                    (string) $dekan->nuptk;

                $labelInduk =
                    'NUPTK';
            }


            if (
                $ttdNama === ''
            ) {

                $ttdNama =
                    'Dr. H. UMAR YAHYA, M.Ag.';

                $nomorInduk =
                    '8932610021';

                $labelInduk =
                    'NIDK';
            }

        } else {

            $fakultasTtd =
                (string) (
                    $mahasiswa->fakultas
                    ?? 'Fakultas Tarbiyah & Keguruan'
                );

            $ttdJabatan =
                'DEKAN FAKULTAS ' .
                strtoupper(
                    $fakultasTtd
                );

            $ttdNama =
                'Dr. H. UMAR YAHYA, M.Ag.';

            $nomorInduk =
                '8932610021';

            $labelInduk =
                'NIDK';
        }


        $ttdNomor =
            $nomorInduk;

        $ttdNomorLabel =
            $labelInduk;


        /*
        |--------------------------------------------------------------------------
        | FOTO MAHASISWA
        |--------------------------------------------------------------------------
        */

        $fotoMahasiswa =
            null;

        if (
            !empty(
                $mahasiswa->foto_path
            )
        ) {

            try {

                $fotoPath =
                    $this->findFotoPath(
                        $mahasiswa
                    );

                if ($fotoPath) {

                    $fotoMahasiswa =
                        $this->getImageDataUri(
                            $fotoPath
                        );
                }

            } catch (\Throwable $e) {

                $fotoMahasiswa =
                    null;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            'mahasiswa' =>
                $mahasiswa,

            'items' =>
                $items,

            'daftarMataKuliah' =>
                $daftarMataKuliah,

            'bagianKiri' =>
                $bagianKiri,

            'bagianKanan' =>
                $bagianKanan,

            'left' =>
                $bagianKiri,

            'right' =>
                $bagianKanan,

            'half' =>
                count($bagianKiri),

            'maxRows' =>
                max(
                    count($bagianKiri),
                    count($bagianKanan)
                ),

            'totalSks' =>
                $totalSks,

            'totalMutu' =>
                round(
                    $totalMutu,
                    2
                ),

            'sksLulus' =>
                $sksLulus,

            'mutuLulus' =>
                round(
                    $mutuLulus,
                    2
                ),

            'ipk' =>
                $ipk,

            'predikat' =>
                $this->predikat(
                    $ipk
                ),

            'nomorTranskrip' =>
                $nomorTranskrip,

            'ujianKompre' =>
                $ujianKompre,

            'ujianAda' =>
                $ujianAda,

            'ujianCount' =>
                $ujianCount,

            'judulSkripsi' =>
                $judulSkripsi,

            'tempatTgl' =>
                $tempatTgl,

            'tanggalLulus' =>
                $tanggalLulus,

            'tanggalTtd' =>
                $tanggalTtd,

            'skBanpt' =>
                $skBanpt,

            'noIjazah' =>
                $noIjazah,

            'fotoMahasiswa' =>
                $fotoMahasiswa,

            'ttdJabatan' =>
                $ttdJabatan,

            'ttdNama' =>
                $ttdNama,

            'ttdNomor' =>
                $ttdNomor,

            'ttdNomorLabel' =>
                $ttdNomorLabel,
        ];
    }
}
