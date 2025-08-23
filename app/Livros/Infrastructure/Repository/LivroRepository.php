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
        $livro = new LivroModel();
        $livro->Titulo = $livroData->titulo;
        $livro->Editora = $livroData->editora;
        $livro->Edicao = $livroData->edicao;
        $livro->AnoPublicacao = $livroData->anoPublicacao;

        return $livro->save();
    }

    public function updateLivro(LivroRequestDto $codl, LivrosSaveDto $livroData): bool
    {
        $livro = $this->livroModel->find($codl->Codl);
        if (!$livro) {
            return false; // Livro não encontrado
        }

        $livro->Titulo = $livroData->titulo;
        $livro->Editora = $livroData->editora;
        $livro->Edicao = $livroData->edicao;
        $livro->AnoPublicacao = $livroData->anoPublicacao;

        return $livro->save();
    }

    public function deleteLivro(LivroResponseDto $livroResponseDto): bool
    {
        $livro = $this->livroModel->find($livroResponseDto->Codl);
        if (!$livro) {
            return false;
        }

        return (bool) $livro->delete();
    }

    public function listLivros(): array
    {
        $livros = $this->livroModel->all();
        $result = [];

        foreach ($livros as $livro) {
            $result[] = new LivroResponseDto(
                $livro->Codl,
                $livro->Titulo,
                $livro->Editora,
                $livro->Edicao,
                $livro->AnoPublicacao
            );
        }

        return $result;
    }
}
