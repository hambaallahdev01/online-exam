<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $availableExams = Exam::where('school_id', $student->school_id)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->with('questionGroup')
            ->get();

        $myResults = ExamResult::where('student_id', $student->id)
            ->with('exam')
            ->latest()
            ->get();

        return view('student.dashboard', compact('availableExams', 'myResults'));
    }

    public function enterToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = strtoupper(trim($request->token));
        $exam = Exam::where('school_id', Auth::user()->school_id)
            ->where('token', $token)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();

        if (! $exam) {
            return back()->withErrors(['token' => 'Invalid exam token or exam is inactive.']);
        }

        $request->session()->put("exam_access.{$exam->id}", true);

        return redirect()->route('student.exam.run', $exam->id);
    }

    public function runExam(Exam $exam)
    {
        $student = Auth::user();

        abort_unless($exam->school_id === $student->school_id, 403, 'This exam belongs to another school.');
        abort_unless(
            $exam->is_active && $exam->starts_at->lte(now()) && $exam->ends_at->gte(now()),
            403,
            'This exam is not currently available.'
        );

        $existingResult = ExamResult::where('exam_id', $exam->id)
            ->where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->first();

        abort_unless(
            $existingResult || session()->pull("exam_access.{$exam->id}", false),
            403,
            'Enter the exam token before opening the exam.'
        );

        // Check or create exam result record
        $result = $existingResult ?: ExamResult::create(
            [
                'school_id' => $student->school_id,
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'status' => 'in_progress',
                'started_at' => now(),
                'time_remaining_seconds' => $exam->duration_minutes * 60,
                'answers_json' => [],
            ]
        );

        if ($result->status === 'submitted' || $result->status === 'graded') {
            return redirect()->route('student.dashboard')->with('info', 'You have already completed this exam.');
        }

        return view('student.exam-runner', compact('exam', 'result'));
    }
}
