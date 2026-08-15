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
            'timezone' => 'Asia/Jakarta',
        ]);

        // 2. School Admin User
        $admin = User::create([
            'school_id' => $school->id,
            'name' => 'School Administrator',
            'email' => 'admin@demo.org',
            'identity_number' => 'ADM001',
            'role' => 'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // 3. Teacher User
        $teacher = User::create([
            'school_id' => $school->id,
            'name' => 'Dr. Jane Smith',
            'email' => 'teacher@demo.org',
            'identity_number' => 'NIP19850101',
            'role' => 'teacher',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // 4. Student User
        $student = User::create([
            'school_id' => $school->id,
            'name' => 'John Doe',
            'email' => 'student@demo.org',
            'identity_number' => 'NIS2026001',
            'role' => 'student',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
        ]);

        // 5. Academic Year & Classrooms
        $academicYear = AcademicYear::create([
            'school_id' => $school->id,
            'start_year' => 2026,
            'end_year' => 2027,
            'semester' => 'odd',
            'is_active' => true,
        ]);

        $classroom10A = Classroom::create([
            'school_id' => $school->id,
            'grade_level' => 'Grade 10',
            'name' => '10-A Science',
        ]);

        $classroom10B = Classroom::create([
            'school_id' => $school->id,
            'grade_level' => 'Grade 10',
            'name' => '10-B Social',
        ]);

        // 6. Subjects (7 mata pelajaran)
        $subjectPAI = Subject::create([
            'school_id' => $school->id,
            'name' => 'Pendidikan Agama Islam',
            'code' => 'PAI',
        ]);

        $subjectMTK = Subject::create([
            'school_id' => $school->id,
            'name' => 'Matematika',
            'code' => 'MTK',
        ]);

        $subjectBIN = Subject::create([
            'school_id' => $school->id,
            'name' => 'Bahasa Indonesia',
            'code' => 'BIN',
        ]);

        $subjectIPA = Subject::create([
            'school_id' => $school->id,
            'name' => 'Ilmu Pengetahuan Alam',
            'code' => 'IPA',
        ]);

        $subjectIPS = Subject::create([
            'school_id' => $school->id,
            'name' => 'Ilmu Pengetahuan Sosial',
            'code' => 'IPS',
        ]);

        $subjectENG = Subject::create([
            'school_id' => $school->id,
            'name' => 'Bahasa Inggris',
            'code' => 'ENG',
        ]);

        $subjectCS = Subject::create([
            'school_id' => $school->id,
            'name' => 'Informatika',
            'code' => 'CS',
        ]);

        // 7. Teacher-Subject Mapping
        foreach ([$subjectPAI, $subjectMTK, $subjectBIN, $subjectIPA, $subjectIPS, $subjectENG, $subjectCS] as $subj) {
            TeacherSubject::create([
                'school_id' => $school->id,
                'teacher_id' => $teacher->id,
                'subject_id' => $subj->id,
            ]);
        }

        // =====================================================================
        // 8. QUESTION GROUP: Pendidikan Agama Islam (PAI) - 10 Soal
        // =====================================================================
        $groupPAI = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectPAI->id,
            'name' => 'Ujian Tengah Semester - Pendidikan Agama Islam',
            'description' => 'Soal PAI mencakup Rukun Islam, Iman, Al-Quran, Shalat, Puasa, dan Akhlak.',
        ]);

        // PAI-01: Single Choice - Rukun Islam
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'single_choice',
            'content' => 'Rukun Islam yang keempat adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Mengerjakan shalat'],
                ['id' => 'B', 'text' => 'Membayar zakat'],
                ['id' => 'C', 'text' => 'Berpuasa di bulan Ramadhan'],
                ['id' => 'D', 'text' => 'Menunaikan ibadah haji'],
            ],
            'correct_answers_json' => ['D'],
            'explanation' => 'Rukun Islam keempat adalah menunaikan ibadah haji bagi yang mampu.',
            'weight' => 10,
        ]);

        // PAI-02: True/False - Al-Fatihah
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'true_false',
            'content' => 'Surah Al-Fatihah terdiri dari 7 ayat dan termasuk golongan surah Makkiyyah.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['true'],
            'explanation' => 'Al-Fatihah terdiri dari 7 ayat dan merupakan surah Makkiyyah berdasarkan ijma ulama.',
            'weight' => 10,
        ]);

        // PAI-03: Single Choice - Rukun Iman
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'single_choice',
            'content' => 'Berikut yang BUKAN termasuk rukun iman adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Iman kepada Allah SWT'],
                ['id' => 'B', 'text' => 'Iman kepada Malaikat'],
                ['id' => 'C', 'text' => 'Iman kepada Hari Kiamat'],
                ['id' => 'D', 'text' => 'Iman kepada Kebudayaan'],
            ],
            'correct_answers_json' => ['D'],
            'explanation' => 'Rukun iman ada 6: iman kepada Allah, Malaikat, Kitab, Rasul, Hari Kiamat, dan Qadha-Qadar.',
            'weight' => 10,
        ]);

        // PAI-04: Single Choice - Waktu Shalat
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'single_choice',
            'content' => 'Shalat yang dikerjakan setelah matahari tergelincir ke barat disebut shalat...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Subuh'],
                ['id' => 'B', 'text' => 'Dzuhur'],
                ['id' => 'C', 'text' => 'Ashar'],
                ['id' => 'D', 'text' => 'Maghrib'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Shalat Dzuhur dikerjakan setelah matahari tergelincir (condong) dari tengah langit ke arah barat.',
            'weight' => 10,
        ]);

        // PAI-05: Single Choice - Nabi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'single_choice',
            'content' => 'Nabi yang mendapat gelar Ulul Azmi adalah nabi yang memiliki...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Kekayaan yang melimpah'],
                ['id' => 'B', 'text' => 'Keteguhan dan kesabaran luar biasa'],
                ['id' => 'C', 'text' => 'Tentara yang banyak'],
                ['id' => 'D', 'text' => 'Istri yang banyak'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Ulul Azmi adalah gelar untuk nabi yang memiliki keteguhan, kesabaran, dan keuletan luar biasa dalam menyampaikan risalah Allah.',
            'weight' => 10,
        ]);

        // PAI-06: Multiple Choice - Rukun Islam
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilihlah yang termasuk rukun Islam:',
            'options_json' => [
                ['id' => 'A', 'text' => 'Syahadat'],
                ['id' => 'B', 'text' => 'Shalat'],
                ['id' => 'C', 'text' => 'Sedekah'],
                ['id' => 'D', 'text' => 'Puasa Ramadhan'],
            ],
            'correct_answers_json' => ['A', 'B', 'D'],
            'explanation' => 'Rukun Islam: 1) Syahadat, 2) Shalat, 3) Puasa Ramadhan, 4) Zakat, 5) Haji. Sedekah bukan rukun Islam.',
            'weight' => 10,
        ]);

        // PAI-07: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Al-Quran terdiri dari 114 surah."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'Al-Quran terdiri dari 114 surah adalah fakta yang dapat diverifikasi.',
            'weight' => 10,
        ]);

        // PAI-08: True/False - Puasa
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'true_false',
            'content' => 'Puasa Ramadhan hukumnya wajib bagi setiap muslim yang sudah baligh dan berakal.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['true'],
            'explanation' => 'Puasa Ramadhan hukumnya wajib (fardhu) bagi setiap muslim yang memenuhi syarat: baligh, berakal, mampu, dan tidak ada halangan.',
            'weight' => 10,
        ]);

        // PAI-09: Single Choice - Akhlak
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'single_choice',
            'content' => 'Sikap terpuji yang mencerminkan akhlakul karimah adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Sombong dan angkuh'],
                ['id' => 'B', 'text' => 'Jujur dan amanah'],
                ['id' => 'C', 'text' => 'Iri dan dengki'],
                ['id' => 'D', 'text' => 'Suka menggunjing'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Jujur dan amanah adalah contoh akhlakul karimah (akhlak terpuji) yang diajarkan dalam Islam.',
            'weight' => 10,
        ]);

        // PAI-10: Matching - Asmaul Husna
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'question_type' => 'matching',
            'content' => 'Jodohkan Asmaul Husna dengan artinya:',
            'options_json' => [
                'left' => [
                    ['id' => 'Ar-Rahman', 'text' => 'Ar-Rahman'],
                    ['id' => 'Ar-Rahim', 'text' => 'Ar-Rahim'],
                    ['id' => 'Al-Malik', 'text' => 'Al-Malik'],
                ],
                'right' => [
                    ['id' => 'Maha Pengasih', 'text' => 'Maha Pengasih'],
                    ['id' => 'Maha Penyayang', 'text' => 'Maha Penyayang'],
                    ['id' => 'Maha Merajai', 'text' => 'Maha Merajai'],
                ],
            ],
            'correct_answers_json' => [
                'Ar-Rahman' => 'Maha Pengasih',
                'Ar-Rahim' => 'Maha Penyayang',
                'Al-Malik' => 'Maha Merajai',
            ],
            'explanation' => 'Ar-Rahman = Maha Pengasih, Ar-Rahim = Maha Penyayang, Al-Malik = Maha Merajai/Menguasai.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 9. QUESTION GROUP: Matematika (MTK) - 10 Soal
        // =====================================================================
        $groupMTK = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectMTK->id,
            'name' => 'Ujian Tengah Semester - Matematika',
            'description' => 'Soal Matematika mencakup Aljabar, Geometri, dan Aritmetika.',
        ]);

        // MTK-01: Single Choice - Persamaan Linear
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'single_choice',
            'content' => 'Hasil dari 3x + 5 = 20 adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'x = 3'],
                ['id' => 'B', 'text' => 'x = 5'],
                ['id' => 'C', 'text' => 'x = 7'],
                ['id' => 'D', 'text' => 'x = 15'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => '3x + 5 = 20, maka 3x = 15, sehingga x = 5.',
            'weight' => 10,
        ]);

        // MTK-02: Single Choice - Persegi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'single_choice',
            'content' => 'Luas persegi dengan sisi 8 cm adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => '16 cm2'],
                ['id' => 'B', 'text' => '32 cm2'],
                ['id' => 'C', 'text' => '64 cm2'],
                ['id' => 'D', 'text' => '128 cm2'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'Luas persegi = sisi x sisi = 8 x 8 = 64 cm2.',
            'weight' => 10,
        ]);

        // MTK-03: Single Choice - FPB
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'single_choice',
            'content' => 'FPB dari 24 dan 36 adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => '6'],
                ['id' => 'B', 'text' => '8'],
                ['id' => 'C', 'text' => '12'],
                ['id' => 'D', 'text' => '24'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'Faktor persekutuan terbesar dari 24 dan 36 adalah 12.',
            'weight' => 10,
        ]);

        // MTK-04: Single Choice - KPK
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'single_choice',
            'content' => 'KPK dari 6 dan 8 adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => '12'],
                ['id' => 'B', 'text' => '24'],
                ['id' => 'C', 'text' => '48'],
                ['id' => 'D', 'text' => '6'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Kelipatan persekutuan terkecil dari 6 dan 8 adalah 24.',
            'weight' => 10,
        ]);

        // MTK-05: Single Choice - Persentase
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'single_choice',
            'content' => '25% dari 200 adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => '25'],
                ['id' => 'B', 'text' => '50'],
                ['id' => 'C', 'text' => '75'],
                ['id' => 'D', 'text' => '100'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => '25% dari 200 = 25/100 x 200 = 50.',
            'weight' => 10,
        ]);

        // MTK-06: Multiple Choice - Bilangan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilihlah bilangan yang habis dibagi 3:',
            'options_json' => [
                ['id' => 'A', 'text' => '12'],
                ['id' => 'B', 'text' => '15'],
                ['id' => 'C', 'text' => '22'],
                ['id' => 'D', 'text' => '27'],
            ],
            'correct_answers_json' => ['A', 'B', 'D'],
            'explanation' => '12/3=4, 15/3=5, 27/3=9 habis dibagi 3. Sedangkan 22/3=7.33 tidak habis.',
            'weight' => 10,
        ]);

        // MTK-07: Essay - Persamaan Kuadrat
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'essay',
            'content' => 'Jelaskan langkah-langkah menyelesaikan persamaan kuadrat x^2 - 5x + 6 = 0 menggunakan metode pemfaktoran!',
            'options_json' => null,
            'correct_answers_json' => ['faktorkan menjadi (x-2)(x-3)=0 sehingga x=2 atau x=3'],
            'explanation' => 'x^2 - 5x + 6 = (x-2)(x-3) = 0, maka x = 2 atau x = 3.',
            'weight' => 15,
        ]);

        // MTK-08: True/False - Bilangan Prima
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'true_false',
            'content' => 'Angka 1 termasuk bilangan prima.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'Angka 1 BUKAN bilangan prima. Bilangan prima adalah bilangan yang memiliki tepat dua faktor: 1 dan dirinya sendiri.',
            'weight' => 10,
        ]);

        // MTK-09: Sorting - Urutan Operasi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'sorting',
            'content' => 'Urutkan operasi hitung berdasarkan aturan prioritas (PEMDAS/BODMAS) dari yang dikerjakan pertama:',
            'options_json' => [
                'Kurung (Parentheses)',
                'Pangkat (Exponents)',
                'Kali dan Bagi (Multiplication/Division)',
                'Tambah dan Kurang (Addition/Subtraction)',
            ],
            'correct_answers_json' => [
                'Kurung (Parentheses)',
                'Pangkat (Exponents)',
                'Kali dan Bagi (Multiplication/Division)',
                'Tambah dan Kurang (Addition/Subtraction)',
            ],
            'explanation' => 'Aturan PEMDAS: Parentheses, Exponents, Multiplication/Division, Addition/Subtraction.',
            'weight' => 10,
        ]);

        // MTK-10: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Matematika adalah pelajaran yang paling sulit."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['opinion'],
            'explanation' => 'Pernyataan tersebut adalah opini karena kesulitan pelajaran bersifat subjektif bagi setiap orang.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 10. QUESTION GROUP: Bahasa Indonesia (BIN) - 10 Soal
        // =====================================================================
        $groupBIN = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectBIN->id,
            'name' => 'Ujian Tengah Semester - Bahasa Indonesia',
            'description' => 'Soal Bahasa Indonesia mencakup Ejaan, Tata Bahasa, Teks, dan Sastra.',
        ]);

        // BIN-01: Single Choice - Ejaan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Kalimat yang menggunakan ejaan yang benar adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Saya pergi ke pasar untuk membeli sayur-mayur.'],
                ['id' => 'B', 'text' => 'Saya pergi kepasar untuk membeli sayur mayur.'],
                ['id' => 'C', 'text' => 'Saya pergi ke pasar untuk membeli sayur mayur.'],
                ['id' => 'D', 'text' => 'Saya pergi kepasar untuk membeli sayur-mayur.'],
            ],
            'correct_answers_json' => ['A'],
            'explanation' => 'Penulisan benar: "ke pasar" (kata depan dipisah) dan "sayur-mayur" (ulang berimbuhan pakai huruf penghubung).',
            'weight' => 10,
        ]);

        // BIN-02: Single Choice - Antonim
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Lawan kata (antonim) dari "rajin" adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Malas'],
                ['id' => 'B', 'text' => 'Pandai'],
                ['id' => 'C', 'text' => 'Cepat'],
                ['id' => 'D', 'text' => 'Baik'],
            ],
            'correct_answers_json' => ['A'],
            'explanation' => 'Lawan kata "rajin" adalah "malas".',
            'weight' => 10,
        ]);

        // BIN-03: Single Choice - Sinonim
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Sinonim (persamaan kata) dari "cantik" adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Jelek'],
                ['id' => 'B', 'text' => 'Indah'],
                ['id' => 'C', 'text' => 'Besar'],
                ['id' => 'D', 'text' => 'Kecil'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Sinonim "cantik" adalah "indah" atau "elok".',
            'weight' => 10,
        ]);

        // BIN-04: Single Choice - Jenis Teks
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Teks yang bertujuan untuk menjelaskan proses terjadinya sesuatu disebut teks...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Narasi'],
                ['id' => 'B', 'text' => 'Deskripsi'],
                ['id' => 'C', 'text' => 'Eksposisi'],
                ['id' => 'D', 'text' => 'Prosedur'],
            ],
            'correct_answers_json' => ['D'],
            'explanation' => 'Teks prosedur menjelaskan langkah-langkah atau proses untuk melakukan sesuatu.',
            'weight' => 10,
        ]);

        // BIN-05: Multiple Choice - Kalimat Efektif
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilihlah kalimat yang termasuk kalimat efektif:',
            'options_json' => [
                ['id' => 'A', 'text' => 'Dia pergi ke sekolah.'],
                ['id' => 'B', 'text' => 'Pergi dia ke sekolah.'],
                ['id' => 'C', 'text' => 'Saya makan nasi.'],
                ['id' => 'D', 'text' => 'Nasi makan saya.'],
            ],
            'correct_answers_json' => ['A', 'C'],
            'explanation' => 'Kalimat efektif memiliki struktur SPO yang benar dan sesuai kaidah bahasa Indonesia.',
            'weight' => 10,
        ]);

        // BIN-06: True/False - Kata Baku
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'true_false',
            'content' => 'Kata "aktifitas" adalah kata baku dalam bahasa Indonesia.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'Kata baku yang benar adalah "aktivitas", bukan "aktifitas".',
            'weight' => 10,
        ]);

        // BIN-07: Single Choice - Majas
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Kalimat "Dia adalah bunga hatiku" menggunakan majas...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Simile'],
                ['id' => 'B', 'text' => 'Metafora'],
                ['id' => 'C', 'text' => 'Personifikasi'],
                ['id' => 'D', 'text' => 'Hiperbola'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Majas metafora membandingkan dua hal secara langsung tanpa kata pembanding seperti "bagai" atau "seperti".',
            'weight' => 10,
        ]);

        // BIN-08: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Bahasa Indonesia adalah bahasa resmi negara Republik Indonesia."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'Hal ini tertuang dalam UUD 1945 Pasal 36 bahwa bahasa negara ialah bahasa Indonesia.',
            'weight' => 10,
        ]);

        // BIN-09: Single Choice - Imbuhan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'single_choice',
            'content' => 'Kata "memperindah" terdiri dari awalan, kata dasar, dan akhiran. Kata dasarnya adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Indah'],
                ['id' => 'B', 'text' => 'Perindah'],
                ['id' => 'C', 'text' => 'Indahkan'],
                ['id' => 'D', 'text' => 'Memper'],
            ],
            'correct_answers_json' => ['A'],
            'explanation' => 'Kata dasar "memperindah" adalah "indah" (awalan: mem-, akhiran: -kan).',
            'weight' => 10,
        ]);

        // BIN-10: Matching - Jenis Paragraf
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'question_type' => 'matching',
            'content' => 'Jodohkan jenis paragraf dengan posisi kalimat utamanya:',
            'options_json' => [
                'left' => [
                    ['id' => 'Deduktif', 'text' => 'Paragraf Deduktif'],
                    ['id' => 'Induktif', 'text' => 'Paragraf Induktif'],
                    ['id' => 'Campuran', 'text' => 'Paragraf Campuran'],
                ],
                'right' => [
                    ['id' => 'Di awal paragraf', 'text' => 'Di awal paragraf'],
                    ['id' => 'Di akhir paragraf', 'text' => 'Di akhir paragraf'],
                    ['id' => 'Di awal dan akhir', 'text' => 'Di awal dan akhir'],
                ],
            ],
            'correct_answers_json' => [
                'Deduktif' => 'Di awal paragraf',
                'Induktif' => 'Di akhir paragraf',
                'Campuran' => 'Di awal dan akhir',
            ],
            'explanation' => 'Deduktif = umum ke khusus (di awal), Induktif = khusus ke umum (di akhir), Campuran = di awal dan akhir.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 11. QUESTION GROUP: IPA (Ilmu Pengetahuan Alam) - 10 Soal
        // =====================================================================
        $groupIPA = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectIPA->id,
            'name' => 'Ujian Tengah Semester - IPA',
            'description' => 'Soal IPA mencakup Biologi, Fisika, dan Kimia dasar.',
        ]);

        // IPA-01: Single Choice - Organ Pencernaan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'single_choice',
            'content' => 'Organ tubuh manusia yang berfungsi sebagai pompa untuk memompa darah ke seluruh tubuh adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Paru-paru'],
                ['id' => 'B', 'text' => 'Jantung'],
                ['id' => 'C', 'text' => 'Hati'],
                ['id' => 'D', 'text' => 'Ginjal'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Jantung berfungsi memompa darah ke seluruh tubuh melalui sistem peredaran darah.',
            'weight' => 10,
        ]);

        // IPA-02: Multiple Choice - Organ Pencernaan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilih organ tubuh manusia yang termasuk dalam sistem pencernaan:',
            'options_json' => [
                ['id' => 'A', 'text' => 'Lambung'],
                ['id' => 'B', 'text' => 'Paru-paru'],
                ['id' => 'C', 'text' => 'Usus halus'],
                ['id' => 'D', 'text' => 'Hati'],
            ],
            'correct_answers_json' => ['A', 'C', 'D'],
            'explanation' => 'Lambung, usus halus, dan hati adalah organ pencernaan. Paru-paru adalah organ pernapasan.',
            'weight' => 10,
        ]);

        // IPA-03: Single Choice - Fotosintesis
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'single_choice',
            'content' => 'Proses fotosintesis menghasilkan...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Karbon dioksida dan air'],
                ['id' => 'B', 'text' => 'Glukosa dan oksigen'],
                ['id' => 'C', 'text' => 'Nitrogen dan karbon'],
                ['id' => 'D', 'text' => 'Air dan garam'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Fotosintesis mengubah CO2 + H2O menjadi glukosa (C6H12O6) + O2 dengan bantuan sinar matahari.',
            'weight' => 10,
        ]);

        // IPA-04: Single Choice - Planet
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'single_choice',
            'content' => 'Planet terbesar dalam tata surya adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Saturnus'],
                ['id' => 'B', 'text' => 'Jupiter'],
                ['id' => 'C', 'text' => 'Neptunus'],
                ['id' => 'D', 'text' => 'Uranus'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Jupiter adalah planet terbesar dalam tata surya kita.',
            'weight' => 10,
        ]);

        // IPA-05: True/False - Air
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'true_false',
            'content' => 'Air mendidih pada suhu 100 derajat Celsius pada tekanan normal (1 atm).',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['true'],
            'explanation' => 'Air mendidih pada suhu 100 derajat Celsius pada tekanan 1 atmosfer (tekanan permukaan laut).',
            'weight' => 10,
        ]);

        // IPA-06: Single Choice - Zat
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'single_choice',
            'content' => 'Lambang kimia untuk air adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'CO2'],
                ['id' => 'B', 'text' => 'H2O'],
                ['id' => 'C', 'text' => 'NaCl'],
                ['id' => 'D', 'text' => 'O2'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Air (H2O) terdiri dari 2 atom hidrogen dan 1 atom oksigen.',
            'weight' => 10,
        ]);

        // IPA-07: Matching - Organ & Fungsi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'matching',
            'content' => 'Jodohkan organ tubuh dengan fungsinya:',
            'options_json' => [
                'left' => [
                    ['id' => 'Ginjal', 'text' => 'Ginjal'],
                    ['id' => 'Otak', 'text' => 'Otak'],
                    ['id' => 'Mata', 'text' => 'Mata'],
                ],
                'right' => [
                    ['id' => 'Menyaring darah', 'text' => 'Menyaring darah'],
                    ['id' => 'Mengendalikan tubuh', 'text' => 'Mengendalikan tubuh'],
                    ['id' => 'Indra penglihatan', 'text' => 'Indra penglihatan'],
                ],
            ],
            'correct_answers_json' => [
                'Ginjal' => 'Menyaring darah',
                'Otak' => 'Mengendalikan tubuh',
                'Mata' => 'Indra penglihatan',
            ],
            'explanation' => 'Ginjal menyaring darah, otak mengendalikan tubuh, mata adalah indra penglihatan.',
            'weight' => 10,
        ]);

        // IPA-08: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Bumi mengelilingi Matahari dalam waktu 365,25 hari."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'Bumi mengorbit Matahari dalam waktu sekitar 365,25 hari adalah fakta ilmiah yang terverifikasi.',
            'weight' => 10,
        ]);

        // IPA-09: Sorting - Rantai Makanan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'sorting',
            'content' => 'Urutkan rantai makanan berikut dari yang paling bawah (produsen) ke paling atas (konsumen puncak):',
            'options_json' => [
                'Rumput (Produsen)',
                'Kelinci (Konsumen I)',
                'Ular (Konsumen II)',
                'Elang (Konsumen III)',
            ],
            'correct_answers_json' => [
                'Rumput (Produsen)',
                'Kelinci (Konsumen I)',
                'Ular (Konsumen II)',
                'Elang (Konsumen III)',
            ],
            'explanation' => 'Rantai makanan: Rumput -> Kelinci -> Ular -> Elang.',
            'weight' => 10,
        ]);

        // IPA-10: Single Choice - Gaya
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'question_type' => 'single_choice',
            'content' => 'Satuan untuk mengukur gaya dalam sistem internasional (SI) adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Watt'],
                ['id' => 'B', 'text' => 'Joule'],
                ['id' => 'C', 'text' => 'Newton'],
                ['id' => 'D', 'text' => 'Pascal'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'Satuan gaya dalam SI adalah Newton (N). Watt untuk daya, Joule untuk energi, Pascal untuk tekanan.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 12. QUESTION GROUP: IPS (Ilmu Pengetahuan Sosial) - 10 Soal
        // =====================================================================
        $groupIPS = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectIPS->id,
            'name' => 'Ujian Tengah Semester - IPS',
            'description' => 'Soal IPS mencakup Sejarah, Geografi, Ekonomi, dan Sosiologi.',
        ]);

        // IPS-01: Fact/Opini - Kemerdekaan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Indonesia merdeka pada tanggal 17 Agustus 1945."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'Indonesia memproklamasikan kemerdekaan pada 17 Agustus 1945 adalah fakta sejarah yang terverifikasi.',
            'weight' => 10,
        ]);

        // IPS-02: Single Choice - Presiden
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'single_choice',
            'content' => 'Presiden pertama Republik Indonesia adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Soeharto'],
                ['id' => 'B', 'text' => 'Soekarno'],
                ['id' => 'C', 'text' => 'B.J. Habibie'],
                ['id' => 'D', 'text' => 'Megawati'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Ir. Soekarno adalah Presiden pertama Republik Indonesia (1945-1967).',
            'weight' => 10,
        ]);

        // IPS-03: Single Choice - Benua
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'single_choice',
            'content' => 'Benua terluas di dunia adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Afrika'],
                ['id' => 'B', 'text' => 'Amerika'],
                ['id' => 'C', 'text' => 'Asia'],
                ['id' => 'D', 'text' => 'Eropa'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'Asia adalah benua terluas dengan luas sekitar 44,58 juta km2.',
            'weight' => 10,
        ]);

        // IPS-04: Single Choice - Kerajaan Islam
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'single_choice',
            'content' => 'Kerajaan Islam tertua di Indonesia adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Kesultanan Demak'],
                ['id' => 'B', 'text' => 'Kerajaan Samudera Pasai'],
                ['id' => 'C', 'text' => 'Kesultanan Mataram'],
                ['id' => 'D', 'text' => 'Kerajaan Majapahit'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Kerajaan Samudera Pasai di Aceh adalah kerajaan Islam tertua di Indonesia (abad ke-13).',
            'weight' => 10,
        ]);

        // IPS-05: Single Choice - Ekonomi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'single_choice',
            'content' => 'Alat pembayaran yang sah di Indonesia adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Dollar Amerika'],
                ['id' => 'B', 'text' => 'Rupiah'],
                ['id' => 'C', 'text' => 'Euro'],
                ['id' => 'D', 'text' => 'Yen Jepang'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Rupiah (IDR) adalah mata uang resmi dan alat pembayaran yang sah di Indonesia berdasarkan UUD 1945.',
            'weight' => 10,
        ]);

        // IPS-06: Multiple Choice - Sumber Daya Alam
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilihlah yang termasuk sumber daya alam hayati:',
            'options_json' => [
                ['id' => 'A', 'text' => 'Hutan'],
                ['id' => 'B', 'text' => 'Minyak bumi'],
                ['id' => 'C', 'text' => 'Ikan'],
                ['id' => 'D', 'text' => 'Tanaman'],
            ],
            'correct_answers_json' => ['A', 'C', 'D'],
            'explanation' => 'Sumber daya alam hayati berasal dari makhluk hidup: hutan, ikan, tanaman. Minyak bumi termasuk non-hayati.',
            'weight' => 10,
        ]);

        // IPS-07: True/False - ASEAN
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'true_false',
            'content' => 'ASEAN (Association of Southeast Asian Nations) didirikan pada tahun 1967.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['true'],
            'explanation' => 'ASEAN didirikan pada 8 Agustus 1967 melalui Deklarasi Bangkok oleh 5 negara pendiri.',
            'weight' => 10,
        ]);

        // IPS-08: Single Choice - Geografi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'single_choice',
            'content' => 'Indonesia terletak di antara dua benua, yaitu...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Asia dan Eropa'],
                ['id' => 'B', 'text' => 'Asia dan Australia'],
                ['id' => 'C', 'text' => 'Afrika dan Asia'],
                ['id' => 'D', 'text' => 'Amerika dan Eropa'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Indonesia terletak di antara benua Asia (di utara) dan Australia (di selatan).',
            'weight' => 10,
        ]);

        // IPS-09: Sorting - Sejarah Kemerdekaan
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'sorting',
            'content' => 'Urutkan peristiwa berikut secara kronologis:',
            'options_json' => [
                'Kedatangan bangsa Eropa ke Nusantara',
                'Berdirinya Budi Utomo (1908)',
                'Sumpah Pemuda (1928)',
                'Proklamasi Kemerdekaan (1945)',
            ],
            'correct_answers_json' => [
                'Kedatangan bangsa Eropa ke Nusantara',
                'Berdirinya Budi Utomo (1908)',
                'Sumpah Pemuda (1928)',
                'Proklamasi Kemerdekaan (1945)',
            ],
            'explanation' => 'Urutan kronologis: Kedatangan Eropa (abad 16) -> Budi Utomo (1908) -> Sumpah Pemuda (1928) -> Proklamasi (1945).',
            'weight' => 10,
        ]);

        // IPS-10: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Jakarta adalah kota terbaik di Asia Tenggara."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['opinion'],
            'explanation' => 'Pernyataan tersebut adalah opini karena kata "terbaik" bersifat subjektif dan tidak terukur secara objektif.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 13. QUESTION GROUP: Bahasa Inggris (ENG) - 10 Soal
        // =====================================================================
        $groupENG = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectENG->id,
            'name' => 'Ujian Tengah Semester - Bahasa Inggris',
            'description' => 'Soal Bahasa Inggris mencakup Grammar, Vocabulary, dan Reading.',
        ]);

        // ENG-01: Single Choice - Grammar
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'Choose the correct sentence:',
            'options_json' => [
                ['id' => 'A', 'text' => 'She don\'t like coffee.'],
                ['id' => 'B', 'text' => 'She doesn\'t likes coffee.'],
                ['id' => 'C', 'text' => 'She doesn\'t like coffee.'],
                ['id' => 'D', 'text' => 'She not like coffee.'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'Third person singular (she/he/it) uses "doesn\'t" + base verb (like), not "don\'t" or "likes".',
            'weight' => 10,
        ]);

        // ENG-02: Single Choice - Vocabulary
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'The opposite (antonym) of "beautiful" is...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Pretty'],
                ['id' => 'B', 'text' => 'Ugly'],
                ['id' => 'C', 'text' => 'Handsome'],
                ['id' => 'D', 'text' => 'Cute'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'The antonym of "beautiful" is "ugly". Pretty, handsome, and cute are synonyms.',
            'weight' => 10,
        ]);

        // ENG-03: Single Choice - Tense
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'She _____ to school every morning.',
            'options_json' => [
                ['id' => 'A', 'text' => 'go'],
                ['id' => 'B', 'text' => 'goes'],
                ['id' => 'C', 'text' => 'going'],
                ['id' => 'D', 'text' => 'went'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Simple present tense with third person singular (she) adds -s/-es to the verb: "goes".',
            'weight' => 10,
        ]);

        // ENG-04: Single Choice - Preposition
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'The book is _____ the table.',
            'options_json' => [
                ['id' => 'A', 'text' => 'in'],
                ['id' => 'B', 'text' => 'on'],
                ['id' => 'C', 'text' => 'at'],
                ['id' => 'D', 'text' => 'by'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'We use "on" for surfaces. The book is on the table (on the surface of the table).',
            'weight' => 10,
        ]);

        // ENG-05: Multiple Choice - Parts of Speech
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'multiple_choice',
            'content' => 'Which of the following are adjectives?',
            'options_json' => [
                ['id' => 'A', 'text' => 'Beautiful'],
                ['id' => 'B', 'text' => 'Quickly'],
                ['id' => 'C', 'text' => 'Happy'],
                ['id' => 'D', 'text' => 'Run'],
            ],
            'correct_answers_json' => ['A', 'C'],
            'explanation' => 'Beautiful and happy are adjectives (describe nouns). Quickly is an adverb, run is a verb.',
            'weight' => 10,
        ]);

        // ENG-06: True/False - Grammar Rule
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'true_false',
            'content' => 'In English, we always use "a" before words that start with a vowel sound.',
            'options_json' => [
                ['id' => 'true', 'text' => 'True'],
                ['id' => 'false', 'text' => 'False'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'We use "an" before vowel sounds (an apple, an hour), not "a".',
            'weight' => 10,
        ]);

        // ENG-07: Single Choice - Conditional
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'If it _____ tomorrow, we will stay at home.',
            'options_json' => [
                ['id' => 'A', 'text' => 'rain'],
                ['id' => 'B', 'text' => 'rains'],
                ['id' => 'C', 'text' => 'rained'],
                ['id' => 'D', 'text' => 'raining'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'First conditional: If + simple present, will + base verb. "If it rains" is correct.',
            'weight' => 10,
        ]);

        // ENG-08: Matching - Word & Meaning
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'matching',
            'content' => 'Match the English words with their Indonesian meanings:',
            'options_json' => [
                'left' => [
                    ['id' => 'Library', 'text' => 'Library'],
                    ['id' => 'Hospital', 'text' => 'Hospital'],
                    ['id' => 'Teacher', 'text' => 'Teacher'],
                ],
                'right' => [
                    ['id' => 'Perpustakaan', 'text' => 'Perpustakaan'],
                    ['id' => 'Rumah Sakit', 'text' => 'Rumah Sakit'],
                    ['id' => 'Guru', 'text' => 'Guru'],
                ],
            ],
            'correct_answers_json' => [
                'Library' => 'Perpustakaan',
                'Hospital' => 'Rumah Sakit',
                'Teacher' => 'Guru',
            ],
            'explanation' => 'Library = Perpustakaan, Hospital = Rumah Sakit, Teacher = Guru.',
            'weight' => 10,
        ]);

        // ENG-09: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'fact_opinion',
            'content' => 'Determine whether the statement is a Fact or Opinion: "English is the most widely spoken language in the world."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fact'],
                ['id' => 'opinion', 'text' => 'Opinion'],
            ],
            'correct_answers_json' => ['fact'],
            'explanation' => 'English is the most widely spoken language globally (by total number of speakers) is a verifiable fact.',
            'weight' => 10,
        ]);

        // ENG-10: Single Choice - Question Tag
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'question_type' => 'single_choice',
            'content' => 'She is a student, _____?',
            'options_json' => [
                ['id' => 'A', 'text' => 'is she'],
                ['id' => 'B', 'text' => 'isn\'t she'],
                ['id' => 'C', 'text' => 'doesn\'t she'],
                ['id' => 'D', 'text' => 'does she'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Question tag rule: positive sentence -> negative tag. "She is" -> "isn\'t she?"',
            'weight' => 10,
        ]);

        // =====================================================================
        // 14. QUESTION GROUP: Informatika (CS) - 10 Soal
        // =====================================================================
        $groupCS = QuestionGroup::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subjectCS->id,
            'name' => 'Ujian Tengah Semester - Informatika',
            'description' => 'Soal Informatika mencakup Pemrograman, Jaringan, dan Sistem Komputer.',
        ]);

        // CS-01: Single Choice - Binary
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'single_choice',
            'content' => 'Bilangan biner dari desimal 10 adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => '1010'],
                ['id' => 'B', 'text' => '1100'],
                ['id' => 'C', 'text' => '1001'],
                ['id' => 'D', 'text' => '1110'],
            ],
            'correct_answers_json' => ['A'],
            'explanation' => 'Desimal 10 = 8+2 = 1010 dalam biner.',
            'weight' => 10,
        ]);

        // CS-02: Single Choice - Hardware
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'single_choice',
            'content' => 'Komponen komputer yang berfungsi sebagai otak (processor) adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'RAM'],
                ['id' => 'B', 'text' => 'CPU'],
                ['id' => 'C', 'text' => 'Hard Disk'],
                ['id' => 'D', 'text' => 'VGA Card'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'CPU (Central Processing Unit) adalah otak komputer yang memproses semua instruksi.',
            'weight' => 10,
        ]);

        // CS-03: Single Choice - Protokol
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'single_choice',
            'content' => 'Protokol yang digunakan untuk mengakses halaman web adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'FTP'],
                ['id' => 'B', 'text' => 'SMTP'],
                ['id' => 'C', 'text' => 'HTTP/HTTPS'],
                ['id' => 'D', 'text' => 'SSH'],
            ],
            'correct_answers_json' => ['C'],
            'explanation' => 'HTTP (HyperText Transfer Protocol) dan HTTPS adalah protokol untuk transfer halaman web.',
            'weight' => 10,
        ]);

        // CS-04: Single Choice - Memory
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'single_choice',
            'content' => 'Jenis memori yang bersifat sementara (hilang saat komputer dimatikan) adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'ROM'],
                ['id' => 'B', 'text' => 'RAM'],
                ['id' => 'C', 'text' => 'Hard Disk'],
                ['id' => 'D', 'text' => 'SSD'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'RAM (Random Access Memory) adalah memori volatile yang isinya hilang saat daya diputus.',
            'weight' => 10,
        ]);

        // CS-05: Multiple Choice - Perangkat Lunak
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'multiple_choice',
            'content' => 'Pilihlah yang termasuk perangkat lunak (software) sistem operasi:',
            'options_json' => [
                ['id' => 'A', 'text' => 'Microsoft Word'],
                ['id' => 'B', 'text' => 'Windows'],
                ['id' => 'C', 'text' => 'Linux'],
                ['id' => 'D', 'text' => 'Google Chrome'],
            ],
            'correct_answers_json' => ['B', 'C'],
            'explanation' => 'Windows dan Linux adalah sistem operasi. Microsoft Word dan Google Chrome adalah aplikasi.',
            'weight' => 10,
        ]);

        // CS-06: True/False - Internet
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'true_false',
            'content' => 'WWW (World Wide Web) dan Internet adalah hal yang sama.',
            'options_json' => [
                ['id' => 'true', 'text' => 'Benar'],
                ['id' => 'false', 'text' => 'Salah'],
            ],
            'correct_answers_json' => ['false'],
            'explanation' => 'Internet adalah jaringan global, sedangkan WWW adalah layanan di atas internet untuk mengakses halaman web.',
            'weight' => 10,
        ]);

        // CS-07: Matching - Protokol & Fungsi
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'matching',
            'content' => 'Jodohkan protokol jaringan dengan fungsinya:',
            'options_json' => [
                'left' => [
                    ['id' => 'HTTP', 'text' => 'HTTP'],
                    ['id' => 'FTP', 'text' => 'FTP'],
                    ['id' => 'SMTP', 'text' => 'SMTP'],
                ],
                'right' => [
                    ['id' => 'Transfer halaman web', 'text' => 'Transfer halaman web'],
                    ['id' => 'Transfer file', 'text' => 'Transfer file'],
                    ['id' => 'Kirim email', 'text' => 'Kirim email'],
                ],
            ],
            'correct_answers_json' => [
                'HTTP' => 'Transfer halaman web',
                'FTP' => 'Transfer file',
                'SMTP' => 'Kirim email',
            ],
            'explanation' => 'HTTP untuk web, FTP untuk file, SMTP untuk email.',
            'weight' => 10,
        ]);

        // CS-08: Sorting - SDLC
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'sorting',
            'content' => 'Urutkan tahapan pengembangan perangkat lunak (SDLC) yang benar:',
            'options_json' => [
                'Perencanaan (Planning)',
                'Analisis Kebutuhan (Analysis)',
                'Desain (Design)',
                'Pengkodean (Coding)',
                'Pengujian (Testing)',
            ],
            'correct_answers_json' => [
                'Perencanaan (Planning)',
                'Analisis Kebutuhan (Analysis)',
                'Desain (Design)',
                'Pengkodean (Coding)',
                'Pengujian (Testing)',
            ],
            'explanation' => 'Tahapan SDLC: Planning -> Analysis -> Design -> Coding -> Testing.',
            'weight' => 10,
        ]);

        // CS-09: Fact/Opini
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'fact_opinion',
            'content' => 'Tentukan apakah pernyataan berikut Fakta atau Opini: "Python adalah bahasa pemrograman yang paling mudah dipelajari."',
            'options_json' => [
                ['id' => 'fact', 'text' => 'Fakta'],
                ['id' => 'opinion', 'text' => 'Opini'],
            ],
            'correct_answers_json' => ['opinion'],
            'explanation' => 'Pernyataan tersebut adalah opini karena kemudahan belajar bahasa pemrograman bersifat subjektif.',
            'weight' => 10,
        ]);

        // CS-10: Single Choice - Algoritma
        Question::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'question_type' => 'single_choice',
            'content' => 'Struktur data yang menggunakan prinsip LIFO (Last In First Out) adalah...',
            'options_json' => [
                ['id' => 'A', 'text' => 'Queue (Antrian)'],
                ['id' => 'B', 'text' => 'Stack (Tumpukan)'],
                ['id' => 'C', 'text' => 'Array'],
                ['id' => 'D', 'text' => 'Linked List'],
            ],
            'correct_answers_json' => ['B'],
            'explanation' => 'Stack menggunakan prinsip LIFO: elemen terakhir yang masuk pertama keluar. Queue menggunakan FIFO.',
            'weight' => 10,
        ]);

        // =====================================================================
        // 15. EXAMS - Satu exam per mata pelajaran
        // =====================================================================
        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'title' => 'UTS Pendidikan Agama Islam 2026',
            'token' => 'PAI26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 45,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupMTK->id,
            'title' => 'UTS Matematika 2026',
            'token' => 'MTK26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 60,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupBIN->id,
            'title' => 'UTS Bahasa Indonesia 2026',
            'token' => 'BIN26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 45,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPA->id,
            'title' => 'UTS Ilmu Pengetahuan Alam 2026',
            'token' => 'IPA26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 45,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupIPS->id,
            'title' => 'UTS Ilmu Pengetahuan Sosial 2026',
            'token' => 'IPS26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 45,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupENG->id,
            'title' => 'UTS Bahasa Inggris 2026',
            'token' => 'ENG26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 45,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupCS->id,
            'title' => 'UTS Informatika 2026',
            'token' => 'CS26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(7),
            'duration_minutes' => 60,
            'is_active' => true,
            'randomize_questions' => false,
            'randomize_options' => false,
            'show_explanation_after_submit' => true,
        ]);

        // Exam gabungan semua mapel
        Exam::create([
            'school_id' => $school->id,
            'question_group_id' => $groupPAI->id,
            'title' => 'Ujian Komprehensif Semua Mata Pelajaran 2026',
            'token' => 'EXAM26',
            'starts_at' => now()->subHours(1),
            'ends_at' => now()->addDays(30),
            'duration_minutes' => 120,
            'is_active' => true,
            'randomize_questions' => true,
            'randomize_options' => true,
            'show_explanation_after_submit' => true,
        ]);
    }
}
