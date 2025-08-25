<?php

namespace App\Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Autor;
use App\Livros\Application\Domain\Repository\AutorRepositoryInterface;
use App\Models\Autor as AutorModel;

class AutorRepository implements AutorRepositoryInterface
{

    public function __construct(
        private readonly AutorModel $autorModel
    )
    {
    }

    #[\Override] public function getAutor(int $codAu): ?Autor
    {
        $autorData = $this->autorModel->find($codAu);

        if (!$autorData) {
            return null;
        }

        return new Autor(
            CodAu: $autorData->CodAu,
            Nome: $autorData->Nome
        );
    }

    #[\Override] public function saveAutor(string $nome): bool
    {
        $autor = new AutorModel();
        $autor->Nome = $nome;

        return $autor->save();
    }

    #[\Override] public function updateAutor(int $codAu, string $nome): bool
    {
        $autor = $this->autorModel->find($codAu);

        if (!$autor) {
            return false;
        }

        $autor->Nome = $nome;

        return $autor->save();
    }

    #[\Override] public function deleteAutor(int $codAu): bool
    {
        $autor = $this->autorModel->find($codAu);

        if (!$autor) {
            return false;
        }

        return $autor->delete();
    }

    #[\Override] public function getAllAutores(): array
    {
        $autoresData = $this->autorModel->all();
        $autores = [];

        foreach ($autoresData as $autorData) {
            $autores[] = new Autor(
                CodAu: $autorData->CodAu,
                Nome: $autorData->Nome
            );
        }

        return $autores;
    }
}
