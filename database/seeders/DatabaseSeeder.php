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

        // 6. Question Group & Questions
        $group = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'name' => 'Midterm Assessment - CS & Web Basics',
            'description' => 'Comprehensive exam covering web standards, database design, and programming concepts.',
        ]);

        // Question 1: Single Choice
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
            'explanation' => 'Laravel Octane supercharges your application performance using high-powered application servers like Swoole, RoadRunner, and FrankenPHP.',
            'weight' => 25,
        ]);

        // Question 2: True / False
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'true_false',
            'content' => 'Using external CDNs for critical assets is safer than bundling assets locally.',
            'options_json' => [
                ['id' => 'true', 'text' => 'True'],
                ['id' => 'false', 'text' => 'False'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'Bundling assets locally avoids third-party supply chain attacks and external network dependency issues.',
            'weight' => 25,
        ]);

        // Question 3: Multiple Choice
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'multiple_choice',
            'content' => 'Which of the following database engines are supported for production deployment?',
            'options_json' => [
                ['id' => 'A', 'text' => 'MySQL 8+'],
                ['id' => 'B', 'text' => 'MariaDB Stable'],
                ['id' => 'C', 'text' => 'Microsoft Access 97'],
                ['id' => 'D', 'text' => 'PostgreSQL'],
            ],
            'correct_answers_json' => ['A', 'B', 'D'],
            'explanation' => 'MySQL 8+, MariaDB, and PostgreSQL are all modern relational database engines supported by Laravel Eloquent.',
            'weight' => 25,
        ]);

        // Question 4: Essay
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $group->id,
            'question_type' => 'essay',
            'content' => 'Explain briefly how caching Redis helps speed up online exam processing during peak submission times.',
            'options_json' => null,
            'correct_answers_json' => ['in-memory storage reduces database I/O contention'],
            'explanation' => 'Redis stores session and cache data in-memory, dramatically decreasing MySQL disk read/write contention during simultaneous exam submissions.',
            'weight' => 25,
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
            'randomize_questions' => true,
            'randomize_options' => true,
            'show_explanation_after_submit' => true,
        ]);
    }
}
