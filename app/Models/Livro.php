<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    /** @use HasFactory<\Database\Factories\LivroFactory> */
    use HasFactory;

    protected $table = 'Livro';

    protected $primaryKey = 'Codl';
    public $timestamps = false;
    protected $fillable = [
        'Titulo',
        'Editora',
        'Edicao',
        'AnoPublicacao',
    ];


}
