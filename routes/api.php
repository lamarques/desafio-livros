<?php

use App\Livros\Presentation\Http\Controllers\AssuntoControler;
use App\Livros\Presentation\Http\Controllers\AutorController;
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
