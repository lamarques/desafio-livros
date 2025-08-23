<?php

namespace App\Providers;

use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Infrastructure\Repository\LivroRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            LivroRepositoryInterface::class,
            LivroRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
