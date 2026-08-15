<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantTimezoneTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_same_local_schedule_is_stored_as_distinct_utc_instants_per_tenant(): void
    {
        [$jakartaSchool, $jakartaTeacher, $jakartaGroup] = $this->tenant('jakarta', 'Asia/Jakarta');
        [$jayapuraSchool, $jayapuraTeacher, $jayapuraGroup] = $this->tenant('jayapura', 'Asia/Jayapura');

        $schedule = [
            'title' => 'Morning Exam',
            'token' => 'MORNING',
            'duration_minutes' => 60,
            'starts_at' => '2026-08-16T09:00',
            'ends_at' => '2026-08-16T11:00',
        ];

        $this->actingAs($jakartaTeacher)
            ->post(route('teacher.exams.store'), $schedule + ['question_group_id' => $jakartaGroup->id])
            ->assertSessionHasNoErrors();

        $this->actingAs($jayapuraTeacher)
            ->post(route('teacher.exams.store'), $schedule + ['question_group_id' => $jayapuraGroup->id])
            ->assertSessionHasNoErrors();

        $jakartaExam = Exam::where('school_id', $jakartaSchool->id)->sole();
        $jayapuraExam = Exam::where('school_id', $jayapuraSchool->id)->sole();

        $this->assertSame('2026-08-16 02:00:00', $jakartaExam->starts_at->format('Y-m-d H:i:s'));
        $this->assertSame('2026-08-16 00:00:00', $jayapuraExam->starts_at->format('Y-m-d H:i:s'));

        $this->actingAs($jakartaTeacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee('16 Aug 2026, 09:00')
            ->assertSee('Asia/Jakarta');

        $this->actingAs($jayapuraTeacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee('16 Aug 2026, 09:00')
            ->assertSee('Asia/Jayapura');

        $this->assertSame('UTC', config('app.timezone'));
        $this->assertSame('UTC', date_default_timezone_get());
    }

    public function test_exam_access_uses_the_exact_utc_schedule_boundaries(): void
    {
        [$school, $teacher, $group] = $this->tenant('boundary', 'Asia/Jakarta');
        $student = User::create([
            'school_id' => $school->id,
            'name' => 'Boundary Student',
            'email' => 'student-boundary@example.test',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
        $exam = Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Boundary Exam',
            'token' => 'BOUNDARY',
            'duration_minutes' => 60,
            'starts_at' => '2026-08-16 02:00:00',
            'ends_at' => '2026-08-16 04:00:00',
            'is_active' => true,
        ]);

        Carbon::setTestNow(Carbon::parse('2026-08-16 01:59:59', 'UTC'));
        $this->actingAs($student)
            ->post(route('student.enter-token'), ['token' => $exam->token])
            ->assertSessionHasErrors('token');

        Carbon::setTestNow(Carbon::parse('2026-08-16 02:00:00', 'UTC'));
        $this->actingAs($student)
            ->post(route('student.enter-token'), ['token' => $exam->token])
            ->assertRedirect(route('student.exam.run', $exam));

        Carbon::setTestNow(Carbon::parse('2026-08-16 04:00:01', 'UTC'));
        $this->actingAs($student)
            ->post(route('student.enter-token'), ['token' => $exam->token])
            ->assertSessionHasErrors('token');
    }

    public function test_changing_tenant_timezone_does_not_move_existing_exam_instants(): void
    {
        [$school, $teacher, $group] = $this->tenant('timezone-change', 'Asia/Jakarta');
        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'School Admin',
            'email' => 'admin-timezone-change@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $exam = Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Fixed Instant Exam',
            'token' => 'FIXED',
            'duration_minutes' => 60,
            'starts_at' => '2026-08-16 02:00:00',
            'ends_at' => '2026-08-16 04:00:00',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.profile.update'), [
            'name' => $school->name,
            'email' => $school->email,
            'locale' => 'id',
            'timezone' => 'Asia/Makassar',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('schools', [
            'id' => $school->id,
            'timezone' => 'Asia/Makassar',
        ]);
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'starts_at' => '2026-08-16 02:00:00',
            'ends_at' => '2026-08-16 04:00:00',
        ]);
    }

    public function test_invalid_or_nonexistent_tenant_local_time_is_rejected(): void
    {
        [$school, $teacher, $group] = $this->tenant('new-york', 'America/New_York');

        $this->actingAs($teacher)->post(route('teacher.exams.store'), [
            'question_group_id' => $group->id,
            'title' => 'DST Exam',
            'token' => 'DST',
            'duration_minutes' => 30,
            'starts_at' => '2026-03-08T02:30',
            'ends_at' => '2026-03-08T04:00',
        ])->assertSessionHasErrors('starts_at');

        $this->assertDatabaseMissing('exams', ['school_id' => $school->id]);
    }

    public function test_admin_cannot_store_a_non_iana_timezone(): void
    {
        [$school] = $this->tenant('invalid-timezone', 'Asia/Jakarta');
        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'Invalid Timezone Admin',
            'email' => 'invalid-timezone-admin@example.test',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->post(route('admin.profile.update'), [
            'name' => $school->name,
            'email' => $school->email,
            'locale' => 'id',
            'timezone' => 'UTC+7',
        ])->assertSessionHasErrors('timezone');

        $this->assertSame('Asia/Jakarta', $school->fresh()->timezone);
    }

    /**
     * @return array{School, User, QuestionGroup}
     */
    private function tenant(string $slug, string $timezone): array
    {
        $school = School::create([
            'name' => ucfirst($slug).' School',
            'email' => $slug.'@school.example.test',
            'timezone' => $timezone,
        ]);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => ucfirst($slug).' Teacher',
            'email' => $slug.'-teacher@example.test',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);
        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => ucfirst($slug).' Subject',
        ]);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => ucfirst($slug).' Questions',
        ]);

        return [$school, $teacher, $group];
    }
}
