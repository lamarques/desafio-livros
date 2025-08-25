<?php

namespace App\Livros\Application;

use App\Livros\Application\Services\AssuntoService;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;

class AssuntoApplication
{
    public function __construct(private readonly AssuntoService $assuntoService)
    {
    }

    public function create(AssuntoRequestDto $assuntoDto): AssuntoResponseDto
    {
        return $this->assuntoService->create($assuntoDto);
    }

    public function list(): array
    {
        return $this->assuntoService->list();
    }

    public function show(int $id): ?AssuntoResponseDto
    {
        return $this->assuntoService->show($id);
    }

}
