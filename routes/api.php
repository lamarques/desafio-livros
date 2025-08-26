<?php

use App\Http\Controllers\LivroRelatorioController;
use App\Livros\Presentation\Http\Controllers\AssuntoControler;
use App\Livros\Presentation\Http\Controllers\AutorController;
use App\Livros\Presentation\Http\Controllers\LivrosController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'assunto'], function () {
    Route::post('/', [AssuntoControler::class, 'create'])
        ->name('api.assunto.create');
    Route::get('/', [AssuntoControler::class, 'list'])
        ->name('api.assunto.index');
    Route::get('/{id}', [AssuntoControler::class, 'show'])
        ->name('api.assunto.show');
    Route::put('/{id}', [AssuntoControler::class, 'update'])
        ->name('api.assunto.update');
    Route::delete('/{id}', [AssuntoControler::class, 'delete'])
        ->name('api.assunto.delete');
});

Route::group(['prefix' => 'autor'], function () {
    Route::get('/', [AutorController::class, 'list'])
        ->name('api.autor.index');
});

Route::group(['prefix' => 'livro'], function () {
    Route::post('/', [LivrosController::class, 'create'])
        ->name('api.livro.create');
    Route::get('/', [LivrosController::class, 'list'])
        ->name('api.livro.index');
    Route::get('/{id}', [LivrosController::class, 'show'])
        ->name('api.livro.show');
    Route::put('/{id}', [LivrosController::class, 'update'])
        ->name('api.livro.update');
    Route::delete('/{id}', [LivrosController::class, 'delete'])
        ->name('api.livro.delete');
});

Route::get('/relatorios/livros', [LivroRelatorioController::class, 'index']);
Route::get('/relatorios/livros.csv', [LivroRelatorioController::class, 'exportCsv']);
