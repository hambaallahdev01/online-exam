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

    public function destroyQuestionGroup(QuestionGroup $group)
    {
        if ($group->school_id !== Auth::user()->school_id || $group->teacher_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $group->delete();

        return back()->with('success', 'Question bank and all its questions deleted successfully!');
    }

    public function storeQuestion(Request $request, QuestionGroup $group)
    {
        $validated = $request->validate([
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,essay,fact_opinion,matching,sorting',
            'content' => 'required|string',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
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
            if (!empty($request->options) && is_array($request->options)) {
                $options = [];
                $labels = range('A', 'Z');
                foreach (array_values($request->options) as $i => $text) {
                    $letter = $labels[$i] ?? ('P' . ($i + 1));
                    $options[] = [
                        'id' => $letter,
                        'text' => $text,
                    ];
                }
            } else {
                $options = array_values(array_filter([
                    ['id' => 'A', 'text' => $request->option_a],
                    ['id' => 'B', 'text' => $request->option_b],
                    ['id' => 'C', 'text' => $request->option_c],
                    ['id' => 'D', 'text' => $request->option_d],
                ], fn($opt) => !empty($opt['text'])));
            }

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

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        try {
            $deleted = \App\Services\MediaUploadService::deleteFile($request->url);

            return response()->json([
                'status' => $deleted ? 'success' : 'info',
                'message' => $deleted ? 'Media file deleted from storage successfully.' : 'Media file not found or already removed.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete media: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroyQuestion(Question $question)
    {
        if ($question->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $question->delete();

        return back()->with('success', 'Question and all associated media files deleted successfully!');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        if ($question->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,essay,fact_opinion,matching,sorting',
            'content' => 'required|string',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
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
            if (!empty($request->options) && is_array($request->options)) {
                $options = [];
                $labels = range('A', 'Z');
                foreach (array_values($request->options) as $i => $text) {
                    $letter = $labels[$i] ?? ('P' . ($i + 1));
                    $options[] = [
                        'id' => $letter,
                        'text' => $text,
                    ];
                }
            } else {
                $options = array_values(array_filter([
                    ['id' => 'A', 'text' => $request->option_a],
                    ['id' => 'B', 'text' => $request->option_b],
                    ['id' => 'C', 'text' => $request->option_c],
                    ['id' => 'D', 'text' => $request->option_d],
                ], fn($opt) => !empty($opt['text'])));
            }

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
            $items = array_map('trim', explode(',', $validated['correct_answer'] ?? ''));
            $options = $items;
            $correctAnswers = $items;
        }

        $oldUrls = $this->extractMediaUrlsFromQuestion($question);

        $question->update([
            'question_type' => $validated['question_type'],
            'content' => $validated['content'],
            'options_json' => $options,
            'correct_answers_json' => $correctAnswers,
            'explanation' => $validated['explanation'] ?? null,
            'weight' => $validated['weight'],
        ]);

        $newUrls = $this->extractMediaUrlsFromQuestion($question->fresh());
        $removedUrls = array_diff($oldUrls, $newUrls);

        foreach ($removedUrls as $removedUrl) {
            \App\Services\MediaUploadService::deleteFile($removedUrl);
        }

        return back()->with('success', 'Question updated successfully!');
    }

    private function extractMediaUrlsFromQuestion(Question $question): array
    {
        $urls = [];
        $contents = [$question->content, $question->explanation, json_encode($question->options_json, JSON_UNESCAPED_SLASHES)];
        foreach ($contents as $text) {
            if (!empty($text)) {
                preg_match_all('/https?:\/\/[^\s"\'<>]+/i', (string)$text, $matches1);
                if (!empty($matches1[0])) $urls = array_merge($urls, $matches1[0]);

                preg_match_all('/(?:src|href)=["\']([^"\']+)["\']/i', (string)$text, $matches2);
                if (!empty($matches2[1])) $urls = array_merge($urls, $matches2[1]);
            }
        }
        return array_unique(array_filter($urls));
    }

    public function updateExam(Request $request, Exam $exam)
    {
        if ($exam->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'token' => 'required|string|max:10',
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        $exam->update([
            'title' => $validated['title'],
            'token' => strtoupper($validated['token']),
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => $validated['is_active'],
        ]);

        return back()->with('success', 'Published exam updated successfully!');
    }

    public function destroyExam(Exam $exam)
    {
        if ($exam->school_id !== Auth::user()->school_id) {
            abort(403, 'Unauthorized action.');
        }

        $exam->delete();

        return back()->with('success', 'Published exam deleted successfully!');
    }
}
