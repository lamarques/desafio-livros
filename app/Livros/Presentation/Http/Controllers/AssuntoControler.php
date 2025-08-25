<?php

namespace App\Livros\Presentation\Http\Controllers;

use App\Livros\Application\AssuntoApplication;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Presentation\Http\Requests\AssuntoCreateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AssuntoControler extends Controller
{
    public function __construct(private readonly AssuntoApplication $assuntoApplication)
    {
    }

    public function create(AssuntoCreateRequest $request): JsonResponse
    {
        $assuntoDto = new AssuntoRequestDto(
            Descricao: $request->input('Descricao')
        );

        $assuntoResponseDto = $this->assuntoApplication->create($assuntoDto);

        return response()->json([
            'message' => 'Assunto criado com sucesso.',
            'data' => $assuntoResponseDto
        ], 201);

    }

    public function list(): JsonResponse
    {
        $assuntos = $this->assuntoApplication->list();

        return response()->json([
            'data' => $assuntos
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $assunto = $this->assuntoApplication->show($id);

        if (!$assunto) {
            return response()->json(['message' => 'Assunto não encontrado.'], 404);
        }

        return response()->json([
            'data' => $assunto
        ]);
    }

}
