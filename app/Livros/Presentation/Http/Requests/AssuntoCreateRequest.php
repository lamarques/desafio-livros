<?php

namespace App\Livros\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssuntoCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'Descricao' => 'required|string|max:20',
        ];
    }

    /**
     * Get the custom messages for the validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Descricao.required' => 'O campo Descricao é obrigatório.',
            'Descricao.string' => 'O campo Descricao deve ser uma string.',
            'Descricao.max' => 'O campo Descricao não pode ter mais de 20 caracteres.',
        ];
    }
}
