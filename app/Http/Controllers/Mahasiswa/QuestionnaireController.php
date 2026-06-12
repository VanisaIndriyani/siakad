<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Khs;
use App\Models\KhsItem;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\QuestionnaireAnswer;
use App\Models\QuestionnaireQuestion;
use App\Models\QuestionnaireResponse;
use App\Models\User;
use App\Support\QuestionnaireService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuestionnaireController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return view('mahasiswa.kuesioner.index', [
                'pendingItems' => collect(),
                'completedResponses' => collect(),
            ]);
        }

        return view('mahasiswa.kuesioner.index', [
            'pendingItems' => QuestionnaireService::pendingItems($mahasiswa),
            'completedResponses' => QuestionnaireResponse::query()
                ->with(['mataKuliah.dosen', 'mataKuliah.dosen2', 'answers.question'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->latest()
                ->get(),
        ]);
    }

    public function show(Request $request, Khs $khs, MataKuliah $mataKuliah): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }

        QuestionnaireService::ensureKhsItemsFromApprovedKrs($mahasiswa);

        $item = $this->resolveEligibleItem($mahasiswa, $khs, $mataKuliah);
        if (! $item) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('error', 'Data kuesioner tidak valid atau sudah tidak tersedia.');
        }

        if ($this->responseExists($mahasiswa, $khs, $mataKuliah)) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('success', 'Kuesioner mata kuliah ini sudah diisi.');
        }

        $questions = QuestionnaireQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('error', 'Pertanyaan kuesioner belum tersedia.');
        }

        return view('mahasiswa.kuesioner.show', [
            'item' => $item,
            'questions' => $questions,
            'scoreLabels' => QuestionnaireService::SCORE_LABELS,
        ]);
    }

    public function store(Request $request, Khs $khs, MataKuliah $mataKuliah): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $mahasiswa = $user->mahasiswa;

        if (! $mahasiswa) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('error', 'Profil mahasiswa belum tersedia.');
        }

        QuestionnaireService::ensureKhsItemsFromApprovedKrs($mahasiswa);

        $item = $this->resolveEligibleItem($mahasiswa, $khs, $mataKuliah);
        if (! $item) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('error', 'Data kuesioner tidak valid atau sudah tidak tersedia.');
        }

        if ($this->responseExists($mahasiswa, $khs, $mataKuliah)) {
            return redirect()->route('mahasiswa.kuesioner.index')->with('success', 'Kuesioner mata kuliah ini sudah diisi.');
        }

        $questions = QuestionnaireQuestion::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $rules = [
            'komentar' => ['nullable', 'string', 'max:2000'],
        ];

        foreach ($questions as $question) {
            $rules['answers.'.$question->id] = ['required', 'integer', 'between:1,4'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $questions, $mahasiswa, $khs, $mataKuliah) {
            $response = QuestionnaireResponse::query()->create([
                'mahasiswa_id' => $mahasiswa->id,
                'khs_id' => $khs->id,
                'mata_kuliah_id' => $mataKuliah->id,
                'semester' => $khs->semester,
                'tahun_ajaran' => $khs->tahun_ajaran,
                'komentar' => trim((string) ($validated['komentar'] ?? '')) ?: null,
            ]);

            foreach ($questions as $question) {
                QuestionnaireAnswer::query()->create([
                    'questionnaire_response_id' => $response->id,
                    'questionnaire_question_id' => $question->id,
                    'score' => (int) $validated['answers'][$question->id],
                ]);
            }
        });

        $remaining = QuestionnaireService::pendingCount($mahasiswa);
        $message = $remaining === 0
            ? 'Kuesioner berhasil dikirim. Semua kuesioner selesai, KHS sekarang bisa diakses.'
            : 'Kuesioner berhasil dikirim.';

        return redirect()->route('mahasiswa.kuesioner.index')->with('success', $message);
    }

    private function resolveEligibleItem(Mahasiswa $mahasiswa, Khs $khs, MataKuliah $mataKuliah): ?KhsItem
    {
        if ((int) $khs->mahasiswa_id !== (int) $mahasiswa->id) {
            return null;
        }

        return KhsItem::query()
            ->with(['khs', 'mataKuliah.dosen', 'mataKuliah.dosen2'])
            ->where('khs_id', $khs->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->first();
    }

    private function responseExists(Mahasiswa $mahasiswa, Khs $khs, MataKuliah $mataKuliah): bool
    {
        return QuestionnaireResponse::query()
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('khs_id', $khs->id)
            ->where('mata_kuliah_id', $mataKuliah->id)
            ->exists();
    }
}
