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

class QuestionMediaDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_question_automatically_purges_all_s3_and_local_media_in_content_and_options()
    {
        Storage::fake('public');
        Storage::fake('s3');

        $school = School::create(['name' => 'Test School', 'email' => 'school_media@test.com']);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Teacher One',
            'email' => 'teacher_media@test.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);
        $subject = Subject::create(['school_id' => $school->id, 'name' => 'General']);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Sample Group',
        ]);

        // Put fake files on storage
        Storage::disk('public')->put('questions/img_content.jpg', 'fake content image');
        Storage::disk('public')->put('questions/img_option_a.jpg', 'fake option A image');
        Storage::disk('public')->put('questions/pdf_doc.pdf', 'fake pdf doc');

        $url1 = Storage::disk('public')->url('questions/img_content.jpg');
        $url2 = Storage::disk('public')->url('questions/img_option_a.jpg');
        $url3 = Storage::disk('public')->url('questions/pdf_doc.pdf');

        $question = Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => "<p>Question text</p><img src=\"{$url1}\"><a href=\"{$url3}\" class=\"pdf-attachment-badge\">PDF</a>",
            'options_json' => [
                ['id' => 'A', 'text' => "<p>Option A</p><img src=\"{$url2}\">"],
                ['id' => 'B', 'text' => "Option B"],
            ],
            'correct_answers_json' => ['A'],
            'weight' => 10,
        ]);

        Storage::disk('public')->assertExists('questions/img_content.jpg');
        Storage::disk('public')->assertExists('questions/img_option_a.jpg');
        Storage::disk('public')->assertExists('questions/pdf_doc.pdf');

        // Delete question
        $response = $this->actingAs($teacher)->delete(route('teacher.questions.destroy', $question->id));
        $response->assertRedirect();

        // Assert question deleted from database
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);

        // Assert media files automatically purged from storage
        Storage::disk('public')->assertMissing('questions/img_content.jpg');
        Storage::disk('public')->assertMissing('questions/img_option_a.jpg');
        Storage::disk('public')->assertMissing('questions/pdf_doc.pdf');
    }
}
