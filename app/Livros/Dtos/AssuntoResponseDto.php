<?php

namespace App\Livros\Dtos;

class AssuntoResponseDto
{
    public function __construct(
        public int $CodAs,
        public string $Descricao
    ) {
    }
}
