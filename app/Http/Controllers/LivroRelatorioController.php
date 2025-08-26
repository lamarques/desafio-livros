<?php

namespace App\Http\Controllers;

use App\Models\LivroRelatorio;
use Illuminate\Http\Request;

/**
 * @codeCoverageIgnore
 */
class LivroRelatorioController extends Controller
{
    public function index(Request $r) {
        $q = LivroRelatorio::query()
            ->busca($r->query('q'))
            ->ano($r->query('ano_ini'), $r->query('ano_fim'))
            ->valor($r->query('valor_min'), $r->query('valor_max'))
            ->when($r->query('editora'), fn($q,$v)=>$q->where('Editora',$v))
            ->orderBy($r->query('order','Titulo'));

        return $q->paginate($r->integer('per_page', 20));
    }

    public function exportCsv(Request $r) {
        $rows = $this->baseQuery($r)->get();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename=livros.csv'];
        $callback = function() use ($rows){
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Codl','Titulo','Edicao','Editora','Ano','Valor','Autores','Assuntos']);
            foreach ($rows as $row) fputcsv($out, [
                $row->Codl,$row->Titulo,$row->Edicao,$row->Editora,$row->AnoPublicacao,$row->Valor,$row->Autores,$row->Assuntos
            ]);
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }

    protected function baseQuery(Request $r) {
        return LivroRelatorio::query()
            ->busca($r->query('q'))
            ->ano($r->query('ano_ini'), $r->query('ano_fim'))
            ->valor($r->query('valor_min'), $r->query('valor_max'))
            ->when($r->query('editora'), fn($q,$v)=>$q->where('Editora',$v));
    }
}
