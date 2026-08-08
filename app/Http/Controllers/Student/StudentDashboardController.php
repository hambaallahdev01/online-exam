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
            ->first();

        if (!$exam) {
            return back()->withErrors(['token' => 'Invalid exam token or exam is inactive.']);
        }

        return redirect()->route('student.exam.run', $exam->id);
    }

    public function runExam(Exam $exam)
    {
        $student = Auth::user();

        // Check or create exam result record
        $result = ExamResult::firstOrCreate(
            [
                'school_id' => $student->school_id,
                'exam_id' => $exam->id,
                'student_id' => $student->id,
            ],
            [
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
