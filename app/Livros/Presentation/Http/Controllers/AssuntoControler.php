<?php

namespace App\Livros\Presentation\Http\Controllers;

use App\Livros\Application\AssuntoApplication;
use App\Livros\Dtos\AssuntoRequestDto;
use App\Livros\Dtos\AssuntoResponseDto;
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

    public function update(int $id, AssuntoCreateRequest $request): JsonResponse
    {
        $descricao = trim((string) $request->input('Descricao'));
       if($this->assuntoApplication->update($id, new AssuntoRequestDto(
            Descricao: $descricao
        ))) {
            return response()->json(
                [
                    'message' => 'Assunto atualizado com sucesso.',
                    'data' => new AssuntoResponseDto(
                        CodAs: $id,
                        Descricao: $descricao
                    )
                ]
            );
        }
        return response()->json(
            [
                'message' => 'Erro ao atualizar o assunto ou assunto nao encontrado.',
                'data' => new AssuntoResponseDto(
                    CodAs: $id,
                    Descricao: $descricao
                )
            ],
            404
        );
    }

    public function delete(int $id): JsonResponse
    {
        if ($this->assuntoApplication->delete($id)) {
            return response()->json(['message' => 'Assunto removido com sucesso.']);
        }

        return response()->json(['message' => 'Erro ao remover o assunto ou assunto não encontrado.'], 404);
    }

}
