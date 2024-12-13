<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Owner;

class OwnerFactory extends Factory
{
    protected $model = Owner::class;

    public function definition(): array
    {
        return [
            'github_id' => (string) fake()->unique()->randomNumber(8),
            'login' => fake()->unique()->userName(),
            'type' => fake()->randomElement(['User', 'Organization']),
            'avatar_url' => fake()->imageUrl(),
            'html_url' => fake()->url(),
        ];
    }

    public function user(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'User',
        ]);
    }

    public function organization(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Organization',
        ]);
    }
}
