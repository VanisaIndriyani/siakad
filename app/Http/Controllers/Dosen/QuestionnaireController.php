<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\QuestionnaireService;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QuestionnaireController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;
        $q = trim((string) $request->get('q', ''));

        abort_unless($dosen, 404);

        $courseStats = $this->courseStatsSubquery();

        $courses = MataKuliah::query()
            ->leftJoinSub($courseStats, 'stats', function ($join) {
                $join->on('mata_kuliah.id', '=', 'stats.mata_kuliah_id');
            })
            ->select('mata_kuliah.*')
            ->selectRaw('COALESCE(stats.responses_count, 0) as responses_count')
            ->selectRaw('stats.average_score')
            ->selectRaw('stats.score_1_pct')
            ->selectRaw('stats.score_2_pct')
            ->selectRaw('stats.score_3_pct')
            ->selectRaw('stats.score_4_pct')
            ->where(function ($query) use ($dosen) {
                $query->where('mata_kuliah.dosen_id', $dosen?->id)
                    ->orWhere('mata_kuliah.dosen_id_2', $dosen?->id);
            });

        if ($q !== '') {
            $courses->where(function ($query) use ($q) {
                $query->where('mata_kuliah.kode', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah.nama', 'like', "%{$q}%");
            });
        }

        return view('dosen.kuesioner.index', [
            'courses' => $courses
                ->orderByDesc('responses_count')
                ->orderBy('mata_kuliah.kode')
                ->paginate(10)
                ->withQueryString(),
            'q' => $q,
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
        ]);
    }

    public function exportSummaryPdf(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;
        $q = trim((string) $request->get('q', ''));

        abort_unless($dosen, 404);

        $data = $this->buildSummaryExportData((int) $dosen->id, $dosen->nama ?? null, $q);

        $html = view('questionnaire.summary-pdf-dosen', $data)->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="rekap-kuesioner-dosen.pdf"',
        ]);
    }

    public function exportSummaryExcel(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;
        $q = trim((string) $request->get('q', ''));

        abort_unless($dosen, 404);

        $data = $this->buildSummaryExportData((int) $dosen->id, $dosen->nama ?? null, $q);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Kuesioner');

        $sheet->fromArray([
            ['Rekap Kuesioner Dosen'],
            ['Nama Dosen', $data['dosen_name'] ?: '-'],
            ['Filter Pencarian', $data['q'] !== '' ? $data['q'] : 'Semua data'],
            ['Jumlah Mata Kuliah', $data['courseSummaries']->count()],
            [],
            ['No', 'Kode Mata Kuliah', 'Nama Mata Kuliah', 'Semester', 'Respon', 'Rata-rata', 'Kurang (%)', 'Cukup (%)', 'Baik (%)', 'Sangat Baik (%)'],
        ]);

        $row = 6;
        foreach ($data['courseSummaries'] as $index => $course) {
            $sheet->fromArray([[
                $index + 1,
                $course->kode,
                $course->nama,
                $course->semester,
                $course->responses_count,
                $course->average_score !== null ? (float) $course->average_score : '-',
                $course->score_1_pct !== null ? (float) $course->score_1_pct : '-',
                $course->score_2_pct !== null ? (float) $course->score_2_pct : '-',
                $course->score_3_pct !== null ? (float) $course->score_3_pct : '-',
                $course->score_4_pct !== null ? (float) $course->score_4_pct : '-',
            ]], null, 'A'.$row);
            $row++;
        }

        foreach (range('A', 'J') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'questionnaire-summary-dosen');
        $writer->save($tempFile);

        return response()->download($tempFile, 'rekap-kuesioner-dosen.xlsx')->deleteFileAfterSend(true);
    }

    public function show(Request $request, MataKuliah $mataKuliah): View
    {
        $this->authorizeCourse($request->user(), $mataKuliah);

        return view('dosen.kuesioner.show', $this->buildShowData($mataKuliah));
    }

    public function exportPdf(Request $request, MataKuliah $mataKuliah)
    {
        $this->authorizeCourse($request->user(), $mataKuliah);
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

    public function exportExcel(Request $request, MataKuliah $mataKuliah)
    {
        $this->authorizeCourse($request->user(), $mataKuliah);
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

    private function authorizeCourse(User $user, MataKuliah $mataKuliah): void
    {
        /** @var User $user */
        $dosen = $user->dosen;

        abort_unless(
            $dosen && in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true),
            403
        );
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

    private function buildCourseSummaryQuery(int $dosenId, string $q)
    {
        $courseStats = $this->courseStatsSubquery();

        $courses = MataKuliah::query()
            ->leftJoinSub($courseStats, 'stats', function ($join) {
                $join->on('mata_kuliah.id', '=', 'stats.mata_kuliah_id');
            })
            ->select('mata_kuliah.*')
            ->selectRaw('COALESCE(stats.responses_count, 0) as responses_count')
            ->selectRaw('stats.average_score')
            ->selectRaw('stats.score_1_pct')
            ->selectRaw('stats.score_2_pct')
            ->selectRaw('stats.score_3_pct')
            ->selectRaw('stats.score_4_pct')
            ->where(function ($query) use ($dosenId) {
                $query->where('mata_kuliah.dosen_id', $dosenId)
                    ->orWhere('mata_kuliah.dosen_id_2', $dosenId);
            });

        if ($q !== '') {
            $courses->where(function ($query) use ($q) {
                $query->where('mata_kuliah.kode', 'like', "%{$q}%")
                    ->orWhere('mata_kuliah.nama', 'like', "%{$q}%");
            });
        }

        return $courses->orderByDesc('responses_count')->orderBy('mata_kuliah.kode');
    }

    private function buildSummaryExportData(int $dosenId, ?string $dosenName, string $q): array
    {
        return [
            'q' => $q,
            'dosen_name' => $dosenName,
            'courseSummaries' => $this->buildCourseSummaryQuery($dosenId, $q)->get(),
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
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
