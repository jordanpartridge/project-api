<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'long_description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['active', 'archived', 'draft']),
            'featured_image' => $this->faker->imageUrl(),
            'demo_url' => $this->faker->url(),
            'is_featured' => $this->faker->boolean(20),
            'display_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function featured(): Factory|ProjectFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_featured' => true,
            ];
        });
    }
}
