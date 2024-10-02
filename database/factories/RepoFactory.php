<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Repo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Repo>
 */
class RepoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->name(),
            'github_id' => $this->faker->unique()->randomNumber(),
            'full_name' => $this->faker->unique()->name(),
            'url' => $this->faker->url(),
            'project_id' => Project::inRandomOrder()->first() ?? Project::factory(),
        ];
    }
}
