<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingQuestionBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_matching_question_without_writing_json(): void
    {
        [$teacher, $group] = $this->teacherAndGroup();

        $response = $this->actingAs($teacher)->post(route('teacher.questions.store', $group), [
            'question_type' => 'matching',
            'content' => 'Pasangkan kota dengan negaranya.',
            'matching_left' => ['Springfield', 'Springfield'],
            'matching_right' => ['Amerika Serikat', 'Kanada'],
            'weight' => 10,
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $question = Question::firstOrFail();
        $this->assertSame([
            'left' => [
                ['id' => 'left_1', 'text' => 'Springfield'],
                ['id' => 'left_2', 'text' => 'Springfield'],
            ],
            'right' => [
                ['id' => 'right_1', 'text' => 'Amerika Serikat'],
                ['id' => 'right_2', 'text' => 'Kanada'],
            ],
        ], $question->options_json);
        $this->assertSame([
            'left_1' => 'right_1',
            'left_2' => 'right_2',
        ], $question->correct_answers_json);

        $this->actingAs($teacher)
            ->get(route('teacher.question-groups.show', $group))
            ->assertOk()
            ->assertSee('Pasangan dan kunci jawaban:')
            ->assertSee('Amerika Serikat')
            ->assertSee('tidak perlu menulis JSON')
            ->assertDontSee('Masukkan pasangan JSON');
    }

    public function test_matching_question_rejects_incomplete_or_misaligned_pairs(): void
    {
        [$teacher, $group] = $this->teacherAndGroup();

        $this->actingAs($teacher)->post(route('teacher.questions.store', $group), [
            'question_type' => 'matching',
            'content' => 'Pasangkan data berikut.',
            'matching_left' => ['A', 'B'],
            'matching_right' => ['1', '2', '3'],
            'weight' => 10,
        ])->assertSessionHasErrors('matching_right');

        $this->assertDatabaseCount('questions', 0);

        $this->actingAs($teacher)->post(route('teacher.questions.store', $group), [
            'question_type' => 'matching',
            'content' => 'Pasangkan data berikut.',
            'matching_left' => ['A'],
            'matching_right' => ['1'],
            'weight' => 10,
        ])->assertSessionHasErrors(['matching_left', 'matching_right']);

        $this->assertDatabaseCount('questions', 0);
    }

    public function test_editing_legacy_matching_question_converts_it_to_internal_ids(): void
    {
        [$teacher, $group] = $this->teacherAndGroup();
        $question = Question::create([
            'school_id' => $teacher->school_id,
            'question_group_id' => $group->id,
            'question_type' => 'matching',
            'content' => 'Data lama',
            'options_json' => [
                'left' => [
                    ['id' => 'Indonesia', 'text' => 'Indonesia'],
                    ['id' => 'Jepang', 'text' => 'Jepang'],
                ],
                'right' => [
                    ['id' => 'Jakarta', 'text' => 'Jakarta'],
                    ['id' => 'Tokyo', 'text' => 'Tokyo'],
                ],
            ],
            'correct_answers_json' => [
                'Indonesia' => 'Jakarta',
                'Jepang' => 'Tokyo',
            ],
            'weight' => 5,
        ]);

        $response = $this->actingAs($teacher)->put(route('teacher.questions.update', $question), [
            'question_type' => 'matching',
            'content' => 'Data diperbarui',
            'matching_left' => ['Indonesia', 'Jepang'],
            'matching_right' => ['Jakarta', 'Tokyo'],
            'weight' => 15,
        ]);

        $response->assertRedirect()->assertSessionHasNoErrors();

        $question->refresh();
        $this->assertSame(['left_1' => 'right_1', 'left_2' => 'right_2'], $question->correct_answers_json);
        $this->assertSame('Indonesia', $question->options_json['left'][0]['text']);
        $this->assertSame('Tokyo', $question->options_json['right'][1]['text']);
        $this->assertSame(15, $question->weight);
    }

    /**
     * @return array{0: User, 1: QuestionGroup}
     */
    private function teacherAndGroup(): array
    {
        $school = School::create([
            'name' => 'Sekolah Matching',
            'email' => 'matching-school@example.test',
        ]);
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Guru Matching',
            'email' => 'matching-teacher@example.test',
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);
        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Pengetahuan Umum',
        ]);
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Bank Matching',
        ]);

        return [$teacher, $group];
    }
}
