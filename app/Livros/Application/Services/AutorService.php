<?php

namespace App\Livros\Application\Services;

use App\Livros\Application\Domain\Repository\AutorRepositoryInterface;

class AutorService
{

    public function __construct(public readonly AutorRepositoryInterface $autorRepository)
    {
    }

    public function list(): array
    {
        $data = $this->autorRepository->getAllAutores();

        return array_map(static fn($autor) => [
            'CodAu' => $autor->getCodAu(),
            'Nome' => $autor->getNome()
        ], $data);
    }

}
