<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{

    use HasFactory;

    protected $table = 'Autor';
    protected $primaryKey = 'CodAu';
    public $timestamps = false;

    protected $fillable = [
        'Nome',
    ];

    protected $casts = [
        'CodAu' => 'integer',
    ];

    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'Livro_Autor',
            'Autor_CodAu',
            'Livro_Codl'
        );
    }
}
