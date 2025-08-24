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
    protected $casts = [
        'Codl' => 'integer',
    ];

    public function autores()
    {
        return $this->belongsToMany(
            Autor::class,
            'Livro_Autor',
            'Livro_Codl',
            'Autor_CodAu'
        );
    }

    public function assuntos()
    {
        return $this->belongsToMany(
            Assunto::class,
            'Livro_Assuntos',
            'Livro_Codl',
            'Assunto_CodAs'
        );
    }


}
