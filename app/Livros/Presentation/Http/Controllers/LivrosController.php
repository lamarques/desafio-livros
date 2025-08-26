<?php

namespace App\Livros\Presentation\Http\Controllers;

use App\Livros\Application\LivroApplication;
use App\Livros\Dtos\LivroRequestDto;
use App\Livros\Presentation\Http\Requests\LivroCreateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class LivrosController extends Controller
{

    public function __construct(private readonly LivroApplication $livroApplication)
    {
    }

    public function create(LivroCreateRequest $request): JsonResponse
    {
        return response()->json([
            'message' => 'Livro criado com sucesso.',
            'data' => $this->livroApplication->createLivro($request->all())
        ], 201);
    }

    public function list(): JsonResponse
    {
        return response()->json(
            ['data' => $this->livroApplication->listLivros()],
            200
        );
    }

    public function show($id): JsonResponse
    {
        return response()->json(
            ['data' => $this->livroApplication->getLivro(
                new LivroRequestDto($id)
            )],
            200
        );
    }

    public function update($id, LivroCreateRequest $request): JsonResponse
    {
        if ($this->livroApplication->updateLivro($id, $request->all())) {
            return response()->json([
                'message' => 'Livro atualizado com sucesso.'
            ], 200);
        }
        return response()->json([
            'message' => 'Livro não encontrado.'
        ], 404);
    }

    public function delete($id): JsonResponse
    {
        if ($this->livroApplication->deleteLivro(new LivroRequestDto($id))) {
            return response()->json([
                'message' => 'Livro deletado com sucesso.'
            ], 200);
        }
        return response()->json([
            'message' => 'Livro não encontrado.'
        ], 404);
    }
}
