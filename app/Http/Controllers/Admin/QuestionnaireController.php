<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Support\QuestionnaireService;
use Dompdf\Dompdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionnaireController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $showAllCourses = $request->boolean('all');
        $courseSummaries = $this->buildCourseSummaryQuery($q);

        return view('admin.kuesioner.index', [
            'questions' => QuestionnaireQuestion::query()
                ->withCount('answers')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'courseSummaries' => $showAllCourses
                ? $courseSummaries
                    ->orderByDesc('responses_count')
                    ->orderBy('mata_kuliah.kode')
                    ->get()
                : $courseSummaries
                    ->orderByDesc('responses_count')
                    ->orderBy('mata_kuliah.kode')
                    ->paginate(10)
                    ->withQueryString(),
            'q' => $q,
            'showAllCourses' => $showAllCourses,
            'summary' => [
                'responses_count' => QuestionnaireResponse::query()->count(),
                'students_count' => QuestionnaireResponse::query()->distinct('mahasiswa_id')->count('mahasiswa_id'),
                'questions_count' => QuestionnaireQuestion::query()->where('is_active', true)->count(),
                'average_score' => QuestionnaireAnswer::query()->avg('score'),
            ],
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
        ]);
    }

    public function create(): View
    {
        return view('admin.kuesioner.create');
    }

    public function exportSummaryPdf(Request $request)
    {
        try {
            $data = $this->buildSummaryExportData($request);
            $html = view('questionnaire.summary-pdf', $data)->render();

            $dompdf = new Dompdf(['isRemoteEnabled' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="rekap-kuesioner.pdf"',
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.kuesioner.index', $request->only(['q', 'all', 'page']))
                ->with('error', 'Gagal generate PDF rekap kuesioner. Coba perkecil data dengan filter atau gunakan pagination.');
        }
    }

    public function exportSummaryExcel(Request $request)
    {
        $data = $this->buildSummaryExportData($request);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kuesioner');

        $sheet->fromArray([
            ['Rekap Kuesioner Mahasiswa'],
            ['Filter Pencarian', $data['q'] !== '' ? $data['q'] : 'Semua data'],
            ['Total Respon', $data['summary']['responses_count']],
            ['Mahasiswa Mengisi', $data['summary']['students_count']],
            ['Pertanyaan Aktif', $data['summary']['questions_count']],
            ['Rata-rata Skor', $data['summary']['average_score'] !== null ? round((float) $data['summary']['average_score'], 2) : '-'],
            [],
            ['No', 'Kode Mata Kuliah', 'Nama Mata Kuliah', 'Semester', 'Dosen 1', 'Dosen 2', 'Respon', 'Rata-rata', 'Kurang (%)', 'Cukup (%)', 'Baik (%)', 'Sangat Baik (%)'],
        ]);

        $row = 9;
        foreach ($data['courseSummaries'] as $index => $course) {
            $sheet->fromArray([[
                $index + 1,
                $course->kode,
                $course->nama,
                $course->semester,
                $course->dosen_1 ?? '-',
                $course->dosen_2 ?? '-',
                $course->responses_count,
                $course->average_score !== null ? (float) $course->average_score : '-',
                $course->score_1_pct !== null ? (float) $course->score_1_pct : '-',
                $course->score_2_pct !== null ? (float) $course->score_2_pct : '-',
                $course->score_3_pct !== null ? (float) $course->score_3_pct : '-',
                $course->score_4_pct !== null ? (float) $course->score_4_pct : '-',
            ]], null, 'A'.$row);
            $row++;
        }

        foreach (range('A', 'L') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'questionnaire-summary');
        $writer->save($tempFile);

        return response()->download($tempFile, 'rekap-kuesioner.xlsx')->deleteFileAfterSend(true);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        QuestionnaireQuestion::query()->create([
            'question' => trim((string) $validated['question']),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.kuesioner.index')->with('success', 'Pertanyaan kuesioner berhasil ditambahkan.');
    }

    public function show(MataKuliah $mataKuliah): View
    {
        return view('admin.kuesioner.show', $this->buildShowData($mataKuliah));
    }

    public function exportPdf(MataKuliah $mataKuliah)
    {
        $report = $this->buildReportData($mataKuliah);

        $html = view('questionnaire.pdf', $report)->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'kuesioner-'.$mataKuliah->kode.'-'.$mataKuliah->id.'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportExcel(MataKuliah $mataKuliah)
    {
        $report = $this->buildReportData($mataKuliah);
        $spreadsheet = new Spreadsheet();

        $statsSheet = $spreadsheet->getActiveSheet();
        $statsSheet->setTitle('Statistik');
        $statsSheet->fromArray([
            ['Laporan Kuesioner Mata Kuliah'],
            ['Kode Mata Kuliah', $mataKuliah->kode],
            ['Nama Mata Kuliah', $mataKuliah->nama],
            ['Dosen 1', $mataKuliah->dosen?->nama ?? '-'],
            ['Dosen 2', $mataKuliah->dosen2?->nama ?? '-'],
            ['Total Respon', $report['responses']->count()],
            [],
            ['No', 'Pertanyaan', 'Jawaban', 'Rata-rata', 'Kurang (%)', 'Cukup (%)', 'Baik (%)', 'Sangat Baik (%)'],
        ]);

        $row = 9;
        foreach ($report['questionStats'] as $index => $stat) {
            $totalAnswers = (int) $stat->answers_count;
            $statsSheet->fromArray([[
                $index + 1,
                $stat->question,
                $totalAnswers,
                $stat->average_score !== null ? (float) $stat->average_score : '-',
                $totalAnswers > 0 ? round(($stat->score_1_total / $totalAnswers) * 100, 2) : '-',
                $totalAnswers > 0 ? round(($stat->score_2_total / $totalAnswers) * 100, 2) : '-',
                $totalAnswers > 0 ? round(($stat->score_3_total / $totalAnswers) * 100, 2) : '-',
                $totalAnswers > 0 ? round(($stat->score_4_total / $totalAnswers) * 100, 2) : '-',
            ]], null, 'A'.$row);
            $row++;
        }

        $commentSheet = $spreadsheet->createSheet();
        $commentSheet->setTitle('Komentar');
        $commentSheet->fromArray([
            ['No', 'Nama Mahasiswa', 'NPM', 'Tanggal', 'Rata-rata', 'Komentar'],
        ]);

        $row = 2;
        foreach ($report['responses'] as $index => $response) {
            $commentSheet->fromArray([[
                $index + 1,
                $response->mahasiswa?->nama_lengkap ?? '-',
                $response->mahasiswa?->npm ?? '-',
                $response->created_at?->format('d/m/Y H:i'),
                round((float) $response->answers->avg('score'), 2),
                $response->komentar ?: '-',
            ]], null, 'A'.$row);
            $row++;
        }

        foreach (range('A', 'H') as $column) {
            $statsSheet->getColumnDimension($column)->setAutoSize(true);
        }
        foreach (range('A', 'F') as $column) {
            $commentSheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'kuesioner-'.$mataKuliah->kode.'-'.$mataKuliah->id.'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'questionnaire');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function edit(QuestionnaireQuestion $question): View
    {
        return view('admin.kuesioner.edit', [
            'question' => $question,
        ]);
    }

    public function update(Request $request, QuestionnaireQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $question->update([
            'question' => trim((string) $validated['question']),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.kuesioner.index')->with('success', 'Pertanyaan kuesioner berhasil diperbarui.');
    }

    public function destroy(QuestionnaireQuestion $question): RedirectResponse
    {
        if ($question->answers()->exists()) {
            return back()->with('error', 'Pertanyaan yang sudah memiliki jawaban tidak dapat dihapus.');
        }

        $question->delete();

        return redirect()->route('admin.kuesioner.index')->with('success', 'Pertanyaan kuesioner berhasil dihapus.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:questionnaire_questions,id'],
        ]);

        $questions = QuestionnaireQuestion::query()
            ->withCount('answers')
            ->whereIn('id', $validated['ids'])
            ->get();

        $blockedCount = $questions->where('answers_count', '>', 0)->count();
        $deletedCount = 0;

        foreach ($questions->where('answers_count', 0) as $question) {
            $question->delete();
            $deletedCount++;
        }

        if ($deletedCount === 0 && $blockedCount > 0) {
            return back()->with('error', 'Pertanyaan yang sudah memiliki jawaban tidak dapat dihapus.');
        }

        if ($blockedCount > 0) {
            return back()->with('success', $deletedCount.' pertanyaan berhasil dihapus. '.$blockedCount.' pertanyaan dilewati karena sudah memiliki jawaban.');
        }

        return back()->with('success', $deletedCount.' pertanyaan berhasil dihapus.');
    }

    public function bulkDestroyCourses(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mata_kuliah_ids' => ['required', 'array', 'min:1'],
            'mata_kuliah_ids.*' => ['integer', 'exists:mata_kuliah,id'],
        ]);

        $deletedCount = QuestionnaireResponse::query()
            ->whereIn('mata_kuliah_id', $validated['mata_kuliah_ids'])
            ->delete();

        return back()->with('success', $deletedCount.' hasil kuesioner berhasil dihapus.');
    }

    private function courseStatsSubquery()
    {
        return DB::table('questionnaire_responses')
            ->leftJoin('questionnaire_answers', 'questionnaire_responses.id', '=', 'questionnaire_answers.questionnaire_response_id')
            ->select('questionnaire_responses.mata_kuliah_id')
            ->selectRaw('COUNT(DISTINCT questionnaire_responses.id) as responses_count')
            ->selectRaw('ROUND(AVG(questionnaire_answers.score), 2) as average_score')
            ->selectRaw('ROUND(100 * SUM(CASE WHEN questionnaire_answers.score = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(questionnaire_answers.id), 0), 2) as score_1_pct')
            ->selectRaw('ROUND(100 * SUM(CASE WHEN questionnaire_answers.score = 2 THEN 1 ELSE 0 END) / NULLIF(COUNT(questionnaire_answers.id), 0), 2) as score_2_pct')
            ->selectRaw('ROUND(100 * SUM(CASE WHEN questionnaire_answers.score = 3 THEN 1 ELSE 0 END) / NULLIF(COUNT(questionnaire_answers.id), 0), 2) as score_3_pct')
            ->selectRaw('ROUND(100 * SUM(CASE WHEN questionnaire_answers.score = 4 THEN 1 ELSE 0 END) / NULLIF(COUNT(questionnaire_answers.id), 0), 2) as score_4_pct')
            ->groupBy('questionnaire_responses.mata_kuliah_id');
    }

    private function buildCourseSummaryQuery(string $q)
    {
        $courseStats = $this->courseStatsSubquery();

        $courseSummaries = MataKuliah::query()
            ->leftJoinSub($courseStats, 'stats', function ($join) {
                $join->on('mata_kuliah.id', '=', 'stats.mata_kuliah_id');
            })
            ->leftJoin('dosen as dosen1', 'mata_kuliah.dosen_id', '=', 'dosen1.id')
            ->leftJoin('dosen as dosen2', 'mata_kuliah.dosen_id_2', '=', 'dosen2.id')
            ->select('mata_kuliah.*')
            ->selectRaw('dosen1.nama as dosen_1')
            ->selectRaw('dosen2.nama as dosen_2')
            ->selectRaw('COALESCE(stats.responses_count, 0) as responses_count')
            ->selectRaw('stats.average_score')
            ->selectRaw('stats.score_1_pct')
            ->selectRaw('stats.score_2_pct')
            ->selectRaw('stats.score_3_pct')
            ->selectRaw('stats.score_4_pct');

        if ($q !== '') {
            $courseSummaries->where(function ($query) use ($q) {
                $query->where('mata_kuliah.kode', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah.nama', 'like', "%{$q}%")
                    ->orWhere('dosen1.nama', 'like', "%{$q}%")
                    ->orWhere('dosen2.nama', 'like', "%{$q}%");
            });
        }

        return $courseSummaries->orderByDesc('responses_count')->orderBy('mata_kuliah.kode');
    }

    private function buildSummaryExportData(Request $request): array
    {
        $q = trim((string) $request->get('q', ''));
        $showAll = $request->boolean('all');
        $page = (int) $request->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }
        $selectedMataKuliahIds = $request->input('mata_kuliah_ids', []);
        if (!is_array($selectedMataKuliahIds)) {
            $selectedMataKuliahIds = [];
        }
        $selectedMataKuliahIds = array_filter(array_map('intval', $selectedMataKuliahIds));

        $courseQuery = $this->buildCourseSummaryQuery($q)
            ->orderByDesc('responses_count')
            ->orderBy('mata_kuliah.kode');

        if (!empty($selectedMataKuliahIds)) {
            $courseQuery->whereIn('mata_kuliah.id', $selectedMataKuliahIds);
        }

        $courseSummaries = $showAll
            ? $courseQuery->get()
            : collect($courseQuery->paginate(10, ['*'], 'page', $page)->items());

        // Build summary stats, filtered if selected courses are provided
        $responseQuery = QuestionnaireResponse::query();
        $answerQuery = QuestionnaireAnswer::query();

        if (!empty($selectedMataKuliahIds)) {
            $responseQuery->whereIn('mata_kuliah_id', $selectedMataKuliahIds);
            $answerQuery->whereHas('response', function ($q) use ($selectedMataKuliahIds) {
                $q->whereIn('mata_kuliah_id', $selectedMataKuliahIds);
            });
        }

        return [
            'q' => $q,
            'selectedMataKuliahIds' => $selectedMataKuliahIds,
            'courseSummaries' => $courseSummaries,
            'summary' => [
                'responses_count' => $responseQuery->count(),
                'students_count' => $responseQuery->distinct('mahasiswa_id')->count('mahasiswa_id'),
                'questions_count' => QuestionnaireQuestion::query()->where('is_active', true)->count(),
                'average_score' => $answerQuery->avg('score'),
            ],
        ];
    }

    private function buildShowData(MataKuliah $mataKuliah): array
    {
        $report = $this->buildReportData($mataKuliah);

        return [
            'mataKuliah' => $report['mataKuliah'],
            'questionStats' => $report['questionStats'],
            'responses' => QuestionnaireResponse::query()
                ->with(['mahasiswa', 'answers'])
                ->where('mata_kuliah_id', $mataKuliah->id)
                ->latest()
                ->paginate(10),
            'scoreLabels' => $report['scoreLabels'],
        ];
    }

    private function buildReportData(MataKuliah $mataKuliah): array
    {
        $mataKuliah->load(['dosen', 'dosen2']);

        $questionStatsSubquery = DB::table('questionnaire_answers')
            ->join('questionnaire_responses', 'questionnaire_responses.id', '=', 'questionnaire_answers.questionnaire_response_id')
            ->where('questionnaire_responses.mata_kuliah_id', $mataKuliah->id)
            ->select('questionnaire_answers.questionnaire_question_id')
            ->selectRaw('COUNT(*) as answers_count')
            ->selectRaw('ROUND(AVG(questionnaire_answers.score), 2) as average_score')
            ->selectRaw('SUM(CASE WHEN questionnaire_answers.score = 1 THEN 1 ELSE 0 END) as score_1_total')
            ->selectRaw('SUM(CASE WHEN questionnaire_answers.score = 2 THEN 1 ELSE 0 END) as score_2_total')
            ->selectRaw('SUM(CASE WHEN questionnaire_answers.score = 3 THEN 1 ELSE 0 END) as score_3_total')
            ->selectRaw('SUM(CASE WHEN questionnaire_answers.score = 4 THEN 1 ELSE 0 END) as score_4_total')
            ->groupBy('questionnaire_answers.questionnaire_question_id');

        return [
            'mataKuliah' => $mataKuliah,
            'questionStats' => QuestionnaireQuestion::query()
                ->leftJoinSub($questionStatsSubquery, 'stats', function ($join) {
                    $join->on('questionnaire_questions.id', '=', 'stats.questionnaire_question_id');
                })
                ->select('questionnaire_questions.*')
                ->selectRaw('COALESCE(stats.answers_count, 0) as answers_count')
                ->selectRaw('stats.average_score')
                ->selectRaw('COALESCE(stats.score_1_total, 0) as score_1_total')
                ->selectRaw('COALESCE(stats.score_2_total, 0) as score_2_total')
                ->selectRaw('COALESCE(stats.score_3_total, 0) as score_3_total')
                ->selectRaw('COALESCE(stats.score_4_total, 0) as score_4_total')
                ->orderBy('questionnaire_questions.sort_order')
                ->orderBy('questionnaire_questions.id')
                ->get(),
            'responses' => QuestionnaireResponse::query()
                ->with(['mahasiswa', 'answers'])
                ->where('mata_kuliah_id', $mataKuliah->id)
                ->latest()
                ->get(),
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
        ];
    }
}
