<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Services\ExamDraftStore;
use App\Services\ExamQuestionPayload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamSessionApiController extends Controller
{
    public function __construct(
        private readonly ExamQuestionPayload $questionPayload,
        private readonly ExamDraftStore $drafts,
    ) {}

    public function getPayload(Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        abort_unless($exam->school_id === $student->school_id, 403);

        $formattedQuestions = $this->questionPayload->forResult($exam, $result);

        $remainingSeconds = $this->remainingSeconds($result, $exam);

        return response()->json([
            'status' => 'success',
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'duration_minutes' => $exam->duration_minutes,
            ],
            'result' => [
                'id' => $result->id,
                'time_remaining_seconds' => $remainingSeconds,
                'answers' => $this->drafts->answersFor($result) ?: (object) [],
            ],
            'questions' => $formattedQuestions,
        ])->header('Cache-Control', 'no-store, private');
    }

    public function autosave(Request $request, Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        abort_unless($exam->school_id === $student->school_id, 403);

        $validated = $request->validate([
            'answers' => ['sometimes', 'array', 'max:500'],
        ]);
        $timeRemaining = $this->remainingSeconds($result, $exam);

        if ($timeRemaining <= 0) {
            return response()->json([
                'status' => 'expired',
                'message' => 'The exam time has expired. Submit the last saved answers.',
            ], 409);
        }

        $answers = $this->filterAnswers($exam, $validated['answers'] ?? []);

        $this->drafts->save($result, $answers, $timeRemaining);

        return response()->json([
            'status' => 'success',
            'message' => 'Answers autosaved successfully.',
        ]);
    }

    public function submit(Request $request, Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($result->status === 'submitted' || $result->status === 'graded') {
            return response()->json(['status' => 'already_submitted']);
        }

        abort_unless($exam->school_id === $student->school_id, 403);

        $validated = $request->validate([
            'answers' => ['sometimes', 'array', 'max:500'],
        ]);
        $expired = $this->remainingSeconds($result, $exam) <= 0;
        $answers = $expired
            ? $this->drafts->answersFor($result)
            : $this->filterAnswers($exam, $validated['answers'] ?? $this->drafts->answersFor($result));
        $questions = $exam->questionGroup->questions;

        $totalScore = 0;
        $maxPossibleScore = 0;
        $requiresManualGrading = false;

        foreach ($questions as $q) {
            $maxPossibleScore += $q->weight;
            $studentAns = $answers[$q->id] ?? null;

            if ($studentAns !== null && $q->correct_answers_json) {
                $type = $q->question_type;

                if ($type === 'single_choice' || $type === 'true_false' || $type === 'fact_opinion') {
                    $studentAnsLower = strtolower(trim((string) $studentAns));
                    $correctArr = array_map(fn ($v) => strtolower(trim((string) $v)), (array) $q->correct_answers_json);
                    if (in_array($studentAnsLower, $correctArr)) {
                        $totalScore += $q->weight;
                    }
                } elseif ($type === 'multiple_choice') {
                    if (is_array($studentAns)) {
                        $studentSorted = array_map('strval', $studentAns);
                        sort($studentSorted);
                        $correctSorted = array_map('strval', (array) $q->correct_answers_json);
                        sort($correctSorted);
                        if ($studentSorted === $correctSorted) {
                            $totalScore += $q->weight;
                        }
                    }
                } elseif ($type === 'sorting') {
                    if (is_array($studentAns)) {
                        if ($studentAns === (array) $q->correct_answers_json) {
                            $totalScore += $q->weight;
                        }
                    }
                } elseif ($type === 'matching') {
                    if (is_array($studentAns)) {
                        ksort($studentAns);
                        $correct = (array) $q->correct_answers_json;
                        ksort($correct);
                        if ($studentAns === $correct) {
                            $totalScore += $q->weight;
                        }
                    }
                } elseif ($type === 'essay') {
                    // Essay answers must never award themselves points.
                    if (trim((string) $studentAns) !== '') {
                        $requiresManualGrading = true;
                    }
                }
            }
        }

        $finalPercentage = $maxPossibleScore > 0 ? round(($totalScore / $maxPossibleScore) * 100, 2) : 0;

        $result->update([
            'answers_json' => $answers,
            'score' => $finalPercentage,
            'status' => $requiresManualGrading ? 'submitted' : 'graded',
            'time_remaining_seconds' => 0,
            'submitted_at' => now('UTC'),
        ]);
        $this->drafts->forget($result);

        return response()->json([
            'status' => 'success',
            'score' => $finalPercentage,
            'requires_manual_grading' => $requiresManualGrading,
            'redirect_url' => route('student.dashboard'),
        ]);
    }

    private function remainingSeconds(ExamResult $result, Exam $exam): int
    {
        $utcNow = now('UTC');
        $startedAt = $result->started_at ?? $utcNow;
        $personalDeadline = $startedAt->copy()->addMinutes($exam->duration_minutes);
        $deadline = $exam->ends_at->lt($personalDeadline) ? $exam->ends_at : $personalDeadline;

        return max(0, $deadline->getTimestamp() - $utcNow->getTimestamp());
    }

    private function filterAnswers(Exam $exam, array $answers): array
    {
        $allowedQuestionIds = $this->questionPayload->allowedQuestionIds($exam);
        $filtered = [];

        foreach ($answers as $questionId => $answer) {
            if (isset($allowedQuestionIds[(string) $questionId])) {
                $filtered[(string) $questionId] = $this->normalizeAnswer($answer);
            }
        }

        return $filtered;
    }

    private function normalizeAnswer(mixed $answer, int $depth = 0): mixed
    {
        if ($depth > 2) {
            return null;
        }

        if (is_array($answer)) {
            $normalized = [];
            foreach (array_slice($answer, 0, 100, true) as $key => $value) {
                $normalized[mb_substr((string) $key, 0, 255)] = $this->normalizeAnswer($value, $depth + 1);
            }

            return $normalized;
        }

        if (is_string($answer)) {
            return mb_substr($answer, 0, 10000);
        }

        return is_bool($answer) || is_int($answer) || is_float($answer) || $answer === null
            ? $answer
            : null;
    }
}
