<?php

namespace App\Livros\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LivroCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (['AutorID', 'AssuntoID'] as $key) {
            $value = $this->input($key);
            if (!is_null($value) && !is_array($value)) {
                $this->merge([$key => [$value]]);
            } elseif (is_array($value)) {
                $this->merge([$key => array_values($value)]);
            }
        }
    }

    public function rules()
    {
        return [
            'Titulo' => 'required|string|max:40',
            'Editora' => 'required|string|max:40',
            'Edicao' => 'required|integer|min:1',
            'AnoPublicacao' => 'required|string|max:4|max:' . date('Y'),
            'AutorID' => 'required|array|min:1',
            'AutorID.*' => 'integer|distinct|exists:Autor,CodAu',
            'AssuntoID' => 'required|array|min:1',
            'AssuntoID.*' => 'integer|distinct|exists:Assunto,CodAs',
        ];
    }

    public function messages()
    {
        return [
            'Titulo.required' => 'O campo Titulo é obrigatório.',
            'Titulo.string' => 'O campo Titulo deve ser uma string.',
            'Titulo.max' => 'O campo Titulo deve ter no máximo 40 caracteres.',
            'Editora.required' => 'O campo Editora é obrigatório.',
            'Editora.string' => 'O campo Editora deve ser uma string.',
            'Editora.max' => 'O campo Editora deve ter no máximo 40 caracteres.',
            'Edicao.required' => 'O campo Edicao é obrigatório.',
            'Edicao.integer' => 'O campo Edicao deve ser um número inteiro.',
            'Edicao.min' => 'O campo Edicao deve ser no mínimo 1.',
            'AnoPublicacao.required' => 'O campo AnoPublicacao é obrigatório.',
            'AnoPublicacao.string' => 'O campo AnoPublicacao deve ser uma string.',
            'AnoPublicacao.max' => 'O campo AnoPublicacao deve ter no máximo 4 caracteres e não pode ser maior que o ano atual.',
            'AutorID.required'   => 'Informe pelo menos um autor.',
            'AutorID.array'      => 'O campo AutorID deve ser uma lista de IDs.',
            'AutorID.*.integer'  => 'Cada AutorID deve ser um número inteiro.',
            'AutorID.*.distinct' => 'AutorID duplicado na lista.',
            'AutorID.*.exists'   => 'AutorID :input não existe.',
            'AssuntoID.required'   => 'Informe pelo menos um assunto.',
            'AssuntoID.array'      => 'O campo AssuntoID deve ser uma lista de IDs.',
            'AssuntoID.*.integer'  => 'Cada AssuntoID deve ser um número inteiro.',
            'AssuntoID.*.distinct' => 'AssuntoID duplicado na lista.',
            'AssuntoID.*.exists'   => 'AssuntoID :input não existe.',
        ];
    }
}
