<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->seller(),
            'store_name' => $this->faker->word,
            'slug' => $this->faker->unique()->slug,
            'store_image_url' => $this->faker->imageUrl(640, 480, 'business', true, 'store'),
        ];
    }
}
