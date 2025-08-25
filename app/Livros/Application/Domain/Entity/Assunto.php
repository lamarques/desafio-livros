<?php

namespace App\Livros\Application\Domain\Entity;

class Assunto
{
    public function __construct(
        private int $CodAs,
        private string $Descricao
    ) {
    }

    public function getCodAs(): int
    {
        return $this->CodAs;
    }

    public function getDescricao(): string
    {
        return $this->Descricao;
    }

}
