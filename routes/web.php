<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Student\Api\ExamSessionApiController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:login');
Route::get('/register-school', [AuthController::class, 'showRegisterSchool'])->name('register.school');
Route::post('/register-school', [AuthController::class, 'registerSchool'])->middleware('throttle:register-school');

// Forgot Password & Reset Password Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email Verification Route
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

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

        // Student Exam API Endpoints (Protected with Rate Limiter)
        Route::prefix('api/exam/{exam}')->name('exam.api.')->middleware('throttle:api-exam')->group(function () {
            Route::get('/payload', [ExamSessionApiController::class, 'getPayload'])->name('payload');
            Route::post('/autosave', [ExamSessionApiController::class, 'autosave'])->name('autosave');
            Route::post('/submit', [ExamSessionApiController::class, 'submit'])->name('submit');
        });
    });
});

// Redis Diagnostic Route for cPanel Testing
Route::get('/test-redis', function () {
    if (!extension_loaded('redis')) {
        return response()->json(['status' => 'error', 'message' => 'PHP extension redis is NOT loaded in cPanel PHP selector.']);
    }
    try {
        \Illuminate\Support\Facades\Redis::set('test_cpanel_key', 'OK - ' . date('Y-m-d H:i:s'));
        $val = \Illuminate\Support\Facades\Redis::get('test_cpanel_key');
        return response()->json([
            'status' => 'success',
            'message' => 'Redis server is UP and connected successfully!',
            'test_value' => $val,
            'redis_host' => env('REDIS_HOST', '127.0.0.1'),
            'redis_port' => env('REDIS_PORT', 6379),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Redis extension is active, but connection to Redis server failed.',
            'error_detail' => $e->getMessage(),
            'hint' => 'Check if Redis server daemon is running on cPanel or ask hosting provider for exact host/port/socket.',
        ]);
    }
});
