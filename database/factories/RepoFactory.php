<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Owner;
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
            'full_name' => $this->faker->unique()->name(),
            'language_id' => Language::factory()->create(),
            'url' => $this->faker->url(),
            'owner_id' => Owner::factory()->create(),
            'project_id' => Project::inRandomOrder()->first() ?? Project::factory(),
            'description' => $this->faker->paragraph(),
            'private' => $this->faker->boolean(),
        ];
    }
}
