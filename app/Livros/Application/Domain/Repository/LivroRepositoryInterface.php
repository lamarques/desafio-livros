<?php

namespace App\Livros\Application\Domain\Repository;

use App\Livros\Application\Domain\Entity\Livro;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;

interface LivroRepositoryInterface
{
    public function getLivro(LivroRequestDto $codl): ?Livro;
    public function saveLivro(LivrosSaveDto $livroData): bool;
    public function updateLivro(LivroRequestDto $codl, LivrosSaveDto $livroData): bool;
    public function deleteLivro(LivroRequestDto $codl): bool;
    public function listLivros(): array;
}
