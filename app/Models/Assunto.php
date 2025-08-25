<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class Assunto extends Model
{
    use HasFactory;

    protected $table = 'Assunto';
    protected $primaryKey = 'CodAs';
    protected $fillable = [
        'Descricao',
    ];
    public $timestamps = false;
    protected $casts = [
        'CodAs' => 'integer',
    ];

    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'Livro_Assuntos',
            'Assunto_CodAs',
            'Livro_Codl'
        );
    }
}
