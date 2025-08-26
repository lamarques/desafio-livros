<?php

use App\Http\Controllers\AutorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('autor', AutorController::class)
    ->parameters(['autor' => 'autor'])
    ->names('autor');

Route::get('/books', function () {
    return view('welcome');
});

Route::get('/authors', function () {
    return view('welcome');
});

Route::get('/subjects', function () {
    return view('welcome');
});

Route::get('/relatorios', function () {
    return view('welcome');
})->name('relatorios.index');
