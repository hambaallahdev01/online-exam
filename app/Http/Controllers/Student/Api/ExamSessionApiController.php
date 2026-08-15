<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Services\HtmlSanitizerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamSessionApiController extends Controller
{
    public function getPayload(Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        abort_unless($exam->school_id === $student->school_id, 403);

        $group = $exam->questionGroup()->with('questions')->firstOrFail();
        $questions = $group->questions;

        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        $formattedQuestions = $questions->map(function ($q) use ($exam) {
            $options = $this->sanitizeOptionsForStudent($q->options_json, $q->question_type);
            if (is_array($options) && $exam->randomize_options && in_array($q->question_type, ['single_choice', 'multiple_choice'])) {
                shuffle($options);
            }

            return [
                'id' => $q->id,
                'type' => $q->question_type,
                'content' => HtmlSanitizerService::sanitize($q->content),
                'options' => $options,
                'weight' => $q->weight,
            ];
        });

        $remainingSeconds = $this->remainingSeconds($result, $exam);
        $result->update(['time_remaining_seconds' => $remainingSeconds]);

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
                'answers' => $result->answers_json ?? (object) [],
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

        $result->update([
            'answers_json' => $answers,
            'time_remaining_seconds' => $timeRemaining,
        ]);

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
            ? ($result->answers_json ?? [])
            : $this->filterAnswers($exam, $validated['answers'] ?? ($result->answers_json ?? []));
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
            'submitted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'score' => $finalPercentage,
            'requires_manual_grading' => $requiresManualGrading,
            'redirect_url' => route('student.dashboard'),
        ]);
    }

    private function remainingSeconds(ExamResult $result, Exam $exam): int
    {
        $startedAt = $result->started_at ?? now();
        $personalDeadline = $startedAt->copy()->addMinutes($exam->duration_minutes);
        $deadline = $exam->ends_at->lt($personalDeadline) ? $exam->ends_at : $personalDeadline;

        return max(0, $deadline->getTimestamp() - now()->getTimestamp());
    }

    private function filterAnswers(Exam $exam, array $answers): array
    {
        $allowedQuestionIds = $exam->questionGroup->questions()
            ->pluck('id')
            ->mapWithKeys(fn ($id) => [(string) $id => true])
            ->all();
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

    private function sanitizeOptionsForStudent(mixed $options, string $questionType): mixed
    {
        if (! is_array($options)) {
            return $options;
        }

        if (in_array($questionType, ['single_choice', 'multiple_choice'], true)) {
            return array_map(function ($option) {
                if (! is_array($option)) {
                    return HtmlSanitizerService::sanitize((string) $option);
                }
                $option['id'] = mb_substr((string) ($option['id'] ?? ''), 0, 20);
                $option['text'] = HtmlSanitizerService::sanitize((string) ($option['text'] ?? ''));

                return $option;
            }, $options);
        }

        return $this->plainTextOptions($options);
    }

    private function plainTextOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $options[$key] = $this->plainTextOptions($value);
            } elseif (is_string($value)) {
                $options[$key] = mb_substr(trim(strip_tags($value)), 0, 1000);
            }
        }

        return $options;
    }
}
