<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class Livro extends Model
{
    use HasFactory;

    protected $table = 'Livro';

    protected $primaryKey = 'Codl';
    public $timestamps = false;
    protected $fillable = [
        'Titulo',
        'Editora',
        'Edicao',
        'AnoPublicacao',
        'Valor',
    ];
    protected $casts = [
        'Codl' => 'integer',
        'Valor' => 'float',
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
            'Livro_Assunto',
            'Livro_Codl',
            'Assunto_CodAs'
        );
    }


}
