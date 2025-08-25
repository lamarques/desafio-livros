<?php

namespace App\Livros\Application;

use App\Livros\Application\Services\AutorService;

class AutorApplication
{

    public function __construct(private readonly AutorService $autorService)
    {
    }

    public function list(): array
    {
        return $this->autorService->list();
    }
}
