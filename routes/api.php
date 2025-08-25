<?php

use App\Livros\Presentation\Http\Controllers\AssuntoControler;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'assunto'], function () {
    Route::post('/', [AssuntoControler::class, 'create'])
        ->name('api.assunto.create');
    Route::get('/', [AssuntoControler::class, 'list'])
        ->name('api.assunto.index');
    Route::get('/{id}', [AssuntoControler::class, 'show'])
        ->name('api.assunto.show');
});
