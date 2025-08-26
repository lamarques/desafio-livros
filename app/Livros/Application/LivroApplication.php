<?php

namespace App\Livros\Application;

use App\Livros\Application\Services\LivroService;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;

readonly class LivroApplication
{

    public function __construct(
        private LivroService $livroService
    )
    {
    }

    public function getLivro(LivroRequestDto $livroRequestDto): ?LivroResponseDto
    {
        $livro = $this->livroService->getLivro($livroRequestDto);

        if (!$livro) {
            return null;
        }

        return $livro;
    }

    public function createLivro(array $livroData): LivroResponseDto
    {
        $livroSaveDto = new LivrosSaveDto(
            $livroData['Titulo'],
            $livroData['Editora'],
            $livroData['Edicao'],
            $livroData['AnoPublicacao'],
            $livroData['Valor'],
            $livroData['AutorID'],
            $livroData['AssuntoID']
        );

        return $this->livroService->createLivro($livroSaveDto);
    }

    public function listLivros(): array
    {
        return $this->livroService->listLivros();
    }

    public function updateLivro(int $codl, array $livroData): bool
    {
        $livroSaveDto = new LivrosSaveDto(
            $livroData['Titulo'],
            $livroData['Editora'],
            $livroData['Edicao'],
            $livroData['AnoPublicacao'],
            $livroData['Valor'],
            $livroData['AutorID'],
            $livroData['AssuntoID']
        );

        $livroRequestDto = new LivroRequestDto($codl);

        return $this->livroService->updateLivro($livroRequestDto, $livroSaveDto);
    }

    public function deleteLivro(LivroRequestDto $codl): bool
    {
        return $this->livroService->deleteLivro($codl);
    }

}
