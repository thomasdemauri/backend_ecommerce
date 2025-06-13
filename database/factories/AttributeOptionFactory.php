<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AttributeOption>
 */
class AttributeOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $labelAndValue = $this->faker->word();
        
        return [
            'attribute_id' => Attribute::factory(),
            'label' => $labelAndValue,
            'value' => $labelAndValue,
        ];
    }
}
