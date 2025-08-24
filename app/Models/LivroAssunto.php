<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class LivroAssunto extends Model
{
    protected $table = 'Livro_Assuntos';
    public $timestamps = false;

    protected $fillable = [
        'Livro_Codl',
        'Assunto_CodAs',
    ];

    protected $casts = [
        'Livro_Codl' => 'integer',
        'Assunto_CodAs' => 'integer',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'Livro_Codl', 'Codl');
    }

    public function assunto()
    {
        return $this->belongsTo(Assunto::class, 'Assunto_CodAs', 'CodAs');
    }
}
