<?php

namespace App\Livros\Application\Domain\Entity;

class Livro
{

    public function __construct(
        private int $Codl,
        private string $Titulo,
        private string $Editora,
        private int $Edicao,
        private string $AnoPublicacao
    )
    {
    }

    public function getCodl(): int
    {
        return $this->Codl;
    }

    public function getTitulo(): string
    {
        return $this->Titulo;
    }

    public function getEditora(): string
    {
        return $this->Editora;
    }

    public function getEdicao(): int
    {
        return $this->Edicao;
    }

    public function getAnoPublicacao(): string
    {
        return $this->AnoPublicacao;
    }
}
