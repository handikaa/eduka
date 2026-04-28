<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\LessonProgressController;
use App\Http\Controllers\Api\CourseReviewController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
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
     * Users Routes (Publ
     */
    Route::prefix('users')->group(function () {
   
        /**
         * Protected Users Routes
         */

        Route::middleware('auth:sanctum')->group(function () {
             Route::get('/', [UserController::class, 'index'])
            ->name('users.index');
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
     * Enrollment Student and Mentor to Course
     */
    Route::middleware('auth:sanctum')->prefix('enrollments')->group(function () {

        Route::prefix('student')->group(function () {
            Route::post('/courses/{id}/enroll', [EnrollmentController::class, 'store'])
                ->whereNumber('id')
                ->name('student.enrollment.store');

            Route::get('/me', [EnrollmentController::class, 'indexMyEnrollments'])
                ->name('student.enrollment.indexMyEnrollments');

            Route::get('/{id}', [EnrollmentController::class, 'showStudentDetail'])
                ->whereNumber('id')
                ->name('student.enrollment.show');
        });

        Route::prefix('instructor')->group(function () {
            Route::get('/courses/{id}', [EnrollmentController::class, 'indexByCourse'])
                ->whereNumber('id')
                ->name('instructor.enrollment.indexByCourse');

            Route::get('/{id}', [EnrollmentController::class, 'show'])
                ->whereNumber('id')
                ->name('instructor.enrollment.show');
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
    /**
     * LessonProgress Routes (Public GET, Protected Create/Update/Delete)
     */

    Route::prefix('lesson-progress')->group(function () {
        // Public routes - Get categories
        // Protected routes - Create/Update/Delete
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/lessons/{id}/start', [LessonProgressController::class, 'start'])
                ->whereNumber('id')->name('lesson-progress.start');

            Route::post('/lessons/{id}/complete', [LessonProgressController::class, 'complete'])
                ->whereNumber('id')
                ->name('lesson-progress.complete');

            Route::get('/student/courses/{id}', [LessonProgressController::class, 'indexByCourse'])
                ->whereNumber('id')
                ->name('student.lesson-progress.indexByCourse');

            Route::get('/student/courses/{id}/summary', [LessonProgressController::class, 'summaryByCourse'])
                ->whereNumber('id')
                ->name('student.lesson-progress.summaryByCourse');
            Route::get('/student/lessons/{id}', [LessonProgressController::class, 'showDetail'])
                ->whereNumber('id')
                ->name('student.lesson-progress.showDetail');
            Route::patch('/lessons/{id}/last-accessed', [LessonProgressController::class, 'updateLastAccessed'])
                ->whereNumber('id')
                ->name('lesson-progress.updateLastAccessed');
        });
    });
    /**
     * CourseReview Routes (Public GET, Protected Create/Update/Delete)
     */

    Route::prefix('course-reviews')->group(function () {

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/student/courses/{id}', [CourseReviewController::class, 'store'])
                ->whereNumber('id')
                ->name('student.course-review.store');

            Route::get('/courses/{id}', [CourseReviewController::class, 'indexByCourse'])
                ->whereNumber('id')
                ->name('course-review.indexByCourse');
            Route::get('/student/courses/{id}', [CourseReviewController::class, 'showStudentReviewByCourse'])
                ->whereNumber('id')
                ->name('student.course-review.showByCourse');

            Route::patch('/student/courses/{id}', [CourseReviewController::class, 'updateByCourse'])
                ->whereNumber('id')
                ->name('student.course-review.updateByCourse');
            Route::delete('/student/courses/{id}', [CourseReviewController::class, 'deleteByCourse'])
                ->whereNumber('id')
                ->name('student.course-review.deleteByCourse');

            Route::patch('/student/courses/{id}/restore', [CourseReviewController::class, 'restoreByCourse'])
                ->whereNumber('id')
                ->name('student.course-review.restoreByCourse');
        });
    });
    Route::prefix('dashboard')->group(function () {

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/student/summary', [DashboardController::class, 'studentSummary'])
                ->name('student.dashboard.summary');

            Route::get('/student/continue-learning', [DashboardController::class, 'studentContinueLearning'])
                ->name('student.dashboard.continueLearning');

            Route::get('/instructor/summary', [DashboardController::class, 'instructorSummary'])
                ->name('instructor.dashboard.summary');

            Route::get('/instructor/course-performance', [DashboardController::class, 'instructorCoursePerformance'])
                ->name('instructor.dashboard.coursePerformance');
        });
    });
});
