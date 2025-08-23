<?php

namespace App\Livros\Infrastructure\Repository;

use App\Livros\Application\Domain\Entity\Livro;
use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;
use App\Models\Livro as LivroModel;

class LivroRepository implements LivroRepositoryInterface
{

    public function __construct(private readonly LivroModel $livroModel)
    {
    }

    public function getLivro(LivroRequestDto $codl): ?Livro
    {
        $data = $this->livroModel->find($codl->Codl);
        if (!$data) {
            return null;
        }
        return new Livro(
            $data->Codl,
            $data->Titulo,
            $data->Editora,
            $data->Edicao,
            $data->AnoPublicacao
        );
    }

    public function saveLivro(LivrosSaveDto $livroData): bool
    {
        // TODO: Implement saveLivro() method.
    }

    public function updateLivro(LivroRequestDto $codl, LivrosSaveDto $livroData): bool
    {
        // TODO: Implement updateLivro() method.
    }

    public function deleteLivro(LivroResponseDto $codl): bool
    {
        // TODO: Implement deleteLivro() method.
    }

    public function listLivros(): array
    {
        // TODO: Implement listLivros() method.
    }
}
