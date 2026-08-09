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

        $subjects = Subject::where('school_id', $teacher->school_id)->get();

        $questionGroups = QuestionGroup::where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->get();

        $exams = Exam::where('school_id', $teacher->school_id)
            ->with('questionGroup')
            ->latest()
            ->get();

        return view('teacher.dashboard', compact('groupsCount', 'examsCount', 'subjects', 'questionGroups', 'exams'));
    }

    public function createQuestionGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => 'nullable|exists:subjects,id',
            'description' => 'nullable|string',
        ]);

        $teacher = Auth::user();

        if ($request->filled('subject_id')) {
            $subjectId = $request->subject_id;
        } else {
            $subject = Subject::where('school_id', $teacher->school_id)->first();
            if (!$subject) {
                $subject = Subject::create([
                    'school_id' => $teacher->school_id,
                    'name' => 'Umum / General',
                    'code' => 'UMUM',
                ]);
            }
            $subjectId = $subject->id;
        }

        QuestionGroup::create([
            'school_id' => $teacher->school_id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectId,
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
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,essay,fact_opinion,matching,sorting',
            'content' => 'required|string',
            'option_a' => 'nullable|string',
            'option_b' => 'nullable|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'explanation' => 'nullable|string',
            'weight' => 'required|integer|min:1',
        ]);

        $options = null;
        $correctAnswers = !empty($validated['correct_answer']) ? [$validated['correct_answer']] : [];

        if ($validated['question_type'] === 'single_choice' || $validated['question_type'] === 'multiple_choice') {
            $options = [
                ['id' => 'A', 'text' => $request->option_a],
                ['id' => 'B', 'text' => $request->option_b],
                ['id' => 'C', 'text' => $request->option_c],
                ['id' => 'D', 'text' => $request->option_d],
            ];
            if ($validated['question_type'] === 'multiple_choice') {
                $correctAnswers = array_map('trim', explode(',', $validated['correct_answer'] ?? ''));
            }
        } elseif ($validated['question_type'] === 'true_false') {
            $options = [
                ['id' => 'true', 'text' => 'True / Benar'],
                ['id' => 'false', 'text' => 'False / Salah'],
            ];
        } elseif ($validated['question_type'] === 'fact_opinion') {
            $options = [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ];
        } elseif ($validated['question_type'] === 'matching') {
            // Options format: left_item => right_item
            $pairs = json_decode($validated['correct_answer'] ?? '{}', true);
            if (is_array($pairs)) {
                $left = [];
                $right = [];
                foreach ($pairs as $k => $v) {
                    $left[] = ['id' => (string)$k, 'text' => (string)$k];
                    $right[] = ['id' => (string)$v, 'text' => (string)$v];
                }
                $options = ['left' => $left, 'right' => $right];
                $correctAnswers = $pairs;
            }
        } elseif ($validated['question_type'] === 'sorting') {
            // Options format: ordered array
            $items = array_map('trim', explode(',', $validated['correct_answer'] ?? ''));
            $options = $items;
            $correctAnswers = $items;
        }

        Question::create([
            'school_id' => Auth::user()->school_id,
            'question_group_id' => $group->id,
            'question_type' => $validated['question_type'],
            'content' => $validated['content'],
            'options_json' => $options,
            'correct_answers_json' => $correctAnswers,
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

    public function uploadMedia(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No file received or file size exceeds upload_max_filesize in php.ini.',
            ], 422);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'File upload error: ' . $file->getErrorMessage() . '. Check upload_max_filesize & post_max_size in php.ini.',
            ], 422);
        }

        try {
            $url = \App\Services\MediaUploadService::upload($file, 'questions');
            $extension = strtolower($file->getClientOriginalExtension());
            $isPdf = $extension === 'pdf';

            return response()->json([
                'status' => 'success',
                'url' => $url,
                'is_pdf' => $isPdf,
                'original_name' => $file->getClientOriginalName(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
