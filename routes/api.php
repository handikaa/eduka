<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 * Prefix: /api
 * Version: v1
 */

Route::prefix('v1')->group(function () {

    /**
     * Authentication Routes (Public)
     */
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->name('auth.register');
        Route::post('login', [AuthController::class, 'login'])
            ->name('auth.login');

        /**
         * Protected Authentication Routes
         */
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])
                ->name('auth.logout');
            Route::get('profile', [AuthController::class, 'profile'])
                ->name('auth.profile');
        });
    });

    /**
     * Protected API Routes (Require Authentication)
     */
    Route::middleware('auth:sanctum')->group(function () {
        // Routes untuk authenticated users akan ditambahkan di sini
        // Category, Course, Lesson, Enrollment, LessonProgress, CourseReview
    });
});
