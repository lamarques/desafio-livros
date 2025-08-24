<?php

namespace App\Providers;

use App\Livros\Application\Domain\Repository\AssuntoRepositoryInterface;
use App\Livros\Application\Domain\Repository\AutorRepositoryInterface;
use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Infrastructure\Repository\AssuntoRepository;
use App\Livros\Infrastructure\Repository\AutorRepository;
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
        $this->app->bind(
            AutorRepositoryInterface::class,
            AutorRepository::class
        );
        $this->app->bind(
            AssuntoRepositoryInterface::class,
            AssuntoRepository::class
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
