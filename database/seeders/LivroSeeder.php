<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LivroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Livro::factory(10)->create([
            'Codl' => null, // Assuming Codl is auto-incremented
            'Titulo' => 'Livro de Exemplo',
            'Editora' => 'Editora Exemplo',
            'Edicao' => 1,
            'AnoPublicacao' => '2023',
        ]);
    }
}
