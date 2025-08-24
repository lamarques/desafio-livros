<?php

namespace App\Livros\Dtos;

class AssuntoRequestDto
{
    public function __construct(
        public string $Descricao
    ) {
    }
}
