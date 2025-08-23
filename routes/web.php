<?php

use App\Http\Controllers\AutorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('autor', AutorController::class)
    ->parameters(['autor' => 'autor'])
    ->names('autor');
