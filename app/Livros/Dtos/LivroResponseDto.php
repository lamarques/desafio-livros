<?php

namespace App\Livros\Dtos;

class LivroResponseDto
{
    public function __construct(
        public int $Codl,
        public string $Titulo,
        public string $Editora,
        public int $Edicao,
        public string $AnoPublicacao
    )
    {
    }
}
