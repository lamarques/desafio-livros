<?php

namespace App\Livros\Dtos;

class LivrosSaveDto
{
    public function __construct(
        public string $titulo,
        public string $editora,
        public int $edicao,
        public string $anoPublicacao
    ) {
    }
}
