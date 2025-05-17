<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'arquivo_3d' => 'objetos/' . $this->faker->uuid . '.glb',
            'capa'      => 'produtos/fotos/' . $this->faker->uuid . '-capa.jpeg',
            'titulo' => $this->faker->sentence(3),
            'descricao' => $this->faker->paragraph(),
            'valor' => $this->faker->randomFloat(2, 45, 4000)
        ];
    }
}
