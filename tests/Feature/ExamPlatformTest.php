<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_school_registration_creates_school_and_admin()
    {
        $response = $this->post('/register-school', [
            'school_name' => 'St. Jude Academy',
            'school_code' => 'STJUDE',
            'school_email' => 'contact@stjude.org',
            'admin_name' => 'Father Francis',
            'admin_email' => 'francis@stjude.org',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('schools', ['code' => 'STJUDE']);
        $this->assertDatabaseHas('users', ['email' => 'francis@stjude.org', 'role' => 'admin']);
    }

    public function test_user_can_login_via_post_to_root_and_login_url()
    {
        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Admin User',
            'email' => 'admin@test.org',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->post('/', [
            'email' => 'admin@test.org',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_unverified_user_cannot_login()
    {
        $school = School::create(['name' => 'Demo', 'email' => 'demo@school.org']);
        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Unverified User',
            'email' => 'unverified@test.org',
            'role' => 'admin',
            'email_verified_at' => null,
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
        ]);

        $response = $this->post('/', [
            'email' => 'unverified@test.org',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_student_can_enter_exam_token_and_fetch_payload()
    {
        $school = School::create(['name' => 'Test School', 'email' => 'test@school.org']);
        $teacher = User::create(['school_id' => $school->id, 'name' => 'Teacher', 'email' => 't@school.org', 'password' => 'pass', 'role' => 'teacher']);
        $student = User::create(['school_id' => $school->id, 'name' => 'Student', 'email' => 's@school.org', 'password' => 'pass', 'role' => 'student']);

        $subject = \App\Models\Subject::create(['school_id' => $school->id, 'name' => 'Math', 'code' => 'MTH']);

        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Group 1',
        ]);

        $q = Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => 'What is 2+2?',
            'options_json' => [['id' => 'A', 'text' => '3'], ['id' => 'B', 'text' => '4']],
            'correct_answers_json' => ['B'],
            'weight' => 10,
        ]);

        $exam = Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Math Exam',
            'token' => 'MATH10',
            'starts_at' => now(),
            'ends_at' => now()->addHour(),
            'duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Student logins and enters token
        $this->actingAs($student);

        $response = $this->get(route('student.exam.run', $exam->id));
        $response->assertStatus(200);

        // Fetch API Payload
        $apiResp = $this->get(route('student.exam.api.payload', $exam->id));
        $apiResp->assertStatus(200)->assertJson(['status' => 'success']);

        // Submit Answer
        $submitResp = $this->postJson(route('student.exam.api.submit', $exam->id), [
            'answers' => [$q->id => 'B']
        ]);

        $submitResp->assertStatus(200)->assertJson(['status' => 'success', 'score' => 100]);
        $this->assertDatabaseHas('exam_results', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'score' => 100,
            'status' => 'graded'
        ]);
    }
}
