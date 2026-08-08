<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $groupsCount = QuestionGroup::where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->count();
        $examsCount = Exam::where('school_id', $teacher->school_id)->count();

        $questionGroups = QuestionGroup::where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->get();

        $exams = Exam::where('school_id', $teacher->school_id)
            ->with('questionGroup')
            ->latest()
            ->get();

        return view('teacher.dashboard', compact('groupsCount', 'examsCount', 'questionGroups', 'exams'));
    }

    public function createQuestionGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $teacher = Auth::user();
        $subject = Subject::where('school_id', $teacher->school_id)->first();

        QuestionGroup::create([
            'school_id' => $teacher->school_id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject ? $subject->id : 1,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Question group created successfully!');
    }

    public function showQuestionGroup(QuestionGroup $group)
    {
        $group->load('questions');
        return view('teacher.questions', compact('group'));
    }

    public function storeQuestion(Request $request, QuestionGroup $group)
    {
        $validated = $request->validate([
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,essay',
            'content' => 'required|string',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answer' => 'required|string',
            'explanation' => 'nullable|string',
            'weight' => 'required|integer|min:1',
        ]);

        $options = null;
        if ($validated['question_type'] === 'single_choice' || $validated['question_type'] === 'multiple_choice') {
            $options = [
                ['id' => 'A', 'text' => $request->option_a],
                ['id' => 'B', 'text' => $request->option_b],
                ['id' => 'C', 'text' => $request->option_c],
                ['id' => 'D', 'text' => $request->option_d],
            ];
        } elseif ($validated['question_type'] === 'true_false') {
            $options = [
                ['id' => 'true', 'text' => 'True'],
                ['id' => 'false', 'text' => 'False'],
            ];
        }

        Question::create([
            'school_id' => Auth::user()->school_id,
            'question_group_id' => $group->id,
            'question_type' => $validated['question_type'],
            'content' => $validated['content'],
            'options_json' => $options,
            'correct_answers_json' => [$validated['correct_answer']],
            'explanation' => $validated['explanation'],
            'weight' => $validated['weight'],
        ]);

        return back()->with('success', 'Question added successfully!');
    }

    public function storeExam(Request $request)
    {
        $validated = $request->validate([
            'question_group_id' => 'required|exists:question_groups,id',
            'title' => 'required|string|max:255',
            'token' => 'required|string|max:10',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Exam::create([
            'school_id' => Auth::user()->school_id,
            'question_group_id' => $validated['question_group_id'],
            'title' => $validated['title'],
            'token' => strtoupper($validated['token']),
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Exam created and published!');
    }
}
