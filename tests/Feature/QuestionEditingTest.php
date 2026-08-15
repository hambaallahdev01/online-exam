<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_editing_question_updates_fields_and_purges_removed_s3_media()
    {
        Storage::fake('public');

        $school = School::create(['name' => 'Test School', 'email' => 'edit_school@test.com']);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Teacher Edit',
            'email' => 'teacher_edit@test.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'Math']);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Algebra',
        ]);

        $mediaPrefix = "questions/{$school->id}/{$teacher->id}";

        // Create 2 fake media files in this teacher's isolated namespace.
        Storage::disk('public')->put("{$mediaPrefix}/old_img.jpg", 'old image');
        Storage::disk('public')->put("{$mediaPrefix}/keep_img.jpg", 'keep image');

        $urlOld = Storage::disk('public')->url("{$mediaPrefix}/old_img.jpg");
        $urlKeep = Storage::disk('public')->url("{$mediaPrefix}/keep_img.jpg");

        $question = Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => "<p>Old Content</p><img src=\"{$urlOld}\"><img src=\"{$urlKeep}\">",
            'options_json' => [
                ['id' => 'A', 'text' => 'Option A'],
                ['id' => 'B', 'text' => 'Option B'],
            ],
            'correct_answers_json' => ['A'],
            'weight' => 10,
        ]);

        Storage::disk('public')->assertExists("{$mediaPrefix}/old_img.jpg");
        Storage::disk('public')->assertExists("{$mediaPrefix}/keep_img.jpg");

        // Edit question: remove old_img.jpg and keep keep_img.jpg
        $response = $this->actingAs($teacher)->put(route('teacher.questions.update', $question->id), [
            'question_type' => 'single_choice',
            'content' => "<p>Updated Content</p><img src=\"{$urlKeep}\">",
            'options' => ['Option A Updated', 'Option B Updated'],
            'correct_answer' => 'A',
            'weight' => 15,
        ]);

        $response->assertRedirect();

        // Assert database updated
        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'weight' => 15,
        ]);

        // Assert old removed image is deleted from storage, and kept image remains
        Storage::disk('public')->assertMissing("{$mediaPrefix}/old_img.jpg");
        Storage::disk('public')->assertExists("{$mediaPrefix}/keep_img.jpg");
    }
}
