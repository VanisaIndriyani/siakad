<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\QuestionnaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

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

    public function show(Request $request, MataKuliah $mataKuliah): View
    {
        /** @var User $user */
        $user = $request->user();
        $dosen = $user->dosen;

        abort_unless(
            $dosen && in_array((int) $dosen->id, [(int) $mataKuliah->dosen_id, (int) $mataKuliah->dosen_id_2], true),
            403
        );

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

        $questionStats = QuestionnaireQuestion::query()
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
            ->get();

        return view('dosen.kuesioner.show', [
            'mataKuliah' => $mataKuliah,
            'questionStats' => $questionStats,
            'responses' => QuestionnaireResponse::query()
                ->with(['mahasiswa', 'answers'])
                ->where('mata_kuliah_id', $mataKuliah->id)
                ->latest()
                ->paginate(10),
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
        ]);
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
}
