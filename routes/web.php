<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\Api\ExamSessionApiController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register-school', [AuthController::class, 'showRegisterSchool'])->name('register.school');
Route::post('/register-school', [AuthController::class, 'registerSchool']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/teachers', [AdminDashboardController::class, 'teachers'])->name('teachers');
        Route::post('/teachers', [AdminDashboardController::class, 'storeTeacher']);
        Route::get('/students', [AdminDashboardController::class, 'students'])->name('students');
        Route::post('/students', [AdminDashboardController::class, 'storeStudent']);
    });

    // Teacher Routes
    Route::prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::post('/question-groups', [TeacherDashboardController::class, 'createQuestionGroup'])->name('question-groups.store');
        Route::get('/question-groups/{group}', [TeacherDashboardController::class, 'showQuestionGroup'])->name('question-groups.show');
        Route::post('/question-groups/{group}/questions', [TeacherDashboardController::class, 'storeQuestion'])->name('questions.store');
        Route::post('/exams', [TeacherDashboardController::class, 'storeExam'])->name('exams.store');
    });

    // Student Routes
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::post('/enter-token', [StudentDashboardController::class, 'enterToken'])->name('enter-token');
        Route::get('/exam/{exam}', [StudentDashboardController::class, 'runExam'])->name('exam.run');

        // Student Exam API Endpoints (Vanilla JS Fetch)
        Route::prefix('api/exam/{exam}')->name('exam.api.')->group(function () {
            Route::get('/payload', [ExamSessionApiController::class, 'getPayload'])->name('payload');
            Route::post('/autosave', [ExamSessionApiController::class, 'autosave'])->name('autosave');
            Route::post('/submit', [ExamSessionApiController::class, 'submit'])->name('submit');
        });
    });
});
