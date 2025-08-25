<?php

namespace App\Livros\Application\Domain\Repository;

use App\Livros\Application\Domain\Entity\Autor;

interface AutorRepositoryInterface
{
    public function getAutor(int $codAu): ?Autor;

    public function saveAutor(string $nome): bool;

    public function updateAutor(int $codAu, string $nome): bool;

    public function deleteAutor(int $codAu): bool;

    public function getAllAutores(): array;
}
