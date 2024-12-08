<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Owner>
 */
class OwnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'login' => $this->faker->name(),
            'avatar_url' => $this->faker->imageUrl(),
            'html_url' => $this->faker->url(),
            'type' => $this->faker->randomElement(['user', 'organization']),
        ];
    }
}
