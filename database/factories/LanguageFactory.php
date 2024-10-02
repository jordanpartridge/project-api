<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     * I know tdd blah blah blah, but do you hate messing up migrations?
     * well write tests stupid.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // wow one field good thing you wrote a factory (and tests)
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
