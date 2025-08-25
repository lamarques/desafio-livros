<?php

namespace App\Livros\Presentation\Http\Controllers;

use App\Livros\Application\AutorApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class AutorController extends Controller
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
