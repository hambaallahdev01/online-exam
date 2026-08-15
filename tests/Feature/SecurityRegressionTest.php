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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_boundaries_block_students_from_admin_and_teacher_routes(): void
    {
        $school = $this->school('roles');
        $student = $this->user($school, 'student', 'student-roles@example.test');
        $teacher = $this->user($school, 'teacher', 'teacher-roles@example.test');
        $group = $this->group($school, $teacher);

        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
        $this->actingAs($student)->get(route('teacher.question-groups.show', $group))->assertForbidden();
    }

    public function test_teacher_cannot_read_or_modify_another_teachers_question_bank(): void
    {
        $school = $this->school('ownership');
        $teacherA = $this->user($school, 'teacher', 'teacher-a@example.test');
        $teacherB = $this->user($school, 'teacher', 'teacher-b@example.test');
        $groupB = $this->group($school, $teacherB);

        $this->actingAs($teacherA)->get(route('teacher.question-groups.show', $groupB))->assertForbidden();
        $this->actingAs($teacherA)->post(route('teacher.questions.store', $groupB), [
            'question_type' => 'single_choice',
            'content' => 'Unauthorized question',
            'options' => ['A', 'B'],
            'correct_answer' => 'A',
            'weight' => 1,
        ])->assertForbidden();

        $this->actingAs($teacherB)->get(route('teacher.question-groups.show', $groupB))->assertOk();

        $this->assertDatabaseCount('questions', 0);
    }

    public function test_exam_requires_same_tenant_and_a_successful_token_entry(): void
    {
        $schoolA = $this->school('tenant-a');
        $schoolB = $this->school('tenant-b');
        $studentA = $this->user($schoolA, 'student', 'student-a@example.test');
        $teacherA = $this->user($schoolA, 'teacher', 'teacher-a-token@example.test');
        $teacherB = $this->user($schoolB, 'teacher', 'teacher-b-token@example.test');
        $examA = $this->exam($schoolA, $this->group($schoolA, $teacherA), 'TOKENA');
        $examB = $this->exam($schoolB, $this->group($schoolB, $teacherB), 'TOKENB');

        $this->actingAs($studentA)->get(route('student.exam.run', $examB))->assertForbidden();
        $this->actingAs($studentA)->get(route('student.exam.run', $examA))->assertForbidden();
        $this->actingAs($studentA)->post(route('student.enter-token'), ['token' => 'TOKENA'])
            ->assertRedirect(route('student.exam.run', $examA));
        $this->actingAs($studentA)->get(route('student.exam.run', $examA))->assertOk();
    }

    public function test_unsigned_email_verification_link_is_rejected(): void
    {
        $school = $this->school('unsigned');
        $user = $this->user($school, 'admin', 'unsigned@example.test', false);

        $this->get(route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]))->assertForbidden();

        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_expired_password_reset_token_cannot_change_password(): void
    {
        $school = $this->school('reset-expiry');
        $user = $this->user($school, 'admin', 'expired-reset@example.test');
        $token = 'expired-reset-token';
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()->subMinutes(61),
        ]);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password-value',
            'password_confirmation' => 'new-password-value',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_public_html_upload_is_rejected(): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public']);
        $school = $this->school('upload');
        $teacher = $this->user($school, 'teacher', 'upload@example.test');
        $file = UploadedFile::fake()->createWithContent('payload.html', '<script>alert(1)</script>');

        $this->actingAs($teacher)->post(route('teacher.media.upload'), ['file' => $file])
            ->assertStatus(422)
            ->assertJson(['status' => 'error']);

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_teacher_cannot_delete_media_from_another_namespace(): void
    {
        Storage::fake('public');
        config(['filesystems.default' => 'public']);
        $school = $this->school('media-scope');
        $teacherA = $this->user($school, 'teacher', 'media-a@example.test');
        $teacherB = $this->user($school, 'teacher', 'media-b@example.test');
        $otherPath = "questions/{$school->id}/{$teacherB->id}/private.pdf";
        $ownPath = "questions/{$school->id}/{$teacherA->id}/own.pdf";
        Storage::disk('public')->put($otherPath, '%PDF-other');
        Storage::disk('public')->put($ownPath, '%PDF-own');

        $this->actingAs($teacherA)->postJson(route('teacher.media.delete'), [
            'url' => Storage::disk('public')->url($otherPath),
        ])->assertOk();
        Storage::disk('public')->assertExists($otherPath);

        $this->actingAs($teacherA)->postJson(route('teacher.media.delete'), [
            'url' => Storage::disk('public')->url($ownPath),
        ])->assertOk()->assertJson(['status' => 'success']);
        Storage::disk('public')->assertMissing($ownPath);
    }

    public function test_question_html_is_sanitized_before_storage(): void
    {
        $school = $this->school('xss');
        $teacher = $this->user($school, 'teacher', 'xss@example.test');
        $group = $this->group($school, $teacher);

        $this->actingAs($teacher)->post(route('teacher.questions.store', $group), [
            'question_type' => 'single_choice',
            'content' => '<p>Safe</p><img src=x onerror="alert(1)"><script>alert(2)</script>',
            'options' => ['<strong>A</strong><img src=x onerror="alert(3)">', 'B'],
            'correct_answer' => 'A',
            'weight' => 1,
        ])->assertRedirect();

        $question = Question::firstOrFail();
        $serialized = $question->content.json_encode($question->options_json);
        $this->assertStringNotContainsString('onerror', $serialized);
        $this->assertStringNotContainsString('<script', $serialized);
        $this->assertStringContainsString('<p>Safe</p>', $question->content);
    }

    public function test_autosave_uses_server_deadline_and_filters_foreign_question_ids(): void
    {
        $school = $this->school('server-time');
        $teacher = $this->user($school, 'teacher', 'timer-teacher@example.test');
        $student = $this->user($school, 'student', 'timer-student@example.test');
        $group = $this->group($school, $teacher);
        $question = Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => 'Question',
            'options_json' => [['id' => 'A', 'text' => 'A']],
            'correct_answers_json' => ['A'],
            'weight' => 1,
        ]);
        $exam = $this->exam($school, $group, 'CLOCK', 30);

        $this->actingAs($student)->post(route('student.enter-token'), ['token' => 'CLOCK']);
        $this->actingAs($student)->get(route('student.exam.run', $exam))->assertOk();
        $this->actingAs($student)->postJson(route('student.exam.api.autosave', $exam), [
            'answers' => [$question->id => 'A', 999999 => 'forged'],
            'time_remaining_seconds' => 999999999,
        ])->assertOk();

        $result = ExamResult::where('student_id', $student->id)->firstOrFail();
        $this->assertLessThanOrEqual(1800, $result->time_remaining_seconds);
        $this->assertArrayHasKey((string) $question->id, $result->answers_json);
        $this->assertArrayNotHasKey('999999', $result->answers_json);
    }

    public function test_untrusted_cloudflare_header_cannot_rotate_login_rate_limit_identity(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
                ->withHeader('CF-Connecting-IP', "203.0.113.{$attempt}")
                ->post('/login', ['email' => 'spoof-target@example.test', 'password' => 'wrong']);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.10'])
            ->withHeader('CF-Connecting-IP', '203.0.113.200')
            ->post('/login', ['email' => 'spoof-target@example.test', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    public function test_email_case_variations_cannot_bypass_login_rate_limit(): void
    {
        $variants = [
            'victim@example.test',
            'Victim@example.test',
            'vIctim@example.test',
            'viCtim@example.test',
            'vicTim@example.test',
        ];

        foreach ($variants as $email) {
            $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20'])
                ->post('/login', ['email' => $email, 'password' => 'wrong']);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.20'])
            ->post('/login', ['email' => 'VICTIM@example.test', 'password' => 'wrong'])
            ->assertStatus(429);
    }

    private function school(string $slug): School
    {
        return School::create([
            'name' => ucfirst($slug).' School',
            'email' => "{$slug}@school.example.test",
            'code' => strtoupper(substr(hash('sha256', $slug), 0, 8)),
        ]);
    }

    private function user(School $school, string $role, string $email, bool $verified = true): User
    {
        return User::create([
            'school_id' => $school->id,
            'name' => ucfirst($role),
            'email' => $email,
            'role' => $role,
            'email_verified_at' => $verified ? now() : null,
            'password' => Hash::make('password'),
        ]);
    }

    private function group(School $school, User $teacher): QuestionGroup
    {
        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Security',
            'code' => 'SEC'.Subject::count(),
        ]);

        return QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Security Group',
        ]);
    }

    private function exam(School $school, QuestionGroup $group, string $token, int $minutes = 30): Exam
    {
        return Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Security Exam',
            'token' => $token,
            'starts_at' => now()->subMinute(),
            'ends_at' => now()->addHour(),
            'duration_minutes' => $minutes,
            'is_active' => true,
        ]);
    }
}
