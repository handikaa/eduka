<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Domain\LessonProgress\Repositories\LessonProgressRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentLessonRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentCourseRepository;
use App\Domain\Enrollment\Repositories\EnrollmentRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentEnrollmentRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentLessonProgressRepository;

class RepositoryServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );
        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );
        $this->app->bind(
            CourseRepositoryInterface::class,
            EloquentCourseRepository::class,
        );
        $this->app->bind(
            LessonRepositoryInterface::class,
            EloquentLessonRepository::class,
        );
        $this->app->bind(
            EnrollmentRepositoryInterface::class,
            EloquentEnrollmentRepository::class
        );
        $this->app->bind(
            LessonProgressRepositoryInterface::class,
            EloquentLessonProgressRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
