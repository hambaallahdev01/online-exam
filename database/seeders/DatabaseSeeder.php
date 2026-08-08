<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\School;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Demo School
        $school = School::create([
            'name' => 'Demo International Academy',
            'code' => 'DEMO01',
            'email' => 'admin@demo-school.org',
            'address' => '123 Education Street, Suite 100',
            'phone' => '+62 812-3456-7890',
        ]);

        // 2. School Admin User
        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'School Administrator',
            'email' => 'admin@demo.org',
            'identity_number' => 'ADM001',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // 3. Teacher User
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Dr. Jane Smith',
            'email' => 'teacher@demo.org',
            'identity_number' => 'NIP19850101',
            'role' => 'teacher',
            'password' => Hash::make('password'),
        ]);

        // 4. Student User
        $student = User::create([
            'school_id' => $school->id,
            'name' => 'John Doe',
            'email' => 'student@demo.org',
            'identity_number' => 'NIS2026001',
            'role' => 'student',
            'password' => Hash::make('password'),
        ]);

        // 5. Academic Year & Classroom & Subject
        $academicYear = AcademicYear::create([
            'school_id' => $school->id,
            'start_year' => 2026,
            'end_year' => 2027,
            'semester' => 'odd',
            'is_active' => true,
        ]);

        $classroom = Classroom::create([
            'school_id' => $school->id,
            'grade_level' => 'Grade 10',
            'name' => '10-A Science',
        ]);

        $subject = Subject::create([
            'school_id' => $school->id,
            'name' => 'Computer Science & AI Basics',
            'code' => 'CS101',
        ]);

        TeacherSubject::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
        ]);

        // 6. Question Group & Questions (All 7 Question Types)
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Complete Assessment - All 7 Question Types',
            'description' => 'Comprehensive exam featuring all 7 distinct question formats.',
        ]);

        // 1. Single Choice
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'single_choice',
            'content' => 'Which PHP framework features Laravel Octane for high-concurrency execution?',
            'options_json' => [
                ['id' => 'A', 'text' => 'CodeIgniter 2'],
                ['id' => 'B', 'text' => 'Laravel 13'],
                ['id' => 'C', 'text' => 'Yii 1'],
                ['id' => 'D', 'text' => 'Vanilla PHP 5'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Laravel Octane supercharges application performance using high-powered servers like Swoole and FrankenPHP.',
            'weight' => 15,
        ]);

        // 2. Multiple Choice
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'multiple_choice',
            'content' => 'Select all relational database engines supported natively by Laravel Eloquent:',
            'options_json' => [
                ['id' => 'A', 'text' => 'MySQL 8+'],
                ['id' => 'B', 'text' => 'MariaDB'],
                ['id' => 'C', 'text' => 'PostgreSQL'],
                ['id' => 'D', 'text' => 'Microsoft Access 97'],
            ],
            'correct_answers_json' => ['A', 'B', 'C'],
            'explanation' => 'MySQL, MariaDB, and PostgreSQL are fully supported relational database drivers in Laravel.',
            'weight' => 15,
        ]);

        // 3. Essay
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'essay',
            'content' => 'Explain briefly how local asset bundling prevents supply-chain security vulnerabilities.',
            'options_json' => null,
            'correct_answers_json' => ['local assets eliminate third-party script injection risks'],
            'explanation' => 'Serving local assets avoids external CDN domain compromise and ensures 100% offline uptime.',
            'weight' => 15,
        ]);

        // 4. True / False
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'true_false',
            'content' => 'Using external CDNs for examination interfaces is safer than local asset compilation.',
            'options_json' => [
                ['id' => 'true', 'text' => 'True / Benar'],
                ['id' => 'false', 'text' => 'False / Salah'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'Local compilation is significantly safer against supply chain hijacking.',
            'weight' => 10,
        ]);

        // 5. Fact / Opinion
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'fact_opinion',
            'content' => 'Determine whether the statement is a Fact or Opinion: "PHP was originally created by Rasmus Lerdorf in 1994."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'This is a verifiable historical fact.',
            'weight' => 15,
        ]);

        // 6. Matching (Menjodohkan)
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'matching',
            'content' => 'Match each country to its capital city:',
            'options_json' => [
                'left' => [
                    ['id' => 'Indonesia', 'text' => 'Indonesia'],
                    ['id' => 'Japan', 'text' => 'Japan'],
                    ['id' => 'France', 'text' => 'France'],
                ],
                'right' => [
                    ['id' => 'Jakarta', 'text' => 'Jakarta'],
                    ['id' => 'Tokyo', 'text' => 'Tokyo'],
                    ['id' => 'Paris', 'text' => 'Paris'],
                ]
            ],
            'correct_answers_json' => [
                'Indonesia' => 'Jakarta',
                'Japan' => 'Tokyo',
                'France' => 'Paris',
            ],
            'explanation' => 'Indonesia -> Jakarta, Japan -> Tokyo, France -> Paris.',
            'weight' => 15,
        ]);

        // 7. Sorting (Mengurutkan)
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'sorting',
            'content' => 'Arrange the execution stages of a Web request in correct chronological order:',
            'options_json' => [
                'Browser sends HTTP Request',
                'Web Server receives Request',
                'Laravel Application processes Logic',
                'Browser renders HTML Response',
            ],
            'correct_answers_json' => [
                'Browser sends HTTP Request',
                'Web Server receives Request',
                'Laravel Application processes Logic',
                'Browser renders HTML Response',
            ],
            'explanation' => 'The order of web execution flows from client request to server processing and client rendering.',
            'weight' => 15,
        ]);

        // 7. Demo Exam
        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'title' => 'CS101 Midterm Examination 2026',
            'token' => 'EXAM26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 60,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);
    }
}
