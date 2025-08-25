<?php

namespace App\Livros\Presentation\Http\Controllers;

use App\Livros\Application\AutorApplication;
use Illuminate\Http\JsonResponse;

class AutorController
{

    public function __construct(private readonly AutorApplication $autorApplication)
    {
    }

    public function list(): JsonResponse
    {
        return response()->json([
            'data' => $this->autorApplication->list()
        ]);
    }

}
