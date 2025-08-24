<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivroAutor extends Model
{
    protected $table = 'Livro_Autor';
    public $timestamps = false;

    protected $fillable = [
        'Livro_Codl',
        'Autor_CodAu',
    ];

    protected $casts = [
        'Livro_Codl' => 'integer',
        'Autor_CodAu' => 'integer',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'Livro_Codl', 'Codl');
    }

    public function autor()
    {
        return $this->belongsTo(Autor::class, 'Autor_CodAu', 'CodAu');
    }
}
