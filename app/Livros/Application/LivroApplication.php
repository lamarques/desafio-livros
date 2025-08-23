<?php

namespace App\Livros\Application;

use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;

readonly class LivroApplication
{

    public function __construct(private LivroRepositoryInterface $livroRepository)
    {
    }

    public function getLivro(LivroRequestDto $livroRequestDto): LivroResponseDto
    {
        $livro = $this->livroRepository->getLivro($livroRequestDto);
        return new LivroResponseDto(
            $livro->getCodl(),
            $livro->getTitulo(),
            $livro->getEditora(),
            $livro->getEdicao(),
            $livro->getAnoPublicacao()
        );
    }

}
