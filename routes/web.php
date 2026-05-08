<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\Student\StudentViolationReportController;

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/news/{slug}', [LandingController::class, 'showNews'])->name('news.show');

// Student Auth Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::middleware('guest:student')->group(function () {
        Route::get('/login', [StudentAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [StudentAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth:student')->group(function () {
        Route::post('/logout', [StudentAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::get('/violations', [StudentDashboardController::class, 'violations'])->name('violations');
        Route::get('/violations/{violation}', [StudentViolationReportController::class, 'show'])->name('violations.show');

        // Lapor Pelanggaran — ditangani controller tersendiri
        Route::get('/report-violation', [StudentViolationReportController::class, 'create'])->name('report.form');
        Route::post('/report-violation', [StudentViolationReportController::class, 'store'])->name('report.store');
    });
});
