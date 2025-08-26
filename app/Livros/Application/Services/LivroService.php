<?php

namespace App\Livros\Application\Services;

use App\Livros\Application\Domain\Repository\LivroRepositoryInterface;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Dtos\LivroResponseDto;
use App\Livros\Dtos\LivrosSaveDto;

class LivroService
{

    public function __construct(private readonly LivroRepositoryInterface $livroRepository)
    {
    }

    public function createLivro(LivrosSaveDto $livroSaveDto): LivroResponseDto
    {
        $this->livroRepository->saveLivro($livroSaveDto);

        $data = $this->livroRepository->getLivro(new LivroRequestDto($this->livroRepository->getLastInsertedId()));

        return new LivroResponseDto(
            $data->getCodl(),
            $data->getTitulo(),
            $data->getEditora(),
            $data->getEdicao(),
            $data->getAnoPublicacao(),
            $data->getValor(),
            $data->getAutores(),
            $data->getAssuntos()
        );
    }

    public function listLivros(): array
    {
        return $this->livroRepository->listLivros();
    }

    public function getLivro(LivroRequestDto $codl): ?LivroResponseDto
    {
        $data = $this->livroRepository->getLivro($codl);
        if (!$data) {
            return null;
        }
        return new LivroResponseDto(
            $data->getCodl(),
            $data->getTitulo(),
            $data->getEditora(),
            $data->getEdicao(),
            $data->getAnoPublicacao(),
            $data->getValor(),
            $data->getAutores(),
            $data->getAssuntos()
        );
    }

    public function updateLivro(LivroRequestDto $codl, LivrosSaveDto $livroData): bool
    {
        $existingLivro = $this->livroRepository->getLivro($codl);
        if (!$existingLivro) {
            return false;
        }
        return $this->livroRepository->updateLivro($codl, $livroData);
    }

    public function deleteLivro(LivroRequestDto $codl): bool
    {
        return $this->livroRepository->deleteLivro($codl);
    }

}
