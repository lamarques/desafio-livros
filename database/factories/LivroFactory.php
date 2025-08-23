<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Livro>
 */
class LivroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'Titulo' => $this->faker->sentence(3),
            'Editora' => $this->faker->company(),
            'Edicao' => $this->faker->numberBetween(1, 10),
            'AnoPublicacao' => $this->faker->year(),
        ];
    }
}
