<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            
            'full_name' => fake()->name(),
            'tax_id' => $this->faker->numerify('###########'),
            'phone' => $this->faker->phoneNumber,
            'address_line1' => $this->faker->streetAddress,
            'address_line2' => $this->faker->secondaryAddress,
            'neighborhood' => $this->faker->word, 
            'city' => $this->faker->city,
            'state' => $this->faker->randomElement(['SP', 'MG', 'PR', 'BH']),
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->randomElement(['BR', 'EUA', 'JP', 'AUS']),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'is_seller' => $this->faker->boolean
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Muda estado de vendedor para true quando chamado
     */
    public function seller(): Factory
    {
        return $this->state(fn () => [
            'is_seller' => true,
        ]);
    }
}
