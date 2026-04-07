<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Infrastructure\Persistance\Eloquent\Repositories\EloquentUserRepository;

class RepositoryServicesProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class, EloquentUserRepository::class
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
