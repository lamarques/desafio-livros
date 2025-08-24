<?php

namespace App\Livros\Application\Domain\Entity;

class Autor
{
    public function __construct(
        private int $CodAu,
        private string $Nome
    ) {
    }

    public function getCodAu(): int
    {
        return $this->CodAu;
    }

    public function getNome(): string
    {
        return $this->Nome;
    }

}
