<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_edit_and_delete_published_exams()
    {
        $school = School::create(['name' => 'School Test', 'email' => 'exam_school@test.com']);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Teacher Exam',
            'email' => 'teacher_exam@test.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Science']);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Physics',
        ]);

        $exam = Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'Initial Exam Title',
            'token' => 'TOKEN1',
            'duration_minutes' => 60,
            'starts_at' => now(),
            'ends_at' => now()->addDays(7),
            'is_active' => true,
        ]);

        // Edit exam
        $response = $this->actingAs($teacher)->put(route('teacher.exams.update', $exam->id), [
            'title' => 'Updated Exam Title',
            'token' => 'TOKEN2',
            'duration_minutes' => 90,
            'is_active' => 0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'title' => 'Updated Exam Title',
            'token' => 'TOKEN2',
            'duration_minutes' => 90,
            'is_active' => false,
        ]);

        // Delete exam
        $deleteResponse = $this->actingAs($teacher)->delete(route('teacher.exams.destroy', $exam->id));
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }
}
