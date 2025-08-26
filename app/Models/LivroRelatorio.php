<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @codeCoverageIgnore
 */
class LivroRelatorio extends Model
{
    protected $table = 'vw_livros_autores_assuntos';
    protected $primaryKey = 'Codl';
    public $incrementing = false;
    public $timestamps = false;

    public function scopeBusca($q, $term)
    {
        if($term) {
            $term = "%$term%";
            $q->where(function($query) use ($term) {
                $query->where('Titulo', 'like', $term)
                      ->orWhere('Autores', 'like', $term)
                      ->orWhere('Assuntos', 'like', $term)
                      ->orWhere('Editora', 'like', $term);
            });
        }
        return $q;
    }

    public function scopeAno($q, $inicio, $fim)
    {
        if($inicio) {
            $q->where('AnoPublicacao', '>=', $inicio);
        }
        if($fim) {
            $q->where('AnoPublicacao', '<=', $fim);
        }
        return $q;
    }

    public function scopeValor($q, $min, $max)
    {
        if($min) {
            $q->where('Valor', '>=', $min);
        }
        if($max) {
            $q->where('Valor', '<=', $max);
        }
        return $q;
    }

}
