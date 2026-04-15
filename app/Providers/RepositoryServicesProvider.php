<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use App\Domain\Courses\Repositories\CourseRepositoryInterface;
use App\Domain\Courses\Repositories\LessonRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentLessonRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentUserRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentCourseRepository;

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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
