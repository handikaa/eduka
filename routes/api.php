<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EnrollmentController;
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
            Route::get('me', [AuthController::class, 'me'])
                ->name('auth.me');
            Route::get('user/{id}', [AuthController::class, 'getUserById'])
                ->name('auth.getUserById');
        });
    });

    /**
     * Course Routes (Public GET, Protected Create/Update/Delete)
     */
    Route::prefix('courses')->group(function () {
        // Public routes - Get courses
        // Protected routes - Create/Update/Delete (require authentication)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/', [CourseController::class, 'index'])
                ->name('courses.index');
            Route::get('/{id}', [CourseController::class, 'show'])
                ->name('courses.show');
            Route::get('/slug/{slug}', [CourseController::class, 'showBySlug'])
                ->name('courses.showBySlug');
            Route::post('/', [CourseController::class, 'store'])
                ->name('courses.store');
            Route::put('/{id}', [CourseController::class, 'update'])
                ->name('courses.update');
            Route::delete('/{id}', [CourseController::class, 'destroy'])
                ->name('courses.destroy');
            Route::post('/{id}/restore', [CourseController::class, 'restore'])
                ->name('courses.restore');
            Route::delete('/{id}/force', [CourseController::class, 'forceDelete'])
                ->name('courses.forceDelete');


            Route::patch('/{slug}/status', [CourseController::class, 'updateStatus'])
                ->name('courses.updateStatus');

            /**
             * Lesson Management Routes (Nested under Courses)
             */
            Route::post('/{courseId}/lessons/{lessonId}/restore', [CourseController::class, 'restoreLesson'])
                ->name('lessons.restore');
            Route::delete('/{courseId}/lessons/{lessonId}/force', [CourseController::class, 'forceDeleteLesson'])
                ->name('lessons.forceDelete');
        });
    });

    /**
     * Enrollment Student to Course
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('enrollments')->group(function () {

            Route::post('/{id}/enroll', [EnrollmentController::class, 'store'])
                ->name('enrollment.store');

            Route::get('/me', [EnrollmentController::class, 'indexMyEnrollments'])
                ->name('enrollment.indexMyEnrollments');
            Route::get('/courses/{id}', [EnrollmentController::class, 'indexByCourse'])
                ->whereNumber('id')->name('enrollment.indexByCourse');

            Route::get('/{id}', [EnrollmentController::class, 'show'])
                ->whereNumber('id')
                ->name('enrollment.show');
        });
    });

    /**
     * Category Routes (Public GET, Protected Create/Update/Delete)
     */
    Route::prefix('categories')->group(function () {
        // Public routes - Get categories
        // Protected routes - Create/Update/Delete
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/{id}', [CategoryController::class, 'show'])
                ->name('categories.show');

            Route::get('/slug/{slug}', [CategoryController::class, 'showBySlug'])
                ->name('categories.showBySlug');

            Route::get('/', [CategoryController::class, 'index'])
                ->name('categories.index');
            Route::post('/', [CategoryController::class, 'store'])
                ->name('categories.store');
            Route::put('/{id}', [CategoryController::class, 'update'])
                ->name('categories.update');

            Route::delete('/{id}', [CategoryController::class, 'destroy'])
                ->name('categories.destroy');
        });
    });
});
