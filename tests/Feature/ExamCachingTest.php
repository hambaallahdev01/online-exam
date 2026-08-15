<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExamCachingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'exam.payload_cache.enabled' => true,
            'exam.payload_cache.store' => 'array',
            'exam.drafts.store' => 'database',
        ]);
        Cache::store('array')->flush();
    }

    public function test_question_cache_is_invalidated_and_randomized_payload_is_stable_per_student(): void
    {
        [$student, $group, $exam] = $this->examContext(randomize: true);
        $firstQuestion = $this->question($group, 'Original question', 'A');
        $this->question($group, 'Second question', 'B');
        $this->question($group, 'Third question', 'C');

        $this->startExam($student, $exam);

        $firstPayload = $this->getJson(route('student.exam.api.payload', $exam))->assertOk()->json('questions');
        $secondPayload = $this->getJson(route('student.exam.api.payload', $exam))->assertOk()->json('questions');

        $this->assertSame($firstPayload, $secondPayload);

        $firstQuestion->update(['content' => 'Updated question']);

        $updatedPayload = $this->getJson(route('student.exam.api.payload', $exam))->assertOk()->json('questions');
        $updatedQuestion = collect($updatedPayload)->firstWhere('id', $firstQuestion->id);

        $this->assertSame('Updated question', $updatedQuestion['content']);
    }

    public function test_cached_drafts_are_restored_and_final_submission_is_persisted(): void
    {
        config([
            'exam.drafts.store' => 'array',
            'exam.drafts.database_checkpoint_seconds' => 3600,
        ]);

        [$student, $group, $exam] = $this->examContext();
        $question = $this->question($group, 'Cached answer question', 'B');

        $this->startExam($student, $exam);

        $this->postJson(route('student.exam.api.autosave', $exam), [
            'answers' => [$question->id => 'A'],
        ])->assertOk();
        $this->postJson(route('student.exam.api.autosave', $exam), [
            'answers' => [$question->id => 'B'],
        ])->assertOk();

        $result = ExamResult::where('student_id', $student->id)->firstOrFail();
        $this->assertSame('A', $result->fresh()->answers_json[(string) $question->id]);

        $this->getJson(route('student.exam.api.payload', $exam))
            ->assertOk()
            ->assertJsonPath("result.answers.{$question->id}", 'B');

        $this->postJson(route('student.exam.api.submit', $exam), [])
            ->assertOk()
            ->assertJsonPath('score', 100);

        $submitted = $result->fresh();
        $this->assertSame('B', $submitted->answers_json[(string) $question->id]);
        $this->assertSame('graded', $submitted->status);
    }

    /**
     * @return array{User, QuestionGroup, Exam}
     */
    private function examContext(bool $randomize = false): array
    {
        $school = School::create(['name' => 'Cache School', 'email' => fake()->unique()->safeEmail()]);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Teacher',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'teacher',
        ]);
        $student = User::create([
            'school_id' => $school->id,
            'name' => 'Student',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'role' => 'student',
        ]);
        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Subject',
            'code' => strtoupper(fake()->unique()->lexify('???')),
        ]);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Question Group',
        ]);
        $exam = Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Cached Exam',
            'token' => strtoupper(fake()->unique()->bothify('CACHE###')),
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addHour(),
            'duration_minutes' => 30,
            'is_active' => true,
            'randomize_questions' => $randomize,
            'randomize_options' => $randomize,
        ]);

        return [$student, $group, $exam];
    }

    private function question(QuestionGroup $group, string $content, string $correctAnswer): Question
    {
        return Question::create([
            'school_id' => $group->school_id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => $content,
            'options_json' => [
                ['id' => 'A', 'text' => 'Option A'],
                ['id' => 'B', 'text' => 'Option B'],
                ['id' => 'C', 'text' => 'Option C'],
            ],
            'correct_answers_json' => [$correctAnswer],
            'weight' => 1,
        ]);
    }

    private function startExam(User $student, Exam $exam): void
    {
        $this->actingAs($student)
            ->post(route('student.enter-token'), ['token' => $exam->token])
            ->assertRedirect(route('student.exam.run', $exam));

        $this->get(route('student.exam.run', $exam))->assertOk();
    }
}
