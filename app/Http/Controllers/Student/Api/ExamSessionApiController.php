<?php

namespace App\Http\Controllers\Student\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamSessionApiController extends Controller
{
    public function getPayload(Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $group = $exam->questionGroup()->with('questions')->firstOrFail();
        $questions = $group->questions;

        if ($exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        $formattedQuestions = $questions->map(function ($q) use ($exam) {
            $options = $q->options_json;
            if ($options && $exam->randomize_options) {
                shuffle($options);
            }
            return [
                'id' => $q->id,
                'type' => $q->question_type,
                'content' => $q->content,
                'options' => $options,
                'weight' => $q->weight,
            ];
        });

        return response()->json([
            'status' => 'success',
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'duration_minutes' => $exam->duration_minutes,
            ],
            'result' => [
                'id' => $result->id,
                'time_remaining_seconds' => $result->time_remaining_seconds,
                'answers' => $result->answers_json ?? (object)[],
            ],
            'questions' => $formattedQuestions,
        ]);
    }

    public function autosave(Request $request, Exam $exam)
    {
        $student = Auth::user();
        $result = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->firstOrFail();

        $answers = $request->input('answers', []);
        $timeRemaining = $request->input('time_remaining_seconds', $result->time_remaining_seconds);

        $result->update([
            'answers_json' => $answers,
            'time_remaining_seconds' => max(0, $timeRemaining),
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
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($result->status === 'submitted' || $result->status === 'graded') {
            return response()->json(['status' => 'already_submitted']);
        }

        $answers = $request->input('answers', $result->answers_json ?? []);
        $questions = $exam->questionGroup->questions;

        $totalScore = 0;
        $maxPossibleScore = 0;

        foreach ($questions as $q) {
            $maxPossibleScore += $q->weight;
            $studentAns = $answers[$q->id] ?? null;

            if ($studentAns !== null && $q->correct_answers_json) {
                if (is_array($studentAns)) {
                    sort($studentAns);
                    $correct = $q->correct_answers_json;
                    sort($correct);
                    if ($studentAns == $correct) {
                        $totalScore += $q->weight;
                    }
                } else {
                    $studentAnsLower = strtolower(trim((string)$studentAns));
                    $correctArr = array_map(fn($v) => strtolower(trim((string)$v)), $q->correct_answers_json);
                    if (in_array($studentAnsLower, $correctArr)) {
                        $totalScore += $q->weight;
                    }
                }
            }
        }

        $finalPercentage = $maxPossibleScore > 0 ? round(($totalScore / $maxPossibleScore) * 100, 2) : 0;

        $result->update([
            'answers_json' => $answers,
            'score' => $finalPercentage,
            'status' => 'graded',
            'time_remaining_seconds' => 0,
            'submitted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'score' => $finalPercentage,
            'redirect_url' => route('student.dashboard'),
        ]);
    }
}
