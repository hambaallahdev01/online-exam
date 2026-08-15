<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Services\HtmlSanitizerService;
use App\Services\MediaUploadService;
use App\Services\TenantDateTime;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class TeacherDashboardController extends Controller
{
    public function index(TenantDateTime $tenantDateTime)
    {
        $teacher = Auth::user();
        $groupsCount = QuestionGroup::where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->count();
        $examsCount = Exam::where('school_id', $teacher->school_id)
            ->whereHas('questionGroup', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->count();

        $subjects = Subject::where('school_id', $teacher->school_id)->get();

        $questionGroups = QuestionGroup::where('school_id', $teacher->school_id)
            ->where('teacher_id', $teacher->id)
            ->withCount('questions')
            ->get();

        $exams = Exam::where('school_id', $teacher->school_id)
            ->whereHas('questionGroup', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->with('questionGroup')
            ->latest()
            ->get();

        $school = $teacher->school;
        $schoolTimezone = $tenantDateTime->timezoneFor($school);
        $defaultStart = now('UTC')->addMinute()->startOfMinute();
        $defaultExamStartsAt = $tenantDateTime->toInputValue($defaultStart, $school);
        $defaultExamEndsAt = $tenantDateTime->toInputValue($defaultStart->addDays(7), $school);
        $examSchedules = $exams->mapWithKeys(fn (Exam $exam): array => [
            $exam->id => [
                'id' => $exam->id,
                'title' => $exam->title,
                'token' => $exam->token,
                'duration_minutes' => $exam->duration_minutes,
                'is_active' => $exam->is_active,
                'starts_at_local' => $tenantDateTime->toInputValue($exam->starts_at, $school),
                'ends_at_local' => $tenantDateTime->toInputValue($exam->ends_at, $school),
                'starts_at_display' => $tenantDateTime->format($exam->starts_at, $school),
                'ends_at_display' => $tenantDateTime->format($exam->ends_at, $school),
            ],
        ]);

        return view('teacher.dashboard', compact(
            'groupsCount',
            'examsCount',
            'subjects',
            'questionGroups',
            'exams',
            'examSchedules',
            'schoolTimezone',
            'defaultExamStartsAt',
            'defaultExamEndsAt',
        ));
    }

    public function createQuestionGroup(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'subject_id' => [
                'nullable',
                Rule::exists('subjects', 'id')->where(fn ($query) => $query->where('school_id', $teacher->school_id)),
            ],
            'description' => 'nullable|string',
        ]);

        if ($request->filled('subject_id')) {
            $subjectId = $request->subject_id;
        } else {
            $subject = Subject::where('school_id', $teacher->school_id)->first();
            if (! $subject) {
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
        $this->assertOwnedGroup($group);
        $group->load('questions');
        foreach ($group->questions as $question) {
            $question->content = HtmlSanitizerService::sanitize($question->content);
            $question->options_json = $this->sanitizeOptions($question->options_json, $question->question_type);
        }

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
        $this->assertOwnedGroup($group);

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
        $correctAnswers = ! empty($validated['correct_answer']) ? [$validated['correct_answer']] : [];

        if ($validated['question_type'] === 'single_choice' || $validated['question_type'] === 'multiple_choice') {
            if (! empty($request->options) && is_array($request->options)) {
                $options = [];
                $labels = range('A', 'Z');
                foreach (array_values($request->options) as $i => $text) {
                    $letter = $labels[$i] ?? ('P'.($i + 1));
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
                ], fn ($opt) => ! empty($opt['text'])));
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
                    $left[] = ['id' => (string) $k, 'text' => (string) $k];
                    $right[] = ['id' => (string) $v, 'text' => (string) $v];
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

        $options = $this->sanitizeOptions($options, $validated['question_type']);

        Question::create([
            'school_id' => Auth::user()->school_id,
            'question_group_id' => $group->id,
            'question_type' => $validated['question_type'],
            'content' => HtmlSanitizerService::sanitize($validated['content']),
            'options_json' => $options,
            'correct_answers_json' => $correctAnswers,
            'explanation' => $validated['explanation'] ?? null,
            'weight' => $validated['weight'],
        ]);

        return back()->with('success', 'Question added successfully!');
    }

    public function storeExam(Request $request, TenantDateTime $tenantDateTime)
    {
        $teacher = Auth::user();

        $validated = $request->validate([
            'question_group_id' => [
                'required',
                Rule::exists('question_groups', 'id')->where(
                    fn ($query) => $query
                        ->where('school_id', $teacher->school_id)
                        ->where('teacher_id', $teacher->id)
                ),
            ],
            'title' => 'required|string|max:255',
            'token' => [
                'required',
                'string',
                'max:10',
                Rule::unique('exams', 'token')->where(fn ($query) => $query->where('school_id', $teacher->school_id)),
            ],
            'duration_minutes' => 'required|integer|min:1',
            'starts_at' => 'required|date_format:'.TenantDateTime::INPUT_FORMAT,
            'ends_at' => 'required|date_format:'.TenantDateTime::INPUT_FORMAT,
        ]);
        $schedule = $this->normalizeExamSchedule($validated, $tenantDateTime, $teacher->school);

        Exam::create([
            'school_id' => $teacher->school_id,
            'question_group_id' => $validated['question_group_id'],
            'title' => $validated['title'],
            'token' => strtoupper($validated['token']),
            'starts_at' => $schedule['starts_at'],
            'ends_at' => $schedule['ends_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => true,
        ]);

        return back()->with('success', 'Exam created and published!');
    }

    public function uploadMedia(Request $request)
    {
        if (! $request->hasFile('file')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No file received or file size exceeds upload_max_filesize in php.ini.',
            ], 422);
        }

        $file = $request->file('file');

        if (! $file->isValid()) {
            return response()->json([
                'status' => 'error',
                'message' => 'File upload error: '.$file->getErrorMessage().'. Check upload_max_filesize & post_max_size in php.ini.',
            ], 422);
        }

        try {
            $teacher = Auth::user();
            $folder = "questions/{$teacher->school_id}/{$teacher->id}";
            $url = MediaUploadService::upload($file, $folder);
            $extension = strtolower($file->getClientOriginalExtension());
            $isPdf = $extension === 'pdf';

            return response()->json([
                'status' => 'success',
                'url' => $url,
                'is_pdf' => $isPdf,
                'original_name' => $file->getClientOriginalName(),
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Media upload failed.', ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'The media file could not be stored. Please try again later.',
            ], 500);
        }
    }

    public function deleteMedia(Request $request)
    {
        $request->validate([
            'url' => 'required|string',
        ]);

        try {
            $teacher = Auth::user();
            $requiredPrefix = "questions/{$teacher->school_id}/{$teacher->id}";
            $deleted = MediaUploadService::deleteFile($request->url, $requiredPrefix);

            return response()->json([
                'status' => $deleted ? 'success' : 'info',
                'message' => $deleted ? 'Media file deleted from storage successfully.' : 'Media file not found or already removed.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Media deletion failed.', ['exception' => $e]);

            return response()->json([
                'status' => 'error',
                'message' => 'The media file could not be deleted. Please try again later.',
            ], 500);
        }
    }

    public function destroyQuestion(Question $question)
    {
        $this->assertOwnedQuestion($question);

        $question->delete();

        return back()->with('success', 'Question and all associated media files deleted successfully!');
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $this->assertOwnedQuestion($question);

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
        $correctAnswers = ! empty($validated['correct_answer']) ? [$validated['correct_answer']] : [];

        if ($validated['question_type'] === 'single_choice' || $validated['question_type'] === 'multiple_choice') {
            if (! empty($request->options) && is_array($request->options)) {
                $options = [];
                $labels = range('A', 'Z');
                foreach (array_values($request->options) as $i => $text) {
                    $letter = $labels[$i] ?? ('P'.($i + 1));
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
                ], fn ($opt) => ! empty($opt['text'])));
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
                    $left[] = ['id' => (string) $k, 'text' => (string) $k];
                    $right[] = ['id' => (string) $v, 'text' => (string) $v];
                }
                $options = ['left' => $left, 'right' => $right];
                $correctAnswers = $pairs;
            }
        } elseif ($validated['question_type'] === 'sorting') {
            $items = array_map('trim', explode(',', $validated['correct_answer'] ?? ''));
            $options = $items;
            $correctAnswers = $items;
        }

        $options = $this->sanitizeOptions($options, $validated['question_type']);

        $oldUrls = $this->extractMediaUrlsFromQuestion($question);

        $question->update([
            'question_type' => $validated['question_type'],
            'content' => HtmlSanitizerService::sanitize($validated['content']),
            'options_json' => $options,
            'correct_answers_json' => $correctAnswers,
            'explanation' => $validated['explanation'] ?? null,
            'weight' => $validated['weight'],
        ]);

        $newUrls = $this->extractMediaUrlsFromQuestion($question->fresh());
        $removedUrls = array_diff($oldUrls, $newUrls);

        foreach ($removedUrls as $removedUrl) {
            $teacher = Auth::user();
            MediaUploadService::deleteFile(
                $removedUrl,
                "questions/{$teacher->school_id}/{$teacher->id}"
            );
        }

        return back()->with('success', 'Question updated successfully!');
    }

    private function extractMediaUrlsFromQuestion(Question $question): array
    {
        $urls = [];
        $contents = [$question->content, $question->explanation, json_encode($question->options_json, JSON_UNESCAPED_SLASHES)];
        foreach ($contents as $text) {
            if (! empty($text)) {
                preg_match_all('/https?:\/\/[^\s"\'<>]+/i', (string) $text, $matches1);
                if (! empty($matches1[0])) {
                    $urls = array_merge($urls, $matches1[0]);
                }

                preg_match_all('/(?:src|href)=["\']([^"\']+)["\']/i', (string) $text, $matches2);
                if (! empty($matches2[1])) {
                    $urls = array_merge($urls, $matches2[1]);
                }
            }
        }

        return array_unique(array_filter($urls));
    }

    public function updateExam(Request $request, Exam $exam, TenantDateTime $tenantDateTime)
    {
        $this->assertOwnedExam($exam);

        $teacher = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'token' => [
                'required',
                'string',
                'max:10',
                Rule::unique('exams', 'token')
                    ->ignore($exam->id)
                    ->where(fn ($query) => $query->where('school_id', $teacher->school_id)),
            ],
            'duration_minutes' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
            'starts_at' => 'required|date_format:'.TenantDateTime::INPUT_FORMAT,
            'ends_at' => 'required|date_format:'.TenantDateTime::INPUT_FORMAT,
        ]);
        $schedule = $this->normalizeExamSchedule($validated, $tenantDateTime, $teacher->school);

        $exam->update([
            'title' => $validated['title'],
            'token' => strtoupper($validated['token']),
            'duration_minutes' => $validated['duration_minutes'],
            'is_active' => $validated['is_active'],
            'starts_at' => $schedule['starts_at'],
            'ends_at' => $schedule['ends_at'],
        ]);

        return back()->with('success', 'Published exam updated successfully!');
    }

    public function destroyExam(Exam $exam)
    {
        $this->assertOwnedExam($exam);

        $exam->delete();

        return back()->with('success', 'Published exam deleted successfully!');
    }

    private function assertOwnedGroup(QuestionGroup $group): void
    {
        $teacher = Auth::user();
        abort_unless(
            $group->school_id === $teacher->school_id && $group->teacher_id === $teacher->id,
            403,
            'Unauthorized action.'
        );
    }

    /**
     * Parse a school-local exam window and normalize it to UTC for persistence.
     *
     * @param  array<string, mixed>  $validated
     * @return array{starts_at: CarbonImmutable, ends_at: CarbonImmutable}
     */
    private function normalizeExamSchedule(array $validated, TenantDateTime $tenantDateTime, School $school): array
    {
        try {
            $startsAt = $tenantDateTime->toUtc($validated['starts_at'], $school);
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'starts_at' => __('messages.invalid_local_datetime'),
            ]);
        }

        try {
            $endsAt = $tenantDateTime->toUtc($validated['ends_at'], $school);
        } catch (InvalidArgumentException) {
            throw ValidationException::withMessages([
                'ends_at' => __('messages.invalid_local_datetime'),
            ]);
        }

        if ($endsAt->lte($startsAt)) {
            throw ValidationException::withMessages([
                'ends_at' => __('messages.end_after_start'),
            ]);
        }

        $windowSeconds = $endsAt->getTimestamp() - $startsAt->getTimestamp();
        if (((int) $validated['duration_minutes']) * 60 > $windowSeconds) {
            throw ValidationException::withMessages([
                'duration_minutes' => __('messages.duration_exceeds_window'),
            ]);
        }

        return [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ];
    }

    private function assertOwnedQuestion(Question $question): void
    {
        $question->loadMissing('questionGroup');
        $teacher = Auth::user();
        abort_unless(
            $question->school_id === $teacher->school_id
                && $question->questionGroup
                && $question->questionGroup->teacher_id === $teacher->id,
            403,
            'Unauthorized action.'
        );
    }

    private function assertOwnedExam(Exam $exam): void
    {
        $exam->loadMissing('questionGroup');
        $teacher = Auth::user();
        abort_unless(
            $exam->school_id === $teacher->school_id
                && $exam->questionGroup
                && $exam->questionGroup->teacher_id === $teacher->id,
            403,
            'Unauthorized action.'
        );
    }

    private function sanitizeOptions(?array $options, string $questionType): ?array
    {
        if ($options === null) {
            return null;
        }

        if (in_array($questionType, ['single_choice', 'multiple_choice'], true)) {
            return array_map(function ($option) {
                if (! is_array($option)) {
                    return HtmlSanitizerService::sanitize((string) $option);
                }
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
